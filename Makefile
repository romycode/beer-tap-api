.SILENT:

build:
	docker compose build

deps: build
	docker compose run --rm skeleton-php-symfony-fpm sh -c "\
			composer install --prefer-dist --no-progress --no-scripts --no-interaction --optimize-autoloader 	;\
			composer dump-autoload --classmap-authoritative 													;"

up: deps
	docker compose up --remove-orphans --wait -d

migrate: up
	docker compose run --rm skeleton-php-symfony-fpm sh -c "\
			bin/console d:d:c -q --if-not-exists 		    ;\
			bin/console d:d:c -q --env=test --if-not-exists;\
			bin/console d:m:m -q		   					;\
			bin/console d:m:m -q --env=test 				;"

migrate/force: up
	docker compose run --rm skeleton-php-symfony-fpm sh -c "\
			bin/console d:d:c -q -f 		    ;\
			bin/console d:d:c -q --env=test -f  ;\
			bin/console d:m:m -q		   					;\
			bin/console d:m:m -q --env=test 				;"


test: test/sunit test/sapplication

test/sunit:
	docker compose run skeleton-php-symfony-fpm bin/phpunit --order-by=random --testdox --testsuite Unit
test/sapplication:
	docker compose run skeleton-php-symfony-fpm bin/phpunit --order-by=random --testdox --testsuite Application

test/coverage: deps
	docker compose run skeleton-php-symfony-fpm bin/phpunit --coverage-text --coverage-clover=coverage.xml --order-by=random

test/unit:
	docker compose run skeleton-php-symfony-fpm bin/phpunit --coverage-text --order-by=random --testsuite Unit

test/application:
	docker compose run skeleton-php-symfony-fpm bin/phpunit --coverage-text --order-by=random --testsuite Application

bash:
	docker compose run --rm skeleton-php-symfony-fpm sh

down:
	docker compose down
