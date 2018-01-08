#!/bin/bash

###################################
## Checkout all plugins to the latest master
###################################

_path=`pwd`;

for plugin in `ls plugins`
do
    cd "$_path/plugins/$plugin";
    git checkout master;
    git pull origin master;
done
