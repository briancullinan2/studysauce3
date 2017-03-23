php app/console cache:clear
php app/console doctrine:generate:entities StudySauceBundle
php app/console doctrine:schema:update --force
php app/console assets:install
php app/console assetic:dump

