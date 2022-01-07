#!/bin/bash

_PLUGIN='wpu-custom-avatar';
_FILES="wp-content/mu-plugins/${_PLUGIN}.php";

git clone 'https://github.com/darklg/wputilities' "${_PLUGIN}";
cd "${_PLUGIN}";
git remote remove origin;
git filter-branch --prune-empty --index-filter "
                        git read-tree --empty
                        git reset \$GIT_COMMIT -- $_FILES
                " \
        -- --all -- $_FILES
