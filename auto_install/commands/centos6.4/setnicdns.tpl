#!/bin/bash

DNS1="{DNS1}"
DNS2="{DNS2}"
GATEWAY="{GATEWAY}"
BINDGATEWAYNICNO="{BINDGATEWAYNICNO}"

cd /etc/sysconfig/network-scripts/
interfaces=$(ls ifcfg* | \
            LANG=C sed -e "$__sed_discard_ignored_files" \
                       -e '/\(ifcfg-lo$\|:\|ifcfg-.*-range\)/d' \
                       -e '/ifcfg-[A-Za-z0-9#\._-]\+$/ { s/^ifcfg-//g;s/[0-9]/ &/}' | \
            LANG=C sort -k 1,1 -k 2n | \
            LANG=C sed 's/ //')

for i in $interfaces
do
	 no=${i:(-1):1}
	 if [ $BINDGATEWAYNICNO -eq $no ]
	 then
	 		/bin/echo "DNS1={DNS1}" >>/etc/sysconfig/network-scripts/ifcfg-${i}
	 		/bin/echo "DNS2={DNS2}" >>/etc/sysconfig/network-scripts/ifcfg-${i}
	 		/bin/echo "GATEWAY={GATEWAY}" >>/etc/sysconfig/network-scripts/ifcfg-${i}
	 fi
done