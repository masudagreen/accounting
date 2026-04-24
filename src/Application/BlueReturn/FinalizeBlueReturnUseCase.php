<?php

declare(strict_types=1);

namespace Rucaro\Application\BlueReturn;

use InvalidArgumentException;
use Rucaro\Domain\BlueReturn\BlueReturnRepositoryInterface;
use Rucaro\Domain\Exception\ValidationException;
use Rucaro\Infrastructure\Ulid\UlidGenerator;
use Rucaro\Support\Clock\ClockInterface;

/**
 * Promote a Draft form to Finalized.
 *
 * The transition is terminal: once finalized the form is frozen.
 * Repeated finalize calls surface as {@see \Rucaro\Domain\Exception\InvariantViolationException}.
 */
final readonly class FinalizeBlueReturnUseCase
{
    public function __construct(
        private BlueReturnRepositoryInterface $forms,
        private ClockInterface $clock,
    ) {
    }

    public function execute(string $id): BlueReturnOutput
    {
        if (!UlidGenerator::isValid($id)) {
            throw new InvalidArgumentException('id must be a ULID.');
        }
        $form = $this->forms->findById($id);
        if ($form === null) {
            throw ValidationException::withErrors([
                'id' => [sprintf('blue return %s was not found.', $id)],
            ]);
        }
        $finalized = $form->finalize($this->clock->getCurrentTime());
        $this->forms->save($finalized);
        return new BlueReturnOutput($finalized);
    }
}
