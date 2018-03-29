#!/usr/bin/env bash

set -x

DEV_MODE=${DEV_MODE:-false}

if [ ${DEV_MODE} = true ]; then
    # install composer
    curl -sS https://getcomposer.org/installer | php -- --filename=composer --install-dir=/usr/local/bin
    # make composer install as far as we mount host directory in the dev mode
    composer install -no
else
    # replace parameters to the appropriate environment variables in symfony parameters.yml
    composer run-script params
fi

chown -R nginx:nginx .

echo "FINISHED!!!"
