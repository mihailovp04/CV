# Лабораторная работа №4. Использование контейнеров как среды выполнения

## Студент

**Mihailov Piotr, группа I2302**  
**Дата выполнения: 07.03.25**

## Цель работы

Данная лабораторная работа призвана напомнить основные команды ОС Debian/Ubuntu. Также она позволит познакомиться с Docker и его основными командами.

## Задание

1. Запустить контейнер Ubuntu.
2. Установить Web-сервер Apache.
3. Вывести в браузере страницу с текстом "Hello, World!".

## Подготовка

Для выполнения данной работы необходимо иметь установленный на компьютере Docker.

У меня он уже скачан, так как в предыдущей лабораторной работе мы работали с ним.

## Выполнение

### Шаг 1: Создание репозитория

Создаю репозиторий `containers04` и клонирую его на локальный компьютер:

```sh
 git clone https://github.com/yourusername/containers04.git
 cd containers04
```

![gitclone](/images/gitclone.png)

### Шаг 2: Запуск контейнера

Открываю терминал в папке `containers04` и выполняю команду:

```sh
docker run -ti -p 8000:80 --name containers04 ubuntu bash
```

![dockerrun](/images/dockerrun.png)

Эта команда:

- `docker run` — запускает контейнер,
- `-ti` — открывает интерактивную сессию в терминале,
- `-p 8000:80` — перенаправляет порт 8000 хоста на порт 80 контейнера,
- `--name containers04` — задает имя контейнера,
- `ubuntu` — образ, который используется для контейнера,
- `bash` — команда, которая будет запущена внутри контейнера.

### Шаг 3: Установка Apache

В контейнере выполняю команды:

```sh
apt update
apt install apache2 -y
service apache2 start
```

Объяснение:

- `apt update` — обновляет список пакетов,
- `apt install apache2 -y` — устанавливает веб-сервер Apache,
- `service apache2 start` — запускает Apache.

### Шаг 4: Проверка работы Apache

Открываю браузер и перехожу по адресу:

```bash
http://localhost:8000
```

Сервер был запущен правильно и в браузере появится стандартная страница Apache.

![site](/images/siteapache.png)

### Шаг 5: Создание страницы "Hello, World!"

Выполняю следующие команды в контейнере:

```sh
ls -l /var/www/html/
echo '<h1>Hello, World!</h1>' > /var/www/html/index.html
```

![ls-l](/images/ls-l.png)

Объяснение:

- `ls -l /var/www/html/` — проверяет содержимое папки с веб-файлами,
- `echo '<h1>Hello, World!</h1>' > /var/www/html/index.html` — создаёт HTML-файл с сообщением.

После обновления страницы в браузере появилась надпись `Hello, World!`.

![hello](/images/hello.png)

### Шаг 6: Проверка конфигурации Apache

Выполняю команды:

```sh
cd /etc/apache2/sites-enabled/
cat 000-default.conf
```

![cat](/images/cat%20default.png)

Команда `cd /etc/apache2/sites-enabled/` переходит в папку с активными конфигурациями виртуальных хостов Apache.

Команда `cat 000-default.conf` выводит содержимое конфигурационного файла виртуального хоста по умолчанию.

Этот файл указывает корневую папку сайта `(DocumentRoot /var/www/html)`, пути к логам `(ErrorLog, CustomLog)` и настройки виртуального хоста, который слушает HTTP-трафик на порту 80 `(<VirtualHost *:80>)`

### Шаг 7: Завершение работы и удаление контейнера

Выхожу из контейнера выполняя следующую команду :

```sh
exit
```

Далее я просматриваю список контейнеров:

```sh
docker ps -a
```

![ps](/images/dockerps.png)

Удаляю контейнер следующей командой:

```sh
docker rm containers04
```

![rm](/images/dockerrm.png)

## Выводы

В ходе лабораторной работы была успешно развернута контейнеризированная среда с использованием Docker.

Установленный веб-сервер Apache корректно обрабатывает HTTP-запросы, а созданная HTML-страница корректно отображается в браузере.

Работа позволила закрепить навыки работы с основными командами Docker и базовыми настройками веб-сервер

## Библиография

1. Официальная документация Docker: [https://docs.docker.com](https://docs.docker.com)
2. Официальная документация Apache HTTP Server: [https://httpd.apache.org/docs/](https://httpd.apache.org/docs/)
3. Debian/Ubuntu Package Management: [https://wiki.debian.org/Apt](https://wiki.debian.org/Apt)
4. Repository by M.Croitor: [https://github.com/mcroitor/app_containerization_ru](https://github.com/mcroitor/app_containerization_ru)
