
<!-- $Id: organization.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit"/>
		</xsl:when>
		<xsl:when test="view">
			<xsl:apply-templates select="view"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<div>
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>

		<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="organization">
					<input type="hidden" id="id" name="id" value="{organization_id}"/>
					<input type="hidden" id="original_org_id" name="original_org_id" value="{original_org_id}"/>
					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Organisasjonsnavn')"/>
						</label>
						<xsl:value-of select="organization_name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Organisasjonsnummer')"/>
						</label>
						<input type="text" name="orgno" id="orgno" value="{organization_no}">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>						
						</input>						
					</div>					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Bydel')"/>
						</label>
						<select id="org_district" name="org_district">							
							<xsl:apply-templates select="list_district_options/options"/>
						</select>											
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Hjemmeside')"/>
						</label>
						<input type="text" name="homepage" id="homepage" value="{homepage}"/>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'E-post')"/>
						</label>
						<input type="text" name="email" id="email" value="{email}"/>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Telefon')"/>
						</label>
						<input type="text" name="phone" id="phone" value="{phone}"/>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'address')"/>
						</label>
						<input type="text" name="address" id="address" value="{address}"/>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Postnummer/Sted')"/>
						</label>
						<input type="text" id="zip_code" name="zip_code" value="{zip_code}" size="6"/>
						<input type="text" id="city" name="city" value="{city}"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'description')"/>
						</label>							
						<textarea rows="10" cols="100" id="org_description" name="org_description">
							<xsl:value-of select="description"/>
						</textarea>
					</div>
					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Kontaktperson 1')"/>
						</label>
					</div>
					<input type="hidden" id="contact1_id" name="contact1_id" value="{contact1_id}"/>					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Navn')"/>
						</label>
						<input type="text" id="contact1_name" name="contact1_name" value="{contact1_name}"/>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Telefon')"/>
						</label>
						<input type="text" id="contact1_phone" name="contact1_phone" value="{contact1_phone}"/>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'E-post')"/>
						</label>
						<input type="text" id="contact1_email" name="contact1_email" value="{contact1_email}"/>						
					</div>
					
					<xsl:if test="contact2_id != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Kontaktperson 2')"/>
							</label>
						</div>
						<input type="hidden" id="contact2_id" name="contact2_id" value="{contact2_id}"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Navn')"/>
							</label>
							<input type="text" id="contact2_name" name="contact2_name" value="{contact2_name}"/>						
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Telefon')"/>
							</label>
							<input type="text" id="contact2_phone" name="contact2_phone" value="{contact2_phone}"/>						
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'E-post')"/>
							</label>
							<input type="text" id="contact2_email" name="contact2_email" value="{contact2_email}"/>						
						</div>
					</xsl:if>
				</div>
			</div>
			<div class="proplist-col">
				<xsl:if test="original_org_id != ''">
					<input type="submit" class="pure-button pure-button-primary" name="update_organization" value="{lang_update_org}" onMouseout="window.status='';return true;"/>
					<input type="submit" class="pure-button pure-button-primary" name="reject_organization_update" value="{lang_reject}" onMouseout="window.status='';return true;"/>
				</xsl:if>
				<xsl:if test="original_org_id = ''">
					<input type="submit" class="pure-button pure-button-primary" name="store_organization" value="{lang_store}" onMouseout="window.status='';return true;"/>
					<input type="submit" class="pure-button pure-button-primary" name="reject_organization" value="{lang_reject}" onMouseout="window.status='';return true;"/>
				</xsl:if>
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
			</div>
		</form>
	</div>
</xsl:template>


<!-- view  -->
<xsl:template xmlns:php="http://php.net/xsl" match="view">
	<div>
		<form id="form" name="form" method="post" action="" class="pure-form pure-form-aligned">
			<div id="tab-content">
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>
				<div id="organization">
					<input type="hidden" id="id" name="id" value="{organization_id}"/>
					<input type="hidden" id="original_org_id" name="original_org_id" value="{original_org_id}"/>
					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Organisasjonsnavn')"/>
						</label>
						<xsl:value-of select="organization_name"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Organisasjonsnummer')"/>
						</label>
						<xsl:value-of select="organization_no"/>					
					</div>		
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Bydel')"/>
						</label>
						<xsl:value-of select="dictrict"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Hjemmeside')"/>
						</label>
						<xsl:value-of select="homepage"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'E-post')"/>
						</label>
						<xsl:value-of select="email"/>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Telefon')"/>
						</label>
						<xsl:value-of select="phone"/>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'address')"/>
						</label>
						<xsl:value-of select="address"/>					
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Postnummer/Sted')"/>
						</label>
						<xsl:value-of select="zip_code"/>
						<xsl:value-of select="city"/>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'description')"/>
						</label>							
						<xsl:value-of select="description"/>						
					</div>
					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Kontaktperson 1')"/>
						</label>
					</div>
					<input type="hidden" id="contact1_id" name="contact1_id" value="{contact1_id}"/>					
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Navn')"/>
						</label>
						<xsl:value-of select="contact1_name"/>						
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Telefon')"/>
						</label>	
						<xsl:value-of select="contact1_phone"/>					
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'E-post')"/>
						</label>
						<xsl:value-of select="contact1_email"/>					
					</div>
					
					<xsl:if test="contact2_id != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Kontaktperson 2')"/>
							</label>
						</div>
						<input type="hidden" id="contact2_id" name="contact2_id" value="{contact2_id}"/>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Navn')"/>
							</label>
							<xsl:value-of select="contact2_name"/>					
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'Telefon')"/>
							</label>
							<xsl:value-of select="contact2_phone"/>					
						</div>
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'E-post')"/>
							</label>
							<xsl:value-of select="contact2_email"/>			
						</div>
					</xsl:if>
				</div>
			</div>
			<div class="proplist-col">
				<xsl:if test="transferred = ''">
					<xsl:variable name="edit_url">
						<xsl:value-of select="edit_url"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="edit" value="{lang_edit}" onMouseout="window.status='';return true;" onClick="window.location = '{edit_url}';"/>
				</xsl:if>
				<xsl:variable name="cancel_url">
					<xsl:value-of select="cancel_url"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel" value="{lang_cancel}" onMouseout="window.status='';return true;" onClick="window.location = '{cancel_url}';"/>
			</div>
		</form>
	</div>
</xsl:template>


<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of select="name"/>
	</option>
</xsl:template>