#!/bin/bash

NOW=$(date +"%Y%m%d%H%M")
SITE_DIR=/var/www/rcnapi-admin

RELEASE_DIR=${SITE_DIR}/releases

mkdir ${RELEASE_DIR}/${NOW}

tar -xzvf ~/release.tar.gz -C ${RELEASE_DIR}/${NOW}
rm -rf ${RELEASE_DIR}/${NOW}/storage

ln -s ${SITE_DIR}/shared/storage ${RELEASE_DIR}/${NOW}/storage
ln -s ${SITE_DIR}/shared/storage/app/public ${RELEASE_DIR}/${NOW}/public/storage
ln -s ${SITE_DIR}/shared/.env ${RELEASE_DIR}/${NOW}/.env

rm ${SITE_DIR}/htdocs; rm ${SITE_DIR}/latest
ln -s ${RELEASE_DIR}/${NOW}/public ${SITE_DIR}/htdocs
ln -s ${RELEASE_DIR}/${NOW} ${SITE_DIR}/latest

php ${RELEASE_DIR}/${NOW}/artisan migrate

# Remove all but the last 3 releases
ls -dt ${RELEASE_DIR}/* | tail -n +3 | xargs rm -rf

sudo service php7.2-fpm restart
