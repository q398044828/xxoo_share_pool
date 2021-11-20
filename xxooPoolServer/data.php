<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/util.php';

switch ($argv[1]) {
    case 'init':
        runSql(__DIR__ . "/db.sql", []);
        break;
    case 'query':
        query($argv[2]);
        break;
    case 'exec':
        sql($argv[2]);
        break;
    case 'run':
        //使用方式： php data.php run db.sql 1 2 3 4
        //其中 1 2 3 4表示sql位置，对应sql文件中的[1] [2] [3] [4] 标记的位置，
        //也可以：php data.php run db.sql 表示执行所有sql
        $file = $argv[2];
        unset($argv[0]);
        unset($argv[1]);
        unset($argv[2]);
        runSql($file, $argv);
        break;
    case 'dataClean':
        dataClean();
        break;
    case 'refreshCurrentCodeNum':
        refreshCurrentCodeNum($argv[2], $argv[3]);
        break;
    case 'refresh':
        refresh($argv[2]);
        break;
    case 'refreshall':
        refreshAll();
        break;
    default :
        echo <<<EOF
    -h                              帮助
    init                            初始化数据库
    dataClean                       清理数据(非清空，将不活跃的数据删除)
    refreshCurrentCodeNum           统计user的助力码数量并更新到数据库中
    refresh                         刷入活动助力码到缓存，例如： refresh FRUITSHARECODES
    refreshall                      刷入所有助力码到缓存 \r\n
EOF;

        break;
}

function refreshCurrentCodeNum($userId, $token)
{
    global $db;
    $count = $db->count('share_code', [
        'USER_ID' => $userId
    ]);
    $db->update('user', ['CURRENT_NUM' => $count], [
        'ID' => $userId
    ]);
    getRedis()->del(getUserKey($token));
}

function refreshAll()
{
    foreach (ENVS as $env => $v) {
        refresh($env);
    }
}

function refresh($env)
{
    global $db;

    //刷数据到缓存前清理数据
    dataClean();

    //批量查询出来刷入缓存
    $page = 0;
    $num = 200;
    $loopMax = 999;
    do {
        $loopMax--;
        $res = $db->select('share_code', ['CODE'], [
            'ENV' => $env,
            'LIMIT' => [$page * $num, $num]
        ]);
        if (count($res) < 1) {
            break;
        }
        $codes = [];
        foreach ($res as $k => $data) {
            $codes[] = $data['CODE'];
        }
        getRedis()->sAddArray(getEnvCodeKey($env), $codes);
        $page++;
    } while ($loopMax > 0);
}

function runSql($file, $posis)
{
    global $db;
    $fileContent = file_get_contents($file);

    if (DB_TYPE == 'mysql') {
        $fileContent = str_replace("autoincrement", "AUTO_INCREMENT", $fileContent);
    }

    $sqls = explode(";", $fileContent);
    $NamedSqls = [];
    foreach ($sqls as $sql) {
        $sql = trim($sql);
        $s = strpos($sql, "[");
        $posi = substr($sql, $s + 1, strpos($sql, "]") - $s - 1);
        $NamedSqls[$posi . ""] = $sql;
    }
    if (count($posis) > 0) {
        foreach ($posis as $posi) {
            $db->query($NamedSqls[$posi]);
            var_dump($db->error());
        }
    } else {
        foreach ($NamedSqls as $name => $sql) {
            $db->query($sql);
            var_dump($db->error());
        }
    }
}

function query($sql)
{
    global $db;
    var_dump($db->query($sql)->fetchAll());
}

function sql($sql)
{
    global $db;
    $db->query($sql);
    var_dump($db->error());
}

function dataClean()
{
    global $db;
    $ctime = time() - 1209600;//2周前
    $count = $db->count('share_code', [
        'CREATE_TIME[<]' => $ctime
    ]);
    //为什么要先查询再执行update? 因为sqlite执行修改时会锁定文件导致并发下降,但是可以共享读
    if ($count > 100) {
        //大于100判断是为了减少进行删除的频率
        $db->delete('share_code', [
            'CREATE_TIME[<]' => $ctime
        ]);
    }
}




