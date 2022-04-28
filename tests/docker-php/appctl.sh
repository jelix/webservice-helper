#!/bin/bash
APPDIR="/app/"
APP_USER=userphp
APP_GROUP=groupphp

COMMAND="$1"
shift

if [ "$COMMAND" == "" ]; then
    echo "Error: command is missing"
    exit 1;
fi

function composerInstall() {
    echo "--- Install Composer packages"
    if [ -f $APPDIR/composer.lock ]; then
        rm -f $APPDIR/composer.lock
    fi
    composer install --prefer-dist --no-progress --no-ansi --no-interaction --working-dir=$APPDIR
    chown -R $APP_USER:$APP_GROUP $APPDIR/vendor $APPDIR/composer.lock
}

function composerUpdate() {
    echo "--- Update Composer packages"
    if [ -f $APPDIR/composer.lock ]; then
        rm -f $APPDIR/composer.lock
    fi
    composer update --prefer-dist --no-progress --no-ansi --no-interaction --working-dir=$APPDIR
    chown -R $APP_USER:$APP_GROUP $APPDIR/vendor $APPDIR/composer.lock
}


case $COMMAND in
    composer_install)
        composerInstall;;
    composer_update)
        composerUpdate;;
    unit-tests)
        rm -rf /tmp/wsdl-*
        rm -f /app/example/wsdl/contactManager.wsdl
        UTCMD="cd $APPDIR/tests/ && ../vendor/bin/phpunit  $@"
        su $APP_USER -c "$UTCMD"
        ;;
    *)
        echo "wrong command"
        exit 2
        ;;
esac

