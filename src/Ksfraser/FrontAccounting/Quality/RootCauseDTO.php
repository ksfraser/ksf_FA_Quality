<?php
declare(strict_types=1);

namespace Ksfraser\FrontAccounting\Quality;

/**
 * Root cause DTO for D4.
 *
 * @since 1.0.0
 */
class RootCauseDTO
{
    public const CATEGORY_MAN = 'Man';
    public const CATEGORY_MACHINE = 'Machine';
    public const CATEGORY_METHOD = 'Method';
    public const CATEGORY_MATERIAL = 'Material';
    public const CATEGORY_MEASUREMENT = 'Measurement';
    public const CATEGORY_ENVIRONMENT = 'Environment';
    public const CATEGORY_OTHER = 'Other';

    /** @var int|null */
    private $id;

    /** @var int */
    private $eightDId;

    /** @var string */
    private $causeCategory;

    /** @var string */
    private $causeDescription;

    /** @var string|null */
    private $evidence;

    /** @var bool */
    private $confirmed;

    /** @var \DateTimeImmutable */
    private $createdAt;

    public function __construct(
        int $eightDId,
        string $causeCategory,
        string $causeDescription,
        bool $confirmed = false
    ) {
        $this->eightDId = $eightDId;
        $this->causeCategory = $causeCategory;
        $this->causeDescription = $causeDescription;
        $this->confirmed = $confirmed;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function fromArray(array $data): self
    {
        $dto = new self(
            (int) $data['eightd_id'],
            $data['cause_category'],
            $data['cause_description'],
            (bool) ($data['confirmed'] ?? false)
        );
        $dto->id = isset($data['id']) ? (int) $data['id'] : null;
        $dto->evidence = $data['evidence'] ?? null;
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

    public function getCauseCategory(): string
    {
        return $this->causeCategory;
    }

    public function getCauseDescription(): string
    {
        return $this->causeDescription;
    }

    public function getEvidence(): ?string
    {
        return $this->evidence;
    }

    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public static function getValidCategories(): array
    {
        return [
            self::CATEGORY_MAN,
            self::CATEGORY_MACHINE,
            self::CATEGORY_METHOD,
            self::CATEGORY_MATERIAL,
            self::CATEGORY_MEASUREMENT,
            self::CATEGORY_ENVIRONMENT,
            self::CATEGORY_OTHER,
        ];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'eightd_id' => $this->eightDId,
            'cause_category' => $this->causeCategory,
            'cause_description' => $this->causeDescription,
            'evidence' => $this->evidence,
            'confirmed' => $this->confirmed ? 1 : 0,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}