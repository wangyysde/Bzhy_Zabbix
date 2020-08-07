#!/bin/bash

if [ ! -f /var/spool/cron/root ]
then
	/bin/touch /var/spool/cron/root
	/bin/chmod 600  /var/spool/cron/root
fi
if [ ! -f /usr/sbin/ntpdate ]
then
	/usr/bin/yum -y -q install ntpdate
fi
/bin/echo "0 */1 * * * /usr/sbin/ntpdate -s 172.16.16.84  >/dev/null 2>&1" >>/var/spool/cron/root
 

