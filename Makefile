.DEFAULT_GOAL := help
.PHONY: help
.SILENT:

GREEN  := $(shell tput -Txterm setaf 2)
WHITE  := $(shell tput -Txterm setaf 7)
YELLOW := $(shell tput -Txterm setaf 3)
RESET  := $(shell tput -Txterm sgr0)

test: lint phpcs phpmd phpunit phpstan

install:
	composer install --prefer-source --no-interaction --no-suggest

## Run PHP unit tests
phpunit:
	@echo "${GREEN}Unit tests${RESET}"
	@php vendor/bin/phpunit

## Run PHP mess detector
phpmd:
	@echo "${GREEN}PHP Mess Detector${RESET}"
	@php vendor/bin/phpmd src/ text cleancode,codesize,naming,design,controversial,unusedcode

## Run PHP code sniffer
phpcs:
	@echo "${GREEN}PHP Code Sniffer${RESET}"
	@php vendor/bin/phpcs -p --standard=psr2 --colors src/

## Run PHPStan
phpstan:
	@echo "${GREEN}PHPStan${RESET}"
	@php vendor/bin/phpstan analyse -l 0 src/

## PHP Parallel Lint
lint:
	@echo "${GREEN}PHP Parallel Lint${RESET}"
	@php vendor/bin/parallel-lint src/ tests/

## Fix PHP syntax with code sniffer
fix:
	@php vendor/bin/php-cs-fixer fix src/ -vv --level=psr2

## Test Coverage HTML
coverage:
	@echo "${GREEN}Tests with coverage${RESET}"
	@phpdbg -qrr vendor/bin/phpunit --coverage-html build/ --coverage-clover coverage.xml

## Prints this help
help:
	@echo "\nUsage: make ${YELLOW}<target>${RESET}\n\nThe following targets are available:\n";
	@awk -v skip=1 \
		'/^##/ { sub(/^[#[:blank:]]*/, "", $$0); doc_h=$$0; doc=""; skip=0; next } \
		 skip  { next } \
		 /^#/  { doc=doc "\n" substr($$0, 2); next } \
		 /:/   { sub(/:.*/, "", $$0); printf "\033[34m%-30s\033[0m\033[1m%s\033[0m %s\n", $$0, doc_h, doc; skip=1 }' \
		$(MAKEFILE_LIST)
