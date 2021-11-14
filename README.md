# xxoo助力池

实现以下功能

- 自动解析jd_get_share_code脚本获取到的助力码并自动上报到服务池
- 自动随机获取池中的互助码
- 定向互助
- 多账户自助
- 可搭建自己的服务池

说明：<br/>
定向互助: 比如你想优先助力某用户，然后再助力池中的其他用户

    
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

- 青龙面板->脚本管理 新增xxoo.js,将xxooPoolClient/scripts/xxoo.js内容拷贝进去
- 青龙面板->对比工具 右上角的当前文件选择 task_before.sh, 追加xxooPoolClient/task_before.sh中的内容
- 青龙面板->环境变量 新增以下环境变量


        XXOO_HOST               必填
        当你不想用默认助力池时，填入你要用的xxoo助力池地址
        本人自建服务池地址：
        XXOO_HOST = https://sharec.siber.cn:889    
    
        XXOO_TOKEN              必填
        接入服务池的验证token,本人自建的服务池提供一个token
        XXOO_TOKEN = dev_token

        XXOO_FOR                选填
        有时候我们希望优先助力某些用户，则此变量为 
        你要定向助力的用户的pt_pin参数，多个时用@符号分隔

        XXOO_READ_SHARE_CODE    选填
        本项目依赖于原版的jd_get_share_code的执行日志，默认不需要填写，
        除非你重命名了脚本名 不是以jd_get_share_code字符串结尾才需要填本字段
    

- 青龙面板->定时任务 新增定时任务

    
        名称：         xxoo助力池             
        命令：         task xxoo.js           说明：不可修改
        定时规则：      0 0 0 */5 * *          说明：互助码不会变，
                                                    但是服务器会定期清理不活跃的助力码，所以还是
                                                    每5天同步一次即可，
                                                    第一次配置可手动执行一次即可



- 接入是否成功检测：

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


# 自建服务池：如果你想要自建的话

环境要求：php > 7.0版本  

### 安装方法
    
    
    - 将xxooPoolServer目录拷贝到你的服务器的某个目录
    - cd到你服务器的xxooPoolServer根目录
    - 输入命令：php data.php init 进行数据库初始化 初始化后，默认自带一个用户token,token=dev_token

        
            如果你需要增加用户表记录，可以这么做
            输入命令：php data.php exec "${这里是insert sql,自己写}"

    - 输入命令：php -S 127.0.0.1:999

    说明：本项目目的不是为了大量用户使用，所以一切从简，目的是为了自用顺便开源出来
         数据库都是采用的sqlite,所以，如果要自建服务池的，自己改吧
