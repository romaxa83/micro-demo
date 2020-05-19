.SILENT:

#=============VARIABLES================
app_name = micro
#======================================

#=====MAIN_COMMAND=====================

init: down pull build up api-init info
up: up_docker info

up_docker:
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

#/////////////////////////////////////////////////////////
#========INFORMATION=====================================

info:
	echo "API - http://localhost:8080";
	echo "FRONT - http://localhost:8081";

#/////////////////////////////////////////////////////////
#========FOR_PRODUCT======================================
#========BUILD_IMAGES_FOR_DOCKER==========================
# команды для сборки образов, для отправки в реестр (подобнее в info.txt)

#билдим образы для всех сервисов
build: build-gateway build-frontend build-api

build-gateway:
	docker --log-level=debug build --pull --file=gateway/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/$(app_name)-gateway:${IMAGE_TAG} gateway/docker

build-frontend:
	docker --log-level=debug build --pull --file=frontend/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/$(app_name)-frontend:${IMAGE_TAG} frontend

build-api:
	docker --log-level=debug build --pull --file=api/docker/prod/nginx/Dockerfile --tag=${REGISTRY}/$(app_name)-api:${IMAGE_TAG} api
	docker --log-level=debug build --pull --file=api/docker/prod/php-fpm/Dockerfile --tag=${REGISTRY}/$(app_name)-api-php-fpm:${IMAGE_TAG} api
	#docker --log-level=debug build --pull --file=api/docker/prod/php-cli/Dockerfile --tag=${REGISTRY}/$(app_name)-api-php-cli:${IMAGE_TAG} api

# команда для дебага, чтоб посмотреть как будут собираться образы
try-build:
	REGISTRY=localhost IMAGE_TAG=0 make build
#========PUSH_IMAGE_FOR_REGISTRY============================
# пушим образы в реестр

push: push-gateway push-frontend push-api

push-gateway:
	docker push ${REGISTRY}/$(app_name)-gateway:${IMAGE_TAG}

push-frontend:
	docker push ${REGISTRY}/$(app_name)-frontend:${IMAGE_TAG}

push-api:
	docker push ${REGISTRY}/$(app_name)-api:${IMAGE_TAG}
	docker push ${REGISTRY}/$(app_name)-api-php-fpm:${IMAGE_TAG}
	#docker push ${REGISTRY}/$(app_name)-api-php-cli:${IMAGE_TAG}

#========DEPLOY=============================================
#деплоем наш сайт на продакте
# HOST=deploy@33.444.33.33 PORT=22 REGISTRY=registry.cubic-dev.tech IMAGE_TAG=develop-1 BUILD_NUMBER=1 make deploy

deploy:
    # удаляем папку,если она есть
	ssh ${HOST} -p ${PORT} 'rm -rf site_${BUILD_NUMBER}'
	# создаем папку site_ с номер нашего билда (которую передаем в консоли)
	ssh ${HOST} -p ${PORT} 'mkdir site_${BUILD_NUMBER}'
	# копируем docker-compose-production
	scp -P ${PORT} docker-compose-production.yml ${HOST}:site_${BUILD_NUMBER}/docker-compose.yml
	# пишем в .env переменые
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "COMPOSE_PROJECT_NAME=micro" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "REGISTRY=${REGISTRY}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose -f docker-compose.yml pull'
	ssh ${HOST} -p ${PORT} 'cd site_${BUILD_NUMBER} && docker-compose -f docker-compose.yml up --build --remove-orphans -d'
	# удаляем (если устарелы) и создаем новую символическую ссылку site -> site_BUILD_NUMBER
	ssh ${HOST} -p ${PORT} 'rm -f site'
	ssh ${HOST} -p ${PORT} 'ln -sr site_${BUILD_NUMBER} site'