# xxoo助力池

本助力池特性

- 自动解析并上传助力码
- 多账户自助
- 定向助力
- 助力池助力
- 可与其他助力池共存
- 可搭建自己的服务池

[加入telegram频道点我](https://t.me/xxoo_pool) <br/>
https://t.me/xxoo_pool

说明：<br/>

        定向互助: 比如你想优先助力某用户，然后再助力池中的其他用户
        所以，当你发现没人给你助力时，邀请几个朋友定向助力你吧！！！
    

    
        实现原理：
        xxoo.js运行时，会解析jd_get_share_code脚本生成的日志文件，从日志文件中获
        取助力码(所以需要保证jd_get_share_code脚本的正常运行)，然后将助力码上传到服务
        池，服务池会根据一定策略下发助力码(上传的是自己的，下发的包含池中其他用户上传的助力码)，
        然后定时任务执行时，会自动先执行task_before.sh脚本，此脚本会导入服务器下发的助
        力码到环境变量中

## 助力规则

- 优先助力多账户之间的互助
- 然后助力配置的定向互助
- 然后才助力池中的用户

提示：本仓库和JDHelloWorld大佬的助力池可共存，共存时，优先采用本仓库的助力逻辑


# 食用方法

- 1.青龙面板->对比工具 右上角的当前文件选择 task_before.sh, 追加以下内容到脚本中
```
## ======重要提示： 如果是追加到已经存在的 task_before.sh 中，则只拷贝以下内容
xxooLogDir="${dir_log}/raw_main_xxoo"
if [[ $(ls $xxooLogDir) ]]; then
    latest_log=$(ls -r $xxooLogDir | head -1)
    . $xxooLogDir/$latest_log
    echo "##  task before  $xxooLogDir/$latest_log"
    echo "##  $GENERATE_INFO";
fi
```
        
- 2.青龙面板->环境变量 新增以下环境变量


        名称:     XXOO_HOST                                       必填
        值:      https://sharec.siber.cn:889   
                
        当你不想用默认助力池时，填入你要用的xxoo助力池地址,默认为作者提供的地址
        当你要用其他人自建服务池时，请修改XXOO_HOST值
        
    
        名称:     XXOO_TOKEN 
        值:       dev_token                                       必填
        
        接入服务池的验证token,本人自建的服务池提供一个token
        当你使用其他人的自建服池或者提示数量限制时，请向服务池所有者反馈


        名称:     XXOO_FOR                                        选填
        值:       pt_pin1@pt_pin2                                
        
        填你想要助力的人，具体请参考定向助力说明


        名称:     XXOO_CLOSE_SELF
        值:       true                                           选填
        
        是否关闭多账号自助，关闭为true,不关为false,不填为不关
        多个小号养大号但是小号本身不需要被助力时使用此开关
        注意：很多脚本默认开启多账户自助，此种情况本脚本无法控制
        

- 3.青龙面板->定时任务->添加定时任务


        名称：xxoo更新
        命令：ql raw https://raw.githubusercontent.com/q398044828/xxoo_share_pool/main/xxoo.js
        定时规则：0 0 */1 * *
        
        配置好后立即执行一遍，会新增一个定时任务：xxoo互助池,此任务即为助力池脚本


- 4.接入是否成功检测：

上报成功且下发助力码成功时，xxoo脚本日志内容应该类似于以下内容

<details>
    <summary>点我展开</summary>
    <pre>
## 开始执行... 2021-11-13 23:55:07

##  task before  /ql/log/xxoo/2021-11-13-23-55-07.log
##  
:<<EOF

🔔获取互助码+参与xxoo互助池, 开始!
========>自动判断 jd_get_share_code 日志所在目录 开始
========>自动判断get_share_code 日志所在目录 shufflewzc_faker2_jd_get_share_code

从
/ql/log/shufflewzc_faker2_jd_get_share_code
目录解析日志最新获取的互助码

=====以下json数据为从原版的jd_get_share_code脚本的日志中解析获取到的互助码，如果没
有数据，请尝试先执行jd_get_share_code后再执行xxoo任务
{
jd_654c2078e51f7: {
FRUITSHARECODES: 'd8d67490c41f42348ba589fd18c50edb',
PETSHARECODES: 'MTAxNzIyNTU1NDAwMDAwMDA1MTMxMjIyMw==',
PLANT_BEAN_SHARECODES: 'olmijoxgmjutyeukiu3el2x5tr6uxjor76jutla',
DDFACTORY_SHARECODES: 'T0225KkcRhsdplbXJxKhkfZccwCjVWnYaS5kRrbA',
DREAM_FACTORY_SHARE_CODES: 'cAzv4fnSw852dboodamfKQ==',
JDSGMH_SHARECODES: 'T0225KkcRhsdplbXJxKhkfZccwCjVQmoaT5kRrbA',
JD_CASH_SHARECODES: 'eU9Yaum2Nf4m9maAznJF0Q'
},
jd_76f67073b047f: {
FRUITSHARECODES: 'ead06cd23c884a69b78e90e656da64b0',
PETSHARECODES: 'MTEyNzEzMjc0MDAwMDAwMDYwMTM5MDEz',
PLANT_BEAN_SHARECODES: '4npkonnsy7xi2mrf7ps6m4sy4hcm6ffzcnrmzli',
DDFACTORY_SHARECODES: 'T0225KkcRxhP81PXJxmmlPMNIgCjVWnYaS5kRrbA',
JDSGMH_SHARECODES: 'T0225KkcRxhP81PXJxmmlPMNIgCjVQmoaT5kRrbA',
JD_CASH_SHARECODES: 'eU9Ya-rkYPsm9m2Hy3cUgA'
}
}
EOF
##====================  xxoo池响应(服务器下发助力码)   ======
export FRUITSHARECODES="99@88"
export PETSHARECODES="fghfgh@456drg"
export PLANT_BEAN_SHARECODES="123123@3245345"
export DDFACTORY_SHARECODES="asdf@dfgsd"
export DREAM_FACTORY_SHARE_CODES="1@2"
export JDSGMH_SHARECODES="aa@bb"
export JD_CASH_SHARECODES="11@22@33"
export GENERATE_INFO="xxoo助力池同步时间===========》 2021年11月13日 23:55:08"


## 执行结束... 2021-11-13 23:55:08 耗时 1 秒

    </pre>
</details>

# 脚本更新

- 自动更新：按照食用方法配置，即可每天晚上自动更新
- 手动更新：当运行日志中提示： 请更新xxoo.js版本 时，请手动执行定时任务:xxoo更新

# 定向互助说明
    
    
    加了其他助力池，却只是给别人助力，没人给自己助力？
    助力被白嫖？
    
    不存在的！！！

    本助力池支持定向助力，可要求一群好友，你帮我助力，我帮你助力！

    方式1：
        配置环境变量： XXOO_FOR = pt_pin1@pt_pin2@pt_pin3
        说明：本容器所有账号都助力这三个pt_pin用户,使用@分隔
    方式2：
        配置环境变量： XXOO_FOR = pt_pin1;pt_pin2@pt_pin3;pt_pin4
        说明：第一个账号助力pt_pin1，
             第二个账号助力pt_pin2 和 pt_pin3,
             第三个账号助力pt_pin4

    pt_pin: jd用户的pt_pin参数值
    
    提示: 由于每个人可助力别人的次数有限,当你和别人互相定向时,如果对方定向助力数
          太多,且定向助力你的顺序比较靠后,那么就不能保证对方能给你助力,
          且对方还可能是多账号自助,能用来助力别人的次数可能不多
            
    
            

# 自建服务池：如果你想要自建的话

环境要求：php > 7.0版本  

### 安装方法
    
    
    - 将xxooPoolServer目录拷贝到你的服务器的某个目录
    - cd到你服务器的xxooPoolServer根目录
    - 输入命令：php data.php init 进行数据库初始化 初始化后，默认自带一个用户token,token=dev_token

        
            如果你需要增加用户表记录，可以这么做
            输入命令：php data.php exec "${这里是insert sql,自己写}"

    - 输入命令：php -S 0.0.0.0:999

    说明：本项目目的不是为了大量用户使用，目的是为了自用顺便开源出来
         所以一切从简，数据库采用的sqlite,所以，如果要自建服务池又对服务池有要求的，请你自己修改或重写
