#!/bin/sh
#
# ispell binary stub
#

folder=$(dirname $0)

case "$*" in
    'which ispell')
        echo "usr/bin/ispell"
        ;;
    "ls usr/lib/ispell")
        ls "$folder/../lib/ispell"
        ;;
    *'-a'*)
        cat "$folder/../check.txt"
        ;;
esac
exit 0
