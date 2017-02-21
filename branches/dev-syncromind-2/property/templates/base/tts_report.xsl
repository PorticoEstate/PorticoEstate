
<!-- $Id$ -->

<!-- New template-->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js"></script>

	<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>
			<div id="report">
				<fieldset>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'from')"/>
						</label>
						<input type="text" id="filter_start_date" name="values[start_date]" size="10" value="{start_date}" readonly="readonly" onMouseout="window.status='';return true;">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_finnish_date_statustext"/>
							</xsl:attribute>
						</input>
						<label>
							<xsl:value-of select="php:function('lang', 'to')"/>
						</label>
						<input type="text" id="filter_end_date" name="values[end_date]" size="10" value="{end_date}" readonly="readonly" onMouseout="window.status='';return true;">
							<xsl:attribute name="title">
								<xsl:value-of select="lang_finnish_date_statustext"/>
							</xsl:attribute>
						</input>
						<label>
							<xsl:value-of select="php:function('lang', 'type')"/>
						</label>
						<select id='type' name="type">
							<option value="1"><xsl:value-of select="php:function('lang', 'categories')"/></option>
							<option value="2"><xsl:value-of select="php:function('lang', 'status')"/></option>
						</select>
					</div>

					<div class="pure-control-group">
						<label></label>
						<button id="btn_search" type="button" class="pure-button pure-button-primary">
							<xsl:value-of select="php:function('lang', 'search')"/>
						</button>
						<img src="{image_loader}" class="processing" align="absmiddle"></img>
					</div>
					
					<div id="canvas-holder" style="width:50%">
						<canvas id="chart-area" />
					</div>
				</fieldset>
			</div>
		</div>
	</form>

</xsl:template>
