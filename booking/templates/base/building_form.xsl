<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<form action="" method="POST" id='form' class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="building/tabs"/>
			<div id="building_form" class="booking-container">
				<div class="pure-control-group">
					<label for="field_location_code_name">
						<xsl:value-of select="php:function('lang', 'Location Code')" />
					</label>
					<input id="field_location_code" name="location_code" type="hidden" value="{building/location_code}"/>
					<input id="field_location_code_name" name="location_code_name" type="text" value="{building/location_code}" class="pure-input-1-2" >
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Location Code')" />
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label for="field_building_name">
						<xsl:value-of select="php:function('lang', 'Building Name')" />
					</label>
					<input id="field_building_name" name="name" type="text" value="{building/name}" class="pure-input-1-2" >
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please enter a name')"/>
						</xsl:attribute>	
					</input>
				</div>
				<div class="pure-control-group">
					<label for="field_street">
						<xsl:value-of select="php:function('lang', 'Street')"/>
					</label>
					<input id="field_street" name="street" type="text" value="{building/street}" class="pure-input-1-2" />
				</div>
				<div class="pure-control-group">
					<label for="field_district">
						<xsl:value-of select="php:function('lang', 'District')"/>
					</label>
					<input type="text" name="district" id="field_district" value="{building/district}" class="pure-input-1-2" />
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Activity')" />
					</label>
					<select id="field_activity_id" name="activity_id" class="pure-input-1-2" >
						<option value="">
							<xsl:value-of select="php:function('lang', 'select')" />
						</option>
						<xsl:for-each select="activitydata">
							<option value="{id}">
								<xsl:if test="activity_id=id">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="name" />
							</option>
						</xsl:for-each>
					</select>
				</div>
				<div class="pure-control-group">
					<label for="field_phone">
						<xsl:value-of select="php:function('lang', 'Telephone')" />
					</label>
					<input id="field_phone" name="phone" type="text" value="{building/phone}" class="pure-input-1-2" >
					</input>
				</div>
				<div class="pure-control-group">
					<label for="field_email">
						<xsl:value-of select="php:function('lang', 'Email')" />
					</label>
					<input id="field_email" name="email" type="text" value="{building/email}" data-validation="email" class="pure-input-1-2" >
						<xsl:attribute name="data-validation-optional">
							<xsl:text>true</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please enter a valid email')" />
						</xsl:attribute>
					</input>
				</div>
				<div class="pure-control-group">
					<label for="field_homepage">
						<xsl:value-of select="php:function('lang', 'Homepage')" />
					</label>
					<input id="field_homepage" name="homepage" type="text" value="{building/homepage}" class="pure-input-1-2" >
					</input>
				</div>

				<div class="pure-control-group">
					<label for="field_zip_code">
						<xsl:value-of select="php:function('lang', 'Zip code')"/>
					</label>
					<input type="text" name="zip_code" id="field_zip_code" value="{building/zip_code}" class="pure-input-1-2" />
				</div>
				<div class="pure-control-group">
					<label for="field_city">
						<xsl:value-of select="php:function('lang', 'Postal City')"/>
					</label>
					<input type="text" name="city" id="field_city" value="{building/city}" class="pure-input-1-2" />
				</div>

				<xsl:if test="not(new_form)">
					<div class="pure-control-group">
						<label for="field_active">
							<xsl:value-of select="php:function('lang', 'Active')"/>
						</label>
						<select id="field_active" name="active" class="pure-input-1-2" >
							<option value="1">
								<xsl:if test="building/active=1">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Active')"/>
							</option>
							<option value="0">
								<xsl:if test="building/active=0">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Inactive')"/>
							</option>
						</select>
					</div>
				</xsl:if>
				<div class="pure-control-group">
					<label for="field_tilsyn_name">
						<xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" />
					</label>
					<input id="field_tilsyn_name" name="tilsyn_name" type="text" value="{building/tilsyn_name}" class="pure-input-1-2" />
				</div>
				<div class="pure-control-group">
					<label for="field_tilsyn_phone">
						<xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" />
					</label>
					<input id="field_tilsyn_phone" name="tilsyn_phone" type="text" value="{building/tilsyn_phone}" class="pure-input-1-2" />
				</div>
				<div class="pure-control-group">
					<label for="field_tilsyn_email">
						<xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" />
					</label>
					<input id="field_tilsyn_email" name="tilsyn_email" type="text" value="{building/tilsyn_email}" class="pure-input-1-2" />
				</div>
				<div class="pure-control-group">
					<label for="field_tilsyn_name2">
						<xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" />
					</label>
					<input id="field_tilsyn_name2" name="tilsyn_name2" type="text" value="{building/tilsyn_name2}" class="pure-input-1-2" />
				</div>
				<div class="pure-control-group">
					<label for="field_tilsyn_phone2">
						<xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" />
					</label>
					<input id="field_tilsyn_phone2" name="tilsyn_phone2" type="text" value="{building/tilsyn_phone2}" class="pure-input-1-2" />
				</div>
				<div class="pure-control-group">
					<label for="field_tilsyn_email2">
						<xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" />
					</label>
					<input id="field_tilsyn_email2" name="tilsyn_email2" type="text" value="{building/tilsyn_email2}" class="pure-input-1-2" />
				</div>
				<xsl:if test="not(new_form)">
					<div class="pure-control-group">
						<label for="for_field_deactivate_application">
							<xsl:value-of select="php:function('lang', 'Deactivate application')"/>
						</label>
						<select id="for_field_deactivate_application" name="deactivate_application" class="pure-input-1-2" >
							<option value="1">
								<xsl:if test="building/deactivate_application=1">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Yes')"/>
							</option>
							<option value="0">
								<xsl:if test="building/deactivate_application=0">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'No')"/>
							</option>
						</select>
					</div>
				</xsl:if>
				<xsl:if test="not(new_form)">
					<div class="pure-control-group">
						<label for="for_deactivate_calendar">
							<xsl:value-of select="php:function('lang', 'Deactivate calendar')"/>
						</label>
						<select id="for_deactivate_calendar" name="deactivate_calendar" class="pure-input-1-2" >
							<option value="1">
								<xsl:if test="building/deactivate_calendar=1">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Yes')"/>
							</option>
							<option value="0">
								<xsl:if test="building/deactivate_calendar=0">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'No')"/>
							</option>
						</select>
					</div>
				</xsl:if>
				<xsl:if test="not(new_form)">
					<div class="pure-control-group">
						<label for="for_deactivate_sendmessage">
							<xsl:value-of select="php:function('lang', 'Deactivate send message')"/>
						</label>
						<select id="for_deactivate_sendmessage" name="deactivate_sendmessage" class="pure-input-1-2" >
							<option value="1">
								<xsl:if test="building/deactivate_sendmessage=1">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Yes')"/>
							</option>
							<option value="0">
								<xsl:if test="building/deactivate_sendmessage=0">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'No')"/>
							</option>
						</select>
					</div>
				</xsl:if>
				<xsl:if test="not(new_form) and building/extra=1">
					<div class="pure-control-group">
						<label for="for_extra_kalendar">
							<xsl:value-of select="php:function('lang', 'Extra kalendar for public opening times')"/>
						</label>
						<select id="for_extra_kalendar" name="extra_kalendar" class="pure-input-1-2" >
							<option value="1">
								<xsl:if test="building/extra_kalendar=1">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Yes')"/>
							</option>
							<option value="0">
								<xsl:if test="building/extra_kalendar=0">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'No')"/>
							</option>
						</select>
					</div>
				</xsl:if>
				<div class="pure-control-group">
					<label for="field_calendar_text">
						<xsl:value-of select="php:function('lang', 'Calendar text')" />
					</label>
					<textarea id="field_calendar_text" name="calendar_text" type="text" class="pure-input-1-2" >
						<xsl:value-of select="building/calendar_text"/>
					</textarea>
				</div>
				<div class="pure-control-group">
					<label for="field_description">
						<xsl:value-of select="php:function('lang', 'Description')" />
					</label>
					<div class="custom-container">
						<textarea id="field_description" name="description" type="text" class="pure-input-1-2" >
							<xsl:value-of select="building/description"/>
						</textarea>
					</div>
				</div>
				<div class="pure-control-group">
					<label for="field_opening_hours">
						<xsl:value-of select="php:function('lang', 'Opening hours')" />
					</label>
					<div class="custom-container">
						<textarea id="field_opening_hours" name="opening_hours" type="text" class="pure-input-1-2" >
							<xsl:value-of select="building/opening_hours"/>
						</textarea>
					</div>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" class="button pure-button pure-button-primary">
				<xsl:attribute name="value">
					<xsl:choose>
						<xsl:when test="new_form">
							<xsl:value-of select="php:function('lang', 'Create')"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="php:function('lang', 'Save')"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
			</input>
			<input type="button" class="pure-button pure-button-primary" name="cancel">
				<xsl:attribute name="onclick">window.location="<xsl:value-of select="building/cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>
