  <!-- $Id$ -->
	<!-- attrib_history -->
	<xsl:template match="attrib_history">
		<div>
			<br/>
		</div>
		<!--  DATATABLE -->
		<div align="left" id="paging_0"> </div>
		<div id="datatable-container_0"/>
		<div id="contextmenu_0"/>
		<div>
			<br/>
		</div>
		<!--  DATATABLE DEFINITIONS-->
		<script type="text/javascript">
			var property_js = <xsl:value-of select="property_js"/>;
			var base_java_url = <xsl:value-of select="base_java_url"/>;
			var datatable = new Array();
			var myColumnDefs = new Array();

			<xsl:for-each select="datatable">
				datatable[<xsl:value-of select="name"/>] = [
					{
						values:<xsl:value-of select="values"/>,
						total_records: <xsl:value-of select="total_records"/>,
						is_paginator:  <xsl:value-of select="is_paginator"/>,
						permission  : <xsl:value-of select="permission"/>,
						footer:<xsl:value-of select="footer"/>
					}
				]
			</xsl:for-each>

			<xsl:for-each select="myColumnDefs">
				myColumnDefs[<xsl:value-of select="name"/>] = <xsl:value-of select="values"/>
			</xsl:for-each>

		</script>
	</xsl:template>
