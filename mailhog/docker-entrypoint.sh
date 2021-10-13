#!/bin/sh
set -e

## Launch mailhog
sudo -u mailhog /opt/mailhog/bin/mailhog

## Launch stunnel
stunnel

# Debug: list content of certs folder
#echo "CAROOT is $CAROOT"
#ls -lsah $CAROOT

# TODO remove
# keeps the container alive for debug purpose
while (true); do 
 sleep 5
 echo "still running"
done
