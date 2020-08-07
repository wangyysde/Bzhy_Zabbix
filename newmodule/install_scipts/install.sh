#!/bin/bash

help=no
zabbixdir=""
moduledir=""
codedir="src"
OVERRRIDELST="override.lst"

function showhelpmsg()
{
cat <<END
    --help                          Print this message

    --zabbixdir=PATH                The path of zabbix front 
    --moduledir=PATH                The path of new module source 
END
}

#parsing command parameters
for option 
do
    case "$option" in
        -*=*) value=`echo "$option" | sed -e 's/[-_a-zA-Z0-9]*=//'` ;;
           *) value="" ;;
    esac
    case "$option" in
        --help)                     help=yes                  ;;
        --zabbixdir*)                zabbixdir="$value"        ;;
        --moduledir*)                moduledir="$value"        ;;
    esac
done

#Zabbix front path and new module path must be specified 
if [ "X${zabbixdir}" == "X" -o "X${moduledir}" == "X" ];then
    help=yes
fi

if [ ! -d "${zabbixdir}"  -o ! -d "${moduledir}" ]; then 
    help=yes
fi

if [ $help == yes ]; then
    showhelpmsg
    exit -1
fi

#Copy override files to zabbix front
if [ -f "${moduledir}/install_scipts/${OVERRRIDELST}" ]; then
    overridelst="${moduledir}/install_scipts/${OVERRRIDELST}"
    for file in `cat ${overridelst}`;do
        destfile=${file%:*}  
        modulefile=${file##*:}
        filename=${destfile##*/}
        pathname=${destfile%/*}
        if [ "X${pathname}" == "X" ]; then
            if [ ! -f "${zabbixdir}/bzhy_org_${filename}" ]; then
               /bin/cp -Rpf "${zabbixdir}${destfile}"  "${zabbixdir}/bzhy_org_${filename}"
            fi
        else
            if [ ! -f "${zabbixdir}/${pathname}/bzhy_org_${filename}" ]; then
                /bin/cp -Rpf "${zabbixdir}${destfile}"  "${zabbixdir}${pathname}/bzhy_org_${filename}"
            fi
        fi
       /bin/cp -Rpf "${moduledir}/src${modulefile}"  "${zabbixdir}${destfile}"
    done
fi


#Copy menu.inc.php and main.js file to zabbix front
#if [ ! -f "${zabbixdir}/include/menu.inc_org.php" ];then 
#    /bin/cp -Rpf "${zabbixdir}/include/menu.inc.php"  "${zabbixdir}/include/menu.inc_org.php"
#fi

#/bin/cp -Rpf "${moduledir}/include/menu.inc.php"  "${zabbixdir}/include/menu.inc.php"

#if [ ! -f "${zabbixdir}/js/main_org.js" ];then 
#    /bin/cp -Rpf "${zabbixdir}/js/main.js"  "${zabbixdir}/js/main_org.js"
#fi

#/bin/cp -Rpf "${moduledir}/js/main.js"  "${zabbixdir}/js/main.js"

#Copy schema.inc.php to zabbix front
#if [ ! -f "${zabbixdir}/include/schema_org.inc.php" ];then 
#    /bin/cp -Rpf "${zabbixdir}/include/schema.inc.php"  "${zabbixdir}/include/schema_org.inc.php"
#fi
#/bin/cp -Rpf "${moduledir}/include/schema.inc.php"  "${zabbixdir}/include/schema.inc.php"

#Copy new module code file to zabbix front 
for dir in `echo ${codedir}`
do
   srcpath="${moduledir}/${dir}/"
   dstpath="${zabbixdir}/"
#   if [ -d ${srcpath} ]; then 
#        [ -d ${dstpath} ] || mkdir -p ${dstpath}
        /bin/cp -Rpf "${srcpath}"* ${dstpath}
#    fi
done

#Copy and product new translate file
#if [ -f "${moduledir}/locale/zh_CN/LC_MESSAGES/frontend.po" ];then
#    [ ! -f "${zabbixdir}/locale/zh_CN/LC_MESSAGES/frontend_org.po" ] && /bin/cp -Rpf "${zabbixdir}/locale/zh_CN/LC_MESSAGES/frontend.po" "${zabbixdir}/locale/zh_CN/LC_MESSAGES/frontend_org.po"
#    /bin/cat "${moduledir}/install_scipts/menu_frontend.pot" >>"${zabbixdir}/locale/zh_CN/LC_MESSAGES/frontend.po"
#    cd "${zabbixdir}"
#    bash +x ./locale/make_mo.sh
#fi