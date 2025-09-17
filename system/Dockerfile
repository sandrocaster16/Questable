FROM yiisoftware/yii2-php:8.4-apache
ENV PHP_ENABLE_XDEBUG=1
  # Change document root for Apache
RUN sed -i -e 's|/app/web|/app/app/web|g' /etc/apache2/sites-available/000-default.conf

# Устанавливаем модуль rewrite для Apache
RUN a2enmod rewrite