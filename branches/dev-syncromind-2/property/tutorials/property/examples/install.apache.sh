#!/bin/bash
# $Id$

#/**
#  * installscript for APACHE with PHP, IMAP, POSTGRESQL, MYSQL, LIBXML, XSLT, FREEDTS(MSSQL), PCRE, APR, APR-UTIL
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

#PCRE from ftp://ftp.csx.cam.ac.uk/pub/software/programming/pcre/
PCRETAR="pcre-8.36.tar.gz"
PCRE="pcre-8.36"

#APR from http://apr.apache.org/
APRTAR="apr-1.5.2.tar.gz"
APR="apr-1.5.2"
APR_UTIL_TAR="apr-util-1.5.4.tar.gz"
APR_UTIL="apr-util-1.5.4"

#/**
#  * Name of the freetds package e.g freetds-stable.tgz
#  *
#  * @var               string FREETDS, FREETDSTAR
#  * Download: http://www.freetds.org/software.html
#  */
FREETDSTAR="freetds-patched.tar.gz"
FREETDS="freetds-0.95.19"

# Download: http://xmlsoft.org/downloads.html
LIBXMLTAR="libxml2-2.9.2.tar.gz"
LIBXML="libxml2-2.9.2"

LIBXSLTAR="libxslt-1.1.28.tar.gz"
LIBXSL="libxslt-1.1.28"

# Download: ftp://ftp.cac.washington.edu/imap/
IMAPTAR="imap-2007f.tar.Z"
IMAP="imap-2007f"

PHP_PREFIX="/usr/local"

#/**
#  * Name of the APACHE tarball e.g httpd-2.2.6.tar.gz
#  *
#  * @var               string APACHE, APACHETAR
#  * Download: http://php.net/
#  */
APACHETAR="httpd-2.4.16.tar.gz"
APACHE="httpd-2.4.16"

#/**
#  * Name of the PHP tarball e.g php-5.2.tar.gz
#  *
#  * @var               string PHP, PHPTAR
#  * Download: http://httpd.apache.org/
#  */
PHPTAR="php-7.0.0RC2.tar.bz2"
PHP="php-7.0.0RC2"

PHP_PREFIX="/usr/local"


#/**
#  * Oracle PDO-Support
#  * Download: http://www.oracle.com/technology/software/tech/oci/instantclient/index.html
#  */

#ORACLETAR="instantclient-basic-linux32-11.2.0.1.zip"
#ORACLE="instantclient_11_2"
#ORACLEDEVELTAR="instantclient-sdk-linux32-11.2.0.1.zip"

ORACLETAR="instantclient-basic-linux.x64-12.1.0.2.0.zip"
ORACLE="instantclient_12_1"
ORACLEDEVELTAR="instantclient-sdk-linux.x64-12.1.0.2.0.zip"

ORACLE_PDO=""

# include the oracle pdo-driver in the install
function include_oracle()
{
    echo -n "Delete /opt/$2 ? answere yes or no: "

    read svar

    if [ $svar = "yes" ];then
      echo "Ok - lets try"
      rm /opt/$2 -rf
      else
      echo "Skipp delete old"
    fi

    unzip $1
    mv $2 /opt/
    unzip $ORACLEDEVELTAR
    mv $2/sdk /opt/$2/
    export ORACLE_HOME=/opt/$2/
    ln -s /opt/$2/libclntsh.so.12.1 /opt/$2/libclntsh.so
    ln -s /opt/$2/libocci.so.12.1 /opt/$2/libocci.so
    ln -s /opt/$2/ /opt/$2/lib
}


# clean up from previous
rm $PCRE -rf &&\
rm $FREETDS -rf &&\
rm $LIBXML -rf &&\
rm $LIBXSL -rf &&\
rm $IMAP -rf &&\
rm $PHP -rf &&\
rm $EACCELERATOR -rf &&\
rm $APACHE -rf &&\
rm $ORACLE -rf &&\

# perform the install

echo -n "Include Oracle-pdo? answere yes or no: "

read svar


if [ $svar = "yes" ];then
    echo "Ok - lets try the Oracle"
    include_oracle $ORACLETAR $ORACLE $ORACLEDEVELTAR
    ORACLE_PDO=" --with-oci8=instantclient,/opt/$ORACLE/ --with-pdo-oci"
    echo $ORACLE_PDO
    else
    echo "Skipping Oracle"
fi

# include the IMAP-support in the install
function include_imap()
{
    cd $1 &&\
    make lmd SSLTYPE=unix.nopwd IP6=4 EXTRACFLAGS=-fPIC&&\
    ln -s c-client include &&\
    mkdir lib &&\
    cd lib &&\
    ln -s ../c-client/c-client.a libc-client.a &&\
    cd ../../
}

echo -n "Include IMAP support? answere yes or no: "

read svar

IMAP_CONFIG=''

if [ $svar = "yes" ];then
    echo "Ok - lets try the IMAP"
    gunzip -c $IMAPTAR | tar xf -
    include_imap $IMAP
    IMAP_CONFIG="--with-imap=/opt/web/$IMAP --with-imap-ssl"
    echo $IMAP_CONFIG
    else
    echo "Skipping IMAP"
fi

# include the MSSQL/SYBASE-support in the install
function include_mssql()
{
    cd $1 &&\
    ./configure --prefix=/usr/local/freetds --with-tdsver=8.0 --enable-msdblib\
    --enable-dbmfix --with-gnu-ld --enable-shared --enable-static &&\
    make &&\
    make install &&\
    touch /usr/local/freetds/include/tds.h &&\
    touch /usr/local/freetds/lib/libtds.a &&\
    cd ..
}

echo -n "Include MSSQL support? answere yes or no: "

read svar

MSSQL_CONFIG=''

if [ $svar = "yes" ];then
    echo "Ok - lets try the MSSQL"
    tar -xzf $FREETDSTAR
    include_mssql $FREETDS
    MSSQL_CONFIG="--with-sybase-ct=/usr/local/freetds"
    echo $MSSQL_CONFIG
    else
    echo "Skipping MSSQL"
fi

tar -xzf $APRTAR &&\
tar -xzf $APR_UTIL_TAR &&\
tar -xzf $PCRETAR &&\
tar -xzf $LIBXMLTAR &&\
tar -xzf $LIBXSLTAR &&\
tar -xzf $APACHETAR &&\
bunzip2 -c $PHPTAR | tar xvf -&&\
mv $APR $APACHE/srclib/apr &&\
mv $APR_UTIL $APACHE/srclib/apr-util &&\
cd $PCRE &&\
./configure &&\
make &&\
make install &&\
cd ../$LIBXML &&\
./configure &&\
make &&\
make install &&\
cd ../$LIBXSL &&\
./configure &&\
make &&\
make install &&\
cd ../$APACHE &&\
./configure \
 --with-included-apr\
 --with-mpm=prefork\
 --enable-so\
 --enable-deflate\
 --enable-headers\
 --enable-rewrite=shared\
 --enable-dav\
 --enable-dav-fs\
 --enable-dav-lock\
 --enable-auth-digest && \
make && \
make install && \
cd ../$PHP &&\
export LDFLAGS=-lstdc++ &&\
./configure \
 $IMAP_CONFIG\
 $MSSQL_CONFIG\
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
 --with-mysqli\
 --enable-shmop\
 --enable-sysvsem\
 --enable-sysvshm\
 --enable-calendar\
 --enable-pdo\
 --with-pdo-sqlite\
 --with-sqlite\
 --with-pdo-pgsql\
 --with-pdo-mysql\
 --with-openssl\
 --enable-mbstring\
 --with-mcrypt\
 --enable-soap\
 --with-xmlrpc \
 --with-gettext \
 --with-snmp \
 --with-curl \
 --enable-zip \
 $ORACLE_PDO &&\
make &&\
make install


#export ORACLE_HOME=/opt/instantclient_12_1/ &&\
#export LDFLAGS=-lstdc++ &&\
#./configure \
# --with-sybase-ct=/usr/local/freetds\
# --with-apxs2=/usr/local/apache2/bin/apxs\
# --with-xsl\
# --with-zlib\
# --with-pspell\
# --with-jpeg-dir=/usr/lib\
# --with-png-dir=/usr/lib\
# --with-freetype-dir=/usr/lib\
# --with-gd\
# --enable-ftp\
# --with-pgsql\
# --with-mysql\
# --with-mysqli\
# --enable-shmop\
# --enable-sysvsem\
# --enable-sysvshm\
# --enable-calendar\
# --enable-pdo\
# --with-pdo-sqlite\
# --with-sqlite\
# --with-pdo-pgsql\
# --with-pdo-mysql\
# --with-openssl\
# --enable-mbstring\
# --with-mcrypt\
# --enable-soap\
# --with-xmlrpc \
# --with-gettext \
# --with-snmp \
# --with-curl \
# --enable-zip \
# --with-oci8=instantclient,/opt/instantclient_12_1/ --with-pdo-oci

# vim: set expandtab :