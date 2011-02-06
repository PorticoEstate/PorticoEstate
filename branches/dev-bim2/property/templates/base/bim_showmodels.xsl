	<xsl:template match="modelData">
	<div> The Model Data </div>
	<!--<table id="bimModelList">
		<tr>
			<th>Database id</th>
			<th>Name</th>
			<th>creationDate</th>
			<th>fileSize</th>
			<th>fileName</th>
			<th>usedItemCount</th>
			<th>vfsFileId</th>
			<th>used</th>
			<th></th>
		</tr>
		<xsl:for-each select="models">
			<tr>
				<td>
					<xsl:value-of select="databaseId" />
				</td>
				<td>
					<xsl:value-of select="name" />
				</td>
				<td>
					<xsl:value-of select="creationDate" />
				</td>
				<td>
					<xsl:value-of select="fileSize" />
				</td>
				<td>
					<xsl:value-of select="fileName" />
				</td>
				<td>
					<xsl:value-of select="usedItemCount" />
				</td>
				<td>
					<xsl:value-of select="vfsFileId" />
				</td>
				<td>
					<xsl:value-of select="used" />
				</td>
				<td>
					<button>
						<xsl:attribute name="value">

							<xsl:value-of select="databaseId" />

						</xsl:attribute>
						Delete
					</button>
				</td>
			</tr>
		</xsl:for-each>
	</table>

	-->
	<div class="showModels">
		<button onClick="reloadModelList()">Reload model list</button>
		<table id="bimModelList2">
			<tr>
				<th>Database id</th>
				<th>Name</th>
				<th>creationDate</th>
				<th>fileSize</th>
				<th>fileName</th>
				<th>usedItemCount</th>
				<th>vfsFileId</th>
				<th>used</th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</table>
		<div id="modelsLoader">
			<xsl:variable name="loadingImage"><xsl:value-of select="loadingImage"/></xsl:variable>
			<img src="{$loadingImage}" />
		</div>
	</div>
	<script type="text/javascript">
		// doDelegate();
		
		if (typeof YUI != 'undefined') {
		YUI().use('node-base', function(Y) {
		Y.on("load",  getModelList);
		//Y.on("load", doDelegateDeleteModel);
		doDelegateDeleteModel();
		Y.on("load", doDelegateLoadModel);
		Y.on("load", doDelegateModelInfo);
		Y.on("load", doDelegateModelView);
		}); 
		}
	</script>
</xsl:template>
