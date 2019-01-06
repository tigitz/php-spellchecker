#!/bin/sh
#
# aspell binary stub
#

folder=$(dirname $0)

case "$*" in
    'dump dicts')
        cat "$folder/../dicts.txt"
        ;;
    *'-a'*)
        cat "$folder/../check.txt"
        ;;
esac
exit 0
