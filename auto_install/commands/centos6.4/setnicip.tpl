#!/bin/bash

IP=("{IP1}" "{IP2}" "{IP3}" "{IP4}")
MASK=("{MASK1}" "{MASK2}" "{MASK3}" "{MASK4}")
EANBLED=("{EANBLED1}" "{EANBLED2}" "{EANBLED3}" "{EANBLED4}")
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
	 tmpip=${IP[$no]}
	 tmpmask=${MASK[$no]}
	 tmpeanbled=${EANBLED[$no]}
	 if [ "X${tmpip}" != "X" -a "X${tmpmask}" != "X" -a "X${tmpeanbled}" != "X1" ]
	 then
	 		/bin/sed -i 's/dhcp/static/' /etc/sysconfig/network-scripts/ifcfg-${i}
	 		/bin/sed -i 's/ONBOOT=no/ONBOOT=yes/' /etc/sysconfig/network-scripts/ifcfg-${i}
	 		/bin/echo "IPADDR=${tmpip}" >>/etc/sysconfig/network-scripts/ifcfg-${i} 
	 		/bin/echo "MASK=${tmpmask}" >>/etc/sysconfig/network-scripts/ifcfg-${i}
	 fi
done
