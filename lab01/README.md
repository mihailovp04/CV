# Лабораторная работа №1. Виртуальный сервер

## Студент: Mihailov Piotr, группа I2302

## Дата выполнения: 16.02.2025

---

## Описание и постановка задачи

Данная лабораторная работа знакомит с виртуализацией операционных систем и настройкой виртуального HTTP сервера на базе QEMU и Debian. В ходе работы необходимо:

- Установить виртуальную машину с Debian.
- Настроить веб-сервер LAMP.
- Установить и настроить PhpMyAdmin и CMS Drupal.
- Настроить виртуальные хосты.
- Проверить работу сервисов и ответить на вопросы.

---

## Ход работы

### 1. Подготовка

1. Для начала, я устанавливаю MSYS2.
![Установка MSYS2](images/instalmsys.png)
2. После установки MSYS2,создаю папку `lab01`, в ней создается:
   - Папка `dvd` для ISO-образа.
   - Файл `readme.md`.
3. Захожу на официальный сайт и копирую ссылку для дальйнешей работы.
![Сайт Debian](images/sitedebian.png)
4. Захожу в папку dvd, с помощью команды cd dvd и прописываю следующую команду в командную строку, для скачивания образа.
wget -O debian.iso https://cdimage.debian.org/debian-cd/current/amd64/iso-dvd/debian-12.9.0-amd64-DVD-1.iso
![Установка Debian](images/downloaddebian.png)
5. После я устанавливаю QEMU, используя следующую команду:
pacman -S mingw-w64-ucrt-x86_64-qemu
![Установка qemu](images/downloadqemu.png)
6. QEMU был успешно установлен.

## 2. Установка ОС

1. Для начала установки ОС, я прописываю следующую команду, для создания образа диска для виртуальной машины, формат qcow2.
qemu-img create -f qcow2 debian.qcow2 8G
2. Запускаю установку ОС с помощью команды и начинаю установку
qemu-system-x86_64 -hda debian.qcow2 -cdrom dvd/debian.iso -boot d -m 2G
![установка](images/install.png)

3. Использовал параметры:
   - Имя компьютера: `debian`
   - Хостовое имя: `debian.localhost`
   - Имя пользователя: `user`
   - Пароль: `password`
4. После установки перезагрузил виртуальную машину

   ```bash
   qemu-system-x86_64 -hda debian.qcow2 -m 2G -smp 2 \
       -device e1000,netdev=net0 -netdev user,id=net0,hostfwd=tcp::1080-:80,hostfwd=tcp::1022-:22
   ```

   ![запуск виртуалки](images/install2.png)

---

## 4. Установка LAMP

1. Авторизируюсь и переключаюсь на суперпользователя командой:

   ```bash
   su
   ```

![логин](images/loginandsu.png)
2. Установил необходимые пакеты:

   ```bash
   apt update -y
   apt install -y apache2 php libapache2-mod-php php-mysql mariadb-server mariadb-client unzip
   ```

![установка2](images/mariadb.png)

## 5. Установка PhpMyAdmin и Drupal

1. Скачал файлы:

   ```bash
   wget https://files.phpmyadmin.net/phpMyAdmin/5.2.2/phpMyAdmin-5.2.2-all-languages.zip
   ```

   ![phpmyadmin](images/phpmyadmin.png)

   ```bash
   wget https://ftp.drupal.org/files/projects/drupal-10.0.5.zip
   ```

   ![drupal](images/drupal.png)

2. Разместил файлы:

   ```bash
   mkdir /var/www
   unzip phpMyAdmin-5.2.2-all-languages.zip
   mv phpMyAdmin-5.2.2-all-languages /var/www/phpmyadmin
   unzip drupal-10.0.5.zip
   mv drupal-10.0.5 /var/www/drupal
   ```

---

## 6. Настройка базы данных

```bash
mysql -u root
CREATE DATABASE drupal_db;
CREATE USER 'piotr'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON drupal_db.* TO 'piotr'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 7. Настройка виртуальных хостов

Создаю файл командой:
nano /etc/apache2/sites-available/01-phpmyadmin.conf
и далее ввожу следующее содержимое:

<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot "/var/www/phpmyadmin"
    ServerName phpmyadmin.localhost
    ServerAlias www.phpmyadmin.localhost
    ErrorLog "/var/log/apache2/phpmyadmin.localhost-error.log"
    CustomLog "/var/log/apache2/phpmyadmin.localhost-access.log" common
</VirtualHost>

Также создаю следующий файл командой:
nano /etc/apache2/sites-available/02-drupal.conf
и далее ввожу следующее содержимое:
<VirtualHost *:80>
    ServerAdmin webmaster@localhost
    DocumentRoot "/var/www/drupal"
    ServerName drupal.localhost
    ServerAlias www.drupal.localhost
    ErrorLog "/var/log/apache2/drupal.localhost-error.log"
    CustomLog "/var/log/apache2/drupal.localhost-access.log" common
</VirtualHost>

Далее я зарегистрировал конфигурацию, выполнив команды:

```bash
/usr/sbin/a2ensite 01-phpmyadmin
/usr/sbin/a2ensite 02-drupal
```

Выполнил перезагрузку Apache HTTP Server
systemctl reload apache2
Далее я в файл /etc/host добавил следующие строки

```bash
127.0.0.1 phpmyadmin.localhost
127.0.0.1 drupal.localhost
```

---

## 8. Тестирование

1. Проверил ОС:

   ```bash
   uname -a
   ```

2. Перегрузил Apache:

   ```bash
   systemctl restart apache2
   ```

3. Проверил сайты в браузере:
   - http://drupal.localhost:1080
   - http://phpmyadmin.localhost:1080

---

## 9. Ответы на вопросы

1. **Как скачать файл с помощью wget?**

   ```bash
   wget <URL>
   ```

2. **Зачем создавать отдельные базы и пользователей?**
   - Для безопасности и разделения прав доступа.
3. **Как изменить порт MySQL на 1234?**
   - В файле `/etc/mysql/mariadb.conf.d/50-server.cnf` изменить `port = 1234`, затем перезапустить MySQL.
4. **Преимущества виртуализации:**
   - Гибкость, изоляция, удобство тестирования.
5. **Зачем настраивать временную зону?**
   - Для корректного ведения логов и времени событий.
6. **Размер установленной ОС?**

   ```bash
   du -sh debian.qcow2
   ```

7. **Рекомендации по разбиению диска:**
   - `/` – 10-20GB, `/home` – 30-50GB, `swap` – 2xRAM. Обоснование: безопасность, удобство резервного копирования.

---

## 10. Выводы

В ходе лабораторной работы была освоена установка и настройка виртуальной машины с Debian, установка стека LAMP, работа с базами данных, настройка виртуальных хостов и тестирование работы серверов.
