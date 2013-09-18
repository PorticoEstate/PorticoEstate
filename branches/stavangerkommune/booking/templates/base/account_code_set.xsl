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
				<xsl:value-of select="account_code_set/name"/>
			</li>
		</ul>
		
		<dl class="proplist">
			<dt><xsl:value-of select="php:function('lang', 'Name')" /></dt>
			<dd><xsl:value-of select="account_code_set/name"/></dd>	
		</dl>

		<dl class="proplist-col">
			<xsl:if test="config_data/dim_3">
				<dt><xsl:value-of select="config_data/dim_3" /></dt>
				<dd><xsl:value-of select="account_code_set/object_number"/></dd>
			</xsl:if>
			
			<xsl:if test="config_data/article">
				<dt><xsl:value-of select="php:function('lang', 'Article')" /></dt>
				<dd><xsl:value-of select="account_code_set/article"/></dd>
			</xsl:if>
			
			<xsl:if test="config_data/dim_value_1">
				<dt><xsl:value-of select="config_data/dim_value_1" /></dt>
				<dd><xsl:value-of select="account_code_set/unit_number"/></dd>
			</xsl:if>
			
			<xsl:if test="config_data/dim_value_4">
				<dt><xsl:value-of select="config_data/dim_value_4" /></dt>
				<dd><xsl:value-of select="account_code_set/dim_value_4"/></dd>
			</xsl:if>
			
			<xsl:if test="config_data/dim_value_5">
				<dt><xsl:value-of select="config_data/dim_value_5" /></dt>
				<dd><xsl:value-of select="account_code_set/dim_value_5"/></dd>
			</xsl:if>
			
			<xsl:if test="config_data/external_format != 'KOMMFAKT'">
				<dt><xsl:value-of select="php:function('lang', 'Unit Prefix')" /></dt>
				<dd><xsl:value-of select="account_code_set/unit_prefix"/></dd>
			</xsl:if>
		</dl>
		
		<dl class="proplist-col">
			<xsl:if test="config_data/dim_1">
				<dt><xsl:value-of select="config_data/dim_1" /></dt>
				<dd><xsl:value-of select="account_code_set/responsible_code"/></dd>
			</xsl:if>
			
			<xsl:if test="config_data/dim_2">
				<dt><xsl:value-of select="config_data/dim_2" /></dt>
				<dd><xsl:value-of select="account_code_set/service"/></dd>
			</xsl:if>
			
			<xsl:if test="config_data/dim_4">
				<dt><xsl:value-of select="config_data/dim_4" /></dt>
				<dd><xsl:value-of select="account_code_set/dim_4"/></dd>
			</xsl:if>

			<xsl:if test="config_data/dim_5">
				<dt><xsl:value-of select="config_data/dim_5" /></dt>
				<dd><xsl:value-of select="account_code_set/project_number"/></dd>
			</xsl:if>
		</dl>
		
			<dl class="proplist">
				<xsl:if test="config_data/external_format != 'KOMMFAKT'">
					<dt><xsl:value-of select="php:function('lang', 'Invoice instruction')" /></dt>
				</xsl:if>
				<xsl:if test="config_data/external_format = 'KOMMFAKT'">
					<dt><xsl:value-of select="php:function('lang', 'Reference')" /></dt>
				</xsl:if>
				<div class="description"><xsl:value-of select="account_code_set/invoice_instruction"/></div>
			</dl>

		<xsl:if test="account_code_set/permission/write">
			<div class="form-buttons">
				<button onclick="window.location.href='{account_code_set/edit_link}'">
					<xsl:value-of select="php:function('lang', 'Edit')" />
				</button>
			</div>
		</xsl:if>
	</div>
</xsl:template>
