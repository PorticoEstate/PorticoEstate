<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div class="content">
        
		<div class="pure-g">
			<div class="pure-u-1">
				<dl class="form-col">
					<dt class="heading">
						<xsl:if test="not(delegate/id)">
							<xsl:value-of select="php:function('lang', 'New delegate')" />
						</xsl:if>
						<xsl:if test="delegate/id">
							<xsl:value-of select="php:function('lang', 'Edit delegate')" />
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
								<xsl:value-of select="php:function('lang', 'name')" />
							</label>
						</dt>
						<dd>
							<input name="name" type="text" value="{delegate/name}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a delegate')" />
								</xsl:attribute>
							</input>
						</dd>
						<dt>
							<label for="field_ssn">
								<xsl:value-of select="php:function('lang', 'ssn')" />
							</label>
						</dt>
						<dd>
							<input name="ssn" type="text" value="{delegate/ssn}">
								<!--xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute-->
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter a ssn')" />
								</xsl:attribute>
							</input>
						</dd>
						<dt>
							<label for="field_email">
								<xsl:value-of select="php:function('lang', 'Email')" />
							</label>
						</dt>
						<dd>
							<input name="email" type="text" value="{delegate/email}">
								<xsl:attribute name="data-validation">
									<xsl:text>required</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:value-of select="php:function('lang', 'Please enter an email')" />
								</xsl:attribute>
							</input>
						</dd>
						<dt>
							<label for="field_phone">
								<xsl:value-of select="php:function('lang', 'Phone')" />
							</label>
						</dt>
						<dd>
							<input type='text' id='field_phone' name="phone" value='{delegate/phone}'/>
						</dd>
						<dt>
							<label for="field_organization">
								<xsl:value-of select="php:function('lang', 'Organization')" />
							</label>
						</dt>
						<dd>
							<div class="autocomplete">
								<input id="field_organization_id" name="organization_id" type="hidden" value="{delegate/organization_id}">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="data-validation-error-msg">
										<xsl:value-of select="php:function('lang', 'Please enter an organization')" />
									</xsl:attribute>
								</input>
								<input name="organization_name" type="text" id="field_organization_name" value="{delegate/organization_name}">
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
						</dd>
						
					</dl>
				</div>
				<div class="pure-u-1 pure-u-md-1-3">
					<dl class="form-col">
						<xsl:if test="delegate/id">
							<dt>
								<label for="field_active">
									<xsl:value-of select="php:function('lang', 'Active')"/>
								</label>
							</dt>
							<dd>
								<select id="field_active" name="active">
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
							</dd>
						</xsl:if>
					</dl>
				</div>
			</div>
                        
			<div class="form-buttons">
				<xsl:if test="not(delegate/id)">
					<input type="submit" value="{php:function('lang', 'Add')}"/>
				</xsl:if>
				<xsl:if test="delegate/id">
					<input type="submit" value="{php:function('lang', 'Save')}"/>
				</xsl:if>
				<a class="cancel" href="{delegate/cancel_link}">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</a>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		var endpoint = '<xsl:value-of select="module" />';
        <![CDATA[
            $(document).ready(function(){
                JqueryPortico.autocompleteHelper(phpGWLink('bookingfrontend/', {menuaction: endpoint+'.uiorganization.index'},true ), 'field_organization_name', 'field_organization_id', 'organization_container');
            });
        ]]>
	</script>
</xsl:template>
