#!/bin/bash
cd "$(dirname "$0")"

docker stop studysauce
docker stop studysaucedb
docker ps -q -a | xargs docker rm
docker build -t studysauce ./
docker build -t studysaucedb ./db/
docker run --name studysaucedb -d studysaucedb --sql_mode=""
docker run --name studysauce -e SYMFONY__DATABASE__HOST=studysaucedb --link studysaucedb:studysaucedb -p 8086:80 -d studysauce
