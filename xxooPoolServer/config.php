<?php

define('DB_URL', 'shareCode.db');
define('TOKEN_PARAMETER_NAME', 'token');
define('ENV_RANDOM_NUM',25);//针对环境变量随机获取的条数

$INIT_SQL = <<<EOF
CREATE TABLE share_code
      (
          ID          INTEGER PRIMARY KEY autoincrement,
          USER_ID     INTEGER    NOT NULL,
          PT_PIN    VARCHER(128),
          ENV    VARCHER(128),
          CODE       VARCHAR(256),
          CREATE_TIME timestamp
      );
|||
CREATE INDEX share_code_user_id
on share_code (USER_ID);
CREATE INDEX share_code_env
on share_code (ENV);
|||
CREATE UNIQUE INDEX share_code_code
on share_code (CODE);
|||
CREATE TABLE user
      (
          ID          INTEGER PRIMARY KEY autoincrement,
          TOKEN       VARCHER(128)    NOT NULL,
          ENABLED     INT,
          LIMITED     int,
          CREATE_TIME timestamp
      );
|||
CREATE UNIQUE INDEX user_token
on user (TOKEN);
|||

insert into user('TOKEN','ENABLED','LIMITED') values ('dev_token',1,1000);

EOF;

