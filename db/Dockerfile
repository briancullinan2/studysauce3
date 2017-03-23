FROM mysql:latest

ENV MYSQL_ROOT_PASSWORD studysauce2017

COPY ./studysauce-03-20-2017-prod.sql.gz /tmp/
RUN gunzip /tmp/studysauce-03-20-2017-prod.sql.gz
RUN echo "CREATE DATABASE studysauce3; GRANT ALL ON studysauce3.* TO 'study2'@'%' IDENTIFIED BY 'itekIO^#(1234'; USE studysauce3;" > /docker-entrypoint-initdb.d/data-dump.sql
RUN cat /tmp/studysauce-03-20-2017-prod.sql >> /docker-entrypoint-initdb.d/data-dump.sql

