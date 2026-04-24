<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatement\Port\Cs;

use InvalidArgumentException;

/**
 * Three flow-category buckets of the J-GAAP indirect-method Cash Flow Statement.
 *
 * - {@see self::Operating}  : 営業活動によるキャッシュフロー
 * - {@see self::Investing}  : 投資活動によるキャッシュフロー
 * - {@see self::Financing}  : 財務活動によるキャッシュフロー
 *
 * The legacy code identified flow categories indirectly via the section tree
 * (`varsInDirect.operating` etc.); we surface them as a typed enum so the
 * mapping rows are self-describing and compile-time safe.
 */
enum CsFlowCategory: string
{
    case Operating = 'operating';
    case Investing = 'investing';
    case Financing = 'financing';

    public static function fromString(string $raw): self
    {
        $normalised = strtolower($raw);
        return match ($normalised) {
            'operating', 'op' => self::Operating,
            'investing', 'inv' => self::Investing,
            'financing', 'fin' => self::Financing,
            default => throw new InvalidArgumentException(
                'Unknown CsFlowCategory: ' . $raw,
            ),
        };
    }
}
