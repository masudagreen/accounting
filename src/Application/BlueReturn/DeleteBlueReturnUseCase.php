<?php

declare(strict_types=1);

namespace Rucaro\Application\BlueReturn;

use Rucaro\Domain\BlueReturn\BlueReturnRepositoryInterface;
use Rucaro\Domain\BlueReturn\BlueReturnStatus;
use Rucaro\Domain\Exception\InvariantViolationException;

/**
 * Soft-delete a blue return form.
 *
 * Only Draft forms may be deleted — finalized forms represent an
 * immutable tax filing and must stay auditable. Delete on a missing ID
 * is idempotent so retried requests don't flap between 200 and 404.
 */
final readonly class DeleteBlueReturnUseCase
{
    public function __construct(
        private BlueReturnRepositoryInterface $forms,
    ) {
    }

    public function execute(string $id): void
    {
        $form = $this->forms->findById($id);
        if ($form === null) {
            return;
        }
        if ($form->status !== BlueReturnStatus::Draft) {
            throw InvariantViolationException::for('blue_return.delete.not_draft', [
                'formId' => $form->id,
                'status' => $form->status->value,
            ]);
        }
        $this->forms->delete($id);
    }
}
