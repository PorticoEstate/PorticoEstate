<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="content">
        
		<div class="pure-g">
			<div class="pure-u-1">
				<dl class="form-col">
					<dt class="heading">
						<xsl:if test="not(group/id)">
							<xsl:value-of select="php:function('lang', 'New Group')" />
						</xsl:if>
						<xsl:if test="group/id">
							<xsl:value-of select="php:function('lang', 'Edit Group')" />
						</xsl:if>
					</dt>
				</dl>
			</div>
		</div>

		<dl class="form">
                
		</dl>

		<xsl:call-template name="msgbox"/>

		<form action="" method="POST" id="form" name="form">
			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-1-3">
					<dl class="form-col">
						<dt>
							<label for="field_name">
								<xsl:value-of select="php:function('lang', 'Group')" />
							</label>
						</dt>
						<dd>
							<input name="name" type="text" value="{group/name}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a group')" />
								</xsl:attribute>
							</input>
						</dd>
						<dt>
							<label for="field_shortname">
								<xsl:value-of select="php:function('lang', 'Group shortname')" />
							</label>
						</dt>
						<dd>
							<input name="shortname" type="text" value="{group/shortname}" />
						</dd>
						<dt>
							<label for="field_organization">
								<xsl:value-of select="php:function('lang', 'Organization')" />
							</label>
						</dt>
						<dd>
							<div class="autocomplete">
								<input id="field_organization_id" name="organization_id" type="hidden" value="{group/organization_id}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter an organization')" />
									</xsl:attribute>
								</input>
								<input name="organization_name" type="text" id="field_organization_name" value="{group/organization_name}">
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
						</dd>
						<dt>
							<label for="field_activity">
								<xsl:value-of select="php:function('lang', 'Activity')" />
							</label>
						</dt>
						<dd>
							<select name="activity_id" id="field_activity">
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
						</dd>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-1-3">
					<dl class="form-col">
						<xsl:if test="group/id">
							<dt>
								<label for="field_active">
									<xsl:value-of select="php:function('lang', 'Active')"/>
								</label>
							</dt>
							<dd>
								<select id="field_active" name="active">
									<option value="1">
										<xsl:if test="group/active=1">
											<xsl:attribute name="selected">checked</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Active')"/>
									</option>
									<option value="0">
										<xsl:if test="group/active=0">
											<xsl:attribute name="selected">checked</xsl:attribute>
										</xsl:if>
										<xsl:value-of select="php:function('lang', 'Inactive')"/>
									</option>
								</select>
							</dd>
						</xsl:if>
						<!--<xsl:if test="not(new_form) and (currentapp = 'booking')">-->
						<dt>
							<label for="field_show_in_portal">
								<xsl:value-of select="php:function('lang', 'Show in portal')"/>
							</label>
						</dt>
						<dd>
							<select id="field_show_in_portal" name="show_in_portal">
								<option value="0">
									<xsl:if test="group/show_in_portal=0">
										<xsl:attribute name="selected">checked</xsl:attribute>
									</xsl:if>
									<xsl:value-of select="php:function('lang', 'No')"/>
								</option>
								<option value="1">
									<xsl:if test="group/show_in_portal=1">
										<xsl:attribute name="selected">checked</xsl:attribute>
									</xsl:if>
									<xsl:value-of select="php:function('lang', 'Yes')"/>
								</option>
							</select>
						</dd>
						<!--</xsl:if>-->
					</dl>
				</div>
			</div>
            
			<div class="pure-g">
				<div class="pure-u-1 pure-u-lg-4-5">
					<dl class="form-col">
						<dt>
							<label for="field_description">
								<xsl:value-of select="php:function('lang', 'Description')" />
							</label>
						</dt>
						<dd>
							<textarea id="field_description" name="description" type="text">
								<xsl:value-of select="group/description"/>
							</textarea>
						</dd>
					</dl>
				</div>
			</div>
            
			<div class="pure-g">
				<div class="pure-u-1 pure-u-md-1-3">
					<dl class="form-col">
						<dt class='heading'>
							<xsl:value-of select="php:function('lang', 'Team leader 1')" />
						</dt>

						<dt>
							<label for="field_admin_name_1">
								<xsl:value-of select="php:function('lang', 'Name')" />
							</label>
							<br />
						</dt>
						<dd>
							<input type='text' id='field_admin_name_1' name="contacts[0][name]" value='{group/contacts[1]/name}'/>
						</dd>

						<dt>
							<label for="field_admin_email_1">
								<xsl:value-of select="php:function('lang', 'Email')" />
							</label>
							<br />
						</dt>
						<dd>
							<input type='text' id='field_admin_email_1' name="contacts[0][email]" value='{group/contacts[1]/email}'/>
						</dd>

						<dt>
							<label for="field_admin_phone_1">
								<xsl:value-of select="php:function('lang', 'Phone')" />
							</label>
							<br />
						</dt>
						<dd>
							<input type='text' id='field_admin_phone_1' name="contacts[0][phone]" value='{group/contacts[1]/phone}'/>
						</dd>
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-1-3">
					<dl class="form-col">
						<dt class='heading'>
							<xsl:value-of select="php:function('lang', 'Team leader 2')" />
						</dt>

						<dt>
							<label for="field_admin_name_2">
								<xsl:value-of select="php:function('lang', 'Name')" />
							</label>
						</dt>
						<dd>
							<input type='text' id='field_admin_name_2' name="contacts[1][name]" value='{group/contacts[2]/name}'/>
						</dd>

						<dt>
							<label for="field_admin_email_2">
								<xsl:value-of select="php:function('lang', 'Email')" />
							</label>
							<br />
						</dt>
						<dd>
							<input type='text' id='field_admin_email_2' name="contacts[1][email]" value='{group/contacts[2]/email}'/>
						</dd>

						<dt>
							<label for="field_admin_phone_2">
								<xsl:value-of select="php:function('lang', 'Phone')" />
							</label>
							<br />
						</dt>
						<dd>
							<input type='text' id='field_admin_phone_2' name="contacts[1][phone]" value='{group/contacts[2]/phone}'/>
						</dd>
					</dl>
				</div>
			</div>
			<!--dl class="form-col">
			</dl-->
			<!--dl class="form-col"-->
			<!--<xsl:if test="not(new_form) and (currentapp = 'booking')">-->
			<!--</xsl:if>-->
			<!--/dl-->

			<!--div style='clear:left; padding:0; margin:0'/-->
			<div class="form-buttons">
				<xsl:if test="not(group/id)">
					<input type="submit" value="{php:function('lang', 'Add')}"/>
				</xsl:if>
				<xsl:if test="group/id">
					<input type="submit" value="{php:function('lang', 'Save')}"/>
				</xsl:if>
				<a class="cancel" href="{group/cancel_link}">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</a>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		var endpoint = '<xsl:value-of select="module" />';
        <![CDATA[
            $(document).ready(function(){
                JqueryPortico.autocompleteHelper(phpGWLink('bookingfrontend/', {menuaction: endpoint+'.uiorganization.index'}, true ), 'field_organization_name', 'field_organization_id', 'organization_container');
            });
        ]]>
	</script>
</xsl:template>
