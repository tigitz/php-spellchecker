#!/usr/bin/env bash

set -x

/usr/bin/jamspell-server ${DATA_DIR}/${MODEL} ${HOST} ${PORT}
