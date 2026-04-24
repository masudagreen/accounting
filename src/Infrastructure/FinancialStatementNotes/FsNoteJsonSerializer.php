<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\FinancialStatementNotes;

use Rucaro\Domain\FinancialStatementNotes\FinancialStatementNote;
use Rucaro\Domain\FinancialStatementNotes\FsNoteTemplate;

/**
 * Serializes notes and templates to the standard API envelope.
 */
final class FsNoteJsonSerializer
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(FinancialStatementNote $note): array
    {
        return [
            'id'             => $note->id,
            'entityId'       => $note->entityId,
            'fiscalTermId'   => $note->fiscalTermId,
            'templateCode'   => $note->templateCode,
            'category'       => $note->category->value,
            'categoryLabel'  => $note->category->jaLabel(),
            'label'          => $note->label,
            'body'           => $note->body,
            'sortOrder'      => $note->sortOrder,
            'isActive'       => $note->isActive,
            'createdAt'      => $note->createdAt->format(DATE_ATOM),
            'updatedAt'      => $note->updatedAt->format(DATE_ATOM),
        ];
    }

    /**
     * @param list<FinancialStatementNote> $notes
     * @return list<array<string, mixed>>
     */
    public static function toArrayList(array $notes): array
    {
        return array_values(array_map([self::class, 'toArray'], $notes));
    }

    /**
     * @return array<string, mixed>
     */
    public static function templateToArray(FsNoteTemplate $tpl): array
    {
        return [
            'id'            => $tpl->id,
            'code'          => $tpl->code,
            'category'      => $tpl->category->value,
            'categoryLabel' => $tpl->category->jaLabel(),
            'label'         => $tpl->label,
            'defaultBody'   => $tpl->defaultBody,
            'sortOrder'     => $tpl->sortOrder,
        ];
    }

    /**
     * @param list<FsNoteTemplate> $templates
     * @return list<array<string, mixed>>
     */
    public static function templateList(array $templates): array
    {
        return array_values(array_map([self::class, 'templateToArray'], $templates));
    }
}
