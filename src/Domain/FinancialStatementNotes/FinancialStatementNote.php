<?php

declare(strict_types=1);

namespace Rucaro\Domain\FinancialStatementNotes;

use DateTimeImmutable;
use Rucaro\Domain\Exception\ValidationException;

/**
 * One note entry on a 注記表 attached to a specific (entity, fiscal term).
 *
 * Invariants:
 *   - `label` non-empty, <= 128 chars (enforced here so repeated
 *     serializations never see an invalid entity);
 *   - `body` non-empty (a blank note is a mistake rather than a feature —
 *     prefer `isActive = false` to suppress a row in the PDF);
 *   - `templateCode`, when set, is <= 32 chars and references
 *     {@see FsNoteTemplate::$code}.
 */
final readonly class FinancialStatementNote
{
    public function __construct(
        public string $id,
        public string $entityId,
        public string $fiscalTermId,
        public ?string $templateCode,
        public FsNoteCategory $category,
        public string $label,
        public string $body,
        public int $sortOrder,
        public bool $isActive,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {
        if ($label === '' || mb_strlen($label) > 128) {
            throw ValidationException::withErrors([
                'label' => ['label must be 1..128 characters.'],
            ]);
        }
        if ($body === '') {
            throw ValidationException::withErrors([
                'body' => ['body must not be empty.'],
            ]);
        }
        if ($templateCode !== null && ($templateCode === '' || strlen($templateCode) > 32)) {
            throw ValidationException::withErrors([
                'templateCode' => ['templateCode must be 1..32 characters when provided.'],
            ]);
        }
        if ($sortOrder < 0) {
            throw ValidationException::withErrors([
                'sortOrder' => ['sortOrder must be non-negative.'],
            ]);
        }
    }

    /**
     * Non-mutating clone with the label/body/category replaced. `templateCode`
     * is retained because a user edit does not break the original linkage.
     */
    public function withContent(
        FsNoteCategory $category,
        string $label,
        string $body,
        DateTimeImmutable $now,
    ): self {
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            templateCode: $this->templateCode,
            category: $category,
            label: $label,
            body: $body,
            sortOrder: $this->sortOrder,
            isActive: $this->isActive,
            createdAt: $this->createdAt,
            updatedAt: $now,
        );
    }

    public function withSortOrder(int $sortOrder, DateTimeImmutable $now): self
    {
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            templateCode: $this->templateCode,
            category: $this->category,
            label: $this->label,
            body: $this->body,
            sortOrder: $sortOrder,
            isActive: $this->isActive,
            createdAt: $this->createdAt,
            updatedAt: $now,
        );
    }

    public function withActive(bool $isActive, DateTimeImmutable $now): self
    {
        return new self(
            id: $this->id,
            entityId: $this->entityId,
            fiscalTermId: $this->fiscalTermId,
            templateCode: $this->templateCode,
            category: $this->category,
            label: $this->label,
            body: $this->body,
            sortOrder: $this->sortOrder,
            isActive: $isActive,
            createdAt: $this->createdAt,
            updatedAt: $now,
        );
    }
}
