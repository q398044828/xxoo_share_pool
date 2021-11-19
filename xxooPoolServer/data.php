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
}

function runSql($file, $posis)
{
    global $db;
    $fileContent = file_get_contents($file);
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






