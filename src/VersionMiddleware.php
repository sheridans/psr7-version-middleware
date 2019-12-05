<?php

declare(strict_types=1);

namespace Psr7Versioning;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware for managing app versions
 */
class VersionMiddleware implements MiddlewareInterface
{

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request);
    }

    /**
     * If path has version, extract and correct path
     * @param ServerRequestInterface $request
     * @return ServerRequestInterface
     */
    public function extractVersionFromPath(ServerRequestInterface $request) : ServerRequestInterface
    {
        // get URI & path
        $uri = $request->getUri();
        $path = $uri->getPath();

        // version extract pattern, ie dev|latest|legacy|v1|v2|v3
        $pattern = '/^\/(legacy|latest|dev|v[\d]+?)\//';
        if (preg_match($pattern, $path, $matches)) {
            // API version extracted from URI
            $apiVersion = $matches[1];
            // Remainder of URI path
            $newPath = substr($path, strlen($matches[0]) - 1);

            // return request with the remainder of the URI path, add API version as request attribute
            return $request
                ->withUri($uri->withPath($newPath))
                ->withAttribute(self::class, $apiVersion);
        }

        // not versioned
        return $request;
    }
}
