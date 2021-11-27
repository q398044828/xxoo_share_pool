<?php

function res($code, $msg, $data = null)
{
    header('Content-Type:application/json; charset=utf-8');
    die(json_encode(['code' => $code, 'msg' => $msg, 'data' => $data]));
}

function dieJson($data)
{
    header('Content-Type:application/json; charset=utf-8');
    return die(json_encode($data));
}

/**
 * 敏感信息隐藏
 * @param $str
 */
function sensitive($str)
{
    $length = strlen($str);
    $reLen = 6;
    if ($length < 10) {
        $reLen = 3;
    }
    $s = ($length / 2) - $reLen / 2;
    return substr_replace($str, "******", $s, $reLen);
}

function test(&$testData, $key, $data)
{
    if ($_GET['devTest']) {
        $testData[$key] = $data;
    }
}

function slog(&$res, $str)
{
    $res['data'] = $res['data'] . "# log=>${str}
";
}

function resAppend(&$res, $appendStr)
{
    $res['data'] = $res['data'] . $appendStr;
}


function resRaw($data)
{
    die($data);
}


function updData($key, $func)
{
    $func();
    if (USE_REDIS) {
        getRedis()->del($key);
    }
}


/*
 * key 缓存key,
 * val Cache对象
 */
function getDataByArr(array $arr)
{
    $keys = array_keys($arr);
    $res = [];
    if (USE_REDIS) {
        $cacheRes = getRedis()->mget($keys);
        foreach ($cacheRes as $i => $r) {
            if ($r == false) {
                $cache = $arr[$keys[$i]];
                $call = $cache->call;
                $r = $call();
                $putCache = $r;
                if ($cache->isArray) {
                    $putCache = json_encode($putCache);
                }
                getRedis()->set($keys[$i], $putCache, $cache->timeout);
                $res[$keys[$i]] = $r;
            } else {
                $res[$keys[$i]] = json_decode($r, true);
            }
        }
    } else {
        foreach ($arr as $key => $c) {
            $call = $c->call;
            $res[$key] = $call();
        }
    }

    return $res;
}

$redis = null;
function getRedis()
{
    global $redis;
    if ($redis == null) {
        $redis = new Redis();
        $redis->pconnect(REDIS_HOST, REDIS_PORT, REDIS_DEFAULT_TIME);//serverip port
        if (REDIS_PASS != '') {
            $redis->auth(REDIS_PASS);
        }
        $redis->select(REDIS_IDNEX);
    }
    return $redis;
}

$db = null;
function getDB()
{
    global $db;
    if ($db == null) {
        $db = new medoo([
            'database_type' => DB_TYPE,
            'database_name' => DB_DATABASE,
            'server' => DB_HOST,
            'port' => DB_PORT,
            'username' => DB_USER,
            'password' => DB_PASS
        ]);
    }
    return $db;
}

function getUserKey($token)
{
    return "user:" . $token;
}

function getAskForMeKey($str)
{
    return "getAskForMe:" . $str;
}

function getReqDataVersion($reqMd5)
{
    return "reqDataVersion:" . $reqMd5;
}

function getEnvCodeKey($env)
{
    return "envCodeCache:${env}";
}

function getPtPinCodeKey($ptPin)
{
    return "ptPinCodeCache:${ptPin}";
}

function getPtPinCoeKeyArr(array $ptPins)
{
    $keys = [];
    foreach ($ptPins as $ptPin) {
        $keys[] = getPtPinCodeKey($ptPin);
    }
    return $keys;
}