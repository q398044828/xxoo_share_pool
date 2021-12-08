#!/bin/bash
## 不要动这个文件-
CRTDIR=$(pwd)
cd ${CRTDIR}
echo "
<?php
putenv('DB_ROOT_PASS=${DB_ROOT_PASS}');
putenv('CONFIG_ENVED=true');
putenv('DB_HOST=${DB_HOST}');
putenv('DB_DATABASE=${DB_DATABASE}');
putenv('DB_PORT=${DB_PORT}');
putenv('DB_USER=${DB_USER}');
putenv('DB_PASS=${DB_PASS}');
putenv('REDIS_HOST=${REDIS_HOST}');
putenv('REDIS_PORT=${REDIS_PORT}');
putenv('TASK_PASS=${TASK_PASS}');
" > env.php