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
			bin/console d:d:c --if-not-exists 		    ;\
			bin/console d:d:c --env=test --if-not-exists;\
			bin/console d:m:m -n 		   					;\
			bin/console d:m:m -n --env=test 				;"

test/migrate: up
	docker compose run --rm skeleton-php-symfony-fpm sh -c "\
			bin/console d:d:c --env=test --if-not-exists;\
			bin/console d:m:m -n --env=test 				;"


migrate/force: up
	docker compose run --rm skeleton-php-symfony-fpm sh -c "\
			bin/console d:d:d -f  		    ;\
			bin/console d:d:d -f --env=test   ;\
			bin/console d:d:c  		    ;\
            bin/console d:d:c --env=test   ;\
			bin/console d:m:m -n		   					;\
			bin/console d:m:m -n --env=test 				;"


test: deps test/migrate test/sunit test/sapplication

test/sunit:
	docker compose run -e APP_ENV=test skeleton-php-symfony-fpm bin/phpunit --order-by=random --testdox --testsuite Unit

test/sapplication:
	docker compose run -e APP_ENV=test skeleton-php-symfony-fpm bin/phpunit --order-by=random --testdox --testsuite Application

test/coverage: deps
	docker compose run -e APP_ENV=test skeleton-php-symfony-fpm bin/phpunit --coverage-text --coverage-clover=coverage.xml --order-by=random

test/unit:
	docker compose run -e APP_ENV=test skeleton-php-symfony-fpm bin/phpunit --coverage-text --order-by=random --testsuite Unit

test/application:
	docker compose run -e APP_ENV=test skeleton-php-symfony-fpm bin/phpunit --coverage-text --order-by=random --testsuite Application

bash:
	docker compose run --rm skeleton-php-symfony-fpm sh

down:
	docker compose down
