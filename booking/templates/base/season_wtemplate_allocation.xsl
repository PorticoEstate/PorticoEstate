<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="jquery_phpgw_i18n"/>

	<div id='message' class='message'/>
 
	<form action="" method="POST" id="form" class="pure-form pure-form-aligned" name="form">
		<div id="tab-content">
			<input type="hidden" name="tab" value=""/>
			<xsl:value-of disable-output-escaping="yes" select="season/tabs"/>
			<div id="allocations">
				<input type="hidden" id="id" name="id">
					<xsl:attribute name="value">
						<xsl:value-of select="season/id"/>
					</xsl:attribute>
				</input>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Organization')" />
					</label>
					<input id="organization_id" name="organization_id" type="hidden">
						<xsl:attribute name="value">
							<xsl:value-of select="season/organization_id"/>
						</xsl:attribute>
					</input>
					<input id="organization_name" name="organization_name" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="season/organization_name"/>
						</xsl:attribute>
					</input>					
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Day of the week')" />
					</label>
					<select id="wday" name="wday">
						<option value="1">
							<xsl:if test="season/wday = '1'">
								<xsl:attribute name="selected" value="selected"/>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Monday')" />
						</option>
						<option value="2">
							<xsl:if test="season/wday = '2'">
								<xsl:attribute name="selected" value="selected"/>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Tuesday')" />
						</option>
						<option value="3">
							<xsl:if test="season/wday = '3'">
								<xsl:attribute name="selected" value="selected"/>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Wednesday')" />
						</option>
						<option value="4">
							<xsl:if test="season/wday = '4'">
								<xsl:attribute name="selected" value="selected"/>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Thursday')" />
						</option>
						<option value="5">
							<xsl:if test="season/wday = '5'">
								<xsl:attribute name="selected" value="selected"/>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Friday')" />
						</option>
						<option value="6">
							<xsl:if test="season/wday = '6'">
								<xsl:attribute name="selected" value="selected"/>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Saturday')" />
						</option>
						<option value="7">
							<xsl:if test="season/wday = '7'">
								<xsl:attribute name="selected" value="selected"/>
							</xsl:if>
							<xsl:value-of select="php:function('lang', 'Sunday')" />
						</option>
					</select>					
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'From')" />
					</label>
					<input id="from_h" name="from_h" type="text" size="5">
						<xsl:attribute name="value">
							<xsl:value-of select="season/from_h"/>
						</xsl:attribute>
					</input>
					:
					<input id="from_m" name="from_m" type="text" size="5">
						<xsl:attribute name="value">
							<xsl:value-of select="season/from_m"/>
						</xsl:attribute>
					</input>					
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'To')" />
					</label>
					<input id="to_h" name="to_h" type="text" size="5">
						<xsl:attribute name="value">
							<xsl:value-of select="season/to_h"/>
						</xsl:attribute>
					</input>
					:
					<input id="to_m" name="to_m" type="text" size="5">
						<xsl:attribute name="value">
							<xsl:value-of select="season/to_m"/>
						</xsl:attribute>
					</input>									
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Cost')" />
					</label>
					<input id="cost" name="cost" type="text">
						<xsl:attribute name="value">
							<xsl:value-of select="season/cost"/>
						</xsl:attribute>
					</input>					
				</div>			
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Resources')" />
					</label>
					<div id="resources_container" class="custom-container"></div>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<input type="button" class="pure-button pure-button-primary" onclick="saveTemplateAlloc()">
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Save')" />
				</xsl:attribute>
			</input>
			<xsl:if test="season/id != ''">
				<input type="button" class="pure-button pure-button-primary" onclick="deleteTemplateAlloc()">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'Delete')" />
					</xsl:attribute>
				</input>
			</xsl:if>
		</div>
	</form>

	<script type="text/javascript">
		var resourceIds = '<xsl:value-of select="season/resource_ids"/>';
		var lang = <xsl:value-of select="php:function('js_lang', 'Name', 'Account', 'Role', 'Actions', 'Edit', 'Delete', 'Resource Type')"/>;
                
    <![CDATA[
        var resourcesURL    = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
    ]]>
		var selection = <xsl:value-of select="season/resource_selected"/>;
		var colDefsRespurces = [
		{label: '', object: [{type: 'input', attrs: [{name: 'type', value: 'checkbox'}, {name: 'name', value: 'resources[]'}, {name: 'class', value: 'resources_checks'}]}], value: 'id', checked: selection},
		{key: 'name', label: lang['Name']}
		];

		createTable('resources_container', resourcesURL, colDefsRespurces, '', 'pure-table pure-table-bordered');
	
	</script>
</xsl:template>
