# Testing

Spellcheckers come in many different forms, from HTTP API to command line tools. **PHP-Spellcheck** wants to ensure real-world usage is OK, so it contains integration tests. To run these, spellcheckers need to all be available during tests execution.

The most convenient way to do it is by using Docker and avoid polluting your local machine.

## Docker

Requires `docker` and `docker-compose` to be installed (tested on Linux).

```sh
$ make build # build container images
$ make setup # start spellcheckers container
$ make tests-dox
```

You can also specify PHP version, dependency version target and if you want coverage. Coverage is only supported by PHP 7.2 for now.

```sh
$ PHP_VERSION=7.2 DEPS=LOWEST WITH_COVERAGE="true" make tests-dox
```

Run `make help` to list all available tasks.

## Locally

Todo

## Environment variables

If spellcheckers execution paths are different than their default values
(e.g., `docker exec -ti myispell` instead of `ispell`) you can override the path used in tests by redefining environment variables in the [PHPUnit config file](phpunit.xml.dist)
