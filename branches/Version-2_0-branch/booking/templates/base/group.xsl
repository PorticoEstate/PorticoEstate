<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="group/tabs"/>
			<div id="group" class="booking-container">
				<fieldset>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Organization')" />
						</label>
						<xsl:value-of select="group/organization_name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Name')" />
						</label>
						<xsl:value-of select="group/name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Group shortname')" />
						</label>
						<xsl:value-of select="group/shortname"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Activity')" />
						</label>
						<xsl:value-of select="group/activity_name" />
					</div>
					<div class="pure-control-group">
						<xsl:if test="count(group/contacts/*) &gt; 0">
							<label style="vertical-align:top;">
								<xsl:value-of select="php:function('lang', 'Team leaders')" />
							</label>
							<ul style="list-style:none;display:inline-block;padding:0;margin:0;">
								<xsl:if test="group/contacts[1]">
									<li>
										<xsl:value-of select="group/contacts[1]/name"/>
									</li>
								</xsl:if>
								<xsl:if test="group/contacts[2]">
									<li>
										<xsl:value-of select="group/contacts[2]/name"/>
									</li>
								</xsl:if>
							</ul>
						</xsl:if>
					</div>
					<div class="pure-control-group">
						<label style="vertical-align:top;">
							<xsl:value-of select="php:function('lang', 'Description')" />
						</label>
						<div style="display:inline-block;max-width:80%;">
							<xsl:value-of select="group/description" disable-output-escaping="yes"/>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="form-buttons">
			<a class="button pure-button pure-button-primary">
				<xsl:attribute name="href">
					<xsl:value-of select="group/edit_link"/>
				</xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Edit')" />
			</a>
			<input type="button" class="pure-button pure-button-primary" name="cencel">
				<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="group/cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>
