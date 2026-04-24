<?php

declare(strict_types=1);

namespace Rucaro\Infrastructure\Journal;

use DateTimeImmutable;
use DateTimeZone;
use PDO;
use Rucaro\Application\Journal\JournalSearchCriteria;
use Rucaro\Application\Journal\JournalSearchResult;
use Rucaro\Domain\Exception\EntityNotFoundException;
use Rucaro\Domain\Journal\Journal;
use Rucaro\Domain\Journal\JournalLine;
use Rucaro\Domain\Journal\JournalRepositoryInterface;
use Rucaro\Infrastructure\Ulid\UlidGenerator;

/**
 * Persists {@see Journal} aggregates into `journal_entries` and
 * `journal_entry_lines`.
 *
 * Writes go through a single transaction so a partial insert never leaves the
 * DB in an unbalanced state.
 */
final class PdoJournalRepository implements JournalRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly UlidGenerator $ulids,
    ) {
    }

    public function save(Journal $journal): void
    {
        $needsTransaction = !$this->pdo->inTransaction();
        if ($needsTransaction) {
            $this->pdo->beginTransaction();
        }
        try {
            if ($this->exists($journal->id)) {
                $this->updateHeader($journal);
                $this->deleteLines($journal->id);
            } else {
                $this->insertHeader($journal);
            }
            $this->insertLines($journal);
            if ($needsTransaction) {
                $this->pdo->commit();
            }
        } catch (\Throwable $e) {
            if ($needsTransaction && $this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function delete(string $id, DateTimeImmutable $at, string $deletedBy): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE journal_entries
                SET deleted_at = :at, updated_at = :at
              WHERE id = :id AND deleted_at IS NULL
              LIMIT 1',
        );
        $stmt->execute([
            ':id' => UlidGenerator::decode($id),
            ':at' => self::fmtTs($at),
        ]);
        if ($stmt->rowCount() === 0) {
            throw new EntityNotFoundException(sprintf('Journal %s not found or already deleted.', $id));
        }
        // $deletedBy is carried by the Journal.approvedBy column on the
        // domain aggregate; DB side tracks it via the existing approved_by
        // column once the soft-delete is paired with a `voided` status.
        unset($deletedBy);
    }

    public function findByCriteria(JournalSearchCriteria $criteria): JournalSearchResult
    {
        [$where, $params, $joinLines] = $this->buildCriteriaFilter($criteria);
        $offset = ($criteria->page - 1) * $criteria->pageSize;

        $from = $joinLines
            ? 'journal_entries je INNER JOIN journal_entry_lines jel ON jel.entry_id = je.id'
            : 'journal_entries je';
        $select = $joinLines
            ? 'SELECT DISTINCT je.id, je.entity_id, je.fiscal_term_id, je.journal_date, je.booked_at, je.summary,
                       je.total_amount, je.currency_code, je.status, je.source, je.source_receipt_id,
                       je.created_by, je.approved_by, je.approved_at, je.created_at, je.updated_at, je.deleted_at'
            : 'SELECT je.id, je.entity_id, je.fiscal_term_id, je.journal_date, je.booked_at, je.summary,
                       je.total_amount, je.currency_code, je.status, je.source, je.source_receipt_id,
                       je.created_by, je.approved_by, je.approved_at, je.created_at, je.updated_at, je.deleted_at';

        $orderBy = $this->buildOrderBy($criteria);
        $sql = $select . ' FROM ' . $from . ' WHERE ' . $where
             . ' ORDER BY ' . $orderBy . ' LIMIT :_limit OFFSET :_offset';
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':_limit', $criteria->pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':_offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $journals = [];
        foreach ($rows as $row) {
            $journals[] = $this->hydrate($row, $this->loadLines((string) $row['id']));
        }

        $countSql = 'SELECT COUNT(DISTINCT je.id) FROM ' . $from . ' WHERE ' . $where;
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($params);
        /** @var string|false $c */
        $c = $countStmt->fetchColumn();
        $total = $c === false ? 0 : (int) $c;

        return new JournalSearchResult(
            items: $journals,
            total: $total,
            page: $criteria->page,
            pageSize: $criteria->pageSize,
        );
    }

    public function findById(string $id): ?Journal
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, entity_id, fiscal_term_id, journal_date, booked_at, summary,
                    total_amount, currency_code, status, source, source_receipt_id,
                    created_by, approved_by, approved_at, created_at, updated_at, deleted_at
             FROM journal_entries
             WHERE id = :id
             LIMIT 1',
        );
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            return null;
        }
        return $this->hydrate($row, $this->loadLines((string) $row['id']));
    }

    public function searchByEntity(
        string $entityId,
        int $page,
        int $pageSize,
        ?string $fiscalTermId = null,
        ?string $from = null,
        ?string $to = null,
        ?string $status = null,
        ?string $source = null,
        ?string $search = null,
        bool $includeTrashed = false,
    ): array {
        [$where, $params] = $this->buildSearchFilter(
            $entityId,
            $fiscalTermId,
            $from,
            $to,
            $status,
            $source,
            $search,
            $includeTrashed,
        );
        $offset = ($page - 1) * $pageSize;
        $sql = 'SELECT id, entity_id, fiscal_term_id, journal_date, booked_at, summary,
                       total_amount, currency_code, status, source, source_receipt_id,
                       created_by, approved_by, approved_at, created_at, updated_at, deleted_at
                FROM journal_entries
                WHERE ' . $where . '
                ORDER BY booked_at DESC, id DESC
                LIMIT :_limit OFFSET :_offset';
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':_limit', $pageSize, PDO::PARAM_INT);
        $stmt->bindValue(':_offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $journals = [];
        foreach ($rows as $row) {
            $journals[] = $this->hydrate($row, $this->loadLines((string) $row['id']));
        }
        return $journals;
    }

    public function countByEntity(
        string $entityId,
        ?string $fiscalTermId = null,
        ?string $from = null,
        ?string $to = null,
        ?string $status = null,
        ?string $source = null,
        ?string $search = null,
        bool $includeTrashed = false,
    ): int {
        [$where, $params] = $this->buildSearchFilter(
            $entityId,
            $fiscalTermId,
            $from,
            $to,
            $status,
            $source,
            $search,
            $includeTrashed,
        );
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM journal_entries WHERE ' . $where);
        $stmt->execute($params);
        /** @var string|false $c */
        $c = $stmt->fetchColumn();
        return $c === false ? 0 : (int) $c;
    }

    /**
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function buildSearchFilter(
        string $entityId,
        ?string $fiscalTermId,
        ?string $from,
        ?string $to,
        ?string $status,
        ?string $source,
        ?string $search,
        bool $includeTrashed,
    ): array {
        $clauses = ['entity_id = :entity'];
        $params = [':entity' => UlidGenerator::decode($entityId)];

        if (!$includeTrashed) {
            $clauses[] = 'deleted_at IS NULL';
        }
        if ($fiscalTermId !== null && $fiscalTermId !== '') {
            $clauses[] = 'fiscal_term_id = :term';
            $params[':term'] = UlidGenerator::decode($fiscalTermId);
        }
        if ($from !== null && $from !== '') {
            $clauses[] = 'journal_date >= :from';
            $params[':from'] = $from;
        }
        if ($to !== null && $to !== '') {
            $clauses[] = 'journal_date <= :to';
            $params[':to'] = $to;
        }
        if ($status !== null && $status !== '') {
            $clauses[] = 'status = :status';
            $params[':status'] = $status;
        }
        if ($source !== null && $source !== '') {
            $clauses[] = 'source = :source';
            $params[':source'] = $source;
        }
        if ($search !== null && $search !== '') {
            $clauses[] = 'summary LIKE :search';
            $params[':search'] = '%' . $search . '%';
        }
        return [implode(' AND ', $clauses), $params];
    }

    /**
     * @return array{0: string, 1: array<string, mixed>, 2: bool}
     */
    private function buildCriteriaFilter(JournalSearchCriteria $criteria): array
    {
        $clauses = ['je.entity_id = :entity'];
        $params = [':entity' => UlidGenerator::decode($criteria->entityId)];

        if (!$criteria->includeTrashed) {
            $clauses[] = 'je.deleted_at IS NULL';
        }
        if ($criteria->fiscalTermId !== null && $criteria->fiscalTermId !== '') {
            $clauses[] = 'je.fiscal_term_id = :term';
            $params[':term'] = UlidGenerator::decode($criteria->fiscalTermId);
        }
        if ($criteria->from !== null) {
            $clauses[] = 'je.journal_date >= :from';
            $params[':from'] = $criteria->from->toPrimitive();
        }
        if ($criteria->to !== null) {
            $clauses[] = 'je.journal_date <= :to';
            $params[':to'] = $criteria->to->toPrimitive();
        }
        if ($criteria->status !== null) {
            $clauses[] = 'je.status = :status';
            $params[':status'] = $criteria->status->value;
        }
        if ($criteria->source !== null && $criteria->source !== '') {
            $clauses[] = 'je.source = :source';
            $params[':source'] = $criteria->source;
        }
        if ($criteria->textQuery !== null && $criteria->textQuery !== '') {
            $clauses[] = 'je.summary LIKE :search';
            $params[':search'] = '%' . $criteria->textQuery . '%';
        }

        $joinLines = false;
        if ($criteria->accountTitleId !== null && $criteria->accountTitleId !== '') {
            $clauses[] = 'jel.account_title_id = :account_id';
            $params[':account_id'] = UlidGenerator::decode($criteria->accountTitleId);
            $joinLines = true;
        }

        return [implode(' AND ', $clauses), $params, $joinLines];
    }

    /**
     * Build a safe ORDER BY fragment from {@see JournalSearchCriteria}.
     *
     * Column and direction are re-validated against the criteria's allow-lists
     * so an attacker controlling the criteria object (which should be
     * impossible in practice — it's built server-side) still cannot smuggle
     * SQL into this interpolation.
     */
    private function buildOrderBy(JournalSearchCriteria $criteria): string
    {
        $column = in_array($criteria->sortBy, JournalSearchCriteria::SORT_BY_ALLOW_LIST, true)
            ? $criteria->sortBy
            : JournalSearchCriteria::SORT_BY_JOURNAL_DATE;
        $direction = strtolower($criteria->sortOrder) === JournalSearchCriteria::SORT_ORDER_ASC
            ? 'ASC'
            : 'DESC';
        // je.<column> — every allow-listed value maps to a real column on
        // journal_entries, so prefixing is safe and keeps the query planner
        // happy when the lines join is present.
        return sprintf('je.%s %s, je.id %s', $column, $direction, $direction);
    }

    private function exists(string $id): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM journal_entries WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => UlidGenerator::decode($id)]);
        return $stmt->fetchColumn() !== false;
    }

    private function updateHeader(Journal $journal): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE journal_entries SET
                entity_id = :entity_id,
                fiscal_term_id = :fiscal_term_id,
                journal_date = :journal_date,
                booked_at = :booked_at,
                summary = :summary,
                total_amount = :total_amount,
                currency_code = :currency_code,
                status = :status,
                source = :source,
                source_receipt_id = :source_receipt_id,
                approved_by = :approved_by,
                approved_at = :approved_at,
                updated_at = :updated_at,
                deleted_at = :deleted_at
             WHERE id = :id',
        );
        $stmt->execute([
            ':id' => UlidGenerator::decode($journal->id),
            ':entity_id' => UlidGenerator::decode($journal->entityId),
            ':fiscal_term_id' => UlidGenerator::decode($journal->fiscalTermId),
            ':journal_date' => $journal->journalDate->format('Y-m-d'),
            ':booked_at' => self::fmtTs($journal->bookedAt),
            ':summary' => $journal->summary,
            ':total_amount' => $journal->totalAmount,
            ':currency_code' => $journal->currencyCode,
            ':status' => $journal->status,
            ':source' => $journal->source,
            ':source_receipt_id' => $journal->sourceReceiptId !== null
                ? UlidGenerator::decode($journal->sourceReceiptId) : null,
            ':approved_by' => $journal->approvedBy !== null
                ? UlidGenerator::decode($journal->approvedBy) : null,
            ':approved_at' => $journal->approvedAt !== null
                ? self::fmtTs($journal->approvedAt) : null,
            ':updated_at' => self::fmtTs($journal->updatedAt),
            ':deleted_at' => $journal->deletedAt !== null
                ? self::fmtTs($journal->deletedAt) : null,
        ]);
    }

    private function deleteLines(string $entryId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM journal_entry_lines WHERE entry_id = :entry');
        $stmt->execute([':entry' => UlidGenerator::decode($entryId)]);
    }

    private function insertHeader(Journal $journal): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO journal_entries (
                id, entity_id, fiscal_term_id, journal_date, booked_at, summary,
                total_amount, currency_code, status, source, source_receipt_id,
                created_by, approved_by, approved_at, created_at, updated_at, deleted_at
             ) VALUES (
                :id, :entity_id, :fiscal_term_id, :journal_date, :booked_at, :summary,
                :total_amount, :currency_code, :status, :source, :source_receipt_id,
                :created_by, :approved_by, :approved_at, :created_at, :updated_at, :deleted_at
             )',
        );
        $stmt->execute([
            ':id' => UlidGenerator::decode($journal->id),
            ':entity_id' => UlidGenerator::decode($journal->entityId),
            ':fiscal_term_id' => UlidGenerator::decode($journal->fiscalTermId),
            ':journal_date' => $journal->journalDate->format('Y-m-d'),
            ':booked_at' => self::fmtTs($journal->bookedAt),
            ':summary' => $journal->summary,
            ':total_amount' => $journal->totalAmount,
            ':currency_code' => $journal->currencyCode,
            ':status' => $journal->status,
            ':source' => $journal->source,
            ':source_receipt_id' => $journal->sourceReceiptId !== null
                ? UlidGenerator::decode($journal->sourceReceiptId) : null,
            ':created_by' => UlidGenerator::decode($journal->createdBy),
            ':approved_by' => $journal->approvedBy !== null
                ? UlidGenerator::decode($journal->approvedBy) : null,
            ':approved_at' => $journal->approvedAt !== null
                ? self::fmtTs($journal->approvedAt) : null,
            ':created_at' => self::fmtTs($journal->createdAt),
            ':updated_at' => self::fmtTs($journal->updatedAt),
            ':deleted_at' => $journal->deletedAt !== null
                ? self::fmtTs($journal->deletedAt) : null,
        ]);
    }

    private function insertLines(Journal $journal): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO journal_entry_lines (
                id, entry_id, line_no, side, account_title_id, sub_account_title_id,
                amount, tax_rate_percent, tax_amount, is_tax_reduced, memo, booked_at,
                created_at, updated_at
             ) VALUES (
                :id, :entry_id, :line_no, :side, :account_title_id, :sub_account_title_id,
                :amount, :tax_rate_percent, :tax_amount, :is_tax_reduced, :memo, :booked_at,
                :created_at, :updated_at
             )',
        );
        foreach ($journal->lines as $line) {
            $id = $line->id ?? $this->ulids->generate();
            $stmt->execute([
                ':id' => UlidGenerator::decode($id),
                ':entry_id' => UlidGenerator::decode($journal->id),
                ':line_no' => $line->lineNo,
                ':side' => $line->side,
                ':account_title_id' => UlidGenerator::decode($line->accountTitleId),
                ':sub_account_title_id' => $line->subAccountTitleId !== null
                    ? UlidGenerator::decode($line->subAccountTitleId) : null,
                ':amount' => $line->amount,
                ':tax_rate_percent' => $line->taxRatePercent,
                ':tax_amount' => $line->taxAmount,
                ':is_tax_reduced' => $line->isTaxReduced ? 1 : 0,
                ':memo' => $line->memo,
                ':booked_at' => self::fmtTs($line->bookedAt),
                ':created_at' => self::fmtTs($journal->createdAt),
                ':updated_at' => self::fmtTs($journal->updatedAt),
            ]);
        }
    }

    /**
     * @param string $entryIdBinary Raw 16-byte entry id as stored in DB
     * @return list<JournalLine>
     */
    private function loadLines(string $entryIdBinary): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, line_no, side, account_title_id, sub_account_title_id,
                    amount, tax_rate_percent, tax_amount, is_tax_reduced, memo, booked_at
             FROM journal_entry_lines
             WHERE entry_id = :entry
             ORDER BY line_no ASC',
        );
        $stmt->execute([':entry' => $entryIdBinary]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $lines = [];
        foreach ($rows as $row) {
            $lines[] = new JournalLine(
                id: self::stringifyId($row['id'] ?? ''),
                lineNo: (int) ($row['line_no'] ?? 1),
                side: (string) ($row['side'] ?? 'debit'),
                accountTitleId: self::stringifyId($row['account_title_id'] ?? ''),
                subAccountTitleId: isset($row['sub_account_title_id']) && is_string($row['sub_account_title_id'])
                    ? self::stringifyId($row['sub_account_title_id'])
                    : null,
                amount: (string) ($row['amount'] ?? '0.0000'),
                taxRatePercent: (string) ($row['tax_rate_percent'] ?? '0.00'),
                taxAmount: (string) ($row['tax_amount'] ?? '0.0000'),
                isTaxReduced: self::toBool($row['is_tax_reduced'] ?? false),
                memo: (string) ($row['memo'] ?? ''),
                bookedAt: self::parseTimestamp($row['booked_at'] ?? null) ?? new DateTimeImmutable('@0'),
            );
        }
        return $lines;
    }

    /**
     * @param array<string, mixed> $row
     * @param list<JournalLine>    $lines
     */
    private function hydrate(array $row, array $lines): Journal
    {
        return new Journal(
            id: self::stringifyId($row['id'] ?? ''),
            entityId: self::stringifyId($row['entity_id'] ?? ''),
            fiscalTermId: self::stringifyId($row['fiscal_term_id'] ?? ''),
            journalDate: new DateTimeImmutable((string) ($row['journal_date'] ?? '1970-01-01'), new DateTimeZone('UTC')),
            bookedAt: self::parseTimestamp($row['booked_at'] ?? null) ?? new DateTimeImmutable('@0'),
            summary: (string) ($row['summary'] ?? ''),
            totalAmount: (string) ($row['total_amount'] ?? '0.0000'),
            currencyCode: (string) ($row['currency_code'] ?? 'JPY'),
            status: (string) ($row['status'] ?? 'draft'),
            source: (string) ($row['source'] ?? 'manual'),
            sourceReceiptId: isset($row['source_receipt_id']) && is_string($row['source_receipt_id'])
                ? self::stringifyId($row['source_receipt_id'])
                : null,
            createdBy: self::stringifyId($row['created_by'] ?? ''),
            approvedBy: isset($row['approved_by']) && is_string($row['approved_by'])
                ? self::stringifyId($row['approved_by'])
                : null,
            approvedAt: self::parseTimestamp($row['approved_at'] ?? null),
            createdAt: self::parseTimestamp($row['created_at'] ?? null) ?? new DateTimeImmutable('@0'),
            updatedAt: self::parseTimestamp($row['updated_at'] ?? null) ?? new DateTimeImmutable('@0'),
            deletedAt: self::parseTimestamp($row['deleted_at'] ?? null),
            lines: $lines,
        );
    }

    private static function stringifyId(mixed $raw): string
    {
        if (!is_string($raw) || $raw === '') {
            return '';
        }
        return strlen($raw) === 16 ? UlidGenerator::encode($raw) : $raw;
    }

    private static function toBool(mixed $v): bool
    {
        if (is_bool($v)) {
            return $v;
        }
        if (is_int($v)) {
            return $v !== 0;
        }
        if (is_string($v)) {
            return $v !== '' && $v !== '0';
        }
        return (bool) $v;
    }

    private static function parseTimestamp(mixed $raw): ?DateTimeImmutable
    {
        if ($raw === null || $raw === '' || !is_string($raw)) {
            return null;
        }
        try {
            return new DateTimeImmutable($raw, new DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }

    private static function fmtTs(DateTimeImmutable $t): string
    {
        return $t->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s.u');
    }
}
