# version-middleware
Provides versioning from URL path, was written for Zend Expressive.

## Installation
Install using composer:
```
$ composer require sheridans/version-middleware
```

## Usage
### Add to pipeline
```
use Psr7Versioning\VersionMiddleware;
...
$app->pipe(ServerMiddleware::class);
$app->pipe(VersionMiddleware::class);
$app->pipe(RouteMiddleware::class);
...
```
## Routing
Now you can add route based on path, for example:
```
$app->get('/home', 'Handler\HomePageHandler::class', 'home');
$app->get('/dev/home', 'Handler\dev\HomePageHandler::class', 'home.dev');
$app->get('/latest/home', 'Handler\latest\HomePageHandler::class', 'home.latest');
$app->get('/legacy/home', 'Handler\legacy\HomePageHandler::class', 'home.legacy');
$app->get('/v1/home', 'Handler\v1\HomePageHandler::class', 'home.v1');
$app->get('/v2/home', 'Handler\v2\HomePageHandler::class', 'home.v2');
```
Built in version routes are
* dev
* latest
* legacy
* vnnn (where nnn is a number)

To get the version number from a request:
```
use Psr7Versioning\VersionMiddleware;
...
public function handle (ServerRequest $request) : ResponseInterface
{
  // get current version (ie dev | latest)
  $version = $request->getAttribute(VersionMiddleware::class);
}
```
