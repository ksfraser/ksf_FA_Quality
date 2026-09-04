<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Quality;

/**
 * Containment action DTO for D3.
 *
 * @since 1.0.0
 */
class ContainmentActionDTO
{
    /** @var int|null */
    private $id;

    /** @var int */
    private $eightDId;

    /** @var string */
    private $actionDescription;

    /** @var int */
    private $responsibleUserId;

    /** @var \DateTimeImmutable */
    private $targetDate;

    /** @var \DateTimeImmutable|null */
    private $completedDate;

    /** @var string|null */
    private $effectiveness;

    /** @var \DateTimeImmutable */
    private $createdAt;

    public function __construct(
        int $eightDId,
        string $actionDescription,
        int $responsibleUserId,
        \DateTimeImmutable $targetDate
    ) {
        $this->eightDId = $eightDId;
        $this->actionDescription = $actionDescription;
        $this->responsibleUserId = $responsibleUserId;
        $this->targetDate = $targetDate;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function fromArray(array $data): self
    {
        $dto = new self(
            (int) $data['eightd_id'],
            $data['action_description'],
            (int) $data['responsible_user_id'],
            new \DateTimeImmutable($data['target_date'])
        );
        $dto->id = isset($data['id']) ? (int) $data['id'] : null;
        $dto->completedDate = isset($data['completed_date']) ? new \DateTimeImmutable($data['completed_date']) : null;
        $dto->effectiveness = $data['effectiveness'] ?? null;
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

    public function getEffectiveness(): ?string
    {
        return $this->effectiveness;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isCompleted(): bool
    {
        return $this->completedDate !== null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'eightd_id' => $this->eightDId,
            'action_description' => $this->actionDescription,
            'responsible_user_id' => $this->responsibleUserId,
            'target_date' => $this->targetDate->format('Y-m-d'),
            'completed_date' => $this->completedDate?->format('Y-m-d'),
            'effectiveness' => $this->effectiveness,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}