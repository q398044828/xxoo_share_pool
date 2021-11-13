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

function getUserId($token){
    global $db;
    $id=$db->select('user','ID',[
        'TOKEN'=>$token
    ]);
    if(count($id)<1){
        res(400,'token不存在');
    }
    return $id[0];
}