<?php

declare(strict_types=1);

namespace Rucaro\Application\BlueReturn;

use InvalidArgumentException;
use Rucaro\Domain\BlueReturn\BlueReturnRepositoryInterface;
use Rucaro\Domain\BlueReturn\BlueReturnSnapshot;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Update the snapshot / form type of an editable (Draft) form.
 *
 * Finalized forms raise {@see \Rucaro\Domain\Exception\InvariantViolationException}
 * via the aggregate's own guard so the domain stays in charge of the
 * state machine.
 */
final readonly class UpdateBlueReturnUseCase
{
    public function __construct(
        private BlueReturnRepositoryInterface $forms,
        private ClockInterface $clock,
    ) {
    }

    public function execute(UpdateBlueReturnInput $input): BlueReturnOutput
    {
        if (!UlidGenerator::isValid($input->id)) {
            throw new InvalidArgumentException('id must be a ULID.');
        }
        $form = $this->forms->findById($input->id);
        if ($form === null) {
            throw ValidationException::withErrors([
                'id' => [sprintf('blue return %s was not found.', $input->id)],
            ]);
        }

        $now = $this->clock->getCurrentTime();
        if ($input->formType !== null && $input->formType !== $form->formType) {
            $form = $form->withFormType($input->formType, $now);
        }
        if ($input->snapshot !== null) {
            $snapshot = BlueReturnSnapshot::fromArray($input->snapshot);
            $form = $form->withSnapshot($snapshot, $now);
        }
        $this->forms->save($form);
        return new BlueReturnOutput($form);
    }
}
