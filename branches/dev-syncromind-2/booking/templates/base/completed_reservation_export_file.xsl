<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<div action="" method="POST" class="pure-form pure-form-aligned" id="form" name="form" >
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="export_file/tabs"/>
			<div id="export_file" class="booking-container">
				<h1>
					<xsl:value-of select="export_file/id"/> (<xsl:value-of select="export_file/type"/>)</h1>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Type')" />
					</label>
					<span>
						<xsl:value-of select="export_file/type"/>
					</span>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Total Items')" />
					</label>
					<span>
						<xsl:value-of select="export_file/total_items"/>
					</span>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Total Cost')" />
					</label>
					<span>
						<xsl:value-of select="export_file/total_cost"/>
					</span>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Created')" />
					</label>
					<span>
						<xsl:value-of select="export_file/created_on"/>
					</span>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Created by')" />
					</label>
					<span>
						<xsl:value-of select="export_file/created_by_name"/>
					</span>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<button class="pure-button pure-button-primary">
				<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="export_file/download_link"/>"</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Download')" />
			</button>
		</div>
	</div>
	<!--h4><xsl:value-of select="php:function('lang', 'Invoice Data Exports')" /></h4>
	<div id="completed_reservation_exports_container"/-->
		
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'ID', 'Building', 'Season', 'From', 'To')"/>;
	</script>
</xsl:template>
