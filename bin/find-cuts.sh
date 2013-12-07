#!/bin/bash

TEMP_DIR=$3

ffmpeg -r 25 -i $1 -s 320x180 -an -sn -vcodec mjpeg -f avi $TEMP_DIR/test.avi

lav2yuv -S $2 $TEMP_DIR/test.avi

rm -f $TEMP_DIR/test.avi
