#!/bin/sh
set -e

# TODO check if already installed, but not gravious because this is idempotent 
mkcert -install

for i in $(echo $DOMAINS | sed "s/,/ /g"); do 
    mkcert $i; 
done

# also enable generation of custom certs
if [ "$#" -gt 1 -a "$1" == "mkcerts" ]; then
    "$@"
fi    

# Debug: list content of certs folder
#echo "CAROOT is $CAROOT"
#ls -lsah $CAROOT

# TODO remove
# keeps the container alive for debug purpose
#while (true); do 
# sleep 5
# echo "still running"
#done
