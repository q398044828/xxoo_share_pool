<?php

$url = $_REQUEST['_url'];
$num = intval($_GET['num']);

//活动助力码环境变量名
$env = explode("/", $url);
$num = isset($env[2]) ? intval($env[2]) : $num;
$num = empty($num) || $num > 20 ? 20 : $num;

$env = $env[1];
$res = getRedis()->sRandMember(getEnvCodeKey($env), $num);
dieJson(['code' => 200, 'data' => $res]);