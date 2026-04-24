<?php

declare(strict_types=1);

namespace Rucaro\Domain\BlueReturn;

/**
 * Variant of the 青色申告決算書 the individual entrepreneur must file.
 *
 * Mirrors the three tax-authority-issued forms:
 *   - general: 一般用 — most common (service, retail, contractor, ...)
 *   - agricultural: 農業所得用
 *   - real_estate: 不動産所得用
 *
 * Legacy code muxed on a string constant; we promote it to an enum so
 * the state machine is exhaustive.
 */
enum BlueReturnFormType: string
{
    case General = 'general';
    case Agricultural = 'agricultural';
    case RealEstate = 'real_estate';
}
