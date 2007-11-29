#!/bin/bash
# $Id: install.apache.sh,v 1.11 2007/09/09 19:30:52 sigurdne Exp $ 

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
#  */
FREETDSTAR="freetds-stable.tgz"
FREETDS="freetds-0.64"

LIBXMLTAR="libxml2-2.6.30.tar.gz"
LIBXML="libxml2-2.6.30"

LIBXSLTAR="libxslt-1.1.22.tar.gz"
LIBXSL="libxslt-1.1.22"

IMAPTAR="imap-2006k.DEV.SNAP-0709051605.tar.Z"
IMAP="imap-2006k.DEV.SNAP-0709051605"

PHP_PREFIX="/usr/local"

#/**
#  * Name of the APACHE tarball e.g httpd-2.2.6.tar.gz
#  * 
#  * @var               string APACHE, APACHETAR
#  */
APACHETAR="httpd-2.2.6.tar.gz"
APACHE="httpd-2.2.6"

#/**
#  * Name of the PHP tarball e.g php-5.2.tar.gz
#  * 
#  * @var               string PHP, PHPTAR
#  */
PHPTAR="php-5.2.5.tar.bz2"
PHP="php-5.2.5"

#/**
#  * Name of the EACCELERATOR tarball e.g eaccelerator-0.9.5.tar.bz2
#  * 
#  * @var               string EACCELERATOR, EACCELERATORTAR
#  */
EACCELERATORTAR="eaccelerator-0.9.5.2.tar.bz2"
EACCELERATOR="eaccelerator-0.9.5.2"

# clean up from previous

rm $FREETDS -rf &&\
rm $LIBXML -rf &&\
rm $LIBXSL -rf &&\
rm $IMAP -rf &&\
rm $PHP -rf &&\
rm $EACCELERATOR -rf &&\
rm $APACHE -rf &&\

# perform the install

tar -xzf $FREETDSTAR &&\
tar -xzf $LIBXMLTAR &&\
tar -xzf $LIBXSLTAR &&\
gunzip -c $IMAPTAR | tar xf - &&\
tar -xzf $APACHETAR &&\
bunzip2 -c $PHPTAR | tar xvf -&&\
bunzip2 -c $EACCELERATORTAR | tar xvf -&&\
cd $FREETDS &&\
./configure --prefix=/usr/local/freetds --with-tdsver=7.0 --enable-msdblib\
--enable-dbmfix --with-gnu-ld --enable-shared --enable-static &&\
gmake &&\
gmake install &&\
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
 --enable-mail\
 --with-xml\
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
 --enable-calendar &&\
make &&\
make install &&\
cd ../$EACCELERATOR &&\
$PHP_PREFIX/bin/phpize &&\
./configure --enable-eaccelerator=shared --with-php-config=$PHP_PREFIX/bin/php-config &&\
make &&\
make install &&\
mkdir /tmp/eaccelerator &&\
chmod 0777 /tmp/eaccelerator

# vim: set expandtab :
