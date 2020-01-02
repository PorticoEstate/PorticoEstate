
<!-- $Id: files.xsl 15892 2016-10-24 13:50:59Z sigurdne $ -->

<xsl:template name="multi_upload">
	<xsl:apply-templates select="multi_upload"/>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="multi_upload">
	<xsl:call-template name="multi_upload_file"/>
</xsl:template>


<!-- New template-->
<xsl:template xmlns:php="http://php.net/xsl" name="file_upload">
	<xsl:param name="section" />
	<xsl:param name="class" />
	
	<div class="pure-control-group">
		<xsl:choose>
			<xsl:when test="multiple_uploader=1">
				<label>
					<xsl:value-of select="php:function('lang', 'upload files')"/>
				</label>

				<xsl:choose>
					<xsl:when test="multi_upload_action">

						<xsl:call-template name="multi_upload_file_inline">

							<xsl:with-param name="class">
								<xsl:value-of select="$class"/>
							</xsl:with-param>
							<xsl:with-param name="multi_upload_action">
								<xsl:value-of select="multi_upload_action"/>
							</xsl:with-param>
							<xsl:with-param name="section">
								<xsl:value-of select="$section"/>
							</xsl:with-param>
						</xsl:call-template>

					</xsl:when>
					<xsl:otherwise>
						<a href="javascript:fileuploader('{$section}')" class="pure-button">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'upload multiple files')"/>
							</xsl:attribute>
							<i class="fa fa-upload" aria-hidden="true"></i>
							<xsl:text> </xsl:text>
							<xsl:value-of select="php:function('lang', 'upload multiple files')"/>
						</a>
					</xsl:otherwise>

				</xsl:choose>

			</xsl:when>
			<xsl:otherwise>
				<label>
					<xsl:value-of select="php:function('lang', 'upload files')"/>
				</label>
				<input type="file" name="file" size="40" >
					<xsl:choose>
						<xsl:when test="$class != ''">
							<xsl:attribute name="class">
								<xsl:value-of select="$class"/>
							</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="class">
								<xsl:text>pure-input-1-2</xsl:text>
							</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'Select file to upload')"/>
					</xsl:attribute>
				</input>
			</xsl:otherwise>
		</xsl:choose>
	</div>
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
