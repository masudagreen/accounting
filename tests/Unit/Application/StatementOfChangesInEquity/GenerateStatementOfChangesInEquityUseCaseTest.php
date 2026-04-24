<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Application\StatementOfChangesInEquity;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Application\StatementOfChangesInEquity\GenerateStatementOfChangesInEquityInput;
use Rucaro\Application\StatementOfChangesInEquity\GenerateStatementOfChangesInEquityUseCase;
use Rucaro\Domain\StatementOfChangesInEquity\Service\StatementOfChangesInEquityBuilder;
use Rucaro\Domain\StatementOfChangesInEquity\SsChangeType;
use Rucaro\Domain\StatementOfChangesInEquity\SsManualAdjustment;
use Rucaro\Domain\StatementOfChangesInEquity\SsSectionCode;
use Rucaro\Tests\Support\Fake\FrozenClock;
use Rucaro\Tests\Support\Fake\InMemorySsManualAdjustmentRepository;

#[CoversClass(GenerateStatementOfChangesInEquityUseCase::class)]
final class GenerateStatementOfChangesInEquityUseCaseTest extends TestCase
{
    public function testComposesBuilderWithPersistedAdjustments(): void
    {
        $repo = new InMemorySsManualAdjustmentRepository();
        $repo->save(new SsManualAdjustment(
            id: '01HAAAAAAAAAAAAAAAAAAAAA01',
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            sectionCode: SsSectionCode::CapitalStock,
            changeType: SsChangeType::NewIssue,
            amount: '2000000.0000',
            label: 'Rights issue',
            sortOrder: 0,
            notes: null,
        ));

        $uc = new GenerateStatementOfChangesInEquityUseCase(
            repo: $repo,
            builder: new StatementOfChangesInEquityBuilder(),
            clock: new FrozenClock(),
        );
        $input = new GenerateStatementOfChangesInEquityInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2027-03-31', new DateTimeZone('UTC')),
            currencyCode: 'JPY',
            openingBalances: [SsSectionCode::CapitalStock->value => '10000000.0000'],
            netIncome: '5000000.0000',
        );
        $ss = $uc->execute($input);
        $cap = $ss->sectionByCode(SsSectionCode::CapitalStock);
        self::assertNotNull($cap);
        self::assertSame('12000000.0000', $cap->endingBalance);
    }

    public function testRejectsInvalidEntityUlid(): void
    {
        $uc = new GenerateStatementOfChangesInEquityUseCase(
            repo: new InMemorySsManualAdjustmentRepository(),
            builder: new StatementOfChangesInEquityBuilder(),
            clock: new FrozenClock(),
        );
        $this->expectException(InvalidArgumentException::class);
        $uc->execute(new GenerateStatementOfChangesInEquityInput(
            entityId: 'not-a-ulid',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            fromDate: new DateTimeImmutable('2026-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2027-03-31', new DateTimeZone('UTC')),
        ));
    }

    public function testRejectsReversedDateRange(): void
    {
        $uc = new GenerateStatementOfChangesInEquityUseCase(
            repo: new InMemorySsManualAdjustmentRepository(),
            builder: new StatementOfChangesInEquityBuilder(),
            clock: new FrozenClock(),
        );
        $this->expectException(InvalidArgumentException::class);
        $uc->execute(new GenerateStatementOfChangesInEquityInput(
            entityId: '01HAAAAAAAAAAAAAAAAAAAAAA1',
            fiscalTermId: '01HAAAAAAAAAAAAAAAAAAAAAA2',
            fromDate: new DateTimeImmutable('2027-04-01', new DateTimeZone('UTC')),
            toDate: new DateTimeImmutable('2026-03-31', new DateTimeZone('UTC')),
        ));
    }
}
