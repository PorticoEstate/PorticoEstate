
<!-- $Id$ -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

	<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="">
		<fieldset>
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'from')"/>
				</label>
				<input type="text" id="filter_start_date" name="filter_start_date" size="10" value="{start_date}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_finnish_date_statustext"/>
					</xsl:attribute>
				</input>
			</div>

			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'to')"/>
				</label>
				<input type="text" id="filter_end_date" name="filter_end_date" size="10" value="{end_date}">
					<xsl:attribute name="title">
						<xsl:value-of select="lang_finnish_date_statustext"/>
					</xsl:attribute>
				</input>
			</div>
			
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'type')"/>
				</label>
				<select id='type' name="type">
					<option value="1"><xsl:value-of select="php:function('lang', 'categories')"/></option>
					<option value="2"><xsl:value-of select="php:function('lang', 'status')"/></option>
				</select>

				<input type="button" id="btn_search" name="btn_search" size="40">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'search')"/>
					</xsl:attribute>
				</input>			
				<img src="{image_loader}" class="processing" align="absmiddle"></img>	
			</div>
			
			<div id="canvas-holder" style="width:80%; display:inline-block;">
				<canvas style="display:inline-block;" id="chart-area" />
			</div>
		</fieldset>
	</form>

</xsl:template>
