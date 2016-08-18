#! /bin/bash

cd /var/www/studysauce3/;
php app/console cache:clear --env=test;
php app/console doctrine:generate:entities StudySauceBundle;
php app/console doctrine:schema:update --force --env=test;
php app/console assets:install --env=test --symlink;
php app/console assetic:dump --env=test;
php vendor/codeception/codeception/codecept build --config src/Admin/Bundle/codeception.yml
chown apache:apache -R app/cache/
chown apache:apache -R app/logs/
chown apache:apache -R src/Admin/Bundle/Resources/public/results/
chown apache:apache -R vendor/ezyang/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer
