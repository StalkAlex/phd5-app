include ../Makefile.base

PHP_SERVICE     ?= php
TESTER_SERVICE  ?= tester
BROWSER_SERVICE ?= chrome
COMPOSE_FILE_QA ?= ../docker-compose.yml:./docker-compose.test.yml:./docker-compose.qa.yml

all: ##@test complete reinitialization of test-stack
	$(MAKE) init-tests version build
	$(MAKE) clean up setup

init-tests: ##@development initialize test-environment
	cp -n .env-dist .env &2>/dev/null
	mkdir -p _log/codeception && chmod 777 _log/codeception
	mkdir -p _log/lint && chmod 777 _log/lint
	$(DOCKER_COMPOSE) run -w /app --rm $(TESTER_SERVICE) composer install

version: ##@development write current version string from git
	$(shell echo $(shell git describe --long --tags --dirty --always) > ../src/version)
	@echo $(shell cat ../src/version)

bash: ##@development open application development bash
	$(DOCKER_COMPOSE) run --rm -e YII_ENV=test $(TESTER_SERVICE) bash

bash-xdebug: ##@development open application development bash with xdebug enabled
	$(DOCKER_COMPOSE) run --rm -e YII_ENV=test -e PHP_ENABLE_XDEBUG=1 $(TESTER_SERVICE) bash

up-xdebug: ##@development open application development bash with xdebug enabled
	PHP_ENABLE_XDEBUG=1 $(DOCKER_COMPOSE) up -d

run-tests: ##@test run tests
	$(DOCKER_COMPOSE) run --rm -e YII_ENV=test $(TESTER_SERVICE) codecept clean
	$(DOCKER_COMPOSE) run --rm -e YII_ENV=test $(TESTER_SERVICE) codecept run --env $(BROWSER_SERVICE) -x optional --steps --html --xml= --tap --json
	$(DOCKER_COMPOSE) logs $(PHP_SERVICE) > _log/docker.log

run-coverage: ##@test run tests with code coverage
	$(DOCKER_COMPOSE) run --rm -e YII_ENV=test $(TESTER_SERVICE) codecept clean
	$(DOCKER_COMPOSE) run --rm -e YII_ENV=test -e PHP_ENABLE_XDEBUG=1 $(TESTER_SERVICE) codecept run --env $(BROWSER_SERVICE) -x optional --coverage-html --coverage-xml --html --xml


open-vnc: ##@test open application database service in browser
	$(OPEN_CMD) vnc://$(DOCKER_HOST_IP):$(shell $(DOCKER_COMPOSE) port $(BROWSER_SERVICE) 5900 | sed 's/[0-9.]*://') &>/dev/null

open-report: ##@test open HTML reports
	$(OPEN_CMD) _log/codeception/report.html &>/dev/null

open-coverage: ##@test open HTML reports
	$(OPEN_CMD) _log/coverage/index.html &>/dev/null

open-c3:
	$(OPEN_CMD) http://$(DOCKER_HOST_IP):$(shell $(DOCKER_COMPOSE) port web 80 | sed 's/[0-9.]*://')/c3/report/clear &>/dev/null


lint-source:	 ##@development run source-code linting
	# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	#
	# Liniting source-code with cs-fixer, phpmetrics & phpmd
	#
	$(DOCKER_COMPOSE) run --rm $(TESTER_SERVICE) php-cs-fixer fix --format=txt -v --dry-run ../src
	docker run --rm -v "${PWD}/..:/app" --workdir=/app herloct/phpmetrics --report-html=tests/_log/lint/metrics --excluded-dirs=migrations src/
	docker run --rm -v "${PWD}/..:/project" jolicode/phaudit phpmd src html tests/phpmd/rulesets.xml --exclude src/migrations > _log/lint/mess.html
	exit ${ERROR}

lint-composer: ##@development run composer linting
	# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	#
	# Liniting composer configuration
	#
	cd ..; \
	$(DOCKER_COMPOSE) run --rm $(PHP_SERVICE) composer --no-ansi validate || ERROR=1; \
	$(DOCKER_COMPOSE) run --rm $(PHP_SERVICE) composer --no-ansi show | tee tests/_log/composer-packages-$(shell cat ../src/version).txt || ERROR=1; \
	$(DOCKER_COMPOSE) run --rm $(PHP_SERVICE) composer --no-ansi show -o | tee tests/_log/composer-outdated-packages-$(shell cat ../src/version).txt || ERROR=1; \
	exit ${ERROR}

lint-html:
	COMPOSE_FILE=$(COMPOSE_FILE_QA) $(DOCKER_COMPOSE) run --rm  validator http://web

lint-links:
	COMPOSE_FILE=$(COMPOSE_FILE_QA) $(DOCKER_COMPOSE) run --rm  linkchecker linkchecker http://web -F html/utf8/./tmp/tests/_log/check.html -f /tmp/tests/linkcheckerrc -r 3 -t 5

lint: lint-source lint-composer
