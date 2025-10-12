# Используем базовый образ Yii 2 для PHP 8.4 с Apache
FROM yiisoftware/yii2-php:8.4-apache

# Устанавливаем DocumentRoot для Apache, чтобы он смотрел в web-папку нашего приложения
RUN sed -i -e 's|/app/web|/app/app/web|g' /etc/apache2/sites-available/000-default.conf

# Включаем mod_rewrite для красивых URL
RUN a2enmod rewrite

# Добавляем репозиторий в безопасный список Git, чтобы избежать ошибки ownership
RUN git config --global --add safe.directory /app

# УСТАНАВЛИВАЕМ РАБОЧУЮ ДИРЕКТОРИЮ
WORKDIR /app/app