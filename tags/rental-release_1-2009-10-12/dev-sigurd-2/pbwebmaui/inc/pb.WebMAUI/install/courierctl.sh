#!/bin/sh
#
# courierctl.sh
# bash script to control some courier functions, this should be called with uid courier
# originally this is used by pb.WebMAUI using sudo

LOG=/var/log/courierctl
CHMOD=/bin/chmod
COURIER=/usr/lib/courier
MAILDIRMAKE=$COURIER/bin/maildirmake
DU=/usr/bin/du
FIND=/usr/bin/find
SORT=/bin/sort
TAR=/bin/tar

#echo "calling courierctl $*">>$LOG
CTLCMD=$1
shift

case "$CTLCMD" in
    createmaildir)
        # command createmaildir, parameter is maildir to create,
        if ! test -d $1
        then
            mkdir -p $1
        fi

        if ! test -d $1/Maildir
        then
            $MAILDIRMAKE $1/Maildir
        fi
        ;;

    listmaildir)
        # command listmaildir, parameter is maildir to list
        if test -e $1/Maildir/ls.out
        then
            #faked
            cat $1/Maildir/ls.out
        else
            $FIND $1/Maildir -type d -maxdepth 1 -name ".*" -printf "%f\n"|$SORT
        fi
        ;;

    loadmailfilter)
        # command loadmailfilter, called with path to maildir
        cat $1/.mailfilter
        ;;

    savemailfilter)
        # command savemailfilter, called with path to maildir
        cat >$1/.mailfilter
        $CHMOD 700 $1/.mailfilter
        ;;

    loadautoreply)
        # command loadautoreply, called with path to maildir and filename for autoreply text file
        cat $1/$2
        ;;

    saveautoreply)
        # command saveautoreply, called with path to maildir and filename for autoreply text file
        cat >$1/$2
        ;;

    rmautoreplydb)
        # command rmautoreplydb, called with path to maildir and filename for autoreply text file
        rm $1/$2.db.*
        ;;

    dudomain)
        # command dudomain, called with path to domain maildirs
        if test -e $1/du.out
        then
            #faked
            cat $1/du.out
        else
            $DU -bSD $1
        fi
        ;;

    duaccount)
        # command dudomain, called with path to maildir
        if test -e $1/du.out
        then
            #faked
            cat $1/du.out
        else
            $DU -bSD $1/Maildir
        fi
        ;;

    archiveaccount)
        # command archiveaccount, called with path to domain, path to account's homedir and uid
        # stdin is an ldif file describing the account
        cat >$2/$3.ldif && $TAR -czf $1/$3.tgz -C $2 .
        ;;

    importmaildir)
        $TAR -xzf $1 -C $2
        ;;

    removeaccount)
        # command removeaccount, called with path to account's homedir
        rm -rf $1
        ;;

    createdomain)
        # command createdomain, called with domainname
        ;;

    archivedomain)
        # command archiveaccount, called with path to maildirs, path to domain and domainname
        $TAR -czf $1/$3.tgz -C $2 .
        ;;

    removedomain)
        # command deletedomain, called with path to domain maildirs and domainname
        rm -rf $1/$2
        ;;
esac
