#!/bin/sh
#
# ispell binary stub
#

folder=$(dirname $0)
#
#abspath() {
#    cd "$(dirname "$1")"
#    printf "%s/%s\n" "$(pwd)" "$(basename "$1")"
#    cd "$OLDPWD"
#}
#
#dictionaries=$(abspath "$folder/../lib/ispell")

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
