#!/bin/zsh
if [ !  -f dist/l10n.sh ]; then
	echo "Run this script from within the gregarius directory!" >&2
  exit 1
fi

POS=`find intl -name LC_MESSAGES -type d`
POFILE=messages.po
MOFILE=messages.mo
for PO in $POS; do
	echo "updating $PO/$POFILE"
	touch $PO/$POFILE
	find . -name \*.php | xargs xgettext -o $PO/$POFILE --no-wrap -j -lPHP -k__
	msgfmt -o $PO/$MOFILE $PO/$POFILE
done

