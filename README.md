[![pipeline status](https://gitlab.com/the.mlex/news-portal/badges/master/pipeline.svg)](https://gitlab.com/the.mlex/news-portal/commits/master)


News Portal
============================

# Demo

[News Portal](https://demo.xmlex.ru/news-portal/web/)

# Configuration

index.php
```bash
$ cp web/index-{prod,dev,test}.php web/index.php
```

Db (MySQL)
```bash
$ cp config/db.php.install config/db.php
$ sed -i -- "s/{host}/$DB_HOST/g" config/db.php
$ sed -i -- "s/{database}/$DB_NAME/g" config/db.php
$ sed -i -- "s/{user}/$DB_USER/g" config/db.php
$ sed -i -- "s/{password}/$DB_PASS/g" config/db.php
```

SMTP and other
```bash
$ cp config/params.php.install config/params.php
$ sed -i -- "s/{cookieValidationKey}/$cookieValidationKey/g" config/params.php
$ sed -i -- "s/{smtp_host}/$smtp_host/g" config/params.php
$ sed -i -- "s/{smtp_port}/$smtp_port/g" config/params.php
$ sed -i -- "s/{smtp_username}/$smtp_username/g" config/params.php
$ sed -i -- "s/{smtp_password}/$smtp_password/g" config/params.php
```


# Migrations

```bash
$ php yii migrate --migrationPath=@yii/rbac/migrations
$ php yii migrate
```

# Tests
```bash
$ php vendor/bin/codecept build
$ php vendor/bin/codecept run unit
```