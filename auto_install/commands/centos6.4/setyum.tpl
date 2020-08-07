#!/bin/bash

/bin/mv /etc/yum.repos.d/CentOS-Base.repo /etc/yum.repos.d/CentOS-Base.repo{DATATIME}
/usr/bin/wget {KSURL}commandstemplates/{OSVER}/CentOS-Base.repo -O /etc/yum.repos.d/CentOS-Base.repo
/bin/chmod 644 /etc/yum.repos.d/CentOS-Base.repo
/usr/bin/yum clean all

