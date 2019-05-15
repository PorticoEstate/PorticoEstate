<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:variable name="edit_action">
		<xsl:value-of select="user/edit_link"/>
	</xsl:variable>
	<form class= "pure-form pure-form-aligned" action="{$edit_action}" method="post" id="form" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="user/tabs"/>
			<div id="user" class="booking-container">
				<fieldset>
					<h1>
						<xsl:value-of select="user/name"/>
					</h1>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'ssn')" />
						</label>
						<span>
							<xsl:value-of select="user/customer_ssn" />
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Customer number')" />
						</label>
						<span>
							<xsl:value-of select="user/customer_number" />
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Homepage')" />
						</label>
						<xsl:if test="user/homepage and normalize-space(user/homepage)">
							<a target="blank" href="{user/homepage}">
								<xsl:value-of select="user/homepage" />
							</a>
						</xsl:if>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Email')" />
						</label>
						<a href="mailto:{user/email}">
							<xsl:value-of select="user/email"/>
						</a>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Phone')" />
						</label>
						<span>
							<xsl:value-of select="user/phone"/>
						</span>
					</div>
					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Street')" />
						</label>
						<span>
							<xsl:value-of select="user/street"/>
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Zip code')" />
						</label>
						<span>
							<xsl:value-of select="user/zip_code"/>
						</span>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Postal City')" />
						</label>
						<span>
							<xsl:value-of select="user/city"/>
						</span>
					</div>
					
				</fieldset>
			</div>
		</div>
	</form>
	<div class="form-buttons">
		<button class="pure-button pure-button-primary" onclick="window.location.href='{user/edit_link}'">
			<xsl:value-of select="php:function('lang', 'Edit')" />
		</button>
		<xsl:variable name="ssn_test">
			<xsl:value-of select="user/customer_ssn"/>
		</xsl:variable>
		<xsl:if test="substring ($ssn_test, 1, 4) != '0000'">
			<button class="pure-button pure-button-primary" onclick="window.location.href='{user/delete_link}'">
				<xsl:value-of select="php:function('lang', 'Delete')" />
			</button>
		</xsl:if>
		<input type="button" class="pure-button pure-button-primary" name="cencel">
			<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="user/cancel_link"/>"</xsl:attribute>
			<xsl:attribute name="value">
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</xsl:attribute>
		</input>		
	</div>
</xsl:template>
