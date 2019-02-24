DOCKER_COMPOSE 	?= docker-compose
EXEC_PHP      	= $(DOCKER_COMPOSE) run --rm -T php$(PHP_VERSION)
PHP_VERSION     ?= 7.3
DEPS     		?= "LOCKED"
COMPOSER      	= $(EXEC_PHP) composer
WITH_COVERAGE   ?= "FALSE"

build:
	@$(DOCKER_COMPOSE) pull --parallel --ignore-pull-failures 2> /dev/null
	$(DOCKER_COMPOSE) build php$(PHP_VERSION)

kill:
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

setup: ## Setup spellcheckers dependencies
setup: build
	$(DOCKER_COMPOSE) up -d --remove-orphans --no-recreate languagetools

.PHONY: build kill setup

tests: ## Run all tests
tests:
	if [ $(WITH_COVERAGE) = true ]; then $(EXEC_PHP) vendor/bin/phpunit --coverage-clover clover.xml; else $(EXEC_PHP) vendor/bin/phpunit; fi

tests-dox: ## Run all tests in dox format
tests-dox:
	if [ $(WITH_COVERAGE) = true ]; then $(EXEC_PHP) vendor/bin/phpunit --coverage-clover clover.xml --testdox; else $(EXEC_PHP) vendor/bin/phpunit --testdox; fi

tu: ## Run unit tests
tu: vendor
	$(EXEC_PHP) vendor/bin/phpunit --exclude-group integration

ti: ## Run functional tests
ti: vendor
	$(EXEC_PHP) vendor/bin/phpunit --group integration

scrutinizer:
	$(EXEC_PHP) curl -L https://scrutinizer-ci.com/ocular.phar -o ocular.phar -s
	$(EXEC_PHP) php ocular.phar code-coverage:upload --format=php-clover clover.xml

.PHONY: tests tests-dox tu ti

vendor:
	if [ $(DEPS) = "LOWEST" ]; then $(COMPOSER) update --prefer-lowest; fi
	if [ $(DEPS) = "LOCKED" ]; then $(COMPOSER) install; fi
	if [ $(DEPS) = "HIGHEST" ]; then $(COMPOSER) update; else $(COMPOSER) install; fi

rector:
	docker run -v $(pwd):/project rector/rector:latest bin/rector process /project/src/ --config vendor/thecodingmachine/safe/rector-migrate.yml --autoload-file /project/vendor/autoload.php

phpcs: vendor
	$(EXEC_PHP) vendor/bin/phpcs

phpcbf: vendor
	$(EXEC_PHP) vendor/bin/phpcbf

phpstan: vendor
	$(EXEC_PHP) vendor/bin/phpstan analyse src -c phpstan.neon -a vendor/autoload.php

infection: vendor
	$(EXEC_PHP) vendor/bin/phpunit --coverage-xml=build/coverage/coverage-xml --log-junit=build/coverage/phpunit.junit.xml
	$(EXEC_PHP) php infection.phar --threads=4 --coverage=build/coverage --min-covered-msi=74

.PHONY: vendor php-cs php-cbf php-stan

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
