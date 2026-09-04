<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Quality;

use Ksfraser\CommonDb\Contract\DbConnectionInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Repository for 8D quality reports.
 *
 * @BABOK Related: FR-QC-001-001, FR-QC-001-002
 * @since 1.0.0
 */
class EightDRepository
{
    /** @var DbConnectionInterface */
    private $db;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $table;

    public function __construct(
        DbConnectionInterface $db,
        ?LoggerInterface $logger = null,
        string $table = '0_ksf_quality_8d'
    ) {
        $this->db = $db;
        $this->logger = $logger ?? new NullLogger();
        $this->table = $table;
    }

    /**
     * Create a new 8D report.
     */
    public function create(EightDDTO $eightD): EightDDTO
    {
        $sql = "INSERT INTO {$this->table}
                (eightd_number, problem_description, part_number, qty_affected, symptom,
                 status, priority, created_by, created_at, due_date, customer_id,
                 supplier_id, reference_po, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->executeUpdate($sql, [
            $eightD->getEightDNumber(),
            $eightD->getProblemDescription(),
            $eightD->getPartNumber(),
            $eightD->getQtyAffected(),
            $eightD->getSymptom(),
            $eightD->getStatus(),
            $eightD->getPriority(),
            $eightD->getCreatedBy(),
            $eightD->getCreatedAt()->format('Y-m-d H:i:s'),
            $eightD->getDueDate()?->format('Y-m-d'),
            $eightD->getCustomerId(),
            $eightD->getSupplierId(),
            $eightD->getReferencePo(),
            $eightD->getNotes(),
        ]);

        $id = (int) $this->db->lastInsertId();
        return $this->findById($id);
    }

    /**
     * Find by ID.
     */
    public function findById(int $id): ?EightDDTO
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $row = $this->db->fetchAssoc($sql, [$id]);

        if ($row === false) {
            return null;
        }

        return EightDDTO::fromArray($row);
    }

    /**
     * Find by 8D number.
     */
    public function findByNumber(string $number): ?EightDDTO
    {
        $sql = "SELECT * FROM {$this->table} WHERE eightd_number = ?";
        $row = $this->db->fetchAssoc($sql, [$number]);

        if ($row === false) {
            return null;
        }

        return EightDDTO::fromArray($row);
    }

    /**
     * Find all with optional filters.
     */
    public function findAll(?string $status = null, ?string $priority = null, int $limit = 100): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($status !== null) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        if ($priority !== null) {
            $sql .= " AND priority = ?";
            $params[] = $priority;
        }

        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;

        $rows = $this->db->fetchAll($sql, $params);
        return array_map(fn(array $row) => EightDDTO::fromArray($row), $rows);
    }

    /**
     * Update an 8D.
     */
    public function update(EightDDTO $eightD): bool
    {
        if ($eightD->getId() === null) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET
                problem_description = ?, part_number = ?, qty_affected = ?,
                symptom = ?, status = ?, priority = ?, updated_at = NOW(),
                due_date = ?, customer_id = ?, supplier_id = ?,
                reference_po = ?, notes = ?
                WHERE id = ?";

        $affected = $this->db->executeUpdate($sql, [
            $eightD->getProblemDescription(),
            $eightD->getPartNumber(),
            $eightD->getQtyAffected(),
            $eightD->getSymptom(),
            $eightD->getStatus(),
            $eightD->getPriority(),
            $eightD->getDueDate()?->format('Y-m-d'),
            $eightD->getCustomerId(),
            $eightD->getSupplierId(),
            $eightD->getReferencePo(),
            $eightD->getNotes(),
            $eightD->getId(),
        ]);

        return $affected > 0;
    }

    /**
     * Close an 8D.
     */
    public function close(int $id, int $userId): bool
    {
        $sql = "UPDATE {$this->table} SET status = 'Closed', closed_at = NOW(), closed_by = ? WHERE id = ? AND status != 'Closed'";
        $affected = $this->db->executeUpdate($sql, [$userId, $id]);
        return $affected > 0;
    }

    /**
     * Generate next 8D number.
     */
    public function generateEightDNumber(): string
    {
        $year = date('Y');
        $sql = "SELECT COUNT(*) + 1 as next_num FROM {$this->table} WHERE eightd_number LIKE ?";
        $result = $this->db->fetchAssoc($sql, ["8D-{$year}-%"]);
        $nextNum = (int) ($result['next_num'] ?? 1);
        return sprintf('8D-%s-%04d', $year, $nextNum);
    }

    /**
     * Get team members for an 8D.
     */
    public function getTeamMembers(int $eightDId): array
    {
        $sql = "SELECT * FROM 0_ksf_quality_8d_team WHERE eightd_id = ?";
        $rows = $this->db->fetchAll($sql, [$eightDId]);
        return array_map(fn(array $row) => TeamMemberDTO::fromArray($row), $rows);
    }

    /**
     * Add team member.
     */
    public function addTeamMember(TeamMemberDTO $member): TeamMemberDTO
    {
        $sql = "INSERT INTO 0_ksf_quality_8d_team (eightd_id, user_id, role) VALUES (?, ?, ?)";
        $this->db->executeUpdate($sql, [$member->getEightDId(), $member->getUserId(), $member->getRole()]);
        $id = (int) $this->db->lastInsertId();
        $result = $this->db->fetchAssoc("SELECT * FROM 0_ksf_quality_8d_team WHERE id = ?", [$id]);
        return TeamMemberDTO::fromArray($result);
    }

    /**
     * Get containment actions.
     */
    public function getContainmentActions(int $eightDId): array
    {
        $sql = "SELECT * FROM 0_ksf_quality_8d_containment WHERE eightd_id = ? ORDER BY target_date";
        $rows = $this->db->fetchAll($sql, [$eightDId]);
        return array_map(fn(array $row) => ContainmentActionDTO::fromArray($row), $rows);
    }

    /**
     * Add containment action.
     */
    public function addContainmentAction(ContainmentActionDTO $action): ContainmentActionDTO
    {
        $sql = "INSERT INTO 0_ksf_quality_8d_containment
                (eightd_id, action_description, responsible_user_id, target_date)
                VALUES (?, ?, ?, ?)";
        $this->db->executeUpdate($sql, [
            $action->getEightDId(),
            $action->getActionDescription(),
            $action->getResponsibleUserId(),
            $action->getTargetDate()->format('Y-m-d'),
        ]);
        $id = (int) $this->db->lastInsertId();
        $result = $this->db->fetchAssoc("SELECT * FROM 0_ksf_quality_8d_containment WHERE id = ?", [$id]);
        return ContainmentActionDTO::fromArray($result);
    }

    /**
     * Get root causes.
     */
    public function getRootCauses(int $eightDId): array
    {
        $sql = "SELECT * FROM 0_ksf_quality_8d_root_cause WHERE eightd_id = ?";
        $rows = $this->db->fetchAll($sql, [$eightDId]);
        return array_map(fn(array $row) => RootCauseDTO::fromArray($row), $rows);
    }

    /**
     * Add root cause.
     */
    public function addRootCause(RootCauseDTO $cause): RootCauseDTO
    {
        $sql = "INSERT INTO 0_ksf_quality_8d_root_cause
                (eightd_id, cause_category, cause_description, evidence, confirmed)
                VALUES (?, ?, ?, ?, ?)";
        $this->db->executeUpdate($sql, [
            $cause->getEightDId(),
            $cause->getCauseCategory(),
            $cause->getCauseDescription(),
            $cause->getEvidence(),
            $cause->isConfirmed() ? 1 : 0,
        ]);
        $id = (int) $this->db->lastInsertId();
        $result = $this->db->fetchAssoc("SELECT * FROM 0_ksf_quality_8d_root_cause WHERE id = ?", [$id]);
        return RootCauseDTO::fromArray($result);
    }

    /**
     * Get corrective actions.
     */
    public function getCorrectiveActions(int $eightDId): array
    {
        $sql = "SELECT * FROM 0_ksf_quality_8d_corrective_action WHERE eightd_id = ?";
        $rows = $this->db->fetchAll($sql, [$eightDId]);
        return array_map(fn(array $row) => CorrectiveActionDTO::fromArray($row), $rows);
    }

    /**
     * Add corrective action.
     */
    public function addCorrectiveAction(CorrectiveActionDTO $action): CorrectiveActionDTO
    {
        $sql = "INSERT INTO 0_ksf_quality_8d_corrective_action
                (eightd_id, root_cause_id, action_description, responsible_user_id,
                 target_date, validation_method, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->db->executeUpdate($sql, [
            $action->getEightDId(),
            $action->getRootCauseId(),
            $action->getActionDescription(),
            $action->getResponsibleUserId(),
            $action->getTargetDate()->format('Y-m-d'),
            $action->getValidationMethod(),
            $action->getStatus(),
        ]);
        $id = (int) $this->db->lastInsertId();
        $result = $this->db->fetchAssoc("SELECT * FROM 0_ksf_quality_8d_corrective_action WHERE id = ?", [$id]);
        return CorrectiveActionDTO::fromArray($result);
    }

    /**
     * Get prevention measures.
     */
    public function getPreventionMeasures(int $eightDId): array
    {
        $sql = "SELECT * FROM 0_ksf_quality_8d_prevention WHERE eightd_id = ?";
        $rows = $this->db->fetchAll($sql, [$eightDId]);
        return array_map(fn(array $row) => PreventionMeasureDTO::fromArray($row), $rows);
    }

    /**
     * Add prevention measure.
     */
    public function addPreventionMeasure(PreventionMeasureDTO $measure): PreventionMeasureDTO
    {
        $sql = "INSERT INTO 0_ksf_quality_8d_prevention
                (eightd_id, prevention_description, system_or_process_changed,
                 responsible_user_id, target_date, status)
                VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->executeUpdate($sql, [
            $measure->getEightDId(),
            $measure->getPreventionDescription(),
            $measure->getSystemOrProcessChanged(),
            $measure->getResponsibleUserId(),
            $measure->getTargetDate()->format('Y-m-d'),
            $measure->getStatus(),
        ]);
        $id = (int) $this->db->lastInsertId();
        $result = $this->db->fetchAssoc("SELECT * FROM 0_ksf_quality_8d_prevention WHERE id = ?", [$id]);
        return PreventionMeasureDTO::fromArray($result);
    }
}