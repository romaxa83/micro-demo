.SILENT:

#=============VARIABLES================
#======================================

#=====MAIN_COMMAND=====================

init: down pull build up api-init

up:
	docker-compose up -d

down:
	docker-compose down --remove-orphans

# флаг -v удаляет все volume (очищает все данные)
down-clear:
	docker-compose down -v --remove-orphans

# скачиает обновление для контейнеров
pull:
	docker-compose pull

build:
	docker-compose build

#=====COMMAND_FOR_API================================

api-init: api-composer-install

api-composer-install:
	docker-compose run --rm api-php-cli composer install