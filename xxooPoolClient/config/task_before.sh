#!/usr/bin/env bash

## ======重要提示： 如果是追加到已经存在的task_before.sh中，则只拷贝以下内容
xxooLogDir="${dir_log}/xxoo"
if [[ $(ls $xxooLogDir) ]]; then
    latest_log=$(ls -r $xxooLogDir | head -1)
    . $xxooLogDir/$latest_log
    echo "##  task before  $xxooLogDir/$latest_log"
    echo "##  $GENERATE_INFO";
fi
