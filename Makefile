.PHONY: tests/unit tests/application build

up: deps
	docker compose up --wait -d --remove-orphans

test: .test/up test/unit test/application
	docker compose -f docker-compose.test.yml down

test/coverage: .test/build deps
	docker compose -f docker-compose.test.yml run skeleton-php-symfony-fpm-test bin/phpunit --coverage-text --coverage-clover=coverage.xml --order-by=random

test/unit: .test/build
	docker compose -f docker-compose.test.yml run skeleton-php-symfony-fpm-test bin/phpunit --coverage-text --order-by=random --testsuite Unit

test/application: .test/build
	docker compose -f docker-compose.test.yml run skeleton-php-symfony-fpm-test bin/phpunit --coverage-text --order-by=random --testsuite Application

deps: build
	docker compose run --rm skeleton-php-symfony-fpm sh -c "\
			composer install --prefer-dist --no-progress --no-scripts --no-interaction --optimize-autoloader 	&& \
			composer dump-autoload --classmap-authoritative 													;"
bash:
	docker compose run --rm skeleton-php-symfony-fpm sh

build:
	docker compose build

down:
	docker compose -f docker-compose.yml -f docker-compose.test.yml down

.test/build:
	docker compose -f docker-compose.test.yml build

.test/up:
	docker compose -f docker-compose.test.yml up --wait --remove-orphans
