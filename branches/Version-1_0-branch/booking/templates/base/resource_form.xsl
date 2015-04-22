<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">

		<dl class="form">
			<dt class="heading">
				<xsl:choose>
					<xsl:when test="new_form">
						<xsl:value-of select="php:function('lang', 'Add Resource')" />
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="php:function('lang', 'Edit Resource')" />
					</xsl:otherwise>
				</xsl:choose>
			</dt>
		</dl>

		<xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

		<form action="" method="POST" id="form">
			<dl class="form-col">
				<dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
				<dd><input name="name" id="field_name" type="text" value="{resource/name}"/></dd>
				
				<dt><label for="field_activity_id"><xsl:value-of select="php:function('lang', 'Activity')" /></label></dt>
				<dd>
					<select id="field_activity_id" name="activity_id">
						<xsl:for-each select="activitydata/results">
							<option value="{id}">
								<xsl:if test="resource_id=id">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="name" />
							</option>
						</xsl:for-each>
					</select>
				</dd>
				<dt><label for="field_sort"><xsl:value-of select="php:function('lang', 'Sort order')" /></label></dt>
				<dd><input name="sort" id="field_sort" type="text" value="{resource/sort}"/></dd>
			</dl>
			<dl class="form-col">
				<dt><label for="field_building_name"><xsl:value-of select="php:function('lang', 'Building')" /></label></dt>
				<dd>
					<div class="autocomplete">
						<xsl:if test="new_form or resource/permission/write/building_id">
							<input id="field_building_id" name="building_id" type="hidden" value="{resource/building_id}"/>
						</xsl:if>
						<input id="field_building_name" name="building_name" type="text" value="{resource/building_name}">
							<xsl:if test="not(new_form) and not(resource/permission/write/building_id)">
								<xsl:attribute name="disabled">disabled</xsl:attribute>
							</xsl:if>
						</input>
						<div id="building_container"/>
					</div>
				</dd>
				<dt style="margin-top: 45px;"><label for="field_type"><xsl:value-of select="php:function('lang', 'Resource Type')" /></label></dt>
				<dd>
					<select name='type' id='field_type'>
						<option value=''><xsl:value-of select="php:function('lang', 'Select Type')" />...</option>
						<xsl:for-each select="resource/types/*">
							<option value="{local-name()}">
								<xsl:if test="../../type = local-name()">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>

								<xsl:value-of select="php:function('lang', string(node()))"/>
							</option>
						</xsl:for-each>
					</select>
				</dd>
				
				<xsl:if test="not(new_form)">
					<dt><label for="field_active"><xsl:value-of select="php:function('lang', 'Active')"/></label></dt>
					<dd>
						<select id="field_active" name="active">
							<option value="1">
								<xsl:if test="resource/active=1">
									<xsl:attribute name="selected">checked</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', 'Active')"/>
							</option>
							<option value="0">
								<xsl:if test="resource/active=0">
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
				<dt><label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
				<dd class="yui-skin-sam">
					<textarea id="field_description" name="description" type="text"><xsl:value-of select="resource/description"/></textarea>
				</dd>
			</dl>
			<div class="clr"/>
			<dl class="form-col">

				<dt><label for="field_organizations_ids"><xsl:value-of select="php:function('lang', 'organzations_ids')" /></label></dt>
				<dt><p><xsl:value-of select="php:function('lang', 'organzations_ids_description')" /></p></dt>
				<dd><input name="organizations_ids" id="field_organizations_ids" type="text" value="{resource/organizations_ids}"/></dd>

			</dl>
			<div class="clr"/>

			<div class="form-buttons">
				<input type="submit" id="button">
					<xsl:attribute name="value">
						<xsl:choose>
							<xsl:when test="new_form">
								<xsl:value-of select="php:function('lang', 'Create')"/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="php:function('lang', 'Update')"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
				</input>
				<a class="cancel" href="{resource/cancel_link}">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</a>			
			</div>
		</form>
	</div>
	
	<script type="text/javascript">
		<![CDATA[
		YAHOO.util.Event.addListener(window, "load", function() {
		YAHOO.booking.rtfEditorHelper('field_description');
		});
		]]>
	</script>
</xsl:template>
