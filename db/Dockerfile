FROM mysql:latest

COPY ./studysauce-03-20-2017-prod.sql.gz /tmp/
RUN gunzip /tmp/studysauce-03-20-2017-prod.sql.gz
RUN cat /tmp/studysauce-03-20-2017-prod.sql > /docker-entrypoint-initdb.d/data-dump.sql



