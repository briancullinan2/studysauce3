sed 's/studysauce3/studysauce/g' export-drupal.sql | mysql -h studysauce.cjucxx5pvknl.us-west-2.rds.amazonaws.com -u study -p > /tmp/import_live.sql
sudo sqlite3 app/data.sqlite < /tmp/import_live.sql
sudo php app/console doctrine:schema:drop --force
sudo php app/console doctrine:schema:create