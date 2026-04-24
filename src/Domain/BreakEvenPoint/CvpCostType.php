<?php

declare(strict_types=1);

namespace Rucaro\Domain\BreakEvenPoint;

use InvalidArgumentException;

/**
 * CVP cost type for one account title.
 *
 * - Variable:    fully variable (e.g. raw materials, COGS).
 * - Fixed:       fully fixed (e.g. rent, admin salaries).
 * - SemiVariable: partly variable at a configurable ratio (CVP
 *                 classification rows carry `variable_ratio` 0..1).
 *
 * Revenue (sales) accounts are not represented here — they are detected
 * from their account category (`revenue`) in the calculator.
 */
enum CvpCostType: string
{
    case Variable = 'variable';
    case Fixed = 'fixed';
    case SemiVariable = 'semi_variable';

    public static function fromString(string $v): self
    {
        return match ($v) {
            'variable' => self::Variable,
            'fixed' => self::Fixed,
            'semi_variable', 'semivariable' => self::SemiVariable,
            default => throw new InvalidArgumentException(sprintf('Unknown CvpCostType: %s', $v)),
        };
    }
}
