#!/bin/bash

/bin/mv /etc/hosts /etc/hosts{DATATIME}
/usr/bin/wget {KSURL}commandstemplates/{OSVER}/hosts   -O /etc/hosts
/bin/chown root:root /etc/hosts
/bin/chmod 644 /etc/hosts
