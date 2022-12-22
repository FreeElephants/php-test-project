PATH := $(shell pwd)/bin:$(PATH)

install:
	docker build -t free-elephants/php-test-project-dev .
	composer install

test:
	php -d xdebug.mode=coverage vendor/bin/phpunit tests/
