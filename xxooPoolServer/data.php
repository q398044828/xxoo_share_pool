<?php
require_once('./db.php');

var_dump($argv);

switch ($argv[1]) {
    case 'init':
        init();
        break;
    case 'query':
        query($argv[2]);
        break;
    case 'exec':
        sql($argv[2]);
        break;
}

function query($sql)
{
    global $db;
    var_dump($db->query($sql)->fetchAll());
}

function sql($sql)
{
    global $db;
    var_dump($db->query($sql));
}

function init()
{
    global $db,$INIT_SQL;

    $sqls=explode("|||",$INIT_SQL);
    foreach ($sqls as $sql) {
        $db->query($sql);
        var_dump($db->error());
    }

}





