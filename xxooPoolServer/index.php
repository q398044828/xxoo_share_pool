<?php

require_once './config.php';
require_once './db.php';
require_once './util.php';

$response = ['data' => ''];


//客户端版本检测
clientVersionChekc($_GET['clientVersion']);

// 用户校验和获取
$user = getUser($_GET[TOKEN_PARAMETER_NAME]);
$userId = $user['ID'];
$limited = $user['LIMITED'];
//用户上传的助力码数量校验
recordLimitCheck($userId, $limited);

//每次被请求都先清理超过2周没有更新的数据
cleanByRule();


$res = "";
$testData = [];
switch ($_SERVER['PATH_INFO']) {
    case '/uploadAndGetCodes':
        $res = uploadAndGetCodes($user, $GLOBALS['HTTP_RAW_POST_DATA'], $_GET['askFor']);
        slog($response, "  ↓↓↓↓↓↓↓↓↓↓↓↓ 以下为下发的助力码 ↓↓↓↓↓↓↓↓↓↓↓↓");
        resAppend($response, $res);
        break;
    default :
        res(400, '不盈利，不推广，自用！！，请勿攻击');
        break;
}
resRaw($response['data']);

function clientVersionChekc($clientVersion)
{
    global $response;
    if ($clientVersion !== CLIENT_VERSION) {
        slog($response, "========== 更新提示 ============");
        slog($response, "");
        slog($response, "       请更新xxoo.js版本");
        slog($response, "");
        slog($response, "===============================");
        slog($response, "");
    }
}

function cleanByRule()
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

/**
 * 用户助力码数量校验
 * @param $userId
 * @param $limited
 */
function recordLimitCheck($userId, $limited)
{
    global $db;
    $count = $db->count('share_code', [
        'USER_ID' => $userId
    ]);

    if ($count > $limited) {
        res(400, '当前用户token上传的助力码数量已超标');
    }
}

/**
 * @param $userId
 * @param $data
 */
function uploadAndGetCodes($user, $req, $askFor)
{
    global $response;
    $userId = $user['ID'];
    $data = json_decode($req, true);

    //根据请求数据的版本判断是否需要更新数据库，如果需要，
    $newVersion = getNewVersionIfNeedUpdate($user, $req, $askFor);
    if ($newVersion != null) {
        slog($response, " 更新助力码版本 ${newVersion}");
        uploadjson($userId, $data);
        updateAskFor($userId, $data, $askFor);
        updateUserDataVersion($user, $newVersion);
    }

    //获取助力码返回给客户端
    $r = getCodes($userId, $data, $askFor);

    //获取定向信息
    $askForMe = getAskForMe($userId, $data);
    slog($response, "");
    if (count($askForMe) > 0) {
        slog($response, " ================== 定向您的用户 ============");
        slog($response, "");
        foreach ($askForMe as $me) {
            slog($response, "   $me");
        }
    } else {
        slog($response, " ==================== 定向您的用户 ============");
        slog($response, "");
        slog($response, "       当前没有用户定向你，快去邀请几个朋友定向你！！！");
    }
    slog($response, "");
    return $r;
}

function getNewVersionIfNeedUpdate($user, $req, $askFor)
{
    $reqMd5 = md5($req . $askFor);
    $oldMd5 = $user['DATA_VERSION'];
    $res = null;
    if ($reqMd5 == $oldMd5) {
        $dbUpdateTime = $user['UPDATED_TIME'];
        if ((time() - $dbUpdateTime) > MAX_NO_UPDATE_DAY) {
            $res = $reqMd5;
        }
    } else {
        $res = $reqMd5;
    }
    return $res;
}

function updateUserDataVersion($user, $dataVersion)
{
    global $db, $response;
    $res = $db->update('user',
        [
            'DATA_VERSION' => $dataVersion,
            'UPDATED_TIME' => time()
        ],
        [
            'ID' => $user['ID']
        ]);
    slog($response, " 数据更新状态：" . json_encode($res));
}

/**
 * 获取定向自己的用户
 */
function getAskForMe($userId, $reqData)
{
    $ptPins = array_keys($reqData);
    if (count($ptPins) > 0) {
        global $db;
        $askForMe = $db->select('user_for', ['PT_PIN', 'ASK_FOR'], ['ASK_FOR' => $ptPins]);
        $res = [];
        foreach ($askForMe as $k => $v) {
            $res[] = $v['PT_PIN'] . ' 助力=> ' . $v['ASK_FOR'];
        }
        return $res;
    }
    return null;
}

/**
 * 保存定向助力
 * @param $userId
 * @param $reqData
 * @param $askFor
 */
function updateAskFor($userId, $reqData, $askFor)
{
    global $db, $response;
    $askForPins = explode("@", $askFor);
    foreach ($reqData as $ptPin => $codes) {
        //删除旧的定向
        $res = $db->delete('user_for', [
            'AND' => [
                'USER_ID' => $userId,
                'PT_PIN' => $ptPin
            ]
        ]);
        //保存新定向
        foreach ($askForPins as $askForPin) {
            if ($askForPin != null || $askForPin != '') {
                $db->insert('user_for', [
                    'USER_ID' => $userId,
                    'PT_PIN' => $ptPin,
                    'ASK_FOR' => $askForPin,
                    'CREATE_TIME' => time()
                ]);
            }
        }
    }
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
    if ($code == '' || $code == null) {
        return false;
    }
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
        'USER_ID' => $userId
    ]);
    if ($n < 1) {
        $res = $db->insert('share_code', $data);
        $res = $res > 0 ? true : false;
    } else {
        $res = true;
    }
    return $res;
}


function getCodes($userId, $req, $askFor)
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
    $askForCodes = askFor($askFor);

    $finalEnvCodes = mergeCodesByEnv($envNames, $envNames, $askForCodes, $dbEnvs);

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
            $ptPinMergedCodes = array_filter($ptPinMergedCodes);
            $ptPinMergedCodes = implode("@", $ptPinMergedCodes);
            $allPtPinEnvCodes[] = $ptPinMergedCodes;
        }
        $allPtPinEnvCodes = implode("&", $allPtPinEnvCodes);
        $sh = "export ${env}=\"${allPtPinEnvCodes}\"\r\n";
        $shell = "${shell}${sh}";
    }
    $date = date('Y年m月d日 H:i:s');
    $shell = "${shell}export GENERATE_INFO=\"xxoo助力池同步时间===========》 ${date}\"\r\n";
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
                $tmpCodes = arrayPushArray($tmpCodes, $envCodes[$env]);
            }
        }
        $tmpCodes = array_unique($tmpCodes);
        $merges[$env] = $tmpCodes;
    }

    return $merges;
}

function askFor($askFor)
{
    global $db, $response;
    $askFor = explode("@", $askFor);
    $res = $db->select('share_code', ['ENV', 'CODE', 'PT_PIN'], [
        'PT_PIN' => $askFor
    ]);


    /**
     * 转成格式且根据传入的askFor顺序进行code的排序
     * [
     *     'env1':['1','2'],
     *     'env2':['a','b']
     * ]
     */
    $codes = [];
    $askForOrder = array_flip($askFor);
    foreach ($res as $envCode) {
        $env = $envCode['ENV'];
        $code = $envCode['CODE'];
        if (!isset($codes[$env])) {
            $codes[$env] = [];
        }
        $order = $askForOrder[$envCode['PT_PIN']];
        $codes[$env][$order] = $code;
    }
    foreach ($codes as $env => &$envCodes) {
        ksort($envCodes);
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
    //HELP_NUM[$env][1] + 2;//+2是为了防止随机获取到用户自己的导致不够
    $canHelpNum = DEFAULT_GET_CODE_NUM;//为什么还是改成大于可助力次数的值？因为有可能下发的已经被助力过了
    $res = $db->query($sql . " ORDER BY RANDOM() limit " . $canHelpNum)->fetchAll();
    $codes = [];
    foreach ($res as $r) {
        $codes[] = $r['CODE'];
    }
    return array_unique($codes);
}

function getUser($token)
{
    global $db;
    $user = $db->select('user', ['ID', 'LIMITED', 'DATA_VERSION', 'UPDATED_TIME'], [
        'TOKEN' => $token
    ]);
    if (count($user) < 1) {
        res(400, 'token不存在');
    }
    return $user[0];
}

