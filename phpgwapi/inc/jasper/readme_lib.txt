
jasperreports-X.Y.Z-project.tar.gz from
http://sourceforge.net/projects/jasperreports/files/jasperreports


Compile the source files, generate the JavaDoc API documentation, or build the distribution JAR files.
Execute the Ant tasks declared in the build.xml file found in the root directory of the project tree (type ant –p at the jasperreports-{ver} root directory).

Install ant: 
$ sudo apt install ant ivy

download apache-ivy-2.5.0-bin.zip from http://ant.apache.org/ivy/, unzipped it, and copy ivy-2.5.0.jar to <ANT_HOME>\lib.

Compile:
$ ant alljars -autoproxy
Retrieve libs:
$ ant retrievelibs -autoproxy


jasperreports-X.Y.Z.jar and jasperreports-javaflow-X.Y.Z.jar from /dist/
+ all jar-files from  /lib/


database connectors from db-system sites
https://dev.mysql.com/downloads/connector/j/
https://jdbc.postgresql.org/


Probably not necesarry:
	poi-X.0-final-YYYYYYYY.jar from http://poi.apache.org/
	iText-X.X.X.jar from http://itextpdf.com/
	
chmod 644  on .jar and .class in phpgwapi/jasper/lib/ and phpgwapi/jasper/bin/


