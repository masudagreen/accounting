<?php

declare(strict_types=1);

namespace Rucaro\Tests\Unit\Domain\BreakEvenPoint;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Rucaro\Domain\BreakEvenPoint\AccountTitleCvpClassification;
use Rucaro\Domain\BreakEvenPoint\CvpCostType;
use Rucaro\Domain\Exception\ValidationException;

#[CoversClass(AccountTitleCvpClassification::class)]
final class AccountTitleCvpClassificationTest extends TestCase
{
    public function testCanonicaliseForcesFullyVariableRatio(): void
    {
        $c = AccountTitleCvpClassification::canonicalise(
            'e1', 'a1', CvpCostType::Variable, '0.3000',
        );
        self::assertSame('1.0000', $c->variableRatio);
    }

    public function testCanonicaliseForcesFixedRatio(): void
    {
        $c = AccountTitleCvpClassification::canonicalise(
            'e1', 'a1', CvpCostType::Fixed, '0.9000',
        );
        self::assertSame('0.0000', $c->variableRatio);
    }

    public function testCanonicaliseKeepsSemiVariableRatio(): void
    {
        $c = AccountTitleCvpClassification::canonicalise(
            'e1', 'a1', CvpCostType::SemiVariable, '0.3000',
        );
        self::assertSame('0.3000', $c->variableRatio);
    }

    public function testRejectsOutOfRangeRatio(): void
    {
        $this->expectException(ValidationException::class);
        new AccountTitleCvpClassification(
            entityId: 'e1',
            accountTitleId: 'a1',
            costType: CvpCostType::SemiVariable,
            variableRatio: '1.5000',
        );
    }

    public function testFromStringCases(): void
    {
        self::assertSame(CvpCostType::Variable, CvpCostType::fromString('variable'));
        self::assertSame(CvpCostType::Fixed, CvpCostType::fromString('fixed'));
        self::assertSame(CvpCostType::SemiVariable, CvpCostType::fromString('semi_variable'));
        self::assertSame(CvpCostType::SemiVariable, CvpCostType::fromString('semivariable'));
    }

    public function testFromStringRejectsUnknown(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        CvpCostType::fromString('mystery');
    }
}
