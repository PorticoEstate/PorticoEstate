
<!-- $Id: price_item.xsl 12604 2015-01-15 17:06:11Z nelson224 $ -->
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="edit">
			<xsl:apply-templates select="edit" />
		</xsl:when>
	</xsl:choose>
	<xsl:choose>
		<xsl:when test="view">
			<xsl:apply-templates select="view" />
		</xsl:when>
	</xsl:choose>

</xsl:template>

<!-- add / edit  -->
<xsl:template xmlns:php="http://php.net/xsl" match="edit">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>

	<div class="content">

		<div id='receipt'></div>
		<div>
			<xsl:variable name="form_action">
				<xsl:value-of select="form_action"/>
			</xsl:variable>

			<script type="text/javascript">
				var lang = <xsl:value-of select="php:function('js_lang', 'Name or company is required', 'next', 'save', 'Name', 'Resource Type', 'Select', 'update service-resource-mapping?')"/>;
			</script>
			<form id="form" name="form" method="post" action="{$form_action}" class="pure-form pure-form-aligned">
				<div id="tab-content">
					<xsl:value-of disable-output-escaping="yes" select="tabs"/>

					<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
					<div id="first_tab">
						<fieldset>
							<!--<legend>-->
								<xsl:value-of select="php:function('lang', 'service')"/>
							<!--</legend>-->
							<xsl:if test="service/id > 0">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'id')"/>
									</label>
									<xsl:value-of select="service/id"/>
								</div>
							</xsl:if>
							<input type="hidden" id="id" name="id" value="{service/id}"/>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'active')"/>
								</label>
								<input type="checkbox" id="field_active" name="active" value="1">
									<xsl:if test="service/active = 1">
										<xsl:attribute name="checked" value="checked"/>
									</xsl:if>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_name">
									<xsl:value-of select="php:function('lang', 'name')"/>
								</label>
								<input type="text" id="field_name" name="name" value="{service/name}" class="pure-input-1-2" >
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:attribute name="placeholder">
										<xsl:value-of select="php:function('lang', 'name')"/>
									</xsl:attribute>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_description">
									<xsl:value-of select="php:function('lang', 'description')"/>
								</label>
								<textarea id="field_description" name="description" class="pure-input-1-2">
									<xsl:attribute name="data-validation">
										<xsl:text>required</xsl:text>
									</xsl:attribute>
									<xsl:value-of select="service/description"/>
								</textarea>
							</div>
		
						</fieldset>
					</div>
					<div id="mapping">
						<fieldset>
							<!--<legend>-->
								<xsl:value-of select="php:function('lang', 'mapping')"/>
							<!--</legend>-->

							<div id="tree_container">
								<script type="text/javascript">
									var treedata = null;
									<xsl:if test="treedata != ''">
										treedata = <xsl:value-of select="treedata"/>;
									</xsl:if>
								</script>
								<!-- markup for expand/contract links -->
								<div id="treecontrol">
									<a id="collapse" title="Collapse the entire tree below" href="#">
										<xsl:value-of select="php:function('lang', 'collapse all')"/>
									</a>
									<xsl:text> | </xsl:text>
									<a id="expand" title="Expand the entire tree below" href="#">
										<xsl:value-of select="php:function('lang', 'expand all')"/>
									</a>
								</div>
								<div id="treeDiv"></div>
							</div>

						</fieldset>
					</div>

				</div>
				<div id="submit_group_bottom" class="proplist-col">
					<xsl:variable name="lang_save">
						<xsl:value-of select="php:function('lang', 'next')"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="save" id="save_button_bottom" onClick="validate_submit();">
						<xsl:attribute name="value">
							<xsl:value-of select="$lang_save"/>
						</xsl:attribute>
						<xsl:attribute name="title">
							<xsl:value-of select="$lang_save"/>
						</xsl:attribute>
					</input>
					<xsl:variable name="cancel_url">
						<xsl:value-of select="cancel_url"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{cancel_url}';">
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'cancel')"/>
						</xsl:attribute>
					</input>
				</div>
			</form>
		</div>
	</div>
</xsl:template>

<xsl:template xmlns:php="http://php.net/xsl" match="view">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>

	<div class="content">

		<div id='receipt'></div>
		<div>

			<script type="text/javascript">
				var lang = <xsl:value-of select="php:function('js_lang', 'Name or company is required', 'next', 'save', 'Name', 'Resource Type', 'Select', 'update service-resource-mapping?')"/>;
			</script>
			<div class="pure-form pure-form-aligned">
				<div id="tab-content">
					<xsl:value-of disable-output-escaping="yes" select="tabs"/>

					<input type="hidden" id="active_tab" name="active_tab" value="{value_active_tab}"/>
					<div id="first_tab">
						<fieldset>
							<!--<legend>-->
								<xsl:value-of select="php:function('lang', 'service')"/>
							<!--</legend>-->
							<xsl:if test="service/id > 0">
								<div class="pure-control-group">
									<label>
										<xsl:value-of select="php:function('lang', 'id')"/>
									</label>
									<xsl:value-of select="service/id"/>
								</div>
							</xsl:if>
							<input type="hidden" id="id" name="id" value="{service/id}"/>
							<div class="pure-control-group">
								<label>
									<xsl:value-of select="php:function('lang', 'active')"/>
								</label>
								<input type="checkbox" id="field_active" disabled="disabled" name="active" value="1">
									<xsl:if test="service/active = 1">
										<xsl:attribute name="checked" value="checked"/>
									</xsl:if>
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_name">
									<xsl:value-of select="php:function('lang', 'name')"/>
								</label>
								<input type="text" id="field_name" name="name" value="{service/name}" disabled="disabled" class="pure-input-1-2" >
								</input>
							</div>
							<div class="pure-control-group">
								<label for="field_description">
									<xsl:value-of select="php:function('lang', 'description')"/>
								</label>
								<xsl:value-of disable-output-escaping="yes" select="service/description"/>
							</div>

						</fieldset>
					</div>
					<div id="mapping">
						<fieldset>
							<!--<legend>-->
								<xsl:value-of select="php:function('lang', 'mapping')"/>
							<!--</legend>-->

							<div id="tree_container">
								<script type="text/javascript">
									var treedata = null;
									<xsl:if test="treedata != ''">
										treedata = <xsl:value-of select="treedata"/>;
									</xsl:if>
								</script>
								<!-- markup for expand/contract links -->
								<div id="treecontrol">
									<a id="collapse" title="Collapse the entire tree below" href="#">
										<xsl:value-of select="php:function('lang', 'collapse all')"/>
									</a>
									<xsl:text> | </xsl:text>
									<a id="expand" title="Expand the entire tree below" href="#">
										<xsl:value-of select="php:function('lang', 'expand all')"/>
									</a>
								</div>
								<div id="treeDiv"></div>
							</div>

						</fieldset>
					</div>

				</div>
				<div id="submit_group_bottom" class="proplist-col">
					<xsl:variable name="cancel_url">
						<xsl:value-of select="cancel_url"/>
					</xsl:variable>
					<input type="button" class="pure-button pure-button-primary" name="cancel" onClick="window.location = '{cancel_url}';">
						<xsl:attribute name="value">
							<xsl:value-of select="php:function('lang', 'cancel')"/>
						</xsl:attribute>
					</input>
				</div>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>


