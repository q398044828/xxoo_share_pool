--[0]用户助力码
CREATE TABLE share_code
(
    ID          INTEGER PRIMARY KEY autoincrement,
    USER_ID     INTEGER NOT NULL, --用户id
    PT_PIN      VARCHAR(128),     --用户上传的pt_pin值
    ENV         VARCHAR(128),     --环境变量名
    CODE        VARCHAR(256),     --助力码
    CREATE_TIME timestamp         --创建时间，也是更新时间
);

--[1]
CREATE INDEX share_code_user_id
    on share_code (USER_ID);

--[2]
CREATE INDEX share_code_env
    on share_code (ENV);

--[3]
CREATE UNIQUE INDEX share_code_code
    on share_code (CODE);

--[4]  用户表
CREATE TABLE user
(
    ID          INTEGER PRIMARY KEY autoincrement,
    TOKEN       VARCHER(128) NOT NULL, --用户对接服务池的token值，需要开放给用户
    ENABLED     INT,                   --启用状态 1启用 0禁用
    LIMITED     int,                   --上传的code数限制
    CREATE_TIME timestamp
);

--[5]
CREATE UNIQUE INDEX user_token
    on 'user' (TOKEN);

--[6]
insert into 'user'('TOKEN', 'ENABLED', 'LIMITED')
values ('dev_token', 1, 1000);

--[7]
ALTER TABLE 'user'
    ADD 'DATA_VERSION' VARCHAR(64);

--[8]
ALTER TABLE 'user'
    ADD 'UPDATED_TIME' timestamp;

--[9] 定向助力表
CREATE TABLE user_for
(
    ID          INTEGER PRIMARY KEY autoincrement,
    USER_ID     INTEGER      NOT NULL, --用户id
    PT_PIN      VARCHAR(128) NOT NULL, --用户的pt_pin值
    ASK_FOR     VARCHAR(128) NOT NULL, --用户要助力的好友pt_pin值
    CREATE_TIME TIMESTAMP
);
--[10]
CREATE INDEX user_for_ask_for
    on user_for (ASK_FOR);