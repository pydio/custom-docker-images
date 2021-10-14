#!/bin/sh
set -e

## stunnel must be launched first and in background 
stunnel

## Launch mailhog in foreground
mailhog
# TODO remove
# keeps the container alive for debug purpose
#while (true); do 
# sleep 5
# echo "still running"
#done
