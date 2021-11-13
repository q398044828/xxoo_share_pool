#!/usr/bin/env bash
xxooLogDir="${dir_log}/xxoo"
if [[ $(ls $xxooLogDir) ]]; then
    latest_log=$(ls -r $xxooLogDir | head -1)
    . $xxooLogDir/$latest_log
    echo "##  task before  $xxooLogDir/$latest_log"
    echo "##  $GENERATE_INFO";
fi
