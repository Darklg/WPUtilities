#!/bin/bash

_SOURCEDIR="$( dirname "${BASH_SOURCE[0]}" )/";
_HTDOCS="${_SOURCEDIR}../"

###################################
## Reset plugins
###################################

cd "${_HTDOCS}";
wputools wp cache flush;
wputools wp plugin deactivate --all;
rm wp-content/object-cache.php;
rm wp-content/db.php;
wputools cache;

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

###################################
## Cache & Stats
###################################

wputools wp plugin activate query-monitor
wputools wp plugin activate redis-cache

# Plugin fixes
rm wp-content/db.php;
redis-cli FLUSHALL;

# Enable redis cache
wputools wp redis enable;

###################################
## Login
###################################

wputools login;
