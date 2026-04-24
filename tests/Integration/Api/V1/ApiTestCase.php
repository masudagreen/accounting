<?php

declare(strict_types=1);

namespace Rucaro\Tests\Integration\Api\V1;

use PDO;
use PHPUnit\Framework\TestCase;
use Rucaro\Http\ApiKernel;
use Rucaro\Http\ServerRequest;
use Rucaro\Infrastructure\Database\ConnectionFactory;
use Rucaro\Support\Container\Container;
use Rucaro\Support\Container\ContainerBootstrap;

/**
 * Shared base for HTTP-level integration tests.
 *
 * Integration tests are skipped when the `RUCARO_TEST_DB_*` env vars are not
 * supplied, so the suite stays green in local/CI environments without a
 * MariaDB instance.
 */
abstract class ApiTestCase extends TestCase
{
    protected ?PDO $pdo = null;
    protected ?Container $container = null;
    protected ?ApiKernel $kernel = null;

    protected function setUp(): void
    {
        parent::setUp();

        $host = getenv('RUCARO_TEST_DB_HOST') ?: ($_ENV['RUCARO_TEST_DB_HOST'] ?? '');
        if ($host === '') {
            $this->markTestSkipped('RUCARO_TEST_DB_HOST not set; integration tests skipped.');
        }

        $this->pdo = ConnectionFactory::createFromArray([
            'host'     => $host,
            'port'     => (int) (getenv('RUCARO_TEST_DB_PORT') ?: 3306),
            'database' => getenv('RUCARO_TEST_DB_NAME') ?: 'rucaro_test',
            'username' => getenv('RUCARO_TEST_DB_USER') ?: 'root',
            'password' => getenv('RUCARO_TEST_DB_PASSWORD') ?: '',
        ]);

        $this->container = ContainerBootstrap::build($this->pdo);
        $this->kernel = new ApiKernel($this->container);
    }

    /**
     * @param array<string, string>             $headers  Lowercased keys.
     * @param array<string, string|int|bool>    $query
     * @param array<string, mixed>|list<mixed>|null $json
     */
    protected function dispatch(
        string $method,
        string $path,
        array $headers = [],
        array $query = [],
        array|null $json = null,
    ): string {
        self::assertNotNull($this->kernel);
        /** @var array<string, string|int|bool|list<string>|null> $normalizedQuery */
        $normalizedQuery = [];
        foreach ($query as $k => $v) {
            $normalizedQuery[$k] = $v;
        }
        $request = new ServerRequest(
            method: $method,
            path: $path,
            headers: $headers,
            query: $normalizedQuery,
            json: $json,
            rawBody: $json !== null ? (string) json_encode($json) : '',
        );
        $response = $this->kernel->handle($request);
        return $response->body;
    }
}
