<?php
require_once './db.php';

function res($code, $msg, $data = null)
{
    header('Content-Type:application/json; charset=utf-8');
    die(json_encode(['code' => $code, 'msg' => $msg, 'data' => $data]));
}

function json($code, $msg, $data = null){
    return ['code' => $code, 'msg' => $msg, 'data' => $data];
}

function resRaw($data){
    die($data);
}

function getUser($token){
    global $db;
    $user=$db->select('user',['ID','LIMITED'],[
        'TOKEN'=>$token
    ]);
    if(count($user)<1){
        res(400,'token不存在');
    }
    return $user[0];
}