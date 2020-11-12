#!/bin/bash

_SOURCEDIR="$( dirname "${BASH_SOURCE[0]}" )/";
_HTDOCS="${_SOURCEDIR}../"

###################################
## Reset plugins
###################################

cd "${_HTDOCS}";
wputools wp plugin deactivate --all;
rm wp-content/object-cache.php;

###################################
## Reset DB
###################################

wputools wp db reset --yes;

###################################
## Reinstall
###################################

wputools wp core install --url=wpu.test --title=WPUtilities --admin_user=admin --admin_password=admin --admin_email=test@example.com
wputools wp theme activate WPUTheme;

###################################
## Sample
###################################

wputools sample post 20;
