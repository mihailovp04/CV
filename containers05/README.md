# Лабораторная работа №5. Запуск сайта в контейнере

## Студент

**Mihailov Piotr, группа I2302**  
**Дата выполнения: 23.03.25**

## Цель работы

Целью данной работы является подготовка образа контейнера для запуска веб-сайта на базе Apache HTTP Server, PHP (mod_php) и MariaDB

В процессе выполнения работы студент научится создавать Dockerfile, настраивать конфигурационные файлы, устанавливать WordPress и проверять его работоспособность

## Задание

* Создать Dockerfile для сборки образа контейнера, содержащего веб-сайт на базе Apache HTTP Server, PHP (mod_php) и MariaDB.

* Установить WordPress и проверить его работоспособность.

* База данных MariaDB должна храниться в монтируемом томе.

* Сервер должен быть доступен по порту 8000

## Выполнение

### Шаг 1

Создаю репозиторий `containers05` и клонирую его на локальный компьютер:

```sh
 git clone git@github.com:mihailovp04/containers05.git
```

### Шаг 2

Далее была создана папка `files` и в ней следующие папки `apache2`, `php`,`mariadb`, эти папки необходимы для хранения конфигурационных файлов

### Шаг 3

Был создан DockerFile и в нем мы прописали следующие команды, чтобы установить необходимые пакеты:

```sh
FROM debian:latest

RUN apt-get update && \
    apt-get install -y apache2 php libapache2-mod-php php-mysql mariadb-server supervisor && \
    apt-get clean
```

Далее нам необходимо построить образ, для этого выполняем следующую команду в терминале `docker build -t apache2-php-mariadb .`

![build](/images/dockerbuild.png)

После этого нам необходимо создать и запустить контейнер в фоновом режиме с командой запуска `bash`.

Для этого мы используем следующую команду в терминале `docker run -d --name apache2-php-mariadb apache2-php-mariadb bash`

![run](/images/dockerrun.png)

### Шаг 4

После этого нам необходимо скопировать из контейнера файлы конфигурации `apache2`,`php`,`mariadb` в папку `/files` на компьютере. Для этого мы выполняем следующие команды

```sh
docker cp apache2-php-mariadb:/etc/apache2/sites-available/000-default.conf files/apache2/
docker cp apache2-php-mariadb:/etc/apache2/apache2.conf files/apache2/
docker cp apache2-php-mariadb:/etc/php/8.2/apache2/php.ini files/php/
docker cp apache2-php-mariadb:/etc/mysql/mariadb.conf.d/50-server.cnf files/mariadb/
```

![cp](/images/cp.png)

После выполнения данных команд в нашей папке `/files` появились файлы конфигурации `apache2`,`php`,`mariadb`.

Далее, нам нужно остановить и удалить контейнер `apache2-php-mariadb`. Для этого используем следующие команды

```sh
docker stop apache2-php-mariadb
docker rm apache2-php-mariadb
```

![delete](/images/delete.png)

### Шаг 5

На следующем шаге нам необходимо настроить конфигурационный файл `apache2`

Первым делом, мы открываем файл `files/apache2/000-default.conf` и находим строку `#ServerName www.example.com` и заменяем её на `ServerName localhost`

Далее, мы находим строку `ServerAdmin webmaster@localhost` и заменяем почтовый адресс на свой

После строки `DocumentRoot /var/www/html` нам необходимо добавить следующую строку `DirectoryIndex index.php index.html`

И последним шагом, мы должны открывать файл `files/apache2/apache2.conf` и в самом конце добавить следующую строку `ServerName localhost`

### Шаг 6

Следующим шагом мы настраиваем конфигурационный файл php

Для этого мы открываем файл `files/php/php.ini` и находим строку `;error_log = php_errors.log`, заменяем её на `error_log = /var/log/php_errors.log`

Потом необходимо настроить параметры `memory_limit`, `upload_max_filesize`, `post_max_size` и `max_execution_time` следующим образом:

```sh
memory_limit = 128M
upload_max_filesize = 128M
post_max_size = 128M
max_execution_time = 120
```

### Шаг 7

И наконец-то настраиваем конфигурационный файл `mariadb`

Для этого открываем файл `files/mariadb/50-server.cnf`, находим строку `#log_error = /var/log/mysql/error.log` и раскоментируем её.

### Шаг 8

На следующем этапе нам необходимо создать в папке `files` новую папку под названием `supervisor`, а в новой папке файл с названием `supervisord.conf`.

В этом файле нам необходимо вставить следующее содержимое:

```sh
[supervisord]
nodaemon=true
logfile=/dev/null
user=root

# apache2
[program:apache2]
command=/usr/sbin/apache2ctl -D FOREGROUND
autostart=true
autorestart=true
startretries=3
stderr_logfile=/proc/self/fd/2
user=root

# mariadb
[program:mariadb]
command=/usr/sbin/mariadbd --user=mysql
autostart=true
autorestart=true
startretries=3
stderr_logfile=/proc/self/fd/2
user=mysql
```

### Шаг 9

После этого нам нужно открыть Dockerfile и добавить в него следующие строки:

* Строка, для монтирования томов
* Строка, для установки пакета `supervisor`
* Строка, для копирования и распаковки сайта WordPress, а также добавить копирование конфигурационных файлов `apache2`, `php`, `mariadb`, а также скрипта запуска
* Строка, для работы `mariadb`, которая устанавливает права и создается папка `/var/run/mysqld`
* Строка, для открытия порта 80
* Строка, для запуска `supervisord`

Получаем Dockerfile со следующим содержимым:

```sh
# create from debian image
FROM debian:latest

# mount volume for mysql data
VOLUME /var/lib/mysql

# mount volume for logs
VOLUME /var/log

# install apache2, php, mod_php for apache2, php-mysql and mariadb
RUN apt-get update && \
    apt-get install -y apache2 php libapache2-mod-php php-mysql mariadb-server supervisor && \
    apt-get clean

# add wordpress files to /var/www/html
ADD https://wordpress.org/latest.tar.gz /var/www/html/
RUN tar -xzf /var/www/html/latest.tar.gz -C /var/www/html/ --strip-components=1 && rm /var/www/html/latest.tar.gz

# copy the configuration file for apache2 from files/ directory
COPY files/apache2/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY files/apache2/apache2.conf /etc/apache2/apache2.conf

# copy the configuration file for php from files/ directory
COPY files/php/php.ini /etc/php/8.2/apache2/php.ini

# copy the configuration file for mysql from files/ directory
COPY files/mariadb/50-server.cnf /etc/mysql/mariadb.conf.d/50-server.cnf

# copy the supervisor configuration file
COPY files/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# copy the configuration file for wordpress from files/ directory
COPY files/wp-config.php /var/www/html/wp-config.php


# create mysql socket directory
RUN mkdir /var/run/mysqld && chown mysql:mysql /var/run/mysqld

# expose port 80
EXPOSE 80

# start supervisor
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
```

В результате получаем доработанный DockerFile, который соответствует всем условиями задания

Собираем образ контейнера с именем `apache2-php-mariadb` и запускаем контейнер из этого образа. Выполняем следующие команды:

Образ:

![build](/images/b2.png)

Запуск:
![run](/images/r2.png)

После этого проверяю файлы `WordPress` в контейнере:
![1](/images/1.png)

Видим, что там файлы, которые принадлежат `WordPress`

Далее, необходимо проверить изменения в конфигурации `Apache2`
![2](/images/2.png)

Как мы видим, все наши изменения были успешно добавлены.

### Шаг 10

После этого нам необходимо создать базу данных `wordpress` и пользователя `wordpress` с паролем `wordpress` в контейнере `apache2-php-mariadb`

Для этого выполняем следующие команды построчно

```sh
docker exec -it apache2-php-mariadb mysql -uroot -p
CREATE DATABASE wordpress;
CREATE USER 'wordpress'@'localhost' IDENTIFIED BY 'wordpress';
GRANT ALL PRIVILEGES ON wordpress.* TO 'wordpress'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

![bd](/images/bd.png)

### Шаг 11

После выполнения всех манипуляций, открываем в нашем браузере сайт `WordPress` по адресу `http://localhost/.`. Там нам необходимо указать следующие параметры:

* имя базы данных: wordpress;
* имя пользователя: wordpress;
* пароль: wordpress;
* адрес сервера базы данных: localhost;
* префикс таблиц: wp_

![e](/images/reg.png)

### Шаг 12

Далее, нам необходимо скопировать содержимое файла конфигурации в файл `files/wp-config.php`

![php](/images/php.png)

### Шаг 13

Нам необходимо добавить в Dockerfile следующие строки

```sh
# copy the configuration file for wordpress from files/ directory
COPY files/wp-config.php /var/www/html/wordpress/wp-config.php
```

### Шаг 14

Заканчиваем установку `WordPress`, заполняя свои данные и завершаем полностью установку.

![reg2](/images/reg2.png)

После этого авторизируемся и проверяем работоспособность сайта и наслаждаемся

![sitek](/images/sitek.png)

## Ответы на вопросы

Какие файлы конфигурации были изменены?

* `000-default.conf`, `apache2.conf`, `php.ini`, `50-server.cnf`, `supervisord.conf`, `wp-config.php`

За что отвечает инструкция DirectoryIndex в файле конфигурации apache2?

* Определяет файл, который сервер загружает первым при открытии директории.

Зачем нужен файл wp-config.php?

* Он содержит настройки базы данных и другие параметры конфигурации WordPress.

За что отвечает параметр post_max_size в файле конфигурации php?

* Определяет максимальный размер данных, передаваемых методом POST.

Какие недостатки есть в созданном образе контейнера?

* Нет механизма автоматической настройки базы данных.

* Хранение конфигурационных файлов внутри образа делает его менее гибким.

* Отсутствует защита MariaDB (нет пароля root).

## Выводы

В ходе выполнения лабораторной работы был создан и запущен контейнер с WordPress на базе Apache, PHP и MariaDB. Изучены методы конфигурации веб-сервера и базы данных в контейнеризированной среде.

## Библиография

1. Repository by M.Croitor: [https://github.com/mcroitor/app_containerization_ru](https://github.com/mcroitor/app_containerization_ru)
2. Docker Documentation.[https://docs.docker.com/get-started/overview/](https://docs.docker.com/get-started/overview/)
3. MariaDB Documentation.[https://mariadb.com/kb/en/mariadb-documentation/](https://mariadb.com/kb/en/mariadb-documentation/)
4. WordPress Documentation.[https://wordpress.org/support/article/how-to-install-wordpress/](https://wordpress.org/support/article/how-to-install-wordpress/)
