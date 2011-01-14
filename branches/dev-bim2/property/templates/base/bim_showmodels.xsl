	<xsl:template match="modelData">
		<div> The Model Data </div>
		<table>
			<tr>
				<th>Database id</th>
				<th>Name</th>
				<th>creationDate</th>
				<th>fileSize</th>
				<th>fileName</th>
				<th>usedItemCount</th>
				<th>vfsFileId</th>
				<th>used</th>
			</tr>
		 <xsl:for-each select="models">
		    <tr>
		      <td><xsl:value-of select="databaseId"/></td>
		      <td><xsl:value-of select="name"/></td>
		       <td><xsl:value-of select="creationDate"/></td>
		       <td><xsl:value-of select="fileSize"/></td>
		       <td><xsl:value-of select="fileName"/></td>
		       <td><xsl:value-of select="usedItemCount"/></td>
		       <td><xsl:value-of select="vfsFileId"/></td>
		       <td><xsl:value-of select="used"/></td>
		    </tr>
    	</xsl:for-each>
    	</table>
	</xsl:template>
