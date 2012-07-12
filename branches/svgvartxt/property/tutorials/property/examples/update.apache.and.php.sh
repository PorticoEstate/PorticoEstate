#!/bin/bash
# $Id: update.apache.and.php.sh,v 1.2 2005/01/13 17:00:04 ceb Exp $ 

#/**
#  * Updatescript for APACHE2 with PHP XSLT FREEDTS and MMCACHE
#  * 
#  * XSLT and FREEDTS are assumed to be installed
#  * 
#  * Download all tarballs to one directory and place this script in the same place
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
#  * Name of the APACHE tarball e.g httpd-2.0.52.tar.gz
#  * 
#  * @var               string APACHE2, APACHE2TAR
#  */
APACHE2TAR="httpd-2.0.52.tar.gz"
APACHE2="httpd-2.0.52"

#/**
#  * Name of the PHP tarball e.g php-4.3.9.tar.gz
#  * 
#  * @var               string PHP, PHPTAR
#  */
PHPTAR="php-4.3.9.tar.gz"
PHP="php-4.3.9"

#/**
#  * Name of the MMCACHE tarball e.g turck-mmcache-2.4.6.tar.gz
#  * 
#  * @var               string MMCACHE, MMCACHETAR
#  */
MMCACHETAR="turck-mmcache-2.4.6.tar.gz"
MMCACHE="turck-mmcache-2.4.6"

# perform the update

tar -xzf $APACHE2TAR &&\
tar -xzf $PHPTAR &&\
tar -xzf $MMCACHETAR &&\
/usr/local/apache2/bin/apachectl stop &&\
cd $APACHE2 &&\
./configure --enable-so --enable-deflate --enable-headers &&\
make &&\
make install &&\
cd ../$PHP &&\
export LDFLAGS=-lstdc++ &&\
make clean &&\
./configure --with-sybase-ct=/usr/local/freetds\
 --with-apxs2=/usr/local/apache2/bin/apxs --enable-mail --enable-xslt\
 --with-xslt-sablot --with-zlib --with-pspell --with-jpeg-dir=/usr/lib\
 --with-png-dir=/usr --with-freetype-dir=/usr/lib --with-gd --enable-ftp &&\
make &&\
make install &&\
cd ../$MMCACHE &&\
export PHP_PREFIX="/usr/local" &&\
$PHP_PREFIX/bin/phpize &&\
./configure --enable-mmcache=shared --with-php-config=$PHP_PREFIX/bin/php-config &&\
make &&\
make install &&\
/usr/local/apache2/bin/apachectl start

# vim: set expandtab :
