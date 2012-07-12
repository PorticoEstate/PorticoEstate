	<xsl:template match="modelInformation">
	<div class="bimObject">
		<h2>Model</h2>
		
		<dl>
			<dt>Name</dt>
			<dd><xsl:value-of select="model/name"/></dd>
			<dt>File name</dt>
			<dd><xsl:value-of select="model/fileName"/></dd>
			<dt>Used items</dt>
			<dd><xsl:value-of select="model/usedItemCount"/></dd>
		</dl>
		<h2>Model Information</h2>
		
		<dl>
			<dt>Author</dt>
			<dd><xsl:value-of select="information/author"/></dd>
			<dt>Authorization</dt>
			<dd><xsl:value-of select="information/authorization"/></dd>
			<dt>Change date</dt>
			<dd><xsl:value-of select="information/changeDate"/></dd>
			<dt>Description</dt>
			<dd><xsl:value-of select="information/description"/></dd>
			<dt>Organization</dt>
			<dd><xsl:value-of select="information/organization"/></dd>
			<dt>Originating system</dt>
			<dd><xsl:value-of select="information/originatingSystem"/></dd>
			<dt>Pre processor</dt>
			<dd><xsl:value-of select="information/preProcessor"/></dd>
			<dt>Val date</dt>
			<dd><xsl:value-of select="information/valDate"/></dd>
			<dt>Native schema</dt>
			<dd><xsl:value-of select="information/nativeSchema"/></dd>
		</dl>
	</div>	
	</xsl:template>
