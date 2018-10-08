<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="delegate-page-content" class="margin-top-content">
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
						<a href="{delegate/organization_link}"><xsl:value-of select="delegate/organization_name"/></a>
					</span>
					<span><xsl:value-of select="delegate/name"/></span>
				</div>

            	<div class="row">
					<div class="col">
						<xsl:if test="delegate/permission/write">
							<button class="btn btn-light" onclick="window.location.href='{edit_self_link}'">
                                <xsl:value-of select="php:function('lang', 'edit')" />
                            </button>
						</xsl:if>
					</div>
				
					<div class="col">
						<xsl:call-template name="msgbox"/>
					</div>

					<div class="col-12 mt-5">
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'name')" /></label>
							<xsl:value-of select="delegate/name"/>
						</div>
					</div>

					<div class="col-12">
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Organization')" /></label>
							<xsl:value-of select="delegate/organization_name"/>
						</div>
					</div>

					<div class="col-12">
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'email')" /></label>
							<xsl:value-of select="delegate/email"/>
						</div>
					</div>

					<div class="col-12">
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'phone')" /></label>
							<xsl:value-of select="delegate/phone"/>
						</div>
					</div>

            	</div>         
            
        	</div>
    	
	</div>
    <div class="push"></div>

</xsl:template>