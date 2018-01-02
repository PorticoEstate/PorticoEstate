	<xsl:template match="uploadResult">
		<div class="bimObject">
			<h1>Upload Bim file Result</h1>
		<xsl:variable name="showModelsLink">
			<xsl:value-of select="linkToModels"/>
		</xsl:variable>
		<xsl:variable name="uploadLink">
			<xsl:value-of select="linkToUpload"/>
		</xsl:variable>
		<xsl:variable name="error">
			<xsl:value-of select="error"/>
		</xsl:variable>
		<xsl:variable name="errorMessage">
			<xsl:value-of select="errorMessage"/>
		</xsl:variable>
			<xsl:choose>
			  <xsl:when test="$error = 1">
				<p>
					<strong>An error has occurred</strong>
				</p>
				<p>
					<strong>Error:</strong>
					<xsl:value-of select="$errorMessage" />
				</p>
				<p>
					<a href="{$uploadLink}">Upload file</a>
				</p>
			  </xsl:when>
			  <xsl:otherwise>
			    <p>Upload success!</p>
				<p>Redirecting</p>
				<p>If you are not promptly redirected, please click <a href="{$showModelsLink}">here</a></p>
				<script type="text/javascript">
					function showModelsPage(){
					    window.location = '<xsl:value-of select="$showModelsLink" />';
					}
					setTimeout('showModelsPage()', 3000);
				</script>
			  </xsl:otherwise>
			</xsl:choose>
			
			
		</div>
</xsl:template>
	