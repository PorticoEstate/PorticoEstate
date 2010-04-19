<?xml version='1.0' encoding='ISO-8859-1' ?>

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
		version="1.0"
		xmlns="http://www.w3.org/TR/xhtml1/transitional"
		execlude-result-prefixes="#default">

<xsl:import href="/usr/local/docbook/xslt/html/chunk.xsl"/>

<xsl:param name="generate.toc">
article toc,sect1,sect2
sect1 toc
</xsl:param>

<xsl:param name="generate.section.toc.level" select="1"/>
<xsl:param name="generate.index" select="1"/>

  
<xsl:param name="base.dir" select="'../html/'"/>
  
<xsl:param name="label.from.part" select="'1'"/>

<xsl:param name="html.cleanup" select="1"/>
<xsl:param name="html.stylesheet.type">text/css</xsl:param>
<xsl:param name="html.stylesheet" select="'manual.css'"/>

<xsl:param name="toc.list.type">ul</xsl:param>

<xsl:param name="use.id.as.filename" select="'1'"/>

<xsl:param name="chunk.first.sections" select="1"/>
<xsl:param name="chunk.section.depth" select="2"/>

<xsl:param name="navig.graphics" select="0"/>

</xsl:stylesheet>
