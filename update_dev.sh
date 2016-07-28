#! /bin/bash

cd /var/www/studysauce3/;
php app/console cache:clear --env=dev;
php app/console doctrine:generate:entities StudySauceBundle;
php app/console doctrine:schema:update --force --env=dev;
php app/console assets:install --env=dev --symlink;
php app/console assetic:dump --env=dev;
chown www-data:www-data -R app/cache/
chown www-data:www-data -R app/logs/
chown www-data:www-data -R src/Admin/Bundle/Tests
chown www-data:www-data -R src/Admin/Bundle/Resources/public/results/
chown www-data:www-data -R vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer

