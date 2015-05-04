  <!-- $Id$ -->
	<!-- add / edit  -->
	<xsl:template xmlns:php="http://php.net/xsl" match="data">
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dl>
					<dt>
						<xsl:call-template name="msgbox"/>
					</dt>
				</dl>
			</xsl:when>
		</xsl:choose>
		<xsl:variable name="lang_download">
			<xsl:value-of select="php:function('lang', 'download')"/>
		</xsl:variable>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>
		<form method="post" id="form" name="form" action="{$form_action}" class= "pure-form">
			<input type="hidden" name="tab" value=""/>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="deposition">
					<div class="pure-control-group">
						<label class="pure-checkbox">
							<xsl:value-of select="php:function('lang', 'deposition')"/>
							<input type="checkbox" name="values[deposition]" value="True">
								<xsl:attribute name="title">
									<xsl:value-of select="php:function('lang', 'deposition')"/>
								</xsl:attribute>
							</input>
						</label>
					</div>
					<input type="submit" class="pure-button pure-button-primary" name="values[export_deposition]" value="{$lang_download}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'save')"/>
						</xsl:attribute>
					</input>
				</div>
				<div id="reconciliation">
					<div class="pure-control-group">
						<xsl:value-of select="php:function('lang', 'periods')"/>
					</div>
					<div class="pure-control-group">
						<xsl:apply-templates select="accounting_periods/options"/>
					</div>
					<input type="submit" class="pure-button pure-button-primary" name="values[export_reconciliation]" value="{$lang_download}">
						<xsl:attribute name="title">
							<xsl:value-of select="php:function('lang', 'save')"/>
						</xsl:attribute>
					</input>			
				</div>
			</div>
		</form>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="options">
		<label class="pure-checkbox">
			<input type="checkbox" name="values[periods][]" value="{id}"/>
			<xsl:value-of disable-output-escaping="yes" select="name"/>
		</label>
	</xsl:template>
