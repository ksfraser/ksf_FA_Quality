<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Quality;

use Ksfraser\FrontAccounting\Common\Exceptions\QualityException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Service for managing 8D quality reports.
 *
 * @BABOK Related: FR-QC-001-001, FR-QC-001-002
 * @since 1.0.0
 */
class EightDService
{
    /** @var EightDRepository */
    private $repository;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        EightDRepository $repository,
        ?LoggerInterface $logger = null
    ) {
        $this->repository = $repository;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Create a new 8D report.
     */
    public function createEightD(
        string $problemDescription,
        int $createdBy,
        string $priority = EightDDTO::PRIORITY_MEDIUM,
        ?string $partNumber = null,
        ?float $qtyAffected = null,
        ?string $symptom = null,
        ?\DateTimeImmutable $dueDate = null,
        ?int $customerId = null
    ): EightDDTO {
        $eightDNumber = $this->repository->generateEightDNumber();

        $eightD = new EightDDTO(
            $eightDNumber,
            $problemDescription,
            $createdBy,
            EightDDTO::STATUS_OPEN,
            $priority
        );

        if ($partNumber !== null) {
            $eightD->partNumber = $partNumber;
        }
        if ($qtyAffected !== null) {
            $eightD->qtyAffected = $qtyAffected;
        }
        if ($symptom !== null) {
            $eightD->symptom = $symptom;
        }
        if ($dueDate !== null) {
            $eightD->dueDate = $dueDate;
        }
        if ($customerId !== null) {
            $eightD->customerId = $customerId;
        }

        $this->logger->info('Creating 8D report', ['number' => $eightDNumber]);

        return $this->repository->create($eightD);
    }

    /**
     * Get 8D by ID with all related data.
     */
    public function getEightDWithDetails(int $id): ?EightDDTO
    {
        $eightD = $this->repository->findById($id);

        if ($eightD === null) {
            return null;
        }

        $eightD->teamMembers = $this->repository->getTeamMembers($id);
        $eightD->containmentActions = $this->repository->getContainmentActions($id);
        $eightD->rootCauses = $this->repository->getRootCauses($id);
        $eightD->correctiveActions = $this->repository->getCorrectiveActions($id);
        $eightD->preventionMeasures = $this->repository->getPreventionMeasures($id);

        return $eightD;
    }

    /**
     * Add team member (D1).
     */
    public function addTeamMember(int $eightDId, int $userId, string $role = 'Team Member'): TeamMemberDTO
    {
        $member = new TeamMemberDTO($eightDId, $userId, $role);
        return $this->repository->addTeamMember($member);
    }

    /**
     * Add containment action (D3).
     */
    public function addContainmentAction(
        int $eightDId,
        string $description,
        int $responsibleUserId,
        \DateTimeImmutable $targetDate
    ): ContainmentActionDTO {
        $action = new ContainmentActionDTO($eightDId, $description, $responsibleUserId, $targetDate);
        return $this->repository->addContainmentAction($action);
    }

    /**
     * Add root cause (D4).
     */
    public function addRootCause(
        int $eightDId,
        string $category,
        string $description,
        bool $confirmed = false,
        ?string $evidence = null
    ): RootCauseDTO {
        $cause = new RootCauseDTO($eightDId, $category, $description, $confirmed);
        if ($evidence !== null) {
            $cause->evidence = $evidence;
        }
        return $this->repository->addRootCause($cause);
    }

    /**
     * Add corrective action (D5).
     */
    public function addCorrectiveAction(
        int $eightDId,
        string $description,
        int $responsibleUserId,
        \DateTimeImmutable $targetDate,
        ?int $rootCauseId = null,
        ?string $validationMethod = null
    ): CorrectiveActionDTO {
        $action = new CorrectiveActionDTO($eightDId, $description, $responsibleUserId, $targetDate, $rootCauseId);
        if ($validationMethod !== null) {
            $action->validationMethod = $validationMethod;
        }
        return $this->repository->addCorrectiveAction($action);
    }

    /**
     * Add prevention measure (D7).
     */
    public function addPreventionMeasure(
        int $eightDId,
        string $description,
        int $responsibleUserId,
        \DateTimeImmutable $targetDate,
        ?string $systemOrProcessChanged = null
    ): PreventionMeasureDTO {
        $measure = new PreventionMeasureDTO($eightDId, $description, $responsibleUserId, $targetDate);
        if ($systemOrProcessChanged !== null) {
            $measure->systemOrProcessChanged = $systemOrProcessChanged;
        }
        return $this->repository->addPreventionMeasure($measure);
    }

    /**
     * Close 8D - validates completeness.
     */
    public function closeEightD(int $id, int $userId): void
    {
        $eightD = $this->getEightDWithDetails($id);

        if ($eightD === null) {
            throw new QualityException("8D report not found: {$id}");
        }

        if ($eightD->getStatus() === EightDDTO::STATUS_CLOSED) {
            throw new QualityException("8D report already closed: {$eightD->getEightDNumber()}");
        }

        if (!$this->isComplete($eightD)) {
            throw new QualityException(
                "Cannot close 8D: Missing required content. " .
                "D1(Team): " . count($eightD->getTeamMembers()) . ", " .
                "D3(Containment): " . count($eightD->getContainmentActions()) . ", " .
                "D4(Root Cause): " . count($eightD->getRootCauses()) . ", " .
                "D5(Corrective): " . count($eightD->getCorrectiveActions()) . ", " .
                "D7(Prevention): " . count($eightD->getPreventionMeasures())
            );
        }

        $this->repository->close($id, $userId);
        $this->logger->info('Closed 8D report', ['id' => $id, 'by' => $userId]);
    }

    /**
     * Check if 8D has all required content.
     */
    public function isComplete(EightDDTO $eightD): bool
    {
        return count($eightD->getTeamMembers()) > 0
            && count($eightD->getContainmentActions()) > 0
            && count($eightD->getRootCauses()) > 0
            && count($eightD->getCorrectiveActions()) > 0
            && count($eightD->getPreventionMeasures()) > 0;
    }

    /**
     * List all 8D reports with optional filter.
     */
    public function listEightDs(?string $status = null, ?string $priority = null): array
    {
        return $this->repository->findAll($status, $priority);
    }

    /**
     * Get completeness percentage.
     */
    public function getCompleteness(EightDDTO $eightD): int
    {
        $required = ['teamMembers', 'containmentActions', 'rootCauses', 'correctiveActions', 'preventionMeasures'];
        $completed = 0;

        foreach ($required as $field) {
            if (count($eightD->$field) > 0) {
                $completed++;
            }
        }

        return (int) (($completed / count($required)) * 100);
    }
}