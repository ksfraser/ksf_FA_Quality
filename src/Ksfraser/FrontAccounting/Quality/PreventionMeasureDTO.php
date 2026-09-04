<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Quality;

/**
 * Prevention measure DTO for D7.
 *
 * @since 1.0.0
 */
class PreventionMeasureDTO
{
    public const STATUS_PLANNED = 'Planned';
    public const STATUS_IMPLEMENTED = 'Implemented';
    public const STATUS_VERIFIED = 'Verified';

    /** @var int|null */
    private $id;

    /** @var int */
    private $eightDId;

    /** @var string */
    private $preventionDescription;

    /** @var string|null */
    private $systemOrProcessChanged;

    /** @var int */
    private $responsibleUserId;

    /** @var \DateTimeImmutable */
    private $targetDate;

    /** @var \DateTimeImmutable|null */
    private $completedDate;

    /** @var string */
    private $status;

    /** @var \DateTimeImmutable */
    private $createdAt;

    public function __construct(
        int $eightDId,
        string $preventionDescription,
        int $responsibleUserId,
        \DateTimeImmutable $targetDate,
        string $status = self::STATUS_PLANNED
    ) {
        $this->eightDId = $eightDId;
        $this->preventionDescription = $preventionDescription;
        $this->responsibleUserId = $responsibleUserId;
        $this->targetDate = $targetDate;
        $this->status = $status;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function fromArray(array $data): self
    {
        $dto = new self(
            (int) $data['eightd_id'],
            $data['prevention_description'],
            (int) $data['responsible_user_id'],
            new \DateTimeImmutable($data['target_date']),
            $data['status'] ?? self::STATUS_PLANNED
        );
        $dto->id = isset($data['id']) ? (int) $data['id'] : null;
        $dto->systemOrProcessChanged = $data['system_or_process_changed'] ?? null;
        $dto->completedDate = isset($data['completed_date']) ? new \DateTimeImmutable($data['completed_date']) : null;
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

    public function getPreventionDescription(): string
    {
        return $this->preventionDescription;
    }

    public function getSystemOrProcessChanged(): ?string
    {
        return $this->systemOrProcessChanged;
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
            'prevention_description' => $this->preventionDescription,
            'system_or_process_changed' => $this->systemOrProcessChanged,
            'responsible_user_id' => $this->responsibleUserId,
            'target_date' => $this->targetDate->format('Y-m-d'),
            'completed_date' => $this->completedDate?->format('Y-m-d'),
            'status' => $this->status,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}