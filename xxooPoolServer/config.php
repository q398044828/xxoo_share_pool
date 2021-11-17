<?php

const DB_URL = 'shareCode.db';
const TOKEN_PARAMETER_NAME = 'token';
const MAX_NO_UPDATE_DAY = 432000;//互助码上报时，最长不更新CREATE_TIME的时间，单位秒 432000=5天
const CLIENT_VERSION = '1.0.1'; //客户端版本，用于提示xxoo.js版本需要更新
const DEFAULT_GET_CODE_NUM = 10;//默认从数据库取的code数量
/**
 * 数据库密码，管理作用,这里需要修改成你自己的
 * 管理地址：/dbmanager.php
 * System：sqlite3
 * Database: shareCode.db
 * username: 不填
 * Password: 填你自己设置的密码
 */
const DB_PASSWORD = "123456";

/**
 * 助力次数配置
 * [a,b]
 * a: 需要助力的次数
 * b: 可提供助力次数
 */
const HELP_NUM = [
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