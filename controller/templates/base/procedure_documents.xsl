<!-- $Id$ -->
<!-- document  -->

<xsl:template name="view_procedure_documents" xmlns:php="http://php.net/xsl">

<xsl:variable name="dateformat"><xsl:value-of select="dateformat" /></xsl:variable>

<div class="yui-content">
		<div id="details">
			<form enctype="multipart/form-data" action="?menuaction=controller.uidocument.add" method="POST">
				<xsl:variable name="lang_upload"><xsl:value-of select="php:function('lang', 'upload')" /></xsl:variable>
				<input type="hidden" name="procedure_id" value = "{procedure_id}" />
				<input type="hidden" name="document_type" value="1" />
				<fieldset>
					<h3><xsl:value-of select="php:function('lang','upload')" /></h3>
					<input type="file" id="file_path" name="file_path" />
					<xsl:value-of select="php:function('lang','title')" />:
					<input type="text" id="document_title" name="document_title" />
					<input type="submit" id="upload_button" value="{$lang_upload}" />
				</fieldset>
			</form>
		</div>
		<div id="details">
			<table cellpadding="10" cellspacing="10" align="left" style="margin-left: 1em;">
				<xsl:call-template name="table_header_documents"/>
				<xsl:call-template name="values_documents"/>
			</table>
		</div>
	</div>
</xsl:template>

<xsl:template name="table_header_documents">
	<tr class="th">
		<xsl:for-each select="table_header" >
			<td class="th_text" style="padding-right: 10px;">
				<xsl:value-of select="header"/>
			</td>
		</xsl:for-each>
	</tr>
</xsl:template>

<xsl:template name="values_documents">
	<xsl:for-each select="values" >
		<tr>
			<xsl:for-each select="document" >
				<xsl:variable name="doc_link"><xsl:value-of select='link'/></xsl:variable>
				<td align="left" style="padding-right: 10px;">
					<a href="{$doc_link}"><xsl:value-of select="title"/></a>
				</td>
				<td align="left" style="padding-right: 10px;">
					<xsl:value-of select="name"/>
				</td>
			</xsl:for-each>
		</tr>
	</xsl:for-each>
</xsl:template>