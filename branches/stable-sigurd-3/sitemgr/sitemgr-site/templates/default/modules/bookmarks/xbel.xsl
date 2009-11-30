<?xml version="1.0" encoding="ISO-8859-1"?>

<!--
    $Id$
-->

<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns="http://www.w3.org/TR/xhtml1/strict">

 <xsl:output method="html" indent="yes" encoding="us-ascii"/>
 <xsl:param name="blockid" />

 <!-- XBEL ========================================================-->
 <xsl:template match="xbel">
  <script type='text/javascript'>
// the whole thing only works in a DOM capable browser or IE 4*/
// the functions are postfixed  by the blockid so that their names do not conflict 
// with other modules that use the same tree menu
function add<xsl:value-of select="$blockid"/>(catid)
{
	document.cookie = 'block[<xsl:value-of select="$blockid"/>][expanded][' + catid + ']=';
}

function remove<xsl:value-of select="$blockid"/>(catid)
{
	var now = new Date();
	document.cookie = 'block[<xsl:value-of select="$blockid"/>][expanded][' + catid + ']=; expires=' + now.toGMTString();
}

function toggle<xsl:value-of select="$blockid"/>(image, catid)
{
	if (document.getElementById)
	{ //DOM capable
		styleObj = document.getElementById(catid);
	}
	else //we're helpless
	{
	return 
	}

	if (styleObj.style.display == 'none')
	{
		add<xsl:value-of select="$blockid"/>(catid);
		image.src = 'images/tree_collapse.gif';
		styleObj.style.display = 'block';
	}
	else
	{
		remove<xsl:value-of select="$blockid"/>(catid);
		image.src = 'images/tree_expand.gif';
		styleObj.style.display = 'none';
	}
}
</script>
  <table border="0" cellspacing="0" cellpadding="0">
   <xsl:apply-templates select="folder"/>
  </table>
 </xsl:template>

 <!-- FOLDER ======================================================-->
 <xsl:template match="folder">
  <tr>
   <td>
	<img>
	 <xsl:attribute name="src">
	  <xsl:text>images/tree_</xsl:text>
	  <xsl:choose>
	   <xsl:when test="@folded = 'yes'">
		<xsl:text>expand</xsl:text>
	   </xsl:when>
	   <xsl:otherwise>
		<xsl:text>collapse</xsl:text>
	   </xsl:otherwise>
	  </xsl:choose>
	  <xsl:text>.gif</xsl:text>
	 </xsl:attribute>
	 <xsl:attribute name="onclick">
	  <xsl:text>toggle</xsl:text><xsl:value-of select="$blockid"/><xsl:text>(this, '</xsl:text>
	  <xsl:value-of select="@id"/>
	  <xsl:text>')</xsl:text>
	 </xsl:attribute>
	</img>
   </td>
	<td>
	 <b>
	  <xsl:value-of select="title"/>
	</b>
   </td>
  </tr>
  <xsl:if test="folder|bookmark">
   <tr>
	<td></td>
	<td>
	 <table border="0" cellspacing="0" cellpadding="0" id="{@id}">
	  <xsl:attribute name="style">
	   <xsl:text>display:</xsl:text>
	   <xsl:choose>
	   <xsl:when test="@folded = 'yes'">
		<xsl:text>none</xsl:text>
	   </xsl:when>
	   <xsl:otherwise>
		<xsl:text>block</xsl:text>
	   </xsl:otherwise>
	  </xsl:choose>
	 </xsl:attribute>
	  <xsl:apply-templates select="bookmark"/>
	  <xsl:apply-templates select="folder"/>
	 </table>
	</td>
   </tr>
  </xsl:if>
 </xsl:template>

  <!-- BOOKMARK ====================================================-->
 <xsl:template match="bookmark">
  <!-- !!! we ignore <info> here! -->
  <tr>
   <td colspan="2">
      <a href="{@href}" target="_bookmark">
        <xsl:choose>
          <xsl:when test="string(title) != ''">
            <xsl:value-of select="title"/>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="@href"/>
          </xsl:otherwise>
        </xsl:choose>
      </a>
     </td>
  </tr>
  <tr>
   <td colspan="2" style="padding:3mm"><xsl:value-of select="desc"/></td>
  </tr>
 </xsl:template>
</xsl:stylesheet>