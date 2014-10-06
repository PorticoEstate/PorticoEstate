OVERVIEW

This module allows users of phpGroupware to access their data using the SyncML
protocol developed by Open Mobile Alliance. Its main purpose is to enable users
to keep copies of a data set synchronized on multiple devices.

This module is the result of Johan Gunnarsson's participation in Google Summer
of Code(tm) 2007 under the mentorship of Dave Hall.

GLOSSARY

Source
	A phpGroupware module with a compatible IPC interface.

Database
	A user<->source relation. Must have a unique name ("uri"). Each user
	must have one database per source her/she wants to be able to synchronize.

Channel
	A client<->database relation. Each client synchronizing a database gets
	its own channel.

SETUP MODULE

Run the regular phpGroupware Application Setup to setup database tables.

SETUP SOURCES

Source modules must have a compatible IPC class. The required methods are:

* addData
* getData
* getIdList
* removeData
* replaceData

Every method must function as described in class.ipc_.inc.php.

To make the SyncML module aware of the source, insert a row looking something
like this in the syncml_sources table.

-- SQL query --
INSERT INTO `phpgw_syncml_sources` ( `name` , `modulename` , `mimetype`,
	`mimeversion` )
VALUES ( 'the notes app', 'notes', 'text/plain', '');
---------------

Field       Description
-----       -----------
modulename  Name of module, for example "notes" if you want to add the notes
            module as source.
name        Descriptive name of the source to be displayed in UIs and similar.
mimetype    The perfered mime type to be used when exporting data from the
            application. Must be supported by IPC interface.
mimeversion The perfered mime type verision to be used when exporting data from
            the application. Must be supported by IPC interface. Leave blank
            it not supply version.

The only modules known to work correctly as sources (as of Aug 20) is Notes
and Addressbook.

SETUP DATABASES

Each user must have its own databases connected to the source. It's done by
inserting a rows in syncml_databases.

-- SQL query --
INSERT INTO `phpgw_syncml_databases` ( `database_uri`,
	`source_id`, `credential_required`, `credential_hash`, `account_id`)
VALUES('my-notes-collection', 1, 0, '', 3);
---------------

Field               Description
-----               -----------
database_uri        Name of the database clients use to identify it.
source_id           ID of source to connect this database to. Sources are
                    specificed in syncml_sources table.
credential_required Integer indicating if this database requires credentials.
                    This is not supported yet.
credential_hash     Hash of credentials used to authenticate users if this
                    database requires credentials. Value of this field is
                    calculated using base64(md5(username ":" password)).
account_id          ID of user account this database is connected to.

RUN

When it's propely setup (see sections above) it should run. It supports SyncML
versions up to 1.1.2. Support for SyncML 1.2 is planned but not yet
implemented.

There is support for WBXML, but you need the PECL WBXML package available
from here: http://pecl.php.net/package/wbxml.

URI to SyncML server is http://host/path/to/phpgw/syncml/syncml.php.

TIPS

* When using addressbook as source application error level NOTICE has to be
  off.

AUTHOR

Johan Gunnarsson <johang@phpgroupware.org>

CREDTIS

Thanks to my mentor Dave Hall and Google.

