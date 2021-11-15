<?php

define('DB_URL', 'shareCode.db');
define('TOKEN_PARAMETER_NAME', 'token');
define('ENV_RANDOM_NUM',15);//针对环境变量随机获取的条数
define('MAX_NO_UPDATE_DAY',432000);//互助码上报时，最长不更新CREATE_TIME的时间，单位秒 432000=5天

/**
 * 数据库密码，管理作用,这里需要修改成你自己的
 * 管理地址：/dbmanager.php
 * System：sqlite3
 * Database: shareCode.db
 * username: 不填
 * Password: 填你自己设置的密码
 */
define('DB_PASSWORD',"TEST_PASS");