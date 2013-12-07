#!/bin/bash

java -Xmx4G -jar MPEG7AudioEnc-0.4-rc3.jar $1 ./MPEG7.config.xml > $2
