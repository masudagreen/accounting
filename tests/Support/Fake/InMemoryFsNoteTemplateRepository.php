<?php

declare(strict_types=1);

namespace Rucaro\Tests\Support\Fake;

use Rucaro\Domain\FinancialStatementNotes\FsNoteTemplate;
use Rucaro\Domain\FinancialStatementNotes\FsNoteTemplateRepositoryInterface;

final class InMemoryFsNoteTemplateRepository implements FsNoteTemplateRepositoryInterface
{
    /** @var array<string, FsNoteTemplate> */
    private array $byCode = [];

    public function add(FsNoteTemplate $tpl): void
    {
        $this->byCode[$tpl->code] = $tpl;
    }

    public function findAll(): array
    {
        return array_values($this->byCode);
    }

    public function findByCode(string $code): ?FsNoteTemplate
    {
        return $this->byCode[$code] ?? null;
    }

    public function findByCodes(array $codes): array
    {
        $out = [];
        foreach ($codes as $c) {
            if (!is_string($c) || $c === '') {
                continue;
            }
            if (isset($this->byCode[$c])) {
                $out[] = $this->byCode[$c];
            }
        }
        return array_values($out);
    }
}
