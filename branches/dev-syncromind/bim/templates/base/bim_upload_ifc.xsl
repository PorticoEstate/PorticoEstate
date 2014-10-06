	<xsl:template match="upload">
		<div class="bimObject">
			<h1>Upload Bim file</h1>
			<xsl:variable name="form_action"><xsl:value-of select="import_action"/></xsl:variable>
			<xsl:variable name="form_field_filename"><xsl:value-of select="form_field_filename"/></xsl:variable>
			<xsl:variable name="form_field_modelname"><xsl:value-of select="form_field_modelname"/></xsl:variable>
			<form method="post" enctype="multipart/form-data" name="uploadIfc" action="{$form_action}">
				<dl>
					<dt>Upload file</dt>
					<dd><input type="file" name="{$form_field_filename}" /></dd>
					
					<dt>IFC Model name</dt>
					<dd><input type="text" name="{$form_field_modelname}" size="64"/></dd>
						
					<dt></dt>
					<dd><input type="submit" /></dd>
					
				</dl>
			</form>
		</div>
	</xsl:template>
	