#!/bin/sh
# Simple bash script to test if jasper "works"

JARS=../../../phpgwapi/inc/jasper/lib
CLASSPATH="../../../phpgwapi/inc/jasper/bin"$(find $JARS -name *.jar -exec printf :{} ';')

#echo $CLASSPATH
#java -Djava.awt.headless=true -cp "$CLASSPATH" XmlJasperInterface

java -Djava.awt.headless=true -cp "$CLASSPATH" XmlJasperInterface -o"pdf" -f"./tilstand.jasper" -x"//Record" < "./tilstand.xml" > "report.pdf"


# Build classpath
#java -Djava.awt.headless=true -cp "../bin/
#XmlJasperInterface -opdf -f../templates/tilstand.jasper
