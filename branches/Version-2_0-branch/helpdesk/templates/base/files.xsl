
<!-- $Id: files.xsl 15892 2016-10-24 13:50:59Z sigurdne $ -->

<xsl:template name="multi_upload">
	<xsl:apply-templates select="multi_upload"/>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="multi_upload">
	<xsl:call-template name="multi_upload_file"/>
</xsl:template>


<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" name="file_upload">
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'upload files')"/>
		</label>
		<input type="file" name="file" size="40" class="pure-input-1-2" >
			<xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'Select file to upload')"/>
			</xsl:attribute>
		</input>
	</div>
	<xsl:choose>
		<xsl:when test="multiple_uploader!=''">
			<div class="pure-control-group">
				<label>
					<a href="javascript:fileuploader()">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'upload multiple files')"/>
						</xsl:attribute>
						<xsl:value-of select="php:function('lang', 'upload multiple files')"/>
					</a>
				</label>
			</div>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" name="jasper_upload">
	<div class="pure-control-group">
		<label>
			<xsl:value-of select="php:function('lang', 'jasper upload')"/>
		</label>
		<input type="file" name="jasperfile" size="40">
			<xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'upload a jasper definition file')"/>
			</xsl:attribute>
		</input>
	</div>
</xsl:template>
