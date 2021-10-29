ifneq (,$(wildcard ./.env))
    include .env
    export
endif

build:
	docker compose build

up:
	docker compose up -d

down:
	docker compose down --remove-orphans

login:
	docker compose exec -u $(USER_ID):$(GROUP_ID) app sh --login

install:
	docker compose exec -u $(USER_ID):$(GROUP_ID) --env XDEBUG_MODE=off --env=COMPOSER_MEMORY_LIMIT=-1 app composer install

test:
	docker compose exec -u $(USER_ID):$(GROUP_ID) --env XDEBUG_MODE=off --env=COMPOSER_MEMORY_LIMIT=-1 app composer run-script --timeout=0 test
