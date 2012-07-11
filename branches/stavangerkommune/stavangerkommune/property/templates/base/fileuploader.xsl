  <!-- $Id$ -->
	<xsl:template xmlns:php="http://php.net/xsl" match="fileuploader">
		<div class="header">
			<h2>
				<xsl:value-of select="php:function('lang', 'fileuploader')"/>
			</h2>
		</div>
		<!--
<style>
#selectFilesLink a, #uploadFilesLink a, #clearFilesLink a {
color: #0000CC;
background-color: #FFFFFF;
}

#selectFilesLink a:visited, #uploadFilesLink a:visited, #clearFilesLink a:visited {
color: #0000CC;
background-color: #FFFFFF;
}

#uploadFilesLink a:hover, #clearFilesLink a:hover {
color: #FFFFFF;
background-color: #000000;
}
</style>
-->
		<div id="uiElements" style="display:inline;">
			<div id="uploaderContainer">
				<div id="uploaderOverlay" style="position:absolute; z-index:2"/>
				<div id="selectFilesLink" style="z-index:1">
					<input id="selectLink" type="button" name="selectLink" title="">
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'Select Files')"/>
						</xsl:attribute>
					</input>
				</div>
			</div>
			<div id="uploadFilesLink">
				<input id="uploadLink" type="button" name="uploadLink" title="" onClick="upload(); return false;">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Upload Files')"/>
					</xsl:attribute>
				</input>
			</div>
		</div>
		<!--
		<div id="simUploads"> Number of simultaneous uploads:
			<select id="simulUploads">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
			</select>
		</div>
		-->
		<div id="dataTableContainer"/>
		<script type="text/javascript">
			<xsl:value-of select="js_code"/>
		</script>
	</xsl:template>
