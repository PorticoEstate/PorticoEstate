	<xsl:template match="bimitems">
		<div> Objects </div>
		<div class="showModels">
			<table id="bimItems">
				<tr>
					<th>Database id</th>
					<th>Guid</th>
					<th>type</th>
				</tr>
				<xsl:for-each select="bimItems/item">
					<tr>
						<td><xsl:value-of select="databaseId"/></td>
						<td class="guid"><xsl:value-of select="guid"/></td>
						<td><xsl:value-of select="type"/></td>
					</tr>
				</xsl:for-each>
			</table>
		</div>
		<script type="text/javascript">
		if (typeof YUI != 'undefined') {
			YUI().use('node-base', function(Y) {
				Y.on("load", doDelegateViewItem);
			}); 
		}
		</script>
	</xsl:template>
