<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<xsl:call-template name="msgbox"/>
	<script type="text/javascript">
		var resource_id = "<xsl:value-of select="resource/id"/>";
		var default_schema = "<xsl:value-of select="resource/activity_name"/>";
		var schema_type = "form";
	</script>
				
	<form action="" method="POST" id="form" class="pure-form pure-form-aligned" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="resource/tabs"/>
			<div id="resource" class="booking-container">
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Name')" />
					</label>
					<input name="name" id="field_name" type="text" value="{resource/name}">
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please enter a name')" />
						</xsl:attribute>
					</input>
				</div>
				<xsl:if test="not(new_form)">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Activity')" />
						</label>
						<input id="field_schema_activity_id" type="hidden" name="schema_activity_id" value=""/>
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
					</div>
				</xsl:if>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Sort order')" />
					</label>
					<input name="sort" id="field_sort" type="text" value="{resource/sort}"/>
				</div>
				<xsl:if test="not(new_form)">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Building')"/>
						</label>
						<div class = 'pure-u-md-1-2'>
							<xsl:for-each select="datatable_def">
								<xsl:if test="container = 'datatable-container_0'">
									<xsl:call-template name="table_setup">
										<xsl:with-param name="container" select ='container'/>
										<xsl:with-param name="requestUrl" select ='requestUrl'/>
										<xsl:with-param name="ColumnDefs" select ='ColumnDefs'/>
										<xsl:with-param name="data" select ='data'/>
										<xsl:with-param name="config" select ='config'/>
									</xsl:call-template>
								</xsl:if>
							</xsl:for-each>
						</div>
					</div>
				</xsl:if>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Building')" />
					</label>
					<input id="field_building_id" name="building_id" type="hidden" value=""/>
					<input id="field_building_name" name="building_name" type="text" value="">
						<xsl:if test="new_form">
							<xsl:attribute name="data-validation">
								<xsl:text>required</xsl:text>
							</xsl:attribute>
							<xsl:attribute name="data-validation-error-msg">
								<xsl:value-of select="php:function('lang', 'Please enter a building name')" />
							</xsl:attribute>
						</xsl:if>
					</input>
					<div id="building_container" class="custom-container"></div>
					<xsl:if test="resource/permission/write">
						<a class='button'>
							<xsl:attribute name="onClick">
								<xsl:text>addBuilding()</xsl:text>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Add')" />
						</a>
						<xsl:text> | </xsl:text>
						<a class='button'>
							<xsl:attribute name="onClick">
								<xsl:text>removeBuilding()</xsl:text>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Delete')" />
						</a>
					</xsl:if>

				</div>
				<div class="pure-control-group">
					<label>
						<div id="schema_name"></div>
					</label>
				</div>
				<div id="custom_fields"></div>

				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Resource Type')" />
					</label>
					<select name='type' id='field_type'>
						<xsl:attribute name="data-validation">
							<xsl:text>required</xsl:text>
						</xsl:attribute>
						<xsl:attribute name="data-validation-error-msg">
							<xsl:value-of select="php:function('lang', 'Please select a resource type')" />
						</xsl:attribute>
						<option value=''>
							<xsl:value-of select="php:function('lang', 'Select Type')" />...</option>
						<xsl:for-each select="resource/types/*">
							<option value="{local-name()}">
								<xsl:if test="../../type = local-name()">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
								<xsl:value-of select="php:function('lang', string(node()))"/>
							</option>
						</xsl:for-each>
					</select>
				</div>
				<xsl:if test="not(new_form)">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Active')"/>
						</label>
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
					</div>
				</xsl:if>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'Description')" />
					</label>
					<div class="custom-container">
						<textarea id="field_description" name="description" type="text">
							<xsl:value-of select="resource/description"/>
						</textarea>
					</div>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'organzations_ids')" />
					</label>
				</div>
				<div class="pure-control-group">
					<label>
						<xsl:value-of select="php:function('lang', 'organzations_ids_description')" />
					</label>
					<input name="organizations_ids" id="field_organizations_ids" type="text" value="{resource/organizations_ids}"/>
				</div>
			</div>
		</div>
		<div class="form-buttons">
			<input type="submit" id="button" class="pure-button pure-button-primary">
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
			<input type="button" class="pure-button pure-button-primary" name="cancel">
				<xsl:attribute name="onclick">window.location="<xsl:value-of select="resource/cancel_link"/>"</xsl:attribute>
				<xsl:attribute name="value">
					<xsl:value-of select="php:function('lang', 'Cancel')" />
				</xsl:attribute>
			</input>
		</div>
	</form>
</xsl:template>
