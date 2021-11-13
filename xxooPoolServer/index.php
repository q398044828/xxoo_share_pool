<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
//define( 'WP_USE_THEMES', true );

/** Loads the WordPress Environment and Template */
//require __DIR__ . '/wp-blog-header.php';

require_once './db.php';
require_once './util.php';


$userId = getUserId($_GET[TOKEN_PARAMETER_NAME]);
$res = "";
switch ($_SERVER['PATH_INFO']) {
    case '/upload':
        upload($userId, $_POST['data']);
        $res = getCodes($userId, $_POST['data']);
        resRaw($res);
        break;
    case '/get':
        $res = getCodes($userId);
        resRaw($res);
        break;
    case '/uploadAndGetCodes':
        $res = uploadAndGetCodes($userId, $GLOBALS['HTTP_RAW_POST_DATA']);
        resRaw($res);
        break;
    default :
        res(400, '不盈利，不推广，自用！！，请勿攻击');
        break;
}
/**
 * @param $userId
 * @param $data
 */
function uploadAndGetCodes($userId, $data)
{
    $data = json_decode($data, true);
    uploadjson($userId, $data);
    $r = getCodes($userId, $data);
    return $r;
}

function uploadjson($userId, $data)
{

    foreach ($data as $ptPin => $envs) {
        foreach ($envs as $env => $code) {
            saveShareCode($userId, $ptPin, $env, $code);
        }
    }
    return $userId;
}

function saveShareCode($userId, $ptPin, $env, $code)
{
    global $db;
    //先尝试修改，修改成功则表示已经存在
    $data = [
        'USER_ID' => $userId,
        'PT_PIN' => $ptPin,
        'ENV' => $env,
        'CODE' => $code,
        'CREATE_TIME' => time()
    ];
    $n = $db->update('share_code', $data, [
        'CODE' => $code,
        'ENV' => $env,
    ]);
    if ($n < 1) {
        $res = $db->insert('share_code', $data);
        $res = $res > 0 ? true : false;
    } else {
        $res = true;
    }
    return $res;
}


function getCodes($userId, $req)
{
    global $db;

    //请求里的互助码
    $envNames = [];
    foreach ($req as $ptPin => $envs) {
        foreach ($envs as $env => $code) {
            if (!isset($envNames[$env])) {
                $envNames[$env] = [];
            }
            $envNames[$env][] = $code;
        }
    }

    // 数据库的互助码
    $dbEnvs = [];
    foreach ($envNames as $env => $v) {
        $dbEnvCodes = getCodesByEnvFromDB($env);
        $dbEnvs[$env] = $dbEnvCodes;
    }

    //请求要求的互助码
    $askFor = askFor($_GET['askFor']);

    $finalEnvCodes = mergeCodesByEnv($envNames, $envNames, $dbEnvs, $askFor);

    $res = mergeDbEnvAndReqEnv($finalEnvCodes, $req);
    return $res;
}

/**
 *
 * @param $finalEnvCodes
 * {
 *     "a":["1","2","3"],
 *     "b":["4","5","6"]
 * }
 * @param $req
 * @return string
 */
function mergeDbEnvAndReqEnv($finalEnvCodes, $req)
{
    $shell = "";
    foreach ($finalEnvCodes as $env => $codes) {
        $allPtPinEnvCodes = [];
        foreach ($req as $ptPin => $envs) {
            $reqEnvCode = $envs[$env];
            $ptPinMergedCodes = array_diff($codes, [$reqEnvCode]);
            $ptPinMergedCodes = implode("@", $ptPinMergedCodes);
            $allPtPinEnvCodes[] = $ptPinMergedCodes;
        }
        $allPtPinEnvCodes = implode("&", $allPtPinEnvCodes);
        $sh = "export ${env}=\"${allPtPinEnvCodes}\"\r\n";
        $shell = "${shell}${sh}";
    }
    $date=date('Y年m月d日 H:i:s');
    $shell="${shell}export GENERATE_INFO=\"xxoo助力池同步时间===========》 ${date}\"\r\n";
    return $shell;
}

/**
 * 按照传入的顺序根据env合并code
 * {
 *     "a":["1","2","3"],
 *     "b":["4","5","6"]
 * }
 * @param $allEnvNames key=env名,value不重要
 * @param ...$b
 */
function mergeCodesByEnv($allEnvNames, ...$b)
{
    $merges = [];
    foreach ($allEnvNames as $env => $va) {
        $tmpCodes = [];
        foreach ($b as $k => $envCodes) {
            if (isset($envCodes[$env])) {
                $tmpCodes = array_merge($envCodes[$env], $tmpCodes);
            }
        }
        $tmpCodes = array_unique($tmpCodes);
        $merges[$env] = $tmpCodes;
    }
    return $merges;
}

function askFor($askFor)
{
    global $db;
    $askFor = explode("@", $askFor);
    $res = $db->select('share_code', ['ENV', 'CODE'], [
        'PT_PIN' => $askFor
    ]);
    $codes = [];
    foreach ($res as $envCode) {
        $env = $envCode['ENV'];
        $code = $envCode['CODE'];
        if (!isset($codes[$env])) {
            $codes[$env] = [];
        }
        $codes[$env][] = $code;
    }

    return $codes;
}

function foreachReq($req, $call)
{
    foreach ($req as $ptPin => $envs) {
        foreach ($envs as $env => $code) {
            call_user_func($call, array($ptPin, $env, $code));
        }
    }
}

/**
 * 从数据库随机获取对应env下的互助码
 */
function getCodesByEnvFromDB($env)
{
    global $db;
    $argEnv = $db->quote($env);
    $sql = <<<EOF
    select CODE from share_code where env = $argEnv
EOF;
    $res = $db->query($sql . " ORDER BY RANDOM() limit " . ENV_RANDOM_NUM)->fetchAll();
    $codes = [];
    foreach ($res as $r) {
        $codes[] = $r['CODE'];
    }
    return array_unique($codes);
}

function upload($userId, $data)
{
    global $db;
    $envs = explode('@', $data);
    foreach ($envs as $env) {
        $env = explode("=", $env);
        $name = $env[0];
        if ($name == '') {
            continue;
        }
        $vals = explode("&", $env[1]);
        foreach ($vals as $code) {
            if ($code == '') {
                continue;
            }
            $res = $db->insert('share_code', [
                'USER_ID' => $userId,
                'ENV_NAME' => $name,
                'CODE' => $code,
                'CREATE_TIME' => time()
            ]);
            $r[$name][$code] = $res > 0 ? true : false;
        }
    }
    return $r;
}

