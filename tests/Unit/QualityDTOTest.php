<?php
declare(strict_types=1);

namespace Ksfraser\Tests\FrontAccounting\Quality;

use Ksfraser\FrontAccounting\Quality\EightDDTO;
use Ksfraser\FrontAccounting\Quality\TeamMemberDTO;
use Ksfraser\FrontAccounting\Quality\ContainmentActionDTO;
use Ksfraser\FrontAccounting\Quality\RootCauseDTO;
use Ksfraser\FrontAccounting\Quality\CorrectiveActionDTO;
use Ksfraser\FrontAccounting\Quality\PreventionMeasureDTO;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Quality DTOs.
 *
 * @BABOK Related: FR-QC-001-001, FR-QC-001-002
 * @since 1.0.0
 */
class QualityDTOTest extends TestCase
{
    public function testEightDDTOConstruction(): void
    {
        $dto = new EightDDTO('8D-2026-0001', 'Test problem description', 1);

        $this->assertEquals('8D-2026-0001', $dto->getEightDNumber());
        $this->assertEquals('Test problem description', $dto->getProblemDescription());
        $this->assertEquals(1, $dto->getCreatedBy());
        $this->assertEquals(EightDDTO::STATUS_OPEN, $dto->getStatus());
        $this->assertEquals(EightDDTO::PRIORITY_MEDIUM, $dto->getPriority());
        $this->assertInstanceOf(\DateTimeImmutable::class, $dto->getCreatedAt());
    }

    public function testEightDDTOFromArray(): void
    {
        $data = [
            'id' => 5,
            'eightd_number' => '8D-2026-0001',
            'problem_description' => 'Test problem',
            'part_number' => 'PART-001',
            'qty_affected' => 10.5,
            'symptom' => 'Customer reported defect',
            'status' => 'In Progress',
            'priority' => 'High',
            'created_by' => 1,
            'created_at' => '2026-01-15 10:00:00',
            'due_date' => '2026-02-15',
            'customer_id' => 3,
        ];

        $dto = EightDDTO::fromArray($data);

        $this->assertEquals(5, $dto->getId());
        $this->assertEquals('8D-2026-0001', $dto->getEightDNumber());
        $this->assertEquals('PART-001', $dto->getPartNumber());
        $this->assertEquals(10.5, $dto->getQtyAffected());
        $this->assertEquals('High', $dto->getPriority());
        $this->assertEquals('In Progress', $dto->getStatus());
    }

    public function testEightDDTOWithClosed(): void
    {
        $dto = new EightDDTO('8D-2026-0001', 'Test', 1);
        $closedDto = $dto->withClosed(5);

        $this->assertEquals(EightDDTO::STATUS_OPEN, $dto->getStatus());
        $this->assertEquals(EightDDTO::STATUS_CLOSED, $closedDto->getStatus());
        $this->assertEquals(5, $closedDto->getClosedBy());
        $this->assertInstanceOf(\DateTimeImmutable::class, $closedDto->getClosedAt());
    }

    public function testEightDDTOIsCompleteFalseWhenEmpty(): void
    {
        $dto = new EightDDTO('8D-2026-0001', 'Test', 1);
        $this->assertFalse($dto->isComplete());
    }

    public function testEightDDTOIsCompleteTrueWhenPopulated(): void
    {
        $dto = new EightDDTO('8D-2026-0001', 'Test', 1);
        $dto->teamMembers = [new TeamMemberDTO(1, 1)];
        $dto->containmentActions = [new ContainmentActionDTO(1, 'Test', 1, new \DateTimeImmutable())];
        $dto->rootCauses = [new RootCauseDTO(1, RootCauseDTO::CATEGORY_MAN, 'Test cause')];
        $dto->correctiveActions = [new CorrectiveActionDTO(1, 'Test', 1, new \DateTimeImmutable())];
        $dto->preventionMeasures = [new PreventionMeasureDTO(1, 'Test', 1, new \DateTimeImmutable())];

        $this->assertTrue($dto->isComplete());
    }

    public function testTeamMemberDTO(): void
    {
        $dto = new TeamMemberDTO(1, 5, 'Quality Engineer');

        $this->assertEquals(1, $dto->getEightDId());
        $this->assertEquals(5, $dto->getUserId());
        $this->assertEquals('Quality Engineer', $dto->getRole());
    }

    public function testContainmentActionDTO(): void
    {
        $targetDate = new \DateTimeImmutable('2026-02-01');
        $dto = new ContainmentActionDTO(1, 'Isolate affected batch', 5, $targetDate);

        $this->assertEquals(1, $dto->getEightDId());
        $this->assertEquals('Isolate affected batch', $dto->getActionDescription());
        $this->assertEquals(5, $dto->getResponsibleUserId());
        $this->assertFalse($dto->isCompleted());
    }

    public function testRootCauseDTOCategories(): void
    {
        $categories = RootCauseDTO::getValidCategories();

        $this->assertContains('Man', $categories);
        $this->assertContains('Machine', $categories);
        $this->assertContains('Method', $categories);
        $this->assertContains('Material', $categories);
        $this->assertContains('Measurement', $categories);
        $this->assertContains('Environment', $categories);
        $this->assertContains('Other', $categories);
        $this->assertCount(7, $categories);
    }

    public function testCorrectiveActionDTO(): void
    {
        $dto = new CorrectiveActionDTO(1, 'Implement SPC', 5, new \DateTimeImmutable('2026-03-01'));

        $this->assertEquals(1, $dto->getEightDId());
        $this->assertEquals('Implement SPC', $dto->getActionDescription());
        $this->assertEquals(CorrectiveActionDTO::STATUS_PLANNED, $dto->getStatus());
    }

    public function testPreventionMeasureDTO(): void
    {
        $dto = new PreventionMeasureDTO(1, 'Update work instruction', 5, new \DateTimeImmutable('2026-03-15'));

        $this->assertEquals(1, $dto->getEightDId());
        $this->assertEquals('Update work instruction', $dto->getPreventionDescription());
        $this->assertEquals(PreventionMeasureDTO::STATUS_PLANNED, $dto->getStatus());
    }
}