<?php

require_once './db.php';
require_once './util.php';


// 用户校验和获取
$user = getUser($_GET[TOKEN_PARAMETER_NAME]);
$userId=$user['ID'];
$limited=$user['LIMITED'];
//用户上传的助力码数量校验
recordLimitCheck($userId,$limited);

//每次被请求都先清理超过2周没有更新的数据
cleanByRul();

$res = "";
switch ($_SERVER['PATH_INFO']) {
    case '/uploadAndGetCodes':
        $res = uploadAndGetCodes($userId, $GLOBALS['HTTP_RAW_POST_DATA']);
        resRaw($res);
        break;
    default :
        res(400, '不盈利，不推广，自用！！，请勿攻击');
        break;
}

function cleanByRul(){
    global $db;
    $ctime=time()-1209600;//2周前
    $count=$db->count('share_code',[
        'CREATE_TIME[<]'=>$ctime
    ]);
    //为什么要先查询再执行update? 因为sqlite执行修改时会锁定文件导致并发下降,但是可以共享读
    if ($count>100) {
        //大于100判断是为了减少进行删除的频率
        $db->delete('share_code',[
            'CREATE_TIME[<]'=>$ctime
        ]);
    }
}

/**
 * 用户助力码数量校验
 * @param $userId
 * @param $limited
 */
function recordLimitCheck($userId,$limited){
    global $db;
    $count=$db->count('share_code',[
        'USER_ID'=>$userId
    ]);

    if ($count>$limited) {
        res(400,'当前用户token上传的助力码数量已超标');
    }
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
                $tmpCodes = array_merge($tmpCodes,$envCodes[$env]);
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


