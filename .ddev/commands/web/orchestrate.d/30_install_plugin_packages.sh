#!/bin/bash

popd

composer install
yarn install

pushd "${DDEV_DOCROOT}"
