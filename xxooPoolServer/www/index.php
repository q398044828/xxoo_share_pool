<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/util.php';


$response = ['data' => ''];
try {
    $req = getReq();

    //客户端版本检测
    clientVersionChekc($req->reqClientVersion);
    //一次性拿到所有需要的数据，减少io次数
    getAllNeedByOnce($req);
    // 用户校验和获取
    $user = $req->user;


    $res = "";
    $testData = [];


    $res = uploadAndGetCodes($req);

    slog($response, "  ↓↓↓↓↓↓↓↓↓↓↓↓ 以下为下发的助力码 ↓↓↓↓↓↓↓↓↓↓↓↓");
    resAppend($response, $res);
    resRaw($response['data']);
} catch (Exception $e) {
    var_dump($e);
}

function clientVersionChekc($clientVersion)
{
    global $response;
    if ($clientVersion !== CLIENT_VERSION) {
        slog($response, "================== 更新提示 ============");
        slog($response, "");
        slog($response, "              请更新xxoo.js版本");
        slog($response, "");
        slog($response, "=======================================");
        slog($response, "");
    }
}


/**
 * 用户助力码数量校验
 * @param $userId
 * @param $limited
 */
function recordLimitCheck(Req $req)
{
    $currentNum = $req->user['CURRENT_NUM'];
    $limited = $req->user['LIMITED'];
    $newAdd = 0;
    if ($currentNum == null) {
        $currentNum = 0;
        $command = "refreshCurrentCodeNum " . $req->user['ID'] . " " . $req->user['TOKEN'];
        triggerTask($command, "../async.log");
    }
    foreach ($req as $env => $d) {
        $newAdd = $newAdd + count($d);
    }
    if ($currentNum + $newAdd > $limited) {
        res(400, '当前用户token上传的助力码数量已超标');
    }
}

/**
 * @param $userId
 * @param $data
 */
function uploadAndGetCodes(Req $req)
{

    global $response;
    $userId = $req->user['ID'];
    $data = $req->reqData;

    //根据请求数据的版本判断是否需要更新数据库，如果需要，
    $newVersion = getNewVersionIfNeedUpdate($req);
    if ($newVersion != null) {
        //用户上传的助力码数量校验
        recordLimitCheck($req);
        slog($response, " 更新助力码版本 ${newVersion}");
        uploadjson($userId, $data);
        updateAskFor($req);
        updateUserDataVersion($req->user, $newVersion);
    }

    //获取助力码返回给客户端
    $r = getCodes($req);

    //获取定向信息
    $askForMe = getAskForMe($req);

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

/**
 * 根据传入的请求参数，判断是否有新数据版本号
 * @param $user
 * @param $req
 * @param $askFor
 * @return string|null
 */
function getNewVersionIfNeedUpdate(Req $req)
{

    $res = null;

    if (USE_REDIS) {
        if ($req->areadyRequestDataVersion != null) {
            return null;
        }
    }
    $user = $req->user;
    $reqMd5 = $req->reqMd5;
    $oldMd5 = $user['DATA_VERSION'];

    if ($reqMd5 == $oldMd5) {
        $dbUpdateTime = $user['UPDATED_TIME'];
        if ((time() - $dbUpdateTime) > MAX_NO_UPDATE_DAY) {
            $res = $reqMd5;
        }
    } else {
        $res = $reqMd5;
    }
    if (USE_REDIS) {
        getRedis()->set(getReqDataVersion($reqMd5), true, 7200);
    }
    return $res;
}

/**
 * 一次性拿到所有需要的数据，减少io次数
 */
function getAllNeedByOnce(Req $req)
{
    $reqDataVersionKey = getReqDataVersion($req->reqMd5);
    $userKey = getUserKey($req->reqToken);
    $getAskForMeKey = getAskForMeKey($req->reqMd5);
    $call = [
        $reqDataVersionKey => new Cache($reqDataVersionKey, 7200, false, function () {
            return null;
        }),
        $userKey => new Cache($userKey, 3600, true, function () use ($req) {
            return getUserFromDB($req->reqToken);
        }),
        $getAskForMeKey => new Cache($getAskForMeKey, 3600, true, function () use ($req) {
            return getAskForMeFromDB($req);
        })
    ];

    $resArr = getDataByArr($call);
    $req->user = $resArr[$userKey];
    $req->areadyRequestDataVersion = $resArr[$reqDataVersionKey];
    $req->getAskForMe = $resArr[$getAskForMeKey];

    return $req;
}

/**
 * 获取定向自己的用户
 */
function getAskForMe(Req $req)
{
    return $req->getAskForMe;
}

function getAskForMeFromDB(Req $req)
{
    $ptPins = array_keys($req->reqData);
    if (count($ptPins) > 0) {
        global $db;
        $askForMe = $db->select('user_for', ['PT_PIN', 'ASK_FOR'], ['ASK_FOR' => $ptPins]);
        $res = [];
        foreach ($askForMe as $k => $v) {
            $res[] = sensitive($v['PT_PIN']) . ' 助力=> ' . sensitive($v['ASK_FOR']);
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
function updateAskFor(Req $req)
{

    $userId = $req->user['ID'];
    updateAskForArr($userId, $req->reqData, $req->reqAskFor);
}


function updateAskForArr($userId, $reqData, array $askForArr)
{
    $i = 0;
    foreach ($reqData as $ptPin => $codes) {
        if ($askForArr[$i] != null) {
            updateAskForByPtPin($userId, $ptPin, $askForArr[$i]);
            $i++;
        }
    }
}


function updateAskForByPtPin($userId, $ptPin, $askForPins)
{
    global $db;
    //删除旧的定向
    $res = $db->delete('user_for', [
        'AND' => [
            'USER_ID' => $userId,
            'PT_PIN' => $ptPin
        ]
    ]);
    //保存新定向
    foreach ($askForPins as $askForPin) {
        if ($askForPin != null && $askForPin != '' && $askForPin != 'undefined') {
            $db->insert('user_for', [
                'USER_ID' => $userId,
                'PT_PIN' => $ptPin,
                'ASK_FOR' => $askForPin,
                'CREATE_TIME' => time()
            ]);
        }
    }
}

function uploadjson($userId, $data)
{

    foreach ($data as $ptPin => $envs) {
        foreach ($envs as $env => $code) {
            if (!isIllegal($code)) {
                saveShareCode($userId, $ptPin, $env, $code);
            }
        }
    }
    return $userId;
}

function isIllegal($code)
{
    foreach (ILLEGAL_CHAR as $word) {
        if (strpos($code, $word) !== false) {
            return true;
        }
    }
    return false;
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

    if (USE_REDIS) {
        getRedis()->sAdd(getEnvCodeKey($env), $code);
    }
    return $res;
}


function getCodes(Req $req)
{

    /**
     * 助力码分3大块，
     * 1 $reqCodes 请求里带的助力码，用户多账号自助
     * 2 $askForCodes 定向助力别人的助力码
     * 3 $dbEnvs 数据库随机获取的助力码，用户给别人随机助力
     * 以上三种数据，都需要是以下格式：才方便合并
     * [
     *     "env1":[
     *          ["askFor1Code","askFor2Code","askFor3Code"],[第二个账号的askFor],[第三个账号的askFor]
     *      ]
     * ]
     */

    //请求里的互助码
    $envNames = parseReqCodes($req);
    $reqCodes = $envNames;

    // 数据库的互助码
    $dbEnvs = [];
    foreach ($envNames as $env => $v) {
        $dbEnvCodes = getCodesByEnv($env, count($req->reqData));
        $dbEnvs[$env] = $dbEnvCodes;
    }

    //请求要求的互助码
    $askForCodes = askFor($req, $envNames);

    $finalEnvCodes = mergeCodesByEnv($reqCodes, $askForCodes, $dbEnvs);

    $res = mergeDbEnvAndReqEnv($req, $finalEnvCodes);
    return $res;
}

function parseReqCodes(Req $req)
{

    $res = [];
    if ($_GET['closeSelf'] !== true) {
        $envNames = [];
        foreach ($req->reqData as $ptPin => $envs) {
            foreach ($envs as $env => $code) {
                if (!isset($envNames[$env])) {
                    $envNames[$env] = [];
                }
                if (isIllegal($code)) {
                    continue;
                }
                $envNames[$env][] = $code;
            }
        }

        $ptPinNum = count($req->reqData);
        foreach ($envNames as $env => $codes) {
            if ($res[$env] == null) {
                $res[$env] = [];
            }
            for ($i = 0; $i < $ptPinNum; $i++) {
                $res[$env][] = $codes;
            }
        }
    }
    return $res;
}

/**
 *
 * @param $finalEnvCodes
 * [
 *     "env1":[
 *          ["askFor1Code","askFor2Code","askFor3Code"],[第二个账号的askFor],[第三个账号的askFor]
 *      ]
 * ]
 * @param $req
 * @return string
 */
function mergeDbEnvAndReqEnv(Req $req, $finalEnvCodes)
{

    $reqPtPins = array_keys($req->reqData);
    $shell = "";
    foreach ($finalEnvCodes as $env => $accounts) {
        $sh = [];
        foreach ($accounts as $i => $ptpinFors) {
            if (count($ptpinFors) > 0) {
                $ptpinFors = array_diff($ptpinFors, [$req->reqData[$reqPtPins[$i]][$env], '']);
                $ptpinFors = array_unique($ptpinFors);
                $sh[] = implode("@", $ptpinFors);
            }
        }
        if (count($sh) > 0) {
            $shell = "${shell}export ${env}=\"" . implode("&", $sh) . "\"\r\n";
        }
    }
    $date = date('Y年m月d日 H:i:s');
    $shell = "${shell}export GENERATE_INFO=\"xxoo助力池同步时间: ${date}\"\r\n";
    return $shell;
}

/**
 * 按照传入的顺序根据env合并code
 *
 * b数组参数的每个元素数据结构：
 * [
 *     "env1":[
 *          ["code1","code2","code3"],[第二个账号要助力的码],[第三个账号要助力的码]
 *      ],
 *      "env2":[ [],[],[] ]
 * ]
 * @param ...$b
 */
function mergeCodesByEnv(...$b)
{
    $merges = [];
    foreach ($b as $el) {
        foreach ($el as $env => $mulitAccount) {
            foreach ($mulitAccount as $i => $ptPinFors) {
                tool3DarrayAppend($env, $i, $ptPinFors, $merges);
            }
        }
    }
    return $merges;
}

/**
 * 三位数组 追加数组值
 * @param $env
 * @param $ptPinLocation
 * @param array $appendArr 要追加的
 * @param array $arr
 */
function tool3DarrayAppend($env, $ptPinLocation, array $appendArr, array &$arr)
{
    if (!isset($arr[$env])) {
        $arr[$env] = [];
    }
    if (!isset($arr[$env][$ptPinLocation])) {
        $arr[$env][$ptPinLocation] = [];
    }
    $old = $arr[$env][$ptPinLocation];
    $arr[$env][$ptPinLocation] = array_merge($old, $appendArr);
}

/**
 * 定向助力
 * @param $askFor
 * @return array
 * [
 *     "env1":[
 *          ["askFor1Code","askFor2Code","askFor3Code"],[第二个账号的askFor],[第三个账号的askFor]
 *      ]
 * ]
 */
function askFor(Req $req, array $allEnvName)
{

    $ptPins = getAskForPtPins($req->reqAskFor);
    $codes = getAskForCodes($req, $ptPins);

    return askForDataConvert($req, $allEnvName, $codes);
}

/**
 * @param Req $req
 * @param $askPtPins
 * ["ptpin1","ptpin2",...]
 * @return 无序数据
 * [
 *      "${pt_pin}__${env}":[
 *            'PT_PIN'=>'ptpin1',
 *            'CODE'=>'code1',
 *            'ENV'=>'env1'
 *      ]
 * ]
 */
function getAskForCodes(Req $req, $askPtPins)
{
    global $db;
    $dbCodes = [];
    $cacheCodes = [];
    $needFromDb = [];
    if (USE_REDIS) {
        $cacheRes = getRedis()->mget(getPtPinCoeKeyArr($askPtPins));
        $size = count($askPtPins);
        for ($i = 0; $i < $size; $i++) {
            $v = $cacheRes[$i];
            if ($v != false) {
                $v = json_decode($v, true);
                $v['PT_PIN'] = $askPtPins[$i];
                $cacheCodes[] = $v;
            } else {
                $needFromDb[] = $askPtPins[$i];
            }
        }
    }
    if (count($needFromDb) > 0) {
        $dbCodes = $db->select('share_code', ['ENV', 'CODE', 'PT_PIN'], [
            'PT_PIN' => $needFromDb
        ]);
    }
    $res = array_merge($dbCodes, $cacheCodes);

    $codes = [];
    foreach ($res as $i => $v) {
        $codes["${v['PT_PIN']}__${v['ENV']}"] = $v['CODE'];
    }

    return $codes;
}

function getAskForPtPins(array $reqAskFor)
{
    $res = [];
    if ($reqAskFor != null) {
        foreach ($reqAskFor as $i => $ptPins) {
            $res = array_merge($res, $ptPins);
        }
    }
    return $res;
}


/**
 * 转换数据格式，并根据请求的askFor排序
 * @param codes
 * [
 *      "${pt_pin}__${env}":[
 *            'PT_PIN'=>'ptpin1',
 *            'CODE'=>'code1',
 *            'ENV'=>'env1'
 *      ]
 * ]
 * 转成：
 * [
 *     "env1":[
 *          ["askFor1Code","askFor2Code","askFor3Code"],[第二个账号的askFor],[第三个账号的askFor]
 *      ]
 * ]
 */
function askForDataConvert(Req $req, $allEnvName, array $codes)
{

    $res = [];
    if (count($req->reqAskFor) < 1) {
        return $res;
    }

    foreach ($allEnvName as $env => $v) {
        $i = 0;
        foreach ($req->reqData as $ptPin => $v2) {
            $askOrder = $req->reqAskFor[$i];
            $accountAskCodes = [];
            foreach ($askOrder as $askPtPin) {
                $accountAskCodes[] = $codes["${askPtPin}__${env}"];

            }
            tool3DarrayAppend($env, $i, $accountAskCodes, $res);
            $i++;
        }
    }

    return $res;
}


/**
 * 从数据库/缓存 随机获取对应env下的互助码
 * @param $ptPinNum 账户个数，用于动态判断随机获取多少个助力码
 */
function getCodesByEnv($env, $ptPinNum)
{

    $codes = [];
    $canHelpNum = DEFAULT_GET_CODE_NUM * $ptPinNum;
    $canHelpNum = $canHelpNum > 50 ? 50 : $canHelpNum;//部分人可能会使用大量账户，获取太多助力码影响性能，所以，限制下
    if (USE_REDIS) {
        $res = getRedis()->sRandMember(getEnvCodeKey($env), $canHelpNum);
        foreach ($res as $k => $code) {
            $codes[] = $code;
        }
    } else {
        global $db;
        $argEnv = $db->quote($env);
        $sql = <<<EOF
    select CODE from share_code where env = $argEnv
EOF;
        $randFunc = DB_TYPE == 'mysql' ? "RAND()" : "RANDOM()";
        $res = $db->query($sql . " ORDER BY ${randFunc} limit " . $canHelpNum)->fetchAll();
        foreach ($res as $r) {
            $codes[] = $r['CODE'];
        }
    }
    triggerRefershEnvCodeCache($env);
    $codes = array_unique($codes);
    return array_chunk($codes, $canHelpNum / $ptPinNum);
}

/**
 * 触发输入根据环境变量刷入助力码到缓存中
 * @param $env
 */
function triggerRefershEnvCodeCache($env)
{
    $needUpdateCodeCaches = getRedis()->get("envCodeNeedUpdate:${env}");
    if ($needUpdateCodeCaches == null) {
        triggerTask("refresh ${env}", "../refresh.log");
        getRedis()->set("envCodeNeedUpdate:${env}", true, 7200 + rand(0, 30) * 120);
    }
}

function triggerTask($command, $log)
{
    pclose(popen("php ../data.php ${command} >> ../${log} 2>&1 &", 'r'));
}

//-------------------------------------------------- user 操作相关 ----------------------------------------
function getUserFromDB($token)
{

    global $db;
    $user = $db->select('user', ['ID', 'LIMITED', 'TOKEN', 'DATA_VERSION', 'UPDATED_TIME', 'CURRENT_NUM'], [
        'TOKEN' => $token
    ]);
    if (count($user) < 1) {
        res(400, 'token不存在');
    }
    return $user[0];

}


function updateUserDataVersion($user, $dataVersion)
{
    updData("user:${user['TOKEN']}", function () use ($user, $dataVersion) {
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
    });
}

/**
 * 解析请求参数
 * @return Req
 */
function getReq()
{

    $req = new Req(
        $_GET[TOKEN_PARAMETER_NAME],
        file_get_contents("php://input"),
        $_GET['clientVersion']
    );

    //定向助力解析
    $askFor = $_GET['askFor'];
    $askForMulit = explode(";", $askFor);
    if (count($askForMulit) > 1) {
        $askForMulitArr = [];
        foreach ($askForMulit as $k => $askForArr) {
            $askForMulitArr[] = explode("@", $askForArr);
        }
        $req->reqAskFor = $askForMulitArr;
    } else {
        //旧的定向助力转新定向助力
        $askForPins = explode("@", $askFor);
        $oldReqAskFor = [];
        foreach ($req->reqData as $reqPtPin => $v) {
            $oldReqAskFor[] = $askForPins;
        }
        $req->reqAskFor = $oldReqAskFor;
    }
    $req->reqAskForRaw = $askFor;

    //请求参数的md5值
    $reqMd5 = md5($req->reqBody . $req->reqAskForRaw);
    $req->reqMd5 = $reqMd5;
    return $req;
}


class Req
{

    /**
     * 不同账号定向助力不同的用户,根据索引和reqData的索引对应
     * @var null
     * [
     *      ["ptpin11","ptpin2"],
     *      ["ptpin33","ptpin44"]
     * ]
     */
    public $reqAskFor = null;

    /**
     * @var null
     * [
     *     "ptpin1":[
     *          "env1"=>"xxx",
     *          "env2"=>"xxx"
     *      ],
     *      "ptpin2":[
     *          "env1"=>"xxx",
     *          "env2"=>"xxx"
     *      ]
     * ]
     */
    public $reqData = null;
    /**
     * @var null 普通字符串
     */
    public $reqToken = null;
    public $reqClientVersion = null;

    /**
     * @var null 请求体里的原始数据
     */
    public $reqBody = null;
    /**
     *
     */
    public $reqAskForRaw = null;

    /**
     * @var null 用户表对象
     */
    public $user = null;

    /**
     * @var null 根据请求参数生成的md5值，如果客户端请求的此参数一直没变，那么定向数据
     */
    public $reqMd5 = null;

    public $areadyRequestDataVersion = null;

    public $getAskForMe = null;


    function __construct($reqToken, $reqData, $clientVersion)
    {
        $this->reqToken = $reqToken;
        $this->reqData = json_decode($reqData, true);
        $this->reqClientVersion = $clientVersion;
        $this->reqBody = $reqData;
    }
}

class Cache
{
    public $key;
    public $timeout;
    public $call;
    public $isArray;

    function __construct($key, $timeout, $isArray, $call)
    {
        $this->key = $key;
        $this->timeout = $timeout;
        $this->call = $call;
        $this->isArray = $isArray;
    }
}