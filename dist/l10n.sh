#!/bin/zsh
if [ !  -f dist/l10n.sh ]; then
	echo "Run this script from within the gregarius directory!" >&2
  exit 1
fi

POS=`find intl -name LC_MESSAGES -type d`
POT=intl/messages.pot
touch $POT
echo "Updating template: $POT"
find . -name \*.php | xargs xgettext -o $POT  -j -lPHP -k__

POFILE=messages.po
MOFILE=messages.mo
POXFILE=messages.pox

for PO in $POS; do
	echo "Updating $PO/$POFILE"
	touch $PO/$POFILE
	msgmerge -N -v --update $PO/$POFILE $POT
	echo "Building $PO/$MOFILE"	
	msgfmt -o $PO/$MOFILE $PO/$POFILE
	echo 
done

