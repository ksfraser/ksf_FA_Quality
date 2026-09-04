<?php
declare(strict_types=1);

namespace Ksfraser\Tests\FrontAccounting\Quality;

use Ksfraser\FrontAccounting\Quality\EightDService;
use Ksfraser\FrontAccounting\Quality\EightDRepository;
use Ksfraser\FrontAccounting\Quality\EightDDTO;
use Ksfraser\FrontAccounting\Quality\TeamMemberDTO;
use Ksfraser\FrontAccounting\Quality\ContainmentActionDTO;
use Ksfraser\FrontAccounting\Quality\RootCauseDTO;
use Ksfraser\FrontAccounting\Quality\CorrectiveActionDTO;
use Ksfraser\FrontAccounting\Quality\PreventionMeasureDTO;
use Ksfraser\FrontAccounting\Common\Exceptions\QualityException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for EightDService.
 *
 * @BABOK Related: FR-QC-001-001, FR-QC-001-002
 * @since 1.0.0
 */
class EightDServiceTest extends TestCase
{
    private EightDService $service;
    private EightDRepository $mockRepository;

    protected function setUp(): void
    {
        $this->mockRepository = $this->createMock(EightDRepository::class);
        $this->service = new EightDService($this->mockRepository);
    }

    public function testCreateEightD(): void
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('generateEightDNumber')
            ->willReturn('8D-2026-0001');

        $this->mockRepository
            ->expects($this->once())
            ->method('create')
            ->willReturnCallback(function (EightDDTO $dto) {
                $dto->id = 1;
                return $dto;
            });

        $result = $this->service->createEightD('Test problem', 1, EightDDTO::PRIORITY_HIGH);

        $this->assertEquals('8D-2026-0001', $result->getEightDNumber());
        $this->assertEquals('Test problem', $result->getProblemDescription());
        $this->assertEquals(EightDDTO::PRIORITY_HIGH, $result->getPriority());
    }

    public function testCloseEightDSuccess(): void
    {
        $eightD = new EightDDTO('8D-2026-0001', 'Test', 1);
        $eightD->id = 1;
        $eightD->teamMembers = [new TeamMemberDTO(1, 1)];
        $eightD->containmentActions = [new ContainmentActionDTO(1, 'Test', 1, new \DateTimeImmutable())];
        $eightD->rootCauses = [new RootCauseDTO(1, 'Man', 'Test cause')];
        $eightD->correctiveActions = [new CorrectiveActionDTO(1, 'Test', 1, new \DateTimeImmutable())];
        $eightD->preventionMeasures = [new PreventionMeasureDTO(1, 'Test', 1, new \DateTimeImmutable())];

        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($eightD);

        $this->mockRepository
            ->expects($this->once())
            ->method('getTeamMembers')
            ->willReturn($eightD->teamMembers);

        $this->mockRepository
            ->expects($this->once())
            ->method('getContainmentActions')
            ->willReturn($eightD->containmentActions);

        $this->mockRepository
            ->expects($this->once())
            ->method('getRootCauses')
            ->willReturn($eightD->rootCauses);

        $this->mockRepository
            ->expects($this->once())
            ->method('getCorrectiveActions')
            ->willReturn($eightD->correctiveActions);

        $this->mockRepository
            ->expects($this->once())
            ->method('getPreventionMeasures')
            ->willReturn($eightD->preventionMeasures);

        $this->mockRepository
            ->expects($this->once())
            ->method('close')
            ->with(1, 1);

        $this->service->closeEightD(1, 1);
        $this->assertTrue(true);
    }

    public function testCloseEightDFailsWhenIncomplete(): void
    {
        $eightD = new EightDDTO('8D-2026-0001', 'Test', 1);
        $eightD->id = 1;
        $eightD->status = EightDDTO::STATUS_OPEN;

        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($eightD);

        $this->mockRepository
            ->method('getTeamMembers')
            ->willReturn([]);

        $this->mockRepository
            ->method('getContainmentActions')
            ->willReturn([]);

        $this->mockRepository
            ->method('getRootCauses')
            ->willReturn([]);

        $this->mockRepository
            ->method('getCorrectiveActions')
            ->willReturn([]);

        $this->mockRepository
            ->method('getPreventionMeasures')
            ->willReturn([]);

        $this->expectException(QualityException::class);
        $this->expectExceptionMessage('Cannot close 8D: Missing required content');

        $this->service->closeEightD(1, 1);
    }

    public function testCloseEightDFailsWhenAlreadyClosed(): void
    {
        $eightD = new EightDDTO('8D-2026-0001', 'Test', 1);
        $eightD->id = 1;
        $eightD->status = EightDDTO::STATUS_CLOSED;

        $this->mockRepository
            ->expects($this->once())
            ->method('findById')
            ->willReturn($eightD);

        $this->expectException(QualityException::class);
        $this->expectExceptionMessage('already closed');

        $this->service->closeEightD(1, 1);
    }

    public function testIsComplete(): void
    {
        $eightD = new EightDDTO('8D-2026-0001', 'Test', 1);
        $eightD->teamMembers = [new TeamMemberDTO(1, 1)];
        $eightD->containmentActions = [new ContainmentActionDTO(1, 'Test', 1, new \DateTimeImmutable())];
        $eightD->rootCauses = [new RootCauseDTO(1, 'Man', 'Test')];
        $eightD->correctiveActions = [new CorrectiveActionDTO(1, 'Test', 1, new \DateTimeImmutable())];
        $eightD->preventionMeasures = [new PreventionMeasureDTO(1, 'Test', 1, new \DateTimeImmutable())];

        $this->assertTrue($this->service->isComplete($eightD));
    }

    public function testIsCompleteFalse(): void
    {
        $eightD = new EightDDTO('8D-2026-0001', 'Test', 1);
        $eightD->teamMembers = [new TeamMemberDTO(1, 1)];

        $this->assertFalse($this->service->isComplete($eightD));
    }

    public function testGetCompleteness(): void
    {
        $eightD = new EightDDTO('8D-2026-0001', 'Test', 1);
        $eightD->teamMembers = [new TeamMemberDTO(1, 1)];
        $eightD->containmentActions = [];

        $this->assertEquals(20, $this->service->getCompleteness($eightD));
    }
}