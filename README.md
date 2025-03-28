# sl-qr-app

PHP 8.0+ (с расширениями: pdo, pdo_mysql, curl, gd, mbstring)

MySQL 5.7+/MariaDB 10.3+

Composer 2.0+

Веб-сервер (Apache/Nginx)

Для запуска приложения необходимо:
- клонировать проект из репозитория на ваш хостинг
  git clone https://github.com/tex3700/sl-qr-app.git
- сделать стартовой (возможно создать символьную ссылку) страницу: sl-qr-app/web/index.php
- прописать параметры Вашей базы данных в config/db
- из папки sl-qr-app выполнить миграции базы данных выполнив "php yii migrate"