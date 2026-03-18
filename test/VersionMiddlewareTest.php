<?php

declare(strict_types=1);

namespace Psr7VersioningTest;

use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr7Versioning\VersionMiddleware;

final class VersionMiddlewareTest extends TestCase
{
    private VersionMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new VersionMiddleware();
    }

    #[DataProvider('pathVersionProvider')]
    public function testGetVersionFromPath(string $path, ?string $expected): void
    {
        $request = new ServerRequest([], [], $path);
        $result  = $this->middleware->extractVersionFromPath($request);

        $this->assertSame($expected, $result->getAttribute(VersionMiddleware::class));
    }

    #[DataProvider('pathUriProvider')]
    public function testGetPathFromUri(string $uri, string $expected): void
    {
        $request = new ServerRequest([], [], $uri);
        $result  = $this->middleware->extractVersionFromPath($request);

        $this->assertSame($expected, $result->getUri()->getPath());
    }

    /** @return array<string, array{string, ?string}> */
    public static function pathVersionProvider(): array
    {
        return [
            'dev version'          => ['/dev/api', 'dev'],
            'latest version'       => ['/latest/api/test', 'latest'],
            'legacy version'       => ['/legacy/api/call', 'legacy'],
            'v1 numeric'           => ['/v1/v2/v3', 'v1'],
            'v2 numeric'           => ['/v2/legacy/dev', 'v2'],
            'v3 numeric'           => ['/v3/v3/call', 'v3'],
            'empty segment'        => ['//v3', null],
            'no leading slash'     => ['v1/v2/v3', null],
        ];
    }

    /** @return array<string, array{string, string}> */
    public static function pathUriProvider(): array
    {
        return [
            'dev path'             => ['/dev/api', '/api'],
            'latest path'          => ['/latest/api/test', '/api/test'],
            'legacy path'          => ['/legacy/api/call', '/api/call'],
            'v1 strips first'      => ['/v1/v2/v3', '/v2/v3'],
            'v2 preserves rest'    => ['/v2/legacy/dev/', '/legacy/dev/'],
            'v3 nested'            => ['/v3/v3/call', '/v3/call'],
            'no leading slash'     => ['v1/v2/v3', 'v1/v2/v3'],
        ];
    }
}
