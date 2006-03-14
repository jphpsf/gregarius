#!/bin/zsh
if [ $# != 1 ]; then
  echo "usage: $0 major.medium.minor" >&2
  exit 1
fi

if [ ! -d gregarius ]; then
  echo "run this script from rss's parent directory!" >&2
  exit 1
fi

tar cvfz gregarius-$1.tar.gz \
 --exclude=*CVS* \
 --exclude=dbinit.php \
 --exclude=dist \
 --exclude=favicon.ico \
 --exclude=ico2png \
 --exclude=imgwrp.php \
 --exclude=test.php \
 --exclude=about.php \
 --exclude=rss_extra.php \
 --exclude=*.\#* \
  --exclude=*.svn* \
 --exclude=*.cvsignore \
 --exclude=*.*~ \
 rss

zip -r  gregarius-$1.zip rss \
 -x \*/CVS\* \
 -x \*/dbinit.php \
 -x \*/dist\* \
 -x \*/favicon.ico \
 -x \*/rss_extra.php \
 -x \*/ico2png\* \
 -x \*/imgwrp.php \
 -x \*/about.php \
 -x \*/test.php \
 -x \*.\#\* \
  -x \*.svn\* \
 -x \*.cvsignore \
 -x \*.\*~ \
