QA_DOCKER_IMAGE=jakzal/phpqa:1.42-php7.4-alpine
QA_DOCKER_COMMAND=docker run --init -t --rm --user "$(shell id -u):$(shell id -g)" --volume /tmp/tmp-phpqa-$(shell id -u):/tmp --volume "$(shell pwd):/project" --workdir /project ${QA_DOCKER_IMAGE}

dist: install cs-full phpstan test
ci: cs-full-check phpstan test
lint: cs-full-check phpstan

install:
	docker-compose run --rm php make in-docker-install

install-dev:
	docker-compose run --rm php make in-docker-install-dev

install-lowest:
	docker-compose run --rm php make in-docker-install-lowest

test:
	sh -c "${QA_DOCKER_COMMAND} vendor/bin/phpunit --verbose"

psalm: ensure
	sh -c "${QA_DOCKER_COMMAND} vendor/bin/psalm --show-info=false"

psalmi: ensure
	sh -c "${QA_DOCKER_COMMAND} vendor/bin/psalm --show-info=true"

test-coverage: ensure docker-up
	mkdir -p build/logs build/cov
	docker-compose run --rm php make in-docker-test-coverage
	sh -c "${QA_DOCKER_COMMAND} phpdbg -qrr /usr/local/bin/phpcov merge --clover build/logs/clover.xml build/cov"
	@$(MAKE) docker-down

phpstan: ensure
	sh -c "${QA_DOCKER_COMMAND} phpstan analyse"

cc: ensure
	sh -c "${QA_DOCKER_COMMAND} vendor/bin/codecept run unit"

cs: ensure
	sh -c "${QA_DOCKER_COMMAND} php-cs-fixer fix -vvv --diff"

cs-full: ensure
	sh -c "${QA_DOCKER_COMMAND} php-cs-fixer fix -vvv --using-cache=false --diff"

cs-full-check: ensure
	sh -c "${QA_DOCKER_COMMAND} php-cs-fixer fix -vvv --using-cache=false --diff --dry-run"

##
# Special operations
##

##
# Private targets
##
in-docker-install:
	rm -f composer.lock
	composer install --no-progress --no-interaction --no-suggest --optimize-autoloader --ansi

in-docker-install-dev:
	rm -f composer.lock
	cp composer.json _composer.json
	composer config minimum-stability dev
	composer update --no-progress --no-interaction --no-suggest --optimize-autoloader --ansi
	mv _composer.json composer.json

in-docker-install-lowest:
	rm -f composer.lock
	composer update --no-progress --no-suggest --prefer-stable --prefer-lowest --optimize-autoloader --ansi

in-docker-test:
	SYMFONY_DEPRECATIONS_HELPER=weak vendor/bin/phpunit --verbose

in-docker-test-coverage:
	SYMFONY_DEPRECATIONS_HELPER=weak phpdbg -qrr vendor/bin/phpunit --verbose --coverage-php build/cov/coverage-phpunit.cov

ensure:
	mkdir -p ${HOME}/.composer /tmp/tmp-phpqa-$(shell id -u)

.PHONY: install install-dev install-lowest phpstan cs cc cs-full cs-full-checks docker-up down-down
.PHONY: in-docker-install in-docker-install-dev in-docker-install-lowest in-docker-test in-docker-test-coverage docs