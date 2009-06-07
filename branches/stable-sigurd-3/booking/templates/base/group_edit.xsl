<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

	<dl class="form">
    	<dt class="heading">
			<xsl:if test="not(group/id)">
				<xsl:value-of select="php:function('lang', 'New Group')" />
			</xsl:if>
			<xsl:if test="group/id">
				<xsl:value-of select="php:function('lang', 'Edit Group')" />
			</xsl:if>
		</dt>
	</dl>

    <xsl:call-template name="msgbox"/>

	<form action="" method="POST">
		<dl class="form-col">
			<dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
			<dd><input name="name" type="text" value="{group/name}" /></dd>

			<dt><label for="field_organization"><xsl:value-of select="php:function('lang', 'Organization')" /></label></dt>
			<dd>
			    <div class="autocomplete">
			        <input id="field_organization_id" name="organization_id" type="hidden" value="{group/organization_id}"/>
			        <input name="organization_name" type="text" id="field_organization_name" value="{group/organization_name}">
						<xsl:if test="group/filter_organization_id">
							<xsl:attribute name='disabled'>disabled</xsl:attribute>
						</xsl:if>
					</input>
			        <div id="organization_container"/>
			    </div>
			</dd>

			<dt><label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
			<dd class="yui-skin-sam">
			<textarea id="field-description" name="description" type="text"><xsl:value-of select="group/description"/></textarea>
			</dd>
		</dl>
		
		<dl class="form-col">
			<xsl:if test="group/id">
				<dt><label for="field_active"><xsl:value-of select="php:function('lang', 'Active')"/></label></dt>
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
		</dl>
		
		<div style='clear:left; padding:0; margin:0'/>

		<dl class="form-col" style='margin-top:0'>
			<dt class='heading'><xsl:value-of select="php:function('lang', 'Contact 1')" /></dt>

			<dt><label for="field_admin_name_1"><xsl:value-of select="php:function('lang', 'Name')" /></label><br /></dt>
			<dd><input type='text' id='field_admin_name_1' name="contacts[0][name]" value='{group/contacts[1]/name}'/></dd>

			<dt><label for="field_admin_email_1"><xsl:value-of select="php:function('lang', 'Email')" /></label><br /></dt>
			<dd><input type='text' id='field_admin_email_1' name="contacts[0][email]" value='{group/contacts[1]/email}'/></dd>

			<dt><label for="field_admin_phone_1"><xsl:value-of select="php:function('lang', 'Phone')" /></label><br /></dt>
			<dd><input type='text' id='field_admin_phone_1' name="contacts[0][phone]" value='{group/contacts[1]/phone}'/></dd>
		</dl>

		<dl class="form-col" style='margin-top:0'>
			<dt class='heading'><xsl:value-of select="php:function('lang', 'Contact 2')" /></dt>

			<dt><label for="field_admin_name_2"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
			<dd><input type='text' id='field_admin_name_2' name="contacts[1][name]" value='{group/contacts[2]/name}'/></dd>

			<dt><label for="field_admin_email_2"><xsl:value-of select="php:function('lang', 'Email')" /></label><br /></dt>
			<dd><input type='text' id='field_admin_email_2' name="contacts[1][email]" value='{group/contacts[2]/email}'/></dd>

			<dt><label for="field_admin_phone_2"><xsl:value-of select="php:function('lang', 'Phone')" /></label><br /></dt>
			<dd><input type='text' id='field_admin_phone_2' name="contacts[1][phone]" value='{group/contacts[2]/phone}'/></dd>
		</dl>
		
		<div class="form-buttons">
			<xsl:if test="not(group/id)"><input type="submit" value="{php:function('lang', 'Add')}"/></xsl:if>
			<xsl:if test="group/id"><input type="submit" value="{php:function('lang', 'Save')}"/></xsl:if>
			<a class="cancel" href="{group/cancel_link}">
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
</div>

<script type="text/javascript">
var endpoint = '<xsl:value-of select="module" />';
<![CDATA[
var descEdit = new YAHOO.widget.SimpleEditor('field-description', {
    height: '300px',
    width: '522px',
    dompath: true,
    animate: true,
	handleSubmit: true
});
descEdit.render();

YAHOO.booking.autocompleteHelper('index.php?menuaction=' + endpoint + '.uiorganization.index&phpgw_return_as=json&',
    'field_organization_name',
    'field_organization_id',
    'organization_container'
);
]]>
</script>
</xsl:template>

