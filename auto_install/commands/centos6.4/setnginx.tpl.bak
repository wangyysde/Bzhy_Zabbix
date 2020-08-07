#!/bin/bash
/usr/bin/yum -y -q install jdk
/bin/mkdir -p /data/apps
cd /data/apps
/usr/bin/wget {KSURL}commandstemplates/{OSVER}/apache-tomcat-7.0.61.tar.gz -O ./
/bin/tar -zxvf ./apache-tomcat-7.0.61.tar.gz
/usr/sbin/useradd -s /sbin/nologin tomcat
/bin/mv ./apache-tomcat-7.0.61 ./tomcat
/bin/chown -R tomcat:tomcat /data/apps 


