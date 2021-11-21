<?php
require_once __DIR__ . '/lib/medoo.php';


const TOKEN_PARAMETER_NAME = 'token';
const MAX_NO_UPDATE_DAY = 432000;//互助码上报时，最长不更新CREATE_TIME的时间，单位秒 432000=5天
const CLIENT_VERSION = '1.0.2'; //客户端版本，用于提示xxoo.js版本需要更新
const DEFAULT_GET_CODE_NUM = 10;//默认从数据库取的code数量

/**
 * 此参数仅仅用于使用无密码sqlite时进行管理作用，如果使用mysql，则不理会此参数
 * 数据库管理密码,进入管理页面时使用,这里需要修改成你自己的
 * 管理地址：根据参数：
 * System：sqlite3
 * Database: ../shareCode.db
 * username: 不填
 * Password: 填你自己设置的密码
 */
const SQLITE_MANAGER_PASSWORD = "123456";

/**
 * 如果你不懂这个参数，就不要动这个参数
 * 本参数是用于控制是否是独立部署管理端，为true时，需要单独部署admin目录下的程序，www目录下的admin.php则无法访问，
 */
const DB_OTHER_MANAGER = false;

/**
 * 数据库采用medoo框架，可以更换数据库，如使用其他数据库，参考官方文档生成$db对象
 * 暂时只支持 mysql,sqlite,只能2选1
 *
 * const DB_TYPE = "sqlite";
 * $db = new medoo([
 *      'database_type' => DB_TYPE,
 *      'database_file' => DB_URL
 * ]);
 *
 * const DB_TYPE = "mysql";
 * $db = new medoo([
 *      'database_type' => DB_TYPE,
 *      'database_name' => '',
 *      'server' =>  '192.168.2.10',
 *      'port'=>3306,
 *      'username'=>'',
 *      'password'=>''
 * ]);
 */

const DB_TYPE = "mysql";
$db = new medoo([
    'database_type' => DB_TYPE,
    'database_name' => 'xxoo_pool',
    'server' => '192.168.1.1',
    'port' => 3306,
    'username' => ' ',
    'password' => ' '
]);

/*
const DB_URL = __DIR__ . '/shareCode.db';
const DB_TYPE = "sqlite";
$db = new medoo([
    'database_type' => DB_TYPE,
    'database_file' => DB_URL
]);*/

/**
 * redis配置，如果你的服务器有phpredis扩展的话才打开，否则不要打开
 */
const USE_REDIS = true;
const REDIS_HOST = '127.0.0.1';
const REDIS_PORT = '6379';
const REDIS_PASS = '';
const REDIS_DEFAULT_TIME = 43200;
const REDIS_IDNEX = 1;


/**
 * 助力次数配置
 * [a,b]
 * a: 需要助力的次数
 * b: 可提供助力次数
 */
const ENVS = [
    'FRUITSHARECODES' => [5, 3],                //京东农场
    'PETSHARECODES' => [5, 5],                  //京东萌宠
    'PLANT_BEAN_SHARECODES' => [9, 3],          //种豆得豆
    'DDFACTORY_SHARECODES' => [5, 3],           //东东工厂
    'DREAM_FACTORY_SHARE_CODES' => [15, 3],     //京喜工厂
    'JXNC_SHARECODES' => [5, 3],                //京喜农场
    'JDZZ_SHARECODES' => [5, 2],                //京东赚赚
    'JDJOY_SHARECODES' => [6, 1],               //疯狂的JOY
    'BOOKSHOP_SHARECODES' => [10, 1],           //京东书店
    'JD_CASH_SHARECODES' => [10, 1],            //签到领现金
    'JDSGMH_SHARECODES' => [10, 1],             //闪购盲盒
    'JDCFD_SHARECODES' => [5, 5],               //京喜财富岛
    'JDHEALTH_SHARECODES' => [5, 5],            //东东健康
    'CITY_SHARECODES' => [5, 5]                 //城城领现金
];

/**
 * 助力码非法字符集，用于过滤
 */
const ILLEGAL_CHAR = [
    '{',
    '}',
    '[',
    ']'
];

/**
 * pt_pin非法字符集，用于过滤
 */
const ILLEGAL_PT_PIN_CHAR = [
    'pt_pin'
];
