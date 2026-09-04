<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Quality;

use DateTimeImmutable;

/**
 * 8D Quality Report data transfer object.
 *
 * @BABOK Related: FR-QC-001-001, FR-QC-001-002
 * @since 1.0.0
 */
class EightDDTO
{
    public const STATUS_OPEN = 'Open';
    public const STATUS_IN_PROGRESS = 'In Progress';
    public const STATUS_CLOSED = 'Closed';
    public const STATUS_REJECTED = 'Rejected';

    public const PRIORITY_LOW = 'Low';
    public const PRIORITY_MEDIUM = 'Medium';
    public const PRIORITY_HIGH = 'High';
    public const PRIORITY_CRITICAL = 'Critical';

    /** @var int|null */
    private $id;

    /** @var string */
    private $eightDNumber;

    /** @var string */
    private $problemDescription;

    /** @var string|null */
    private $partNumber;

    /** @var float|null */
    private $qtyAffected;

    /** @var string|null */
    private $symptom;

    /** @var string */
    private $status;

    /** @var string */
    private $priority;

    /** @var int */
    private $createdBy;

    /** @var DateTimeImmutable */
    private $createdAt;

    /** @var DateTimeImmutable|null */
    private $updatedAt;

    /** @var DateTimeImmutable|null */
    private $closedAt;

    /** @var int|null */
    private $closedBy;

    /** @var DateTimeImmutable|null */
    private $dueDate;

    /** @var int|null */
    private $customerId;

    /** @var int|null */
    private $supplierId;

    /** @var string|null */
    private $referencePo;

    /** @var string|null */
    private $notes;

    /** @var TeamMemberDTO[] */
    private $teamMembers = [];

    /** @var ContainmentActionDTO[] */
    private $containmentActions = [];

    /** @var RootCauseDTO[] */
    private $rootCauses = [];

    /** @var CorrectiveActionDTO[] */
    private $correctiveActions = [];

    /** @var PreventionMeasureDTO[] */
    private $preventionMeasures = [];

    public function __construct(
        string $eightDNumber,
        string $problemDescription,
        int $createdBy,
        string $status = self::STATUS_OPEN,
        string $priority = self::PRIORITY_MEDIUM
    ) {
        $this->eightDNumber = $eightDNumber;
        $this->problemDescription = $problemDescription;
        $this->createdBy = $createdBy;
        $this->status = $status;
        $this->priority = $priority;
        $this->createdAt = new DateTimeImmutable();
    }

    public static function fromArray(array $data): self
    {
        $dto = new self(
            $data['eightd_number'],
            $data['problem_description'],
            (int) $data['created_by'],
            $data['status'] ?? self::STATUS_OPEN,
            $data['priority'] ?? self::PRIORITY_MEDIUM
        );

        $dto->id = isset($data['id']) ? (int) $data['id'] : null;
        $dto->partNumber = $data['part_number'] ?? null;
        $dto->qtyAffected = isset($data['qty_affected']) ? (float) $data['qty_affected'] : null;
        $dto->symptom = $data['symptom'] ?? null;
        $dto->dueDate = isset($data['due_date']) ? new DateTimeImmutable($data['due_date']) : null;
        $dto->customerId = isset($data['customer_id']) ? (int) $data['customer_id'] : null;
        $dto->supplierId = isset($data['supplier_id']) ? (int) $data['supplier_id'] : null;
        $dto->referencePo = $data['reference_po'] ?? null;
        $dto->notes = $data['notes'] ?? null;
        $dto->createdAt = isset($data['created_at']) ? new DateTimeImmutable($data['created_at']) : new DateTimeImmutable();
        $dto->updatedAt = isset($data['updated_at']) ? new DateTimeImmutable($data['updated_at']) : null;
        $dto->closedAt = isset($data['closed_at']) ? new DateTimeImmutable($data['closed_at']) : null;
        $dto->closedBy = isset($data['closed_by']) ? (int) $data['closed_by'] : null;

        return $dto;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEightDNumber(): string
    {
        return $this->eightDNumber;
    }

    public function getProblemDescription(): string
    {
        return $this->problemDescription;
    }

    public function getPartNumber(): ?string
    {
        return $this->partNumber;
    }

    public function getQtyAffected(): ?float
    {
        return $this->qtyAffected;
    }

    public function getSymptom(): ?string
    {
        return $this->symptom;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getClosedAt(): ?DateTimeImmutable
    {
        return $this->closedAt;
    }

    public function getClosedBy(): ?int
    {
        return $this->closedBy;
    }

    public function getDueDate(): ?DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function getSupplierId(): ?int
    {
        return $this->supplierId;
    }

    public function getReferencePo(): ?string
    {
        return $this->referencePo;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @return TeamMemberDTO[]
     */
    public function getTeamMembers(): array
    {
        return $this->teamMembers;
    }

    /**
     * @return ContainmentActionDTO[]
     */
    public function getContainmentActions(): array
    {
        return $this->containmentActions;
    }

    /**
     * @return RootCauseDTO[]
     */
    public function getRootCauses(): array
    {
        return $this->rootCauses;
    }

    /**
     * @return CorrectiveActionDTO[]
     */
    public function getCorrectiveActions(): array
    {
        return $this->correctiveActions;
    }

    /**
     * @return PreventionMeasureDTO[]
     */
    public function getPreventionMeasures(): array
    {
        return $this->preventionMeasures;
    }

    public function withStatus(string $status): self
    {
        $clone = clone $this;
        $clone->status = $status;
        return $clone;
    }

    public function withClosed(int $userId): self
    {
        $clone = clone $this;
        $clone->status = self::STATUS_CLOSED;
        $clone->closedAt = new DateTimeImmutable();
        $clone->closedBy = $userId;
        return $clone;
    }

    public function isComplete(): bool
    {
        return count($this->teamMembers) > 0
            && count($this->containmentActions) > 0
            && count($this->rootCauses) > 0
            && count($this->correctiveActions) > 0
            && count($this->preventionMeasures) > 0;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'eightd_number' => $this->eightDNumber,
            'problem_description' => $this->problemDescription,
            'part_number' => $this->partNumber,
            'qty_affected' => $this->qtyAffected,
            'symptom' => $this->symptom,
            'status' => $this->status,
            'priority' => $this->priority,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt?->format('Y-m-d H:i:s'),
            'closed_at' => $this->closedAt?->format('Y-m-d H:i:s'),
            'closed_by' => $this->closedBy,
            'due_date' => $this->dueDate?->format('Y-m-d'),
            'customer_id' => $this->customerId,
            'supplier_id' => $this->supplierId,
            'reference_po' => $this->referencePo,
            'notes' => $this->notes,
        ];
    }
}