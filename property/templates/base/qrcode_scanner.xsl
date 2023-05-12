
<!-- $Id$ -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">


	<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="">
		<fieldset>
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'location')"/>
				</label>
				<input type="text" id="filter_location" name="filter_location" readonly="true" class="pure-input-1-2">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'location')"/>
					</xsl:attribute>
				</input>
			</div>
			<div style="width: 500px" id="reader_location"></div>

			
			<div class="pure-control-group">
				<input type="button" id="btn_search" name="btn_search" size="40">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'search')"/>
					</xsl:attribute>
				</input>			
			</div>
			
		</fieldset>
	</form>

</xsl:template>
