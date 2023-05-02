#!/usr/bin/env bash

MYSQL_DATABASE="eurobudget_db"
MYSQL_USER="eurobudget_admin"
MYSQL_PASSWORD="admin"
MYSQL_TABLE="operation"
MYSQL_PORT="3325"

MYSQL_CONNECT_PARAMS="-h 172.17.0.1 --port=${MYSQL_PORT} -u ${MYSQL_USER} -p${MYSQL_PASSWORD}"

while read -r output;
do
    id=$(echo "$output" | awk -F"\t" '{print $1}')
    createdAt=$(echo "$output" | awk -F"\t" '{print $2}')

    echo ${id} - ${createdAt}

    docker compose exec db mysql ${MYSQL_DATABASE} ${MYSQL_CONNECT_PARAMS} <<<"
    UPDATE ${MYSQL_TABLE} \
    SET date = '${createdAt}' \
    WHERE id = '${id}' \
    ;"

done< <(docker compose exec db mysql ${MYSQL_DATABASE} ${MYSQL_CONNECT_PARAMS} <<<"
    SELECT id, created_at
    FROM ${MYSQL_TABLE}
;" | sed 1d)
