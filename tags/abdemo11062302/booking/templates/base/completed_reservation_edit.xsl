<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<dl class="form">
			<dt class="heading"><xsl:value-of select="php:function('lang', 'Edit completed reservation')"/></dt>
		</dl>
		<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>
		
		<form action="" method="POST">
			<dl class="form-col">
				<dt><label for="field_cost"><xsl:value-of select="php:function('lang', 'Cost')" /></label></dt>
				<dd><input id="field_cost" name="cost" type="text" value="{reservation/cost}"/></dd>
				
				<dt><label for="field_customer_type"><xsl:value-of select="php:function('lang', 'Customer Type')" /></label></dt>
				<dd>
					<select name='customer_type' id='field_customer_type'>
						<option value=''><xsl:value-of select="php:function('lang', 'Select...')" /></option>
						<xsl:for-each select="reservation/customer_types/*">
							<option>
								<xsl:if test="../../customer_type = local-name()">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
						
								<xsl:attribute name="value"><xsl:value-of select="local-name()"/></xsl:attribute>
								<xsl:value-of select="php:function('lang', string(node()))"/>
							</option>
						</xsl:for-each>
					</select>
				</dd>
			</dl>

			<dl class="form-col">
				<xsl:copy-of select="phpgw:booking_customer_identifier(reservation, 'Customer ID')"/>
			</dl>

			<div style='clear:both'/>
			<dl class="form">
				<dt><label for="field_article_description"><xsl:value-of select="php:function('lang', 'Article Description')" /></label></dt>
				<dd>
					<dd><input type='text' id='article_description' name="description" value='{reservation/article_description}' maxlength='35' style="width: 20em"/></dd>
				</dd>
				<dt><label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
				<dd>
					<dd><input type='text' id='field_description' name="description" value='{reservation/description}' maxlength='60' style="width: 28em"/></dd>
				</dd>
			</dl>

			<div class="form-buttons">
				<input type="submit" value="{php:function('lang', 'Save')}"/>
				<a class="cancel" href="{reservation/cancel_link}">
					<xsl:value-of select="php:function('lang', 'Cancel')"/>
				</a>
			</div>
		</form>
	</div>
</xsl:template>