<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="group-page-content" class="margin-top-content">
        	<div class="container wrapper">
				<div class="location">
					<span>
						<a><xsl:attribute name="href">
								<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Home')" />
						</a>
					</span>
					<span>
						<a href="{group/organization_link}"><xsl:value-of select="group/organization_name"/></a>
					</span>
					<span><xsl:value-of select="group/name"/></span>
				</div>

            	<div class="row">
					<div class="col-12">
						<xsl:if test="group/permission/write">
							<button class="btn btn-light" onclick="window.location.href='{edit_self_link}'">
									<xsl:value-of select="php:function('lang', 'edit')" />
							</button>
						</xsl:if>
					</div>

					<div class="col mb-5">
						<xsl:call-template name="msgbox"/>
					</div>

					<div class="col-12">
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Group')" /></label>
							<xsl:value-of select="group/name"/>
						</div>
					</div>

					<div class="col-12">
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Organization')" /></label>
							<xsl:value-of select="group/organization_name"/>
						</div>
					</div>

					<xsl:if test="group/description and normalize-space(group/description)">
						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Description')" /></label>
								<xsl:value-of select="group/description" disable-output-escaping="yes"/>
							</div>
						</div>
					</xsl:if>

					<xsl:for-each select="group/contacts">
						<xsl:if test="normalize-space(.)">
							<div class="col mt-5">
								<h5 class="mb-4"><xsl:value-of select="php:function('lang', 'Contact Person')" /></h5>
								
								<xsl:if test="name and string-length(normalize-space(name)) &gt; 0">
									<div class="form-group">
										<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Name')" /></label>
										<xsl:value-of select="name"/>
									</div>
								</xsl:if>
								
								<xsl:if test="phone and string-length(normalize-space(phone)) &gt; 0">
									<div class="form-group">
										<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Phone')" /></label>
										<xsl:value-of select="phone"/>
									</div>									
								</xsl:if>
								
								<xsl:if test="email and string-length(normalize-space(email)) &gt; 0">
									<div class="form-group">
										<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Email')" /></label>
										<xsl:value-of select="email"/>
									</div>									
								</xsl:if>
							</div>
						</xsl:if>
					</xsl:for-each>

				</div>         
            
        	</div>
    	
	</div>
    <div class="push"></div>
</xsl:template>
