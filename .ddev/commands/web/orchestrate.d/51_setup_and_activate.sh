#!/bin/bash

flags=""
if [ "${WP_MULTISITE}" = "true" ]; then
  flags+=" --network"
fi

wp plugin activate "${PLUGIN_NAME:-$DDEV_PROJECT}" $flags
