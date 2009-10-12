#!/bin/bash
# $Id$ 

#/**
#  * installscript for APACHE with PHP, IMAP, POSTGRESQL, MYSQL, LIBXML, XSLT, FREEDTS(MSSQL) and EACCELERATOR
#  * 
#  * 
#  * Download all tarballs to one directory(here: '/opt/web') and place this script in the same place
#  * 
#  * NOTE: Do not add spaces after bash variables.
#  *
#  * @author            Sigurd Nes <Sigurdne (inside) online (dot) no>
#  * @version           Release-1.0.0
#  */

##############################
# should be edited
##############################

#/**
#  * Name of the freetds package e.g freetds-stable.tgz
#  * 
#  * @var               string FREETDS, FREETDSTAR
#  * Download: http://www.freetds.org/software.html
#  */
FREETDSTAR="freetds-stable.tgz"
FREETDS="freetds-0.82"

# Download: http://xmlsoft.org/downloads.html
LIBXMLTAR="libxml2-2.7.3.tar.gz"
LIBXML="libxml2-2.7.3"

LIBXSLTAR="libxslt-1.1.24.tar.gz"
LIBXSL="libxslt-1.1.24"

# Download: ftp://ftp.cac.washington.edu/imap/
IMAPTAR="imap-2007e.tar.Z"
IMAP="imap-2007e"

PHP_PREFIX="/usr/local"

#/**
#  * Name of the APACHE tarball e.g httpd-2.2.6.tar.gz
#  * 
#  * @var               string APACHE, APACHETAR
#  * Download: http://php.net/
#  */
APACHETAR="httpd-2.2.12.tar.gz"
APACHE="httpd-2.2.12"

#/**
#  * Name of the PHP tarball e.g php-5.2.tar.gz
#  * 
#  * @var               string PHP, PHPTAR
#  * Download: http://httpd.apache.org/
#  */
PHPTAR="php-5.3.0.tar.bz2"
PHP="php-5.3.0"

#/**
#  * Name of the EACCELERATOR tarball e.g eaccelerator-0.9.5.tar.bz2
#  * 
#  * @var               string EACCELERATOR, EACCELERATORTAR
#  * Download: http://eaccelerator.net/
#  */
EACCELERATORTAR="eaccelerator-0.9.6-rc1.tar.bz2"
EACCELERATOR="eaccelerator-0.9.6-rc1"

# APC as Alternative:
# Download: http://pecl.php.net/package/APC
# APCTAR="APC-3.1.2.tgz"
# APC="APC-3.1.2"


# clean up from previous

rm $FREETDS -rf &&\
rm $LIBXML -rf &&\
rm $LIBXSL -rf &&\
rm $IMAP -rf &&\
rm $PHP -rf &&\
rm $APC -rf &&\
rm $APACHE -rf &&\

# perform the install

tar -xzf $FREETDSTAR &&\
tar -xzf $LIBXMLTAR &&\
tar -xzf $LIBXSLTAR &&\
gunzip -c $IMAPTAR | tar xf - &&\
tar -xzf $APACHETAR &&\
bunzip2 -c $PHPTAR | tar xvf -&&\
tar -xzf $APCTAR &&\
cd $FREETDS &&\
./configure --prefix=/usr/local/freetds --with-tdsver=8.0 --enable-msdblib\
--enable-dbmfix --with-gnu-ld --enable-shared --enable-static &&\
gmake &&\
gmake install &&\
touch /usr/local/freetds/include/tds.h &&\
touch /usr/local/freetds/lib/libtds.a &&\
cd ../$IMAP &&\
make lmd SSLTYPE=unix.nopwd IP6=4 &&\
ln -s c-client include &&\
mkdir lib &&\
cd lib &&\
ln -s ../c-client/c-client.a libc-client.a &&\
cd ../../$LIBXML &&\
./configure &&\
make &&\
make install &&\
cd ../$LIBXSL &&\
./configure &&\
make &&\
make install &&\
cd ../$APACHE/srclib/apr &&\
./configure --prefix=/usr/local/apr-httpd/ &&\
make &&\
make install &&\
# Build and install apr-util 1.2
cd ../apr-util &&\
./configure --prefix=/usr/local/apr-util-httpd/\
 --with-apr=/usr/local/apr-httpd/ &&\
make &&\
make install &&\
# Configure httpd
cd ../../ &&\
./configure --with-apr=/usr/local/apr-httpd/\
 --with-apr-util=/usr/local/apr-util-httpd/\
 --with-mpm=prefork\
 --enable-so\
 --enable-deflate\
 --enable-headers &&\
make &&\
make install &&\
cd ../$PHP &&\
export LDFLAGS=-lstdc++ &&\
./configure --with-imap=/opt/web/$IMAP\
 --with-imap-ssl\
 --with-sybase-ct=/usr/local/freetds\
 --with-apxs2=/usr/local/apache2/bin/apxs\
 --with-xsl\
 --with-zlib\
 --with-pspell\
 --with-jpeg-dir=/usr/lib\
 --with-png-dir=/usr/lib\
 --with-freetype-dir=/usr/lib\
 --with-gd\
 --enable-ftp\
 --with-pgsql\
 --with-mysql\
 --enable-shmop\
 --enable-sysvsem\
 --enable-sysvshm\
 --enable-calendar\
 --enable-pdo\
 --with-pdo-sqlite\
 --with-sqlite\
 --with-pdo-pgsql\
 --with-openssl\
 --enable-mbstring\
 --with-mcrypt\
 --enable-soap\
 --with-xmlrpc &&\
make &&\
make install &&\
cd ../$EACCELERATOR &&\
$PHP_PREFIX/bin/phpize &&\
./configure --enable-eaccelerator=shared --with-php-config=$PHP_PREFIX/bin/php-config &&\
make &&\
make install &&\
mkdir /tmp/eaccelerator &&\
chmod 0777 /tmp/eaccelerator


#cd ../$APC &&\
#$PHP_PREFIX/bin/phpize &&\
#./configure --enable-apc-mmap --with-apxs --with-php-config=$PHP_PREFIX/bin/php-config &&\
#make &&\
#make install

# vim: set expandtab :
