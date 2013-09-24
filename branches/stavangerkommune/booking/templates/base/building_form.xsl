<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">
		<ul class="pathway">
			<li>
				<a href="{building/buildings_link}">
					<xsl:value-of select="php:function('lang', 'Buildings')" />
				</a>
			</li>
			<xsl:if test="not(new_form)">
				<li>
						<xsl:value-of select="building/name"/>
				</li>
			</xsl:if>
		</ul>

		<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

		<form action="" method="POST">
			<dl class="form-col">
				<dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Building Name')" /></label></dt>
				<dd><input name="name" type="text" value="{building/name}"/></dd>

				<dt><label for="field_phone"><xsl:value-of select="php:function('lang', 'Telephone')" /></label></dt>
				<dd><input id="field_phone" name="phone" type="text" value="{building/phone}"/></dd>

				<dt><label for="field_email"><xsl:value-of select="php:function('lang', 'Email')" /></label></dt>
				<dd><input id="field_email" name="email" type="text" value="{building/email}"/></dd>

				<dt><label for="homepage"><xsl:value-of select="php:function('lang', 'Homepage')" /></label></dt>
				<dd><input name="homepage" type="text" value="{building/homepage}"/></dd>

				<dt><label for="location_code"><xsl:value-of select="php:function('lang', 'Location Code')" /></label></dt>
				<dd>

					<div class="autocomplete">
						<input id="field_location_code" name="location_code" type="hidden" value="{building/location_code}"/>
						<input id="field_location_code_name" name="location_code_name" type="text" value="{building/location_code}"/>
						<div id="location_code_container"/>
					</div>
				</dd>
			</dl>

			<dl class="form-col">
				<dt><label for="field_street"><xsl:value-of select="php:function('lang', 'Street')"/></label></dt>
				<dd><input id="field_street" name="street" type="text" value="{building/street}"/></dd>

				<dt><label for="field_zip_code"><xsl:value-of select="php:function('lang', 'Zip code')"/></label></dt>
				<dd><input type="text" name="zip_code" id="field_zip_code" value="{building/zip_code}"/></dd>

				<dt><label for="field_city"><xsl:value-of select="php:function('lang', 'Postal City')"/></label></dt>
				<dd><input type="text" name="city" id="field_city" value="{building/city}"/></dd>

				<dt><label for='field_district'><xsl:value-of select="php:function('lang', 'District')"/></label></dt>
				<dd><input type="text" name="district" id="field_district" value="{building/district}"/></dd>
				
				<xsl:if test="not(new_form)">
					<dt><label for="field_active"><xsl:value-of select="php:function('lang', 'Active')"/></label></dt>
					<dd>
						<select id="field_active" name="active">
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
					</dd>
				</xsl:if>
			</dl>
			<div class="clr"/>
			<dl class="form-col">
				<dt><label for="field_tilsyn_name"><xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" /></label></dt>
				<dd><input name="tilsyn_name" type="text" value="{building/tilsyn_name}"/></dd>

				<dt><label for="field_tilsyn_phone"><xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" /></label></dt>
				<dd><input id="field_tilsyn_phone" name="tilsyn_phone" type="text" value="{building/tilsyn_phone}"/></dd>

				<dt><label for="field_tilsyn_email"><xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" /></label></dt>
				<dd><input id="field_tilsyn_email" name="tilsyn_email" type="text" value="{building/tilsyn_email}"/></dd>
			</dl>
			<dl class="form-col">

				<dt><label for="field_tilsyn_name2"><xsl:value-of select="php:function('lang', 'Tilsynsvakt name')" /></label></dt>
				<dd><input name="tilsyn_name2" type="text" value="{building/tilsyn_name2}"/></dd>

				<dt><label for="field_tilsyn_phone2"><xsl:value-of select="php:function('lang', 'Tilsynsvakt telephone')" /></label></dt>
				<dd><input id="field_tilsyn_phone2" name="tilsyn_phone2" type="text" value="{building/tilsyn_phone2}"/></dd>

				<dt><label for="field_tilsyn_email2"><xsl:value-of select="php:function('lang', 'Tilsynsvakt email')" /></label></dt>
				<dd><input id="field_tilsyn_email2" name="tilsyn_email2" type="text" value="{building/tilsyn_email2}"/></dd>
			</dl>

			<div class="clr"/>
			<dl class="form-col">
				<xsl:if test="not(new_form)">
					<dt><label for="field_deactivate_application"><xsl:value-of select="php:function('lang', 'Deactivate application')"/></label></dt>
					<dd>
						<select id="for_field_deactivate_application" name="deactivate_application">
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
					</dd>
				</xsl:if>
			</dl>
			<dl class="form-col">
				<xsl:if test="not(new_form)">
					<dt><label for="field_deactivate_calendar"><xsl:value-of select="php:function('lang', 'Deactivate calendar')"/></label></dt>
					<dd>
						<select id="for_deactivate_calendar" name="deactivate_calendar">
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
					</dd>
				</xsl:if>
			</dl>
			<div class="clr"/>
			<dl class="form-col">
				<xsl:if test="not(new_form)">
					<dt><label for="field_deactivate_sendmessage"><xsl:value-of select="php:function('lang', 'Deactivate send message')"/></label></dt>
					<dd>
						<select id="for_deactivate_sendmessage" name="deactivate_sendmessage">
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
					</dd>
				</xsl:if>
			</dl>
			<dl class="form-col">
				<xsl:if test="not(new_form) and building/extra=1">
					<dt><label for="field_extra_kalendar"><xsl:value-of select="php:function('lang', 'Extra kalendar for public opening times')"/></label></dt>

					<dd>
						<select id="for_extra_kalendar" name="extra_kalendar">
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
					</dd>
				</xsl:if>
			</dl>
			<div class="clr"/>

			<dl class="form-col">
				<dt><label for="field_calendar_text"><xsl:value-of select="php:function('lang', 'Calendar text')" /></label></dt>
				<dd class="yui-skin-sam">
					<textarea id="field_calendar_text" name="calendar_text" type="text"><xsl:value-of select="building/calendar_text"/></textarea>
				</dd>
			</dl>

			<div class="clr"/>

			<dl class="form-col">
				<dt><label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
				<dd class="yui-skin-sam">
					<textarea id="field_description" name="description" type="text"><xsl:value-of select="building/description"/></textarea>
				</dd>
			</dl>

			<div class="clr"/>

			<div class="form-buttons">
				<input type="submit">
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
				<a class="cancel" href="{building/cancel_link}">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</a>
			</div>
		</form>
	</div>

	<script type="text/javascript">
		<![CDATA[
		YAHOO.util.Event.addListener(window, "load", function() {
			YAHOO.booking.rtfEditorHelper('field_description');

    		YAHOO.booking.autocompleteHelper('index.php?menuaction=booking.uibuilding.properties&phpgw_return_as=json&',
                                     	'field_location_code_name', 'field_location_code', 'location_code_container');
			});
		]]>
	</script>
</xsl:template>


