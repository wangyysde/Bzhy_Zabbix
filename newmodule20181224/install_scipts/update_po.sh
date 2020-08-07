#!/bin/bash

echo "Generating translation template..."

# xgettext will be used on all php files
codedir="include" 
cd $(dirname $0)/..
>locale/POTFILES.in
for dir in `echo ${codedir}`
do
    find "./${dir}" -type f -name '*.php' | sort -d -f >> locale/POTFILES.in
done

# keyword "_n" is Zabbix frontend plural function
# keyword "_s" is Zabbix frontend placeholder function
# keyword "_x" is Zabbix frontend context function
>locale/frontend.pot
xgettext --files-from=locale/POTFILES.in --from-code=UTF-8 \
--output=locale/frontend.pot || exit 1
#--copyright-holder="Zabbix SIA" --no-wrap --sort-output \
#--add-comments="GETTEXT:" --keyword=_n:1,2 --keyword=_s \
#--keyword=_x:1,2c --keyword=_xn:1,2,4c || exit 1

cd locale
#--sort-by-file

sed -i 's/^#, php-format/#, c-format/' frontend.pot
echo "Merging new strings in po files..."

for translation in */LC_MESSAGES/frontend.po; do
	echo -n "$translation" | cut -d/ -f1
	# fuzzy matching provides all kinds of interesting results, for example,
	# "NTLM authentication" is translated as "LDAP-Authentifizierung" - thus
	# it is disabled
	msgmerge --no-fuzzy-matching --no-wrap --update \
--backup=off "$translation" frontend.pot
	# dropping obsolete strings
	msgattrib --no-obsolete --no-wrap --sort-output $translation -o $translation
done

for translation in */LC_MESSAGES/frontend.po; do
	echo -ne "$translation\t"
	# setting output file to /dev/null so that unneeded messages.mo file
	# is not created
	msgfmt --use-fuzzy -c --statistics -o /dev/null $translation
done
