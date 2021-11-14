<?php
require_once './db.php';

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

function test(&$testData, $key, $data)
{
    if ($_GET['devTest']) {
        $testData[$key] = $data;
    }
}

function slog(&$res,$str)
{
    $res['data'] = $res['data'] . "# log=>${str}
";
}
function resAppend(&$res,$appendStr){
    $res['data'] = $res['data'] . $appendStr;
}

function testDie($data)
{
    if ($_GET['devTest']) {
        dieJson($data);
    }
}

function isTest()
{
    if ($_GET['devTest']) {
        return true;
    } else {
        return false;
    }
}

function arrayPushArray(&$array, $addArray)
{
    foreach ($addArray as $v) {
        array_push($array, $v);
    }
    return $array;
}

function resRaw($data)
{
    die($data);
}

function getUser($token)
{
    global $db;
    $user = $db->select('user', ['ID', 'LIMITED', 'DATA_VERSION', 'UPDATED_TIME'], [
        'TOKEN' => $token
    ]);
    if (count($user) < 1) {
        res(400, 'token不存在');
    }
    return $user[0];
}