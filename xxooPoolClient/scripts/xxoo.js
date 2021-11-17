/*
 */
var version='1.0.0';
var fs = require("fs");
console.log(":<<EOF");
const $ = new Env("获取互助码+参与xxoo互助池");
let cookiesArr = [], cookie = '', message;
const jdCookieNode = $.isNode() ? jdCookies() : '';
var shareCodeSources=[];
!function(n){"use strict";function r(n,r){var t=(65535&n)+(65535&r);return(n>>16)+(r>>16)+(t>>16)<<16|65535&t}function t(n,r){return n<<r|n>>>32-r}function u(n,u,e,o,c,f){return r(t(r(r(u,n),r(o,f)),c),e)}function e(n,r,t,e,o,c,f){return u(r&t|~r&e,n,r,o,c,f)}function o(n,r,t,e,o,c,f){return u(r&e|t&~e,n,r,o,c,f)}function c(n,r,t,e,o,c,f){return u(r^t^e,n,r,o,c,f)}function f(n,r,t,e,o,c,f){return u(t^(r|~e),n,r,o,c,f)}function i(n,t){n[t>>5]|=128<<t%32,n[14+(t+64>>>9<<4)]=t;var u,i,a,h,g,l=1732584193,d=-271733879,v=-1732584194,C=271733878;for(u=0;u<n.length;u+=16)i=l,a=d,h=v,g=C,d=f(d=f(d=f(d=f(d=c(d=c(d=c(d=c(d=o(d=o(d=o(d=o(d=e(d=e(d=e(d=e(d,v=e(v,C=e(C,l=e(l,d,v,C,n[u],7,-680876936),d,v,n[u+1],12,-389564586),l,d,n[u+2],17,606105819),C,l,n[u+3],22,-1044525330),v=e(v,C=e(C,l=e(l,d,v,C,n[u+4],7,-176418897),d,v,n[u+5],12,1200080426),l,d,n[u+6],17,-1473231341),C,l,n[u+7],22,-45705983),v=e(v,C=e(C,l=e(l,d,v,C,n[u+8],7,1770035416),d,v,n[u+9],12,-1958414417),l,d,n[u+10],17,-42063),C,l,n[u+11],22,-1990404162),v=e(v,C=e(C,l=e(l,d,v,C,n[u+12],7,1804603682),d,v,n[u+13],12,-40341101),l,d,n[u+14],17,-1502002290),C,l,n[u+15],22,1236535329),v=o(v,C=o(C,l=o(l,d,v,C,n[u+1],5,-165796510),d,v,n[u+6],9,-1069501632),l,d,n[u+11],14,643717713),C,l,n[u],20,-373897302),v=o(v,C=o(C,l=o(l,d,v,C,n[u+5],5,-701558691),d,v,n[u+10],9,38016083),l,d,n[u+15],14,-660478335),C,l,n[u+4],20,-405537848),v=o(v,C=o(C,l=o(l,d,v,C,n[u+9],5,568446438),d,v,n[u+14],9,-1019803690),l,d,n[u+3],14,-187363961),C,l,n[u+8],20,1163531501),v=o(v,C=o(C,l=o(l,d,v,C,n[u+13],5,-1444681467),d,v,n[u+2],9,-51403784),l,d,n[u+7],14,1735328473),C,l,n[u+12],20,-1926607734),v=c(v,C=c(C,l=c(l,d,v,C,n[u+5],4,-378558),d,v,n[u+8],11,-2022574463),l,d,n[u+11],16,1839030562),C,l,n[u+14],23,-35309556),v=c(v,C=c(C,l=c(l,d,v,C,n[u+1],4,-1530992060),d,v,n[u+4],11,1272893353),l,d,n[u+7],16,-155497632),C,l,n[u+10],23,-1094730640),v=c(v,C=c(C,l=c(l,d,v,C,n[u+13],4,681279174),d,v,n[u],11,-358537222),l,d,n[u+3],16,-722521979),C,l,n[u+6],23,76029189),v=c(v,C=c(C,l=c(l,d,v,C,n[u+9],4,-640364487),d,v,n[u+12],11,-421815835),l,d,n[u+15],16,530742520),C,l,n[u+2],23,-995338651),v=f(v,C=f(C,l=f(l,d,v,C,n[u],6,-198630844),d,v,n[u+7],10,1126891415),l,d,n[u+14],15,-1416354905),C,l,n[u+5],21,-57434055),v=f(v,C=f(C,l=f(l,d,v,C,n[u+12],6,1700485571),d,v,n[u+3],10,-1894986606),l,d,n[u+10],15,-1051523),C,l,n[u+1],21,-2054922799),v=f(v,C=f(C,l=f(l,d,v,C,n[u+8],6,1873313359),d,v,n[u+15],10,-30611744),l,d,n[u+6],15,-1560198380),C,l,n[u+13],21,1309151649),v=f(v,C=f(C,l=f(l,d,v,C,n[u+4],6,-145523070),d,v,n[u+11],10,-1120210379),l,d,n[u+2],15,718787259),C,l,n[u+9],21,-343485551),l=r(l,i),d=r(d,a),v=r(v,h),C=r(C,g);return[l,d,v,C]}function a(n){var r,t="",u=32*n.length;for(r=0;r<u;r+=8)t+=String.fromCharCode(n[r>>5]>>>r%32&255);return t}function h(n){var r,t=[];for(t[(n.length>>2)-1]=void 0,r=0;r<t.length;r+=1)t[r]=0;var u=8*n.length;for(r=0;r<u;r+=8)t[r>>5]|=(255&n.charCodeAt(r/8))<<r%32;return t}function g(n){return a(i(h(n),8*n.length))}function l(n,r){var t,u,e=h(n),o=[],c=[];for(o[15]=c[15]=void 0,e.length>16&&(e=i(e,8*n.length)),t=0;t<16;t+=1)o[t]=909522486^e[t],c[t]=1549556828^e[t];return u=i(o.concat(h(r)),512+8*r.length),a(i(c.concat(u),640))}function d(n){var r,t,u="";for(t=0;t<n.length;t+=1)r=n.charCodeAt(t),u+="0123456789abcdef".charAt(r>>>4&15)+"0123456789abcdef".charAt(15&r);return u}function v(n){return unescape(encodeURIComponent(n))}function C(n){return g(v(n))}function A(n){return d(C(n))}function m(n,r){return l(v(n),v(r))}function s(n,r){return d(m(n,r))}function b(n,r,t){return r?t?m(r,n):s(r,n):t?C(n):A(n)}$.md5=b}();
if ($.isNode()) {
    Object.keys(jdCookieNode).forEach((item) => {
        cookiesArr.push(jdCookieNode[item])
    })
} else {
    cookiesArr = [$.getdata('CookieJD'), $.getdata('CookieJD2'), ...jsonParse($.getdata('CookiesJD') || "[]").map(item => item.cookie)].filter(item => !!item);
}
var ptPins = [];
for (let i = 0; i < cookiesArr.length; i++) {
    ptPins[i] = null;
    if (cookiesArr[i]) {
        cookie = cookiesArr[i];
        var ptPin = decodeURIComponent(cookie.match(/pt_pin=([^; ]+)(?=;?)/) && cookie.match(/pt_pin=([^; ]+)(?=;?)/)[1])
        ptPins[i] = ptPin;
    }
}

//jd_get_share_code.js脚本的命名映射
var envsGetShareCodeJs = {
    "京东农场": "FRUITSHARECODES",
    "京东萌宠": "PETSHARECODES",
    "种豆得豆": "PLANT_BEAN_SHARECODES",
    "东东工厂": "DDFACTORY_SHARECODES",
    "京喜农场": "JXNC_SHARECODES",
    "闪购盲盒": "JDSGMH_SHARECODES",
    "签到领现金": "JD_CASH_SHARECODES",
    "京喜工厂": "DREAM_FACTORY_SHARE_CODES"
};

//code.sh脚本的命名映射
var envsCodeSh = {
    'MyFruit': 'FRUITSHARECODES',
    'MyPet': 'PETSHARECODES',
    'MyBean': 'PLANT_BEAN_SHARECODES',
    'MyDreamFactory': 'DREAM_FACTORY_SHARE_CODES',
    'MyJdFactory': 'DDFACTORY_SHARECODES',
    'MyJdzz': 'JDZZ_SHARECODES',
    'MyJoy': 'JDJOY_SHARECODES',
    'MyJxnc': 'JXNC_SHARECODES',
    'MyBookShop': 'BOOKSHOP_SHARECODES',
    'MyCash': 'JD_CASH_SHARECODES',
    'MySgmh': 'JDSGMH_SHARECODES',
    'MyCfd': 'JDCFD_SHARECODES',
    'MyHealth': 'JDHEALTH_SHARECODES'
};

/**
 * 读取互助码
 * @returns {null}
 */

// 从 jd_get_share_code脚本日志中获取助力码
addSource("jd_get_share_code.js", function () {
    var getShareCodeRes = null;
    if (process.env.XXOO_READ_SHARE_CODE) {
        getShareCodeRes = getShareCodeFrom_get_share_code_js_log_ByDir(
            process.env.XXOO_READ_SHARE_CODE);
    } else {
        getShareCodeRes = getShareCodeFrom_get_share_code_js_log_ByAutoJudge();
    }
    return getShareCodeRes;
});

// 从 code.sh 脚本日志中获取助力码
addSource("code.sh", function () {
    return getShareCodeFrom_code();
});

var codes = readFromConfigSources();
console.log('------ 整合后的助力码------------');
console.log(codes);
uploadAndGetShareCodes(codes);


/**
 * 上传互助码并拉取互助池中的互助码
 */
function uploadAndGetShareCodes(data) {
    if (process.env.XXOO_HOST && process.env.XXOO_TOKEN) {
        var host = process.env.XXOO_HOST;
        var token = process.env.XXOO_TOKEN;
        var askPtPin = process.env.XXOO_FOR;
        var ops = {
            'url': `${host}/uploadAndGetCodes?token=${token}&askFor=${askPtPin}&clientVersion=${version}`,
            'headers': {
                "Content-Type": "application/json",
            },
            'body': JSON.stringify(data)
        }
        $.post(ops, async (err, resp, data) => {
                console.log("EOF");
                console.log("##====================  xxoo池响应   ======")
                console.log('%s', data);
            }
        );
    }
}

function addSource(desc, func) {
    shareCodeSources.push({'desc': desc, 'func': func});
}

function readFromConfigSources() {
    var res = [];
    for (let k in shareCodeSources) {
        var source = shareCodeSources[k];
        var func = source['func'];
        var r = func();
        source['response'] = r;
        res.push(r);
    }
    var mergedCodes = {};
    console.log("========= 获取助力码来源 =========");
    for (const k in shareCodeSources) {
        var source = shareCodeSources[k];
        console.log(`         ${source['desc']}`);
        mergedCodes = mergeCodes(mergedCodes, source['response']);
    }
    console.log("                          ");
    return mergedCodes;
}

function mergeCodes(source1, source2) {
    var res = {};
    for (let ptPin of ptPins) {
        var a = source1[ptPin];
        var b = source2[ptPin];
        var mergedCodes = {};
        mergedCodes = Object.assign(a == undefined ? {} : a, mergedCodes);
        mergedCodes = Object.assign(b == undefined ? {} : b, mergedCodes,);
        res[ptPin] = mergedCodes;
    }
    return res;
}

/**
 * 从code.sh脚本日志中获取助力码
 */
function getShareCodeFrom_code() {
    var data = getLastFileDataFromDir('code');
    if (data != null) {
        res={};
        for (let key in envsCodeSh) {
            var env=envsCodeSh[key];
            for (let i = 0; i < ptPins.length; i++) {
                var pt_pin=ptPins[i];
                var code=readFrom(`${key}${i+1}\=\'`,"\'\n",data,0);
                if (code.start < 0) {
                    continue;
                }
                if (!res[pt_pin]) {
                    res[pt_pin] = {};
                }
                res[pt_pin][env] = code.str;
            }
        }
        return res;
    }
    return {};
}


function getShareCodeFrom_get_share_code_js_log_ByAutoJudge() {
    var pathName = `${process.env.QL_DIR}/log`;
    var dirs = fs.readdirSync(pathName);
    for (let i = 0; i < dirs.length; i++) {
        if (dirs[i].endsWith("get_share_code")) {
            console.log(`=>自动判断 jd_get_share_code 日志目录
                     ${dirs[i]}\r\n`);
            return getShareCodeFrom_get_share_code_js_log_ByDir(dirs[i]);
        }
    }
    return {};
}

function getShareCodeFrom_get_share_code_js_log_ByDir(dir) {
    var data = getLastFileDataFromDir(dir);
    if (data == null) {
        return {};
    }
    return parseFrom_get_share_code_js_log(data);
}

/**
 * 获取给定目录里的最新文件内容
 * @param dir
 * @returns {null|*}
 */
function getLastFileDataFromDir(dir) {
    var pathName = `${process.env.QL_DIR}/log/${dir}`;
    if (!fs.existsSync(pathName)) {
        return null;
    }
    var files = fs.readdirSync(pathName);
    if (files.length > 0) {
        var lastLog = files[files.length - 1];
        var data = fs.readFileSync(`${pathName}/${lastLog}`, "utf8");
        return data;
    }
    return null;
}

function parseFrom_get_share_code_js_log(data) {


    var res = {};

    var i = 0;
    var idx = 0;
    /**
     * 看起来很low的解析方式对不对？
     * 对，但是我菜啊，正则不太会用！！！
     */
    do {
        try {
            i++;
            var line = null;
            try {
                line = readFrom("【", "\n", data, idx);
            } catch (b) {
                break;
            }
            idx = line.end + 1;
            var code = readAfter("】", line.str, 0).str;
            var pt_pin = readFrom("（", "）", line.str, 0).str;
            var name = readFrom("）", "】", line.str).str;
            var env = envsGetShareCodeJs[name];
            if (!checkChinese(code) && pt_pin != '') {
                if (!res[pt_pin]) {
                    res[pt_pin] = {};
                }
                res[pt_pin][env] = code;
            }
        } catch (e) {
            $.log(e);
        }
    } while (i < 99999);
    return res;
}

/**
 * cookie解析
 */
function jdCookies() {
    var CookieJDs = [];
    if (process.env.JD_COOKIE) {
        if (process.env.JD_COOKIE.indexOf('&') > -1) {
            CookieJDs = process.env.JD_COOKIE.split('&');
        } else if (process.env.JD_COOKIE.indexOf('\n') > -1) {
            CookieJDs = process.env.JD_COOKIE.split('\n');
        } else {
            CookieJDs = [process.env.JD_COOKIE];
        }
    }
    var res = {};
    CookieJDs = [...new Set(CookieJDs.filter(item => !!item))];
    console.log(`\n=========共${CookieJDs.length}个京东账号Cookie=========\n`);
    for (let i = 0; i < CookieJDs.length; i++) {
        if (!CookieJDs[i].match(/pt_pin=(.+?);/)
            || !CookieJDs[i].match(/pt_key=(.+?);/)) {
            console.log(
                `\n提示: cookie 【${CookieJDs[i]}】填写不规范,正确格式: pt_key=xxx;pt_pin=xxx;（分号;不可少）\n`);
        }
        const index = (i + 1 === 1) ? '' : (i + 1);
        res['CookieJD' + index] = CookieJDs[i].trim();
    }
    return res;
}

/**
 * 从给定的str中截取start和end之间的字符串
 * 不包括start和end自己
 * @param startIdx: 从哪个位置开始查找
 */
function readFrom(start, end, str, startIdx) {
    var s = str.indexOf(start, startIdx);
    var e = str.indexOf(end, s+start.length);
    var o = {
        str: str.substring(s + start.length, e),
        start: s,
        end: e
    }
    if (s < 0 || e < 0|| s === e) {
        o.str = "";
        o.start = -1;
        o.end = -1;
    }
    return o;
}

/**
 *  从给定的str中截取start到最后的字符串，不包括start
 *  @param startIdx: 从哪个位置开始查找
 */
function readAfter(start, str, startIdx) {
    var s = str.indexOf(start, startIdx);
    var o = {
        str: str.substring(s + 1, str.length),
        start: s
    }

    return o;
}

/**
 * 是否包含中文
 * @param val
 * @returns {boolean}
 */
function checkChinese(val) {
    var reg = new RegExp("[\\u4E00-\\u9FFF]+", "g");
    return reg.test(val);
}

// prettier-ignore
function Env(t,e){"undefined"!=typeof process&&JSON.stringify(process.env).indexOf("GITHUB")>-1&&process.exit(0);class s{constructor(t){this.env=t}send(t,e="GET"){t="string"==typeof t?{url:t}:t;let s=this.get;return"POST"===e&&(s=this.post),new Promise((e,i)=>{s.call(this,t,(t,s,r)=>{t?i(t):e(s)})})}get(t){return this.send.call(this.env,t)}post(t){return this.send.call(this.env,t,"POST")}}return new class{constructor(t,e){this.name=t,this.http=new s(this),this.data=null,this.dataFile="box.dat",this.logs=[],this.isMute=!1,this.isNeedRewrite=!1,this.logSeparator="\n",this.startTime=(new Date).getTime(),Object.assign(this,e),this.log("",`🔔${this.name}, 开始!`)}isNode(){return"undefined"!=typeof module&&!!module.exports}isQuanX(){return"undefined"!=typeof $task}isSurge(){return"undefined"!=typeof $httpClient&&"undefined"==typeof $loon}isLoon(){return"undefined"!=typeof $loon}toObj(t,e=null){try{return JSON.parse(t)}catch{return e}}toStr(t,e=null){try{return JSON.stringify(t)}catch{return e}}getjson(t,e){let s=e;const i=this.getdata(t);if(i)try{s=JSON.parse(this.getdata(t))}catch{}return s}setjson(t,e){try{return this.setdata(JSON.stringify(t),e)}catch{return!1}}getScript(t){return new Promise(e=>{this.get({url:t},(t,s,i)=>e(i))})}runScript(t,e){return new Promise(s=>{let i=this.getdata("@chavy_boxjs_userCfgs.httpapi");i=i?i.replace(/\n/g,"").trim():i;let r=this.getdata("@chavy_boxjs_userCfgs.httpapi_timeout");r=r?1*r:20,r=e&&e.timeout?e.timeout:r;const[o,h]=i.split("@"),n={url:`http://${h}/v1/scripting/evaluate`,body:{script_text:t,mock_type:"cron",timeout:r},headers:{"X-Key":o,Accept:"*/*"}};this.post(n,(t,e,i)=>s(i))}).catch(t=>this.logErr(t))}loaddata(){if(!this.isNode())return{};{this.fs=this.fs?this.fs:require("fs"),this.path=this.path?this.path:require("path");const t=this.path.resolve(this.dataFile),e=this.path.resolve(process.cwd(),this.dataFile),s=this.fs.existsSync(t),i=!s&&this.fs.existsSync(e);if(!s&&!i)return{};{const i=s?t:e;try{return JSON.parse(this.fs.readFileSync(i))}catch(t){return{}}}}}writedata(){if(this.isNode()){this.fs=this.fs?this.fs:require("fs"),this.path=this.path?this.path:require("path");const t=this.path.resolve(this.dataFile),e=this.path.resolve(process.cwd(),this.dataFile),s=this.fs.existsSync(t),i=!s&&this.fs.existsSync(e),r=JSON.stringify(this.data);s?this.fs.writeFileSync(t,r):i?this.fs.writeFileSync(e,r):this.fs.writeFileSync(t,r)}}lodash_get(t,e,s){const i=e.replace(/\[(\d+)\]/g,".$1").split(".");let r=t;for(const t of i)if(r=Object(r)[t],void 0===r)return s;return r}lodash_set(t,e,s){return Object(t)!==t?t:(Array.isArray(e)||(e=e.toString().match(/[^.[\]]+/g)||[]),e.slice(0,-1).reduce((t,s,i)=>Object(t[s])===t[s]?t[s]:t[s]=Math.abs(e[i+1])>>0==+e[i+1]?[]:{},t)[e[e.length-1]]=s,t)}getdata(t){let e=this.getval(t);if(/^@/.test(t)){const[,s,i]=/^@(.*?)\.(.*?)$/.exec(t),r=s?this.getval(s):"";if(r)try{const t=JSON.parse(r);e=t?this.lodash_get(t,i,""):e}catch(t){e=""}}return e}setdata(t,e){let s=!1;if(/^@/.test(e)){const[,i,r]=/^@(.*?)\.(.*?)$/.exec(e),o=this.getval(i),h=i?"null"===o?null:o||"{}":"{}";try{const e=JSON.parse(h);this.lodash_set(e,r,t),s=this.setval(JSON.stringify(e),i)}catch(e){const o={};this.lodash_set(o,r,t),s=this.setval(JSON.stringify(o),i)}}else s=this.setval(t,e);return s}getval(t){return this.isSurge()||this.isLoon()?$persistentStore.read(t):this.isQuanX()?$prefs.valueForKey(t):this.isNode()?(this.data=this.loaddata(),this.data[t]):this.data&&this.data[t]||null}setval(t,e){return this.isSurge()||this.isLoon()?$persistentStore.write(t,e):this.isQuanX()?$prefs.setValueForKey(t,e):this.isNode()?(this.data=this.loaddata(),this.data[e]=t,this.writedata(),!0):this.data&&this.data[e]||null}initGotEnv(t){this.got=this.got?this.got:require("got"),this.cktough=this.cktough?this.cktough:require("tough-cookie"),this.ckjar=this.ckjar?this.ckjar:new this.cktough.CookieJar,t&&(t.headers=t.headers?t.headers:{},void 0===t.headers.Cookie&&void 0===t.cookieJar&&(t.cookieJar=this.ckjar))}get(t,e=(()=>{})){t.headers&&(delete t.headers["Content-Type"],delete t.headers["Content-Length"]),this.isSurge()||this.isLoon()?(this.isSurge()&&this.isNeedRewrite&&(t.headers=t.headers||{},Object.assign(t.headers,{"X-Surge-Skip-Scripting":!1})),$httpClient.get(t,(t,s,i)=>{!t&&s&&(s.body=i,s.statusCode=s.status),e(t,s,i)})):this.isQuanX()?(this.isNeedRewrite&&(t.opts=t.opts||{},Object.assign(t.opts,{hints:!1})),$task.fetch(t).then(t=>{const{statusCode:s,statusCode:i,headers:r,body:o}=t;e(null,{status:s,statusCode:i,headers:r,body:o},o)},t=>e(t))):this.isNode()&&(this.initGotEnv(t),this.got(t).on("redirect",(t,e)=>{try{if(t.headers["set-cookie"]){const s=t.headers["set-cookie"].map(this.cktough.Cookie.parse).toString();s&&this.ckjar.setCookieSync(s,null),e.cookieJar=this.ckjar}}catch(t){this.logErr(t)}}).then(t=>{const{statusCode:s,statusCode:i,headers:r,body:o}=t;e(null,{status:s,statusCode:i,headers:r,body:o},o)},t=>{const{message:s,response:i}=t;e(s,i,i&&i.body)}))}post(t,e=(()=>{})){if(t.body&&t.headers&&!t.headers["Content-Type"]&&(t.headers["Content-Type"]="application/x-www-form-urlencoded"),t.headers&&delete t.headers["Content-Length"],this.isSurge()||this.isLoon())this.isSurge()&&this.isNeedRewrite&&(t.headers=t.headers||{},Object.assign(t.headers,{"X-Surge-Skip-Scripting":!1})),$httpClient.post(t,(t,s,i)=>{!t&&s&&(s.body=i,s.statusCode=s.status),e(t,s,i)});else if(this.isQuanX())t.method="POST",this.isNeedRewrite&&(t.opts=t.opts||{},Object.assign(t.opts,{hints:!1})),$task.fetch(t).then(t=>{const{statusCode:s,statusCode:i,headers:r,body:o}=t;e(null,{status:s,statusCode:i,headers:r,body:o},o)},t=>e(t));else if(this.isNode()){this.initGotEnv(t);const{url:s,...i}=t;this.got.post(s,i).then(t=>{const{statusCode:s,statusCode:i,headers:r,body:o}=t;e(null,{status:s,statusCode:i,headers:r,body:o},o)},t=>{const{message:s,response:i}=t;e(s,i,i&&i.body)})}}time(t,e=null){const s=e?new Date(e):new Date;let i={"M+":s.getMonth()+1,"d+":s.getDate(),"H+":s.getHours(),"m+":s.getMinutes(),"s+":s.getSeconds(),"q+":Math.floor((s.getMonth()+3)/3),S:s.getMilliseconds()};/(y+)/.test(t)&&(t=t.replace(RegExp.$1,(s.getFullYear()+"").substr(4-RegExp.$1.length)));for(let e in i)new RegExp("("+e+")").test(t)&&(t=t.replace(RegExp.$1,1==RegExp.$1.length?i[e]:("00"+i[e]).substr((""+i[e]).length)));return t}msg(e=t,s="",i="",r){const o=t=>{if(!t)return t;if("string"==typeof t)return this.isLoon()?t:this.isQuanX()?{"open-url":t}:this.isSurge()?{url:t}:void 0;if("object"==typeof t){if(this.isLoon()){let e=t.openUrl||t.url||t["open-url"],s=t.mediaUrl||t["media-url"];return{openUrl:e,mediaUrl:s}}if(this.isQuanX()){let e=t["open-url"]||t.url||t.openUrl,s=t["media-url"]||t.mediaUrl;return{"open-url":e,"media-url":s}}if(this.isSurge()){let e=t.url||t.openUrl||t["open-url"];return{url:e}}}};if(this.isMute||(this.isSurge()||this.isLoon()?$notification.post(e,s,i,o(r)):this.isQuanX()&&$notify(e,s,i,o(r))),!this.isMuteLog){let t=["","==============📣系统通知📣=============="];t.push(e),s&&t.push(s),i&&t.push(i),console.log(t.join("\n")),this.logs=this.logs.concat(t)}}log(...t){t.length>0&&(this.logs=[...this.logs,...t]),console.log(t.join(this.logSeparator))}logErr(t,e){const s=!this.isSurge()&&!this.isQuanX()&&!this.isLoon();s?this.log("",`❗️${this.name}, 错误!`,t.stack):this.log("",`❗️${this.name}, 错误!`,t)}wait(t){return new Promise(e=>setTimeout(e,t))}done(t={}){const e=(new Date).getTime(),s=(e-this.startTime)/1e3;this.log("",`🔔${this.name}, 结束! 🕛 ${s} 秒`),this.log(),(this.isSurge()||this.isQuanX()||this.isLoon())&&$done(t)}}(t,e)}
