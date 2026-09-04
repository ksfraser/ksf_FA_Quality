<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Quality;

/**
 * Team member DTO for D1.
 *
 * @since 1.0.0
 */
class TeamMemberDTO
{
    /** @var int|null */
    private $id;

    /** @var int */
    private $eightDId;

    /** @var int */
    private $userId;

    /** @var string */
    private $role;

    /** @var \DateTimeImmutable */
    private $addedAt;

    public function __construct(
        int $eightDId,
        int $userId,
        string $role = 'Team Member',
        ?\DateTimeImmutable $addedAt = null
    ) {
        $this->eightDId = $eightDId;
        $this->userId = $userId;
        $this->role = $role;
        $this->addedAt = $addedAt ?? new \DateTimeImmutable();
    }

    public static function fromArray(array $data): self
    {
        $dto = new self(
            (int) $data['eightd_id'],
            (int) $data['user_id'],
            $data['role'] ?? 'Team Member',
            isset($data['added_at']) ? new \DateTimeImmutable($data['added_at']) : null
        );
        $dto->id = isset($data['id']) ? (int) $data['id'] : null;
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

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getAddedAt(): \DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'eightd_id' => $this->eightDId,
            'user_id' => $this->userId,
            'role' => $this->role,
            'added_at' => $this->addedAt->format('Y-m-d H:i:s'),
        ];
    }
}