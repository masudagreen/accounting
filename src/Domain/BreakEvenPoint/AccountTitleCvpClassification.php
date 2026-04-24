<?php

declare(strict_types=1);

namespace Rucaro\Domain\BreakEvenPoint;

use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Support\Decimal\Decimal;

/**
 * Classification of one account title (entity-scoped) into fixed /
 * variable / semi-variable cost.
 *
 * For semi-variable lines, `variableRatio` is the fraction of the balance
 * that counts as variable cost (0..1). Fixed / variable lines force the
 * ratio to 0 or 1 respectively at construction so callers do not have to
 * branch on cost-type in the aggregation step.
 */
final readonly class AccountTitleCvpClassification
{
    public function __construct(
        public string $entityId,
        public string $accountTitleId,
        public CvpCostType $costType,
        public string $variableRatio,
        public ?string $notes = null,
    ) {
        $normalized = Decimal::normalize($variableRatio);
        if (Decimal::compare($normalized, '0.0000') < 0 || Decimal::compare($normalized, '1.0000') > 0) {
            throw ValidationException::withErrors([
                'variableRatio' => ['variableRatio must be between 0 and 1 inclusive.'],
            ]);
        }
    }

    /**
     * Convenience constructor that canonicalises variableRatio from
     * cost_type:
     *   - Variable  -> 1.0000
     *   - Fixed     -> 0.0000
     *   - SemiVariable uses the caller-supplied ratio.
     */
    public static function canonicalise(
        string $entityId,
        string $accountTitleId,
        CvpCostType $type,
        string $variableRatio = '1.0000',
        ?string $notes = null,
    ): self {
        $ratio = match ($type) {
            CvpCostType::Variable => '1.0000',
            CvpCostType::Fixed => '0.0000',
            CvpCostType::SemiVariable => $variableRatio,
        };
        return new self(
            entityId: $entityId,
            accountTitleId: $accountTitleId,
            costType: $type,
            variableRatio: $ratio,
            notes: $notes,
        );
    }
}
