#!/bin/sh
#
# hunspell binary stub
#

folder=$(dirname $0)

case "$*" in
    '-D')
        cat "$folder/dicts.txt" >&2
        ;;
    *'-a'*)
        cat "$folder/check.txt"
        ;;
esac
exit 0
