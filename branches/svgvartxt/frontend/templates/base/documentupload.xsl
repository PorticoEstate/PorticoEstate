<xsl:template match="documentupload_data" xmlns:php="http://php.net/xsl">
   	<div class="yui-navset" id="ticket_tabview">
        <xsl:value-of disable-output-escaping="yes" select="tabs" />
		<div class="yui-content">
		<h3>Vi skal laste opp greier!</h3>
			<img src="frontend/templates/base/images/32x32/page_white.png" class="list_image"/><br/>
			file: <xsl:value-of select="file"/><br/>
			test: <xsl:value-of select="test"/><br/>
			fn: <xsl:value-of select="filename"/><br/>
			stored: <xsl:value-of select="storage"/><br/>
			success: <xsl:value-of select="success"/><br/>
		    <form ENCTYPE="multipart/form-data" name="uploadform" method="post" action="{form_action}">
		    	<dl>
		    		<dt><input type="file" name="help_filename" id="help_filename"/></dt>
		    		<dt><input type="submit" value="Last opp" name="file_upload"/></dt>
		    	</dl> 
		    </form>
		</div>
	</div>
</xsl:template>