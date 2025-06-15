# Лабораторная работа №9: Оптимизация образов контейнеров

## Выполнил

* Mihailov Piotr I2302
* Дата выполнения: 16.04.25

## Цель работы

Целью работы является знакомство с методами оптимизации Docker-образов для уменьшения их размера и повышения эффективности.

## Задание

Сравнить различные методы оптимизации Docker-образов:

1. Удаление неиспользуемых зависимостей и временных файлов.
2. Уменьшение количества слоев.
3. Использование минимального базового образа.
4. Перепаковка образа.
5. Комбинированное использование всех методов.

## Подготовка

Для выполнения работы необходимо:

1. Установить Docker на компьютер.
2. Создать репозиторий `containers09` и склонировать его.
3. В папке `containers09` создать папку `site` и поместить в нее файлы сайта (html, css, js).

## Выполнение работы

### 1. Исходный образ (Dockerfile.raw)

Создаем Dockerfile.raw для базового образа на основе `ubuntu:latest`.

```dockerfile
# create from ubuntu image
FROM ubuntu:latest

# update system
RUN apt-get update && apt-get upgrade -y

# install nginx
RUN apt-get install -y nginx

# copy site
COPY site /var/www/html

# expose port 80
EXPOSE 80

# run nginx
CMD ["nginx", "-g", "daemon off;"]
```

Собираем образ:

```bash
docker image build -t mynginx:raw -f Dockerfile.raw .
```

![raw](/images/dockerraw.png)

### 2. Удаление неиспользуемых зависимостей и временных файлов (Dockerfile.clean)

Модифицируем Dockerfile, добавляя очистку кэша и временных файлов.

```dockerfile
# create from ubuntu image
FROM ubuntu:latest

# update system
RUN apt-get update && apt-get upgrade -y

# install nginx
RUN apt-get install -y nginx

# remove apt cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# copy site
COPY site /var/www/html

# expose port 80
EXPOSE 80

# run nginx
CMD ["nginx", "-g", "daemon off;"]
```

Собираем образ и проверяем размер:

```bash
docker image build -t mynginx:clean -f Dockerfile.clean .
docker image list
```

![clean](/images/clean.png)

![list](/images/list1.png)

Размер составил 225мб.

### 3. Уменьшение количества слоев (Dockerfile.few)

Объединяем команды в один слой для уменьшения количества слоев.

```dockerfile
# create from ubuntu image
FROM ubuntu:latest

# update system
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y nginx && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# copy site
COPY site /var/www/html

# expose port 80
EXPOSE 80

# run nginx
CMD ["nginx", "-g", "daemon off;"]
```

Собираем образ и проверяем размер:

```bash
docker image build -t mynginx:few -f Dockerfile.few .
docker image list
```

![few](/images/few.png)

![list2](/images/list2.png)

Размер составил 143мб.

### 4. Минимальный базовый образ (Dockerfile.alpine)

Заменяем базовый образ на `alpine:latest`.

```dockerfile
# create from alpine image
FROM alpine:latest

# update system
RUN apk update && apk upgrade

# install nginx
RUN apk add nginx

# copy site
COPY site /var/www/html

# expose port 80
EXPOSE 80

# run nginx
CMD ["nginx", "-g", "daemon off;"]
```

Собираем образ и проверяем размер:

```bash
docker image build -t mynginx:alpine -f Dockerfile.alpine .
docker image list
```

![alp](/images/alphine.png)

![list3](/images/list3.png)

Размер составил 19,5мб.

### 5. Перепаковка образа

Перепаковываем образ `mynginx:raw` в `mynginx:repack`. Здесь я использовал другие команды, так как у меня были ошибки.

```bash
docker container create --name mynginx mynginx:raw
docker image import mynginx.tar -o mynginx:repack
docker container rm mynginx
docker image list
```

![repack](/images/repack.png)

![list4](/images/list4.png)

### 6. Использование всех методов (Dockerfile.min)

Комбинируем все методы оптимизации.

```dockerfile
# create from alpine image
FROM alpine:latest

# update system, install nginx and clean
RUN apk update && apk upgrade && \
    apk add nginx && \
    rm -rf /var/cache/apk/*

# copy site
COPY site /var/www/html

# expose port 80
EXPOSE 80

# run nginx
CMD ["nginx", "-g", "daemon off;"]
```

Собираем образ `mynginx:minx`, затем перепаковываем в `mynginx:min`. Здесь я также использовал другие команды.

```bash
docker image build -t mynginx:minx -f Dockerfile.min .
docker container create --name mynginx mynginx:minx
docker container export mynginx -o mynginx_min.tar
docker image import mynginx_min.tar mynginx:min
docker container rm mynginx
docker image list
```

![image](/images/buildmin.png)

![min](/images/buildmin.png)

![list](/images/list.png)

### 7. Запуск и тестирование

Проверяем размеры всех образов:

```bash
docker image list
```

![image](/images/list.png)

Как мы видим, с помощью этой команды мы видим все размеры наших образов.

#### Таблица размеров образов

| Образ            | Размер (примерный) |
|------------------|--------------------|
| mynginx:raw      | 224 MB             |
| mynginx:clean    | 225 MB             |
| mynginx:few      | 143 MB             |
| mynginx:alpine   | 19,5 MB            |
| mynginx:repack   | 211 MB             |
| mynginx:min      | 14,3 MB            |

## Ответы на вопросы

1. **Какой метод оптимизации образов вы считаете наиболее эффективным?**  
   Наиболее эффективным методом является использование минимального базового образа (например, `alpine`). Это позволяет значительно сократить размер образа за счет минимизации базовой операционной системы и зависимостей. В сочетании с другими методами (очистка кэша, уменьшение слоев, перепаковка) достигается максимальная оптимизация.

2. **Почему очистка кэша пакетов в отдельном слое не уменьшает размер образа?**  
   Очистка кэша в отдельном слое не уменьшает размер образа, так как Docker сохраняет все промежуточные слои в истории образа. Даже если кэш удаляется в последующем слое, предыдущие слои, содержащие кэш, остаются в образе. Для эффективной очистки кэша необходимо выполнять все команды (обновление, установка, очистка) в одном слое.

3. **Что такое перепаковка образа?**  
   Перепаковка образа — это процесс экспорта файловой системы контейнера и последующего импорта ее в новый образ. Это позволяет удалить историю слоев и создать плоский образ, что может уменьшить размер за счет устранения метаданных и промежуточных слоев.

## Выводы

В ходе работы были изучены и применены различные методы оптимизации Docker-образов. Использование минимального базового образа (`alpine`) показало наибольшую эффективность, особенно в сочетании с очисткой кэша и уменьшением количества слоев. Перепаковка также способствует уменьшению размера, но менее значительно. Оптимизация образов позволяет сократить их размер, ускорить развертывание и снизить потребление ресурсов.

## Библиография

* [Docker Documentation – Best practices for writing Dockerfiles](https://docs.docker.com/develop/develop-images/dockerfile_best-practices/) - Официальная документация Docker с рекомендациями по созданию оптимизированных и безопасных Dockerfile, включая советы по уменьшению числа слоев, очистке временных файлов и использованию минимальных базовых образов.

* [Docker Documentation – docker export](https://docs.docker.com/engine/reference/commandline/export/) - Официальное описание команды `docker container export`, которая позволяет сохранить файловую систему контейнера для последующего импорта и создания нового образа.

* [Docker Documentation – docker import](https://docs.docker.com/engine/reference/commandline/import/) - Официальное руководство по использованию команды `docker image import` для создания образов из экспортированных файловых систем контейнеров.
