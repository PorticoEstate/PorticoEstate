<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="group-edit-page-content" class="margin-top-content">
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
						<a href="{group/organization_link}">
							<xsl:value-of select="group/organization_name"/>
						</a>
					</span>
					<xsl:if test="not(group/id)">
						<span><xsl:value-of select="php:function('lang', 'New Group')" /></span>
					</xsl:if>
					<xsl:if test="group/id">
						<span><xsl:value-of select="php:function('lang', 'Edit Group')" /></span>						
					</xsl:if>				
				</div>
				
				<form action="" method="POST" id="form" name="form" class="col-lg-8">
					<div class="row">
					
						<div class="col-12">
							<xsl:call-template name="msgbox"/>
						</div>

						<div class="col-12">
							<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Group (2018)')" /></label>
								<input name="name" class="form-control" type="text" value="{group/name}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter a group')" />
									</xsl:attribute>
								</input>
							</div>
						</div>

						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Group shortname')" /></label>
								<input name="shortname" class="form-control" type="text" value="{group/shortname}" />
							</div>
						</div>

						<div class="col-12">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Organization')" /></label>
								<div class="autocomplete">
									<input id="field_organization_id" class="form-control" name="organization_id" type="hidden" value="{group/organization_id}">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter an organization')" />
										</xsl:attribute>
									</input>
									<input name="organization_name" class="form-control" type="text" id="field_organization_name" value="{group/organization_name}">
										<xsl:attribute name="data-validation">
											<xsl:text>required</xsl:text>
										</xsl:attribute>
										<xsl:attribute name="data-validation-error-msg">
											<xsl:value-of select="php:function('lang', 'Please enter an organization')" />
										</xsl:attribute>
										<xsl:if test="group/organization_id">
											<xsl:attribute name='disabled'>disabled</xsl:attribute>
										</xsl:if>
									</input>
									<div id="organization_container"/>
								</div>
							</div>
						</div>

						<div class="col-12">
								<div class="form-group">
									<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Activity')" /></label>
									<select name="activity_id" class="form-control" id="field_activity">
										<option value="">
											<xsl:value-of select="php:function('lang', '-- select an activity --')" />
										</option>
										<xsl:for-each select="activities">
											<option>
												<xsl:if test="../group/activity_id = id">
													<xsl:attribute name="selected">selected</xsl:attribute>
												</xsl:if>
												<xsl:attribute name="value">
													<xsl:value-of select="id"/>
												</xsl:attribute>
												<xsl:value-of select="name"/>
											</option>
										</xsl:for-each>
									</select>
								</div>
							</div>

							<div class="col-12">
								<div class="form-group">
									<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Description')" /></label>
									<textarea id="field_description" class="form-control" name="description" type="text">
										<xsl:value-of select="group/description"/>
									</textarea>
								</div>
							</div>
						
						
						<div class="col-md-6">
							<h5 class="mt-4 mb-4"><xsl:value-of select="php:function('lang', 'Team leader 1')" /></h5>
						
								<div class="form-group">
									<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Name')" /></label>
									<input type='text' class="form-control" id='field_admin_name_1' name="contacts[0][name]" value='{group/contacts[1]/name}'/>
								</div>

								<div class="form-group">
									<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Email')" /></label>
									<input type='text' class="form-control" id='field_admin_email_1' name="contacts[0][email]" value='{group/contacts[1]/email}'/>
								</div>

								<div class="form-group">
									<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Phone')" /></label>
									<input type='text' class="form-control" id='field_admin_phone_1' name="contacts[0][phone]" value='{group/contacts[1]/phone}'/>
								</div>
						</div>

						<div class="col-md-6">
							<h5 class="mt-4 mb-4"><xsl:value-of select="php:function('lang', 'Team leader 2')" /></h5>
						
								<div class="form-group">
									<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Name')" /></label>
									<input type='text' class="form-control" id='field_admin_name_2' name="contacts[1][name]" value='{group/contacts[2]/name}'/>
								</div>

								<div class="form-group">
									<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Email')" /></label>
									<input type='text' class="form-control" id='field_admin_email_2' name="contacts[1][email]" value='{group/contacts[2]/email}'/>
								</div>

								<div class="form-group">
									<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Phone')" /></label>
									<input type='text' class="form-control" id='field_admin_phone_2' name="contacts[1][phone]" value='{group/contacts[2]/phone}'/>
								</div>
						</div>
						





						<div class="col-12 mt-3 mb-2">
							<xsl:if test="not(group/id)">
								<input type="submit" class="btn btn-light mr-4" value="{php:function('lang', 'Add')}"/>
							</xsl:if>
							<xsl:if test="group/id">
								<input type="submit" class="btn btn-light mr-4" value="{php:function('lang', 'Save')}"/>
							</xsl:if>
							<a class="cancel" href="{group/cancel_link}">
								<xsl:value-of select="php:function('lang', 'Cancel')" />
							</a>
						</div>

					
				         
            	</div>
			</form>
        	</div>
    	
	</div>
    <div class="push"></div>
	<script type="text/javascript">
		var endpoint = '<xsl:value-of select="module" />';
        <![CDATA[
            $(document).ready(function(){
                JqueryPortico.autocompleteHelper(phpGWLink('bookingfrontend/', {menuaction: endpoint+'.uiorganization.index'}, true ), 'field_organization_name', 'field_organization_id', 'organization_container');
            });
        ]]>
	</script>
</xsl:template>

