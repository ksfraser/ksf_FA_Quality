<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Quality;

/**
 * Corrective action DTO for D5.
 *
 * @since 1.0.0
 */
class CorrectiveActionDTO
{
    public const STATUS_PLANNED = 'Planned';
    public const STATUS_IN_PROGRESS = 'In Progress';
    public const STATUS_COMPLETED = 'Completed';
    public const STATUS_VERIFIED = 'Verified';
    public const STATUS_CANCELLED = 'Cancelled';

    /** @var int|null */
    private $id;

    /** @var int */
    private $eightDId;

    /** @var int|null */
    private $rootCauseId;

    /** @var string */
    private $actionDescription;

    /** @var int */
    private $responsibleUserId;

    /** @var \DateTimeImmutable */
    private $targetDate;

    /** @var \DateTimeImmutable|null */
    private $completedDate;

    /** @var string|null */
    private $validationMethod;

    /** @var \DateTimeImmutable|null */
    private $effectivenessReviewDate;

    /** @var string */
    private $status;

    /** @var \DateTimeImmutable */
    private $createdAt;

    public function __construct(
        int $eightDId,
        string $actionDescription,
        int $responsibleUserId,
        \DateTimeImmutable $targetDate,
        ?int $rootCauseId = null,
        string $status = self::STATUS_PLANNED
    ) {
        $this->eightDId = $eightDId;
        $this->actionDescription = $actionDescription;
        $this->responsibleUserId = $responsibleUserId;
        $this->targetDate = $targetDate;
        $this->rootCauseId = $rootCauseId;
        $this->status = $status;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function fromArray(array $data): self
    {
        $dto = new self(
            (int) $data['eightd_id'],
            $data['action_description'],
            (int) $data['responsible_user_id'],
            new \DateTimeImmutable($data['target_date']),
            isset($data['root_cause_id']) ? (int) $data['root_cause_id'] : null,
            $data['status'] ?? self::STATUS_PLANNED
        );
        $dto->id = isset($data['id']) ? (int) $data['id'] : null;
        $dto->completedDate = isset($data['completed_date']) ? new \DateTimeImmutable($data['completed_date']) : null;
        $dto->validationMethod = $data['validation_method'] ?? null;
        $dto->effectivenessReviewDate = isset($data['effectiveness_review_date'])
            ? new \DateTimeImmutable($data['effectiveness_review_date']) : null;
        $dto->createdAt = isset($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : new \DateTimeImmutable();
        return $dto;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEightDId(): int
    {
        return $this->eightDId;
    }

    public function getRootCauseId(): ?int
    {
        return $this->rootCauseId;
    }

    public function getActionDescription(): string
    {
        return $this->actionDescription;
    }

    public function getResponsibleUserId(): int
    {
        return $this->responsibleUserId;
    }

    public function getTargetDate(): \DateTimeImmutable
    {
        return $this->targetDate;
    }

    public function getCompletedDate(): ?\DateTimeImmutable
    {
        return $this->completedDate;
    }

    public function getValidationMethod(): ?string
    {
        return $this->validationMethod;
    }

    public function getEffectivenessReviewDate(): ?\DateTimeImmutable
    {
        return $this->effectivenessReviewDate;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'eightd_id' => $this->eightDId,
            'root_cause_id' => $this->rootCauseId,
            'action_description' => $this->actionDescription,
            'responsible_user_id' => $this->responsibleUserId,
            'target_date' => $this->targetDate->format('Y-m-d'),
            'completed_date' => $this->completedDate?->format('Y-m-d'),
            'validation_method' => $this->validationMethod,
            'effectiveness_review_date' => $this->effectivenessReviewDate?->format('Y-m-d'),
            'status' => $this->status,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}