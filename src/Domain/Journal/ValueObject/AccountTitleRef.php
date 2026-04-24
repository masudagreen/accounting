<?php

declare(strict_types=1);

namespace Rucaro\Domain\Journal\ValueObject;

use Rucaro\Support\Validation\AbstractValueObject;
use Rucaro\Support\Validation\Assert;

/**
 * Lightweight reference to an `account_titles` row used by journal lines.
 *
 * Holds both the ULID (stable primary key) and the short code (human-
 * readable shorthand such as "1000"). Journals reference accounts by ID,
 * but reports and UIs present the code, so carrying both in one VO keeps
 * domain code from having to join the full `AccountTitle` aggregate on
 * every line.
 */
final readonly class AccountTitleRef extends AbstractValueObject
{
    public function __construct(
        public string $id,
        public string $code,
    ) {
        Assert::notEmpty($id, 'accountTitleId');
        Assert::notEmpty($code, 'accountTitleCode');
    }

    /**
     * @return array{id: string, code: string}
     */
    public function toPrimitive(): array
    {
        return ['id' => $this->id, 'code' => $this->code];
    }
}
