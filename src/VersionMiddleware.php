<?php

declare(strict_types=1);

namespace Psr7Versioning;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class VersionMiddleware implements MiddlewareInterface
{
    private const string VERSION_PATTERN = '/^\/(legacy|latest|dev|v[\d]+?)\//';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($this->extractVersionFromPath($request));
    }

    public function extractVersionFromPath(ServerRequestInterface $request): ServerRequestInterface
    {
        $uri  = $request->getUri();
        $path = $uri->getPath();

        if (preg_match(self::VERSION_PATTERN, $path, $matches)) {
            $apiVersion = $matches[1];
            $newPath    = substr($path, strlen($matches[0]) - 1);

            return $request
                ->withUri($uri->withPath($newPath))
                ->withAttribute(self::class, $apiVersion);
        }

        return $request;
    }
}
