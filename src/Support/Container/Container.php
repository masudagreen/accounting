<?php

declare(strict_types=1);

namespace Rucaro\Support\Container;

use Closure;
use Psr\Container\ContainerInterface;
use Rucaro\Support\Container\Exception\ContainerException;
use Rucaro\Support\Container\Exception\NotFoundException;

/**
 * Tiny self-contained PSR-11 compatible container.
 *
 * Designed to be sufficient for Phase 3 bootstrap wiring without pulling in
 * `php-di/php-di` yet. The service identifiers are plain strings (typically
 * fully-qualified class names); factories receive the container so they can
 * look up their own dependencies.
 *
 * The Psr\Container interfaces are declared inside this file when the official
 * package is not installed, which keeps the tiny footprint self-contained.
 */
final class Container implements ContainerInterface
{
    /** @var array<string, Closure(self):mixed> */
    private array $factories = [];

    /** @var array<string, mixed> */
    private array $resolved = [];

    /**
     * Register a factory. Factories are called lazily and cached (singleton
     * semantics by default).
     *
     * @param Closure(self):mixed $factory
     */
    public function set(string $id, Closure $factory): void
    {
        $this->factories[$id] = $factory;
        unset($this->resolved[$id]);
    }

    /**
     * Eagerly bind a pre-built instance.
     */
    public function setInstance(string $id, mixed $instance): void
    {
        $this->resolved[$id] = $instance;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->resolved)
            || array_key_exists($id, $this->factories);
    }

    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->resolved)) {
            return $this->resolved[$id];
        }

        if (!array_key_exists($id, $this->factories)) {
            throw new NotFoundException(sprintf("Service '%s' is not registered.", $id));
        }

        try {
            $value = ($this->factories[$id])($this);
        } catch (NotFoundException | ContainerException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ContainerException(
                sprintf("Failed to build service '%s': %s", $id, $e->getMessage()),
                0,
                $e,
            );
        }

        $this->resolved[$id] = $value;
        return $value;
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    public function getTyped(string $id): object
    {
        /** @var object $instance */
        $instance = $this->get($id);
        if (!$instance instanceof $id) {
            throw new ContainerException(sprintf(
                "Service '%s' resolved to an instance that is not of the expected type.",
                $id,
            ));
        }
        /** @var T $instance */
        return $instance;
    }
}
