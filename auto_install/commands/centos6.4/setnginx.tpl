#!/bin/bash
/usr/bin/yum -y -q install nginx
/usr/sbin/useradd -s /sbin/nologin www
 /bin/chown -R www:root /usr/local/nginx/{client_body_temp,fastcgi_temp,proxy_temp,scgi_temp,uwsgi_temp} 

