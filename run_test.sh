#!/bin/bash

RUNNING_DIR=`pwd`;

if [ "$1" == "-i" ]; then
    cd $RUNNING_DIR  && composer install
    cd $RUNNING_DIR && cd tests/framework/test && composer install
fi

cd $RUNNING_DIR && ./vendor/bin/phpunit
cd tests/framework/test && ./bin/phpunit
cd $RUNNING_DIR
