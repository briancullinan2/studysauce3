#!/bin/bash
cd "$(dirname "$0")"

docker stop studysauce
docker ps -q -a | xargs docker rm
docker build -t megamind/studysauce ./
docker build -t megamind/studysaucedb ./db/
docker run --name studysaucedb -d megamind/studysaucedb --sql_mode=""
docker run --name studysauce --link studysaucedb:studysaucedb -p 8085:80 -d megamind/studysaucedb
