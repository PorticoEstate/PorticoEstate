<!-- $Id: $ -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

<div id="main_content">

	<h1><xsl:value-of select="php:function('lang','Activity')" /></h1>

  <div id="activity" class="content-wrp">
	  <xsl:call-template name="datatable" />
  </div>
</div>
</xsl:template>
