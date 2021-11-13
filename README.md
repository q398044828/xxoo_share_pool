# xxoo助力池

实现以下功能

- 自动上报自己的互助码
- 自动随机获取池中的互助码
- 定向互助
- 多账户自助
- 可搭建自己的服务池

说明：<br/>
定向互助: 比如你想优先助力某用户，然后再助力池中的其他用户

## 助力规则

- 优先助力多账户之间的互助
- 然后助力配置的定向互助
- 然后才助力池中的用户

提示：本仓库和JDHelloWorld大佬的助力池可共存，共存时，优先采用本仓库的助力逻辑


# 食用方法

- 青龙面板->脚本管理 新增xxoo.js,将xxooPoolClient/scripts/xxoo.js内容拷贝进去
- 青龙面板->对比工具 右上角的当前文件选择 task_before.sh, 追加xxooPoolClient/task_before.sh中的内容
- 青龙面板->环境变量 新增以下环境变量


        XXOO_FOR                必填 
        有时候我们希望优先助力某些用户，则此变量为 
        你要定向互助的用户的pt_pin参数，多个时用@符号分隔 
    
        XXOO_HOST               必填
        当你不想用默认助力池时，填入你要用的xxoo助力池地址
        本人自建服务池地址：
        XXOO_HOST = https://sharec.siber.cn:889    
    
        XXOO_TOKEN              必填
        接入服务池的验证token,本人自建的服务池提供一个token
        XXOO_TOKEN = dev_token
    
        XXOO_READ_SHARE_CODE    选填
        你原有的定时任务的jd_get_share_code脚本名称，不包括后缀名
        比如我的是：shufflewzc_faker2_jd_get_share_code 
    

- 青龙面板->定时任务 新增定时任务

    
    名称：         xxoo助力池             
    命令：         task xxoo.js            说明：不可修改
    定时规则：      0 0 0 */5 * *           说明：互助码不会变，每5天同步一次即可，第一次配置可手动执行一次即可

# 自建服务池：如果你想要自建的话

环境要求：php > 7.0版本  

### 安装方法
    
    
    - 将xxooPoolServer目录拷贝到你的服务器的某个目录
    - cd到你服务器的xxooPoolServer根目录
    - 输入命令：php -S 127.0.0.1:999

    说明：本项目目的不是为了大量用户使用，所以一切从简，目的是为了自用顺便开源出来
         数据库都是采用的sqlite,所以，如果要自建服务池的，自己改吧