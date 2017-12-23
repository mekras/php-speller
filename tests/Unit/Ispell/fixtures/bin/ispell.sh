#!/bin/sh
#
# ispell binary stub
#

folder=$(dirname $0)

case "$*" in
    *'-a'*)
        cat "$folder/../check.txt"
        ;;
esac
exit 0
