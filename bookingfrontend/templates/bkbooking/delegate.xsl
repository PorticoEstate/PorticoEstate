<xsl:template match="data" xmlns:php="http://php.net/xsl">
	
	<div class="content">
		<ul class="pathway">
			<li>
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
					</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Home')" />
				</a>
			</li>
			<li>
				<a href="{delegate/organization_link}">
					<xsl:value-of select="delegate/organization_name"/>
				</a>
			</li>
			<li>
				<xsl:value-of select="delegate/name"/>
			</li>
		</ul>

		<xsl:if test="delegate/permission/write">
			<span class="loggedin">
				<button onclick="window.location.href='{edit_self_link}'">
					<xsl:value-of select="php:function('lang', 'edit')" />
				</button>
			</span>
		</xsl:if>
		<xsl:call-template name="msgbox"/>

		<dl class="proplist">
			<dt>
				<xsl:value-of select="php:function('lang', 'name')" />
			</dt>
			<dd>
				<xsl:value-of select="delegate/name"/>
			</dd>

			<dt>
				<xsl:value-of select="php:function('lang', 'Organization')" />
			</dt>
			<dd>
				<xsl:value-of select="delegate/organization_name"/>
			</dd>
			<dt>
				<xsl:value-of select="php:function('lang', 'email')" />
			</dt>
			<dd>
				<xsl:value-of select="delegate/email"/>
			</dd>
			<dt>
				<xsl:value-of select="php:function('lang', 'phone')" />
			</dt>
			<dd>
				<xsl:value-of select="delegate/phone"/>
			</dd>

		</dl>
	</div>
</xsl:template>
