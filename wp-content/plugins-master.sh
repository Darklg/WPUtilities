#!/bin/bash

###################################
## Checkout all plugins to the latest master
###################################

_path=`pwd`;

for _plugin in `ls plugins`
do
    _path_plugin="${_path}/plugins/${_plugin}";
    if [ -f "${_path_plugin}/.git" ];then
        cd "${_path_plugin}";
        echo "${_path_plugin}";
        git checkout master;
        git pull origin master;
    fi;
done
