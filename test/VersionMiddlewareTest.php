<?php

namespace Psr7Versioning;

use PHPUnit\Framework\TestCase;
use Zend\Diactoros\ServerRequest;

/**
 * Class VersionMiddlewareTest
 *
 */
class VersionMiddlewareTest extends TestCase
{
    /**
     * Return middleware class
     *
     * @return VersionMiddleware
     */
    protected function getMiddleware() : VersionMiddleware
    {
        return new VersionMiddleware();
    }

    /**
     * Data provider version tests
     *
     * @dataProvider pathVersionProvider
     * @param        $path
     * @param        $expected
     */
    public function testGetVersionFromPath($path, $expected) : void
    {
        $this->assertEquals($expected, $this->getVersionFromPath($path));
    }

    /**
     * Data provider for path tests
     *
     * @dataProvider pathUriProvider
     * @param $uri
     * @param $expected
     */
    public function testGetPathFromUri($uri, $expected) : void
    {
        $this->assertEquals($expected, $this->getPathFromUri($uri));
    }

    /**
     * Extract version from path
     *
     * @param  string $path
     * @return string|null
     */
    public function getVersionFromPath(string $path) : ?string
    {
        $request = new ServerRequest([], [], $path);
        $middleware = $this->getMiddleware();
        $result = $middleware->extractVersionFromPath($request);

        return $result->getAttribute(VersionMiddleware::class);
    }

    /**
     * Extract path component from uri
     *
     * @param string $uri
     * @return string|null
     */
    public function getPathFromUri(string $uri) : ?string
    {
        $request = new ServerRequest([], [], $uri);
        $middleware = $this->getMiddleware();

        $result = $middleware->extractVersionFromPath($request);

        return $result->getUri()->getPath();
    }

    /**
     * @return array
     */
    public function pathVersionProvider() : array
    {
        return [
            ['/dev/api', 'dev'],
            ['/latest/api/test', 'latest'],
            ['/legacy/api/call', 'legacy'],
            ['/v1/v2/v3', 'v1'],
            ['/v2/legacy/dev', 'v2'],
            ['/v3/v3/call', 'v3'],
            ['//v3', null],
            ['v1/v2/v3', null],
        ];
    }

    /**
     * @return array
     */
    public function pathUriProvider() : array
    {
        return [
            ['/dev/api', '/api'],
            ['/latest/api/test', '/api/test'],
            ['/legacy/api/call', '/api/call'],
            ['/v1/v2/v3', '/v2/v3'],
            ['/v2/legacy/dev/', '/legacy/dev/'],
            ['/v3/v3/call', '/v3/call'],
            ['v1/v2/v3', 'v1/v2/v3'],
        ];
    }
}
