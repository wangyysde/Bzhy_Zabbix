#!/bin/sh

if [ "X{IP1}" != "X" -a "X{MASK1}" != "X" -a "X{EANBLED1}" != "X1" ]
then 	
   /bin/esxcli.py network ip interface ipv4 set -i vmk0 -I {IP1} -N {MASK1} -t static
fi

if [ "X{IP2}" != "X" -a "X{MASK2}" != "X" -a "X{EANBLED2}" != "X1" ]
then    
   /bin/esxcli.py network ip interface ipv4 set -i vmk1 -I {IP2} -N {MASK2} -t static
fi


if [ "X{IP3}" != "X" -a "X{MASK3}" != "X" -a "X{EANBLED3}" != "X1" ]
then
   /bin/esxcli.py network ip interface ipv4 set -i vmk2 -I {IP3} -N {MASK3} -t static
fi

if [ "X{IP4}" != "X" -a "X{MASK4}" != "X" -a "X{EANBLED4}" != "X1" ]
then
   /bin/esxcli.py network ip interface ipv4 set -i vmk3 -I {IP4} -N {MASK4} -t static
fi
