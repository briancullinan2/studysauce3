#! /bin/bash

cd /var/www/Symfony/;
php app/console cache:clear --env=prod;
php app/console doctrine:generate:entities StudySauceBundle;
php app/console doctrine:generate:entities Course1;
php app/console doctrine:generate:entities Course2;
php app/console doctrine:generate:entities Course3;
php app/console doctrine:schema:update --force --env=prod;
php app/console assets:install --env=prod --symlink;
php app/console assetic:dump --env=prod;
chown apache:apache -R app/cache/prod/
chown apache:apache -R app/logs/
chown apache:apache -R vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer
