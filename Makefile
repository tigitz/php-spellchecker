DOCKER_COMPOSE 	?= docker compose
EXEC_PHP      	= $(DOCKER_COMPOSE) run --rm php
PHP_VERSION     ?= 8.4
DEPS_STRATEGY   ?= --prefer-stable
COMPOSER      	= $(EXEC_PHP) composer
WITH_COVERAGE   ?= "FALSE"
EXAMPLES_DIR   ?= "examples"

pull:
	@$(DOCKER_COMPOSE) pull languagetools jamspell

build:
	$(DOCKER_COMPOSE) build --no-cache php

push:
	$(DOCKER_COMPOSE) push php

kill:
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

setup: ## Setup spellcheckers dependencies
setup: build
	$(DOCKER_COMPOSE) up -d --remove-orphans --no-recreate languagetools jamspell

.PHONY: build kill setup

PHPUNIT_FLAGS = $(if $(filter 8.4,$(PHP_VERSION)),--display-deprecations,) \
                $(if $(filter true,$(WITH_COVERAGE)),--coverage-clover clover.xml,)

tests: ## Run all tests
tests:
	$(EXEC_PHP) vendor/bin/phpunit $(PHPUNIT_FLAGS)

tests-dox: ## Run all tests in dox format
tests-dox:
	$(EXEC_PHP) vendor/bin/phpunit $(PHPUNIT_FLAGS) --testdox

# @TODO not optimized, it recreates a container for each example
examples-test:
	for file in `ls examples/*.php`; \
           do \
           echo "\n**************************************************************"; \
           echo "Run example: $$file"; \
           echo "**************************************************************\n"; \
           sh -c "$(EXEC_PHP) php $$file" || exit 1 ;\
        done

tu: ## Run unit tests
tu: vendor
	$(EXEC_PHP) vendor/bin/phpunit --display-deprecations --exclude-group integration

ti: ## Run functional tests
ti: vendor
	$(EXEC_PHP) vendor/bin/phpunit --display-deprecations --group integration

.PHONY: tests tests-dox examples-test tu ti

vendor:
	$(COMPOSER) update $(DEPS_STRATEGY)

PHP_CS_FIXER = docker-compose run --rm -T php tools/php-cs-fixer/vendor/bin/php-cs-fixer fix -vv --allow-risky=yes

phpcs:
	PHP_VERSION=8.2 $(EXEC_PHP) composer -d tools/php-cs-fixer update
	PHP_VERSION=8.2 $(PHP_CS_FIXER) --dry-run

phpcbf:
	PHP_VERSION=8.2 $(EXEC_PHP) composer -d tools/php-cs-fixer update
	PHP_VERSION=8.2 $(PHP_CS_FIXER)

phpstan: vendor
	$(EXEC_PHP) vendor/bin/phpstan analyse src -c phpstan.neon -a vendor/autoload.php

phpstan-baseline: vendor
	$(EXEC_PHP) vendor/bin/phpstan analyse src -c phpstan.neon -a vendor/autoload.php --generate-baseline

infection: vendor
	$(EXEC_PHP) vendor/bin/phpunit --coverage-xml=build/coverage/coverage-xml --log-junit=build/coverage/phpunit.junit.xml
	$(EXEC_PHP) php infection.phar --threads=4 --coverage=build/coverage --min-covered-msi=74

.PHONY: vendor php-cs phpcbf phpstan

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
