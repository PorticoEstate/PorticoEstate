<xsl:template match="data" xmlns:php="http://php.net/xsl">

	<div id="delegate-edit-page-content" class="margin-top-content">
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
					<xsl:if test="not(delegate/id)"><span><xsl:value-of select="php:function('lang', 'New delegate')" /></span></xsl:if>
					<xsl:if test="delegate/id">
						<span><xsl:value-of select="delegate/name"/></span>
						<span><xsl:value-of select="php:function('lang', 'Edit delegate')" /></span>
					</xsl:if>
				</div>

            	<div class="row">
					
					<form action="" method="POST" id="form" name="form" class="col-md-8">
						<div class="col mb-5">
							<xsl:call-template name="msgbox"/>
						</div>

						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'name')" /></label>
								<input name="name" type="text" class="form-control" value="{delegate/name}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a delegate')" />
									</xsl:attribute>
								</input>
							</div>
						</div>

						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'ssn')" /></label>
								<input name="ssn" type="text" class="form-control" value="{delegate/ssn}">
									<!--xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute-->
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a ssn')" />
									</xsl:attribute>
								</input>
							</div>
						</div>

						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Organization')" /></label>
								<div class="autocomplete">
									<input id="field_organization_id" class="form-control" name="organization_id" type="hidden" value="{delegate/organization_id}">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter an organization')" />
										</xsl:attribute>
									</input>
									<input name="organization_name" class="form-control" type="text" id="field_organization_name" value="{delegate/organization_name}">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter an organization')" />
										</xsl:attribute>
										<xsl:if test="delegate/organization_id">
											<xsl:attribute name='disabled'>disabled</xsl:attribute>
										</xsl:if>
									</input>
									<div id="organization_container"/>
								</div>
							</div>
						</div>

						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'email')" /></label>
								<input name="email" type="text" class="form-control" value="{delegate/email}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter an email')" />
									</xsl:attribute>
								</input>
							</div>
						</div>

						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'phone')" /></label>
								<input type='text' id='field_phone' class="form-control" name="phone" value='{delegate/phone}'/>
							</div>
						</div>

						<xsl:if test="delegate/id">
							<div class="col">
								<div class="form-group">
									<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Active')"/></label>
									<select id="field_active" name="active" class="form-control">
										<option value="1">
											<xsl:if test="delegate/active=1">
												<xsl:attribute name="selected">checked</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="php:function('lang', 'Active')"/>
										</option>
										<option value="0">
											<xsl:if test="delegate/active=0">
												<xsl:attribute name="selected">checked</xsl:attribute>
											</xsl:if>
											<xsl:value-of select="php:function('lang', 'Inactive')"/>
										</option>
									</select>
								</div>
							</div>
						</xsl:if>

						<div class="col mt-5">
							<xsl:if test="not(delegate/id)">
								<input type="submit" class="btn btn-light mr-4" value="{php:function('lang', 'Add')}"/>
							</xsl:if>
							<xsl:if test="delegate/id">
								<input type="submit" class="btn btn-light mr-4" value="{php:function('lang', 'Save')}"/>
							</xsl:if>
							<a class="cancel" href="{delegate/cancel_link}">
								<xsl:value-of select="php:function('lang', 'Cancel')" />
							</a>
						</div>
					</form>
            	</div>         
            
        	</div>
    	
	</div>
    <div class="push"></div>

	<script type="text/javascript">
		var endpoint = '<xsl:value-of select="module" />';
        <![CDATA[
            $(document).ready(function(){
                JqueryPortico.autocompleteHelper(phpGWLink('bookingfrontend/', {menuaction: endpoint+'.uiorganization.index'},true ), 'field_organization_name', 'field_organization_id', 'organization_container');
            });
        ]]>
	</script>
</xsl:template>