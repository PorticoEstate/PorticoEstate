<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<!--xsl:call-template name="yui_booking_i18n"/-->
    <!--div id="content"-->
        <!--ul class="pathway">
            <li>
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="organization/organizations_link"/></xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Organization')" />
                </a>
            </li>
            <li>
                    <xsl:value-of select="organization/name"/>
            </li>
        </ul-->
		<xsl:variable name="edit_action">
			<xsl:value-of select="organization/edit_link"/>
		</xsl:variable>
		<form class= "pure-form pure-form-aligned" action="{$edit_action}" method="post" id="form" name="form">
			<input type="hidden" name="tab" value=""/>
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="organization/tabs"/>
				<div id="organization">
					<fieldset>
						
						<h1>
							<xsl:value-of select="organization/name"/>
						</h1>
						
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Organization shortname')" />
							</label>
							<xsl:value-of select="organization/shortname" />
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Organization number')" />
							</label>
							<xsl:value-of select="organization/organization_number" />
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Customer number')" />
							</label>
							<xsl:value-of select="organization/customer_number" />
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Activity')" />
							</label>
							<xsl:value-of select="organization/activity_name" />
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Homepage')" />
							</label>
							<xsl:if test="organization/homepage and normalize-space(organization/homepage)">
								<a target="blank" href="{organization/homepage}"><xsl:value-of select="organization/homepage" /></a>
							</xsl:if>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Email')" />
							</label>
							<a href="mailto:{organization/email}"><xsl:value-of select="organization/email"/></a>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Phone')" />
							</label>
							<xsl:value-of select="organization/phone"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Description')" />
							</label>
							<xsl:value-of select="organization/description" disable-output-escaping="yes"/>
						</div>

						<xsl:if test="count(organization/contacts/*) &gt; 0">
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'Admins')" />
								</label>
								<ul>
									<xsl:if test="organization/contacts[1]">
										<li><xsl:value-of select="organization/contacts[1]/name"/></li>
									</xsl:if>

									<xsl:if test="organization/contacts[2]">
										<li><xsl:value-of select="organization/contacts[2]/name"/></li>
									</xsl:if>
								</ul>
							</div>
						</xsl:if>

						<xsl:copy-of select="phpgw:booking_customer_identifier_show(organization)"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Street')" />
							</label>
							<xsl:value-of select="organization/street"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Zip code')" />
							</label>
							<xsl:value-of select="organization/zip_code"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Postal City')" />
							</label>
							<xsl:value-of select="organization/city"/>
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'District')" />
							</label>
							<xsl:value-of select="organization/district"/>
						</div>
					</fieldset>
				</div>
			</div>

			<dl class="proplist-col">
				<button class="pure-button pure-button-primary" onclick="window.location.href='{organization/edit_link}'">
					<xsl:value-of select="php:function('lang', 'Edit')" />
				</button>
			</dl>
		</form>
    <!--/div-->
	
</xsl:template>
