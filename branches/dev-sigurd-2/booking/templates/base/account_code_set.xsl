<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="yui_booking_i18n"/>
	<div id="content">
		<ul class="pathway">
			<li>
				<a>
					<xsl:attribute name="href"><xsl:value-of select="account_code_set/account_codes_link"/></xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Account Codes')" />
				</a>
			</li>
			<li>
				<xsl:value-of select="php:function('lang', string(account_code_set/name))"/>
			</li>
		</ul>
		
		<dl class="proplist">
			<dt><xsl:value-of select="php:function('lang', 'Name')" /></dt>
			<dd><xsl:value-of select="account_code_set/name"/></dd>	
		</dl>

		<dl class="proplist-col">
			<dt><xsl:value-of select="php:function('lang', 'Object No.')" /></dt>
			<dd><xsl:value-of select="account_code_set/object_number"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'Article')" /></dt>
			<dd><xsl:value-of select="account_code_set/article"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'Unit No.')" /></dt>
			<dd><xsl:value-of select="account_code_set/unit_number"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'Unit Prefix')" /></dt>
			<dd><xsl:value-of select="account_code_set/unit_prefix"/></dd>
		</dl>
		
		<dl class="proplist-col">
			<dt><xsl:value-of select="php:function('lang', 'Responsible Code')" /></dt>
			<dd><xsl:value-of select="account_code_set/responsible_code"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'Service')" /></dt>
			<dd><xsl:value-of select="account_code_set/service"/></dd>
			
			<dt><xsl:value-of select="php:function('lang', 'Project No.')" /></dt>
			<dd><xsl:value-of select="account_code_set/project_number"/></dd>
		</dl>
		
		<dl class="proplist">
			<dt><xsl:value-of select="php:function('lang', 'Invoice instruction')" /></dt>
			<div class="description"><xsl:value-of select="account_code_set/invoice_instruction"/></div>
		</dl>

		<div class="form-buttons">
			<button onclick="window.location.href='{account_code_set/edit_link}'">
				<xsl:value-of select="php:function('lang', 'Edit')" />
			</button>
		</div>
	</div>
</xsl:template>
