<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/util.php';


$res = true;
$err = [];
try {
    $db = getDB()->count('share_code');
    echo "数据库：正常\r\n";
} catch (Exception $e) {
    $err[] = $e;
    echo "数据库：异常\r\n";
    $res = true;
}
try {
    $redistest = getRedis()->set("test", "test");
    echo "缓存：正常\r\n";
} catch (Exception $e) {
    echo "缓存：异常\r\n";
    $err[] = $e;
    $res = false;
}

if ($res == false) {
    echo "异常\r\n";
    var_dump($err);
}else{
    "已正常启动";
}