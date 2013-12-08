#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

java -Xmx4G -jar $DIR/MPEG7AudioEnc-0.4-rc3.jar $1 $DIR/MPEG7.config.xml > $2
