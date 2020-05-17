# пингуем ansible для удаленых хостов
ping:
	ansible all -m ping -i hosts.yml

# запускаем таски для site (для большей информации используем -vv/-vvv/-vvvv)
site:
	ansible-playbook -i hosts.yml site.yml -v

# прокидываем свой публичный ключ для пользователя deploy
auth:
	ansible-playbook -i hosts.yml authorize.yml -v

# залогиниться в наш реестр нашего докера
docker-login:
	ansible-playbook -i hosts.yml docker-login.yml -v
