#!/bin/sh
set -e

## stunnel must be launched first and in background 
stunnel

## Launch mailhog in foreground
mailhog
