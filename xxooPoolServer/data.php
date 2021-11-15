<?php

require_once 'config.php';

//define('DB_URL', 'test.db');


require_once('./db.php');
require_once './util.php';

switch ($argv[1]) {
    case 'init':
        runSql("db.sql", []);
        break;
    case 'query':
        query($argv[2]);
        break;
    case 'exec':
        sql($argv[2]);
        break;
    case 'run':
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






