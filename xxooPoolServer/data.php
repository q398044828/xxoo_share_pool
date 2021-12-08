<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/util.php';
switch ($argv[1]) {
    case 'init':
        if (createDatabase() == true) {
            runSql(__DIR__ . "/db.sql", []);
        }
        break;
    case 'create_database':
        createDatabase();
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

/**
 * 创建数据库
 */
function createDatabase()
{
    $host = getenv('DB_HOST');
    $database = getenv('DB_DATABASE');
    $pass = getenv('DB_PASS');
    $rootPass = getenv('DB_ROOT_PASS');
    $port = getenv('DB_PORT');
    $user = getenv('DB_USER');
    $dsn = "mysql:host=${host};port=${port}";
    var_dump($dsn);
    var_dump($user);
    var_dump($pass);
    var_dump($database);
    $conn = new PDO($dsn, 'root', $rootPass);
    $res = $conn->exec("CREATE DATABASE $database");
    if ($res == false) {
        var_dump("创建数据库失败");
        var_dump($conn->errorInfo());
        return false;
    }
    var_dump('分配数据库权限');
    $conn->exec("GRANT ALL PRIVILEGES ON *.* TO '${user}'@'%' IDENTIFIED BY '${pass}' WITH GRANT OPTION");
    $conn->exec("FLUSH PRIVILEGES");
    return true;

}

function refreshCurrentCodeNum($userId, $token)
{
    $db = getDB();
    $count = $db->count('share_code', [
        'USER_ID' => $userId
    ]);
    $db->update('user', ['CURRENT_NUM' => $count], [
        'ID' => $userId
    ]);
    getRedis()->del(getUserKey($token));
    dlog("refreshCurrentCodeNum_${userId}","count ${count}");
}

function refreshAll()
{
    foreach (ENVS as $env => $v) {
        refresh($env);
    }
}

function refresh($env)
{
    $db = getDB();

    //刷数据到缓存前清理数据
    dataClean();

    //批量查询出来刷入缓存
    $page = 0;
    $num = 200;
    $loopMax = 999;
    $c = 0;
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
        $c = $c + count($res);
        getRedis()->sAddArray(getEnvCodeKey($env), $codes);
        $page++;
    } while ($loopMax > 0);
    dlog("refresh_${env}", " count " . $c);
}

function runSql($file, $posis)
{
    $db = getDB();
    $fileContent = file_get_contents($file);

    if (DB_TYPE == 'mysql') {
        //兼容mysql
        $fileContent = str_replace("autoincrement", "AUTO_INCREMENT", $fileContent);
        $fileContent = str_replace("TIMESTAMP", "BIGINT", $fileContent);
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
    $db = getDB();
    var_dump($db->query($sql)->fetchAll());
}

function sql($sql)
{
    $db = getDB();
    $db->query($sql);
    var_dump($db->error());
}

function dataClean()
{
    $db = getDB();
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
        echo getDatetime() . ' > 刪除' . $count . "条数据\r\n";
    }
}




