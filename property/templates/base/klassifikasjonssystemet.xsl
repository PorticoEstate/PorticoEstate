
<!-- $Id$ -->

<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="login">
			<xsl:apply-templates select="login"/>
		</xsl:when>
		<xsl:when test="get_all">
			<xsl:apply-templates select="get_all"/>
		</xsl:when>
		<xsl:when test="export_data">
			<xsl:apply-templates select="export_data"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- login -->
<xsl:template xmlns:php="http://php.net/xsl" match="login">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>

			<div id="message" class='message'/>

			<div id="login">
				<fieldset>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'username')"/>
						</label>
						<input type="text" id="external_username" name="external_username" value="{value_external_username}" required = '1' class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'username')"/>
							</xsl:attribute>
						</input>
					</div>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'password')"/>
						</label>

						<input type="password" id="external_password" name="external_password" value="" required = '1' class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'password')"/>
							</xsl:attribute>
						</input>
					</div>
					<xsl:if test="value_token != ''">
						<label>
							<xsl:value-of select="php:function('lang', 'token')"/>
						</label>
						<xsl:value-of disable-output-escaping="yes" select="value_token"/>
					</xsl:if>
				</fieldset>
			</div>
		</div>
		<xsl:call-template name="submit_data"/>

	</form>
</xsl:template>

<!-- get_all -->
<xsl:template xmlns:php="http://php.net/xsl" match="get_all">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>

			<div id="message" class='message'/>

			<div id="get_all">
				<fieldset>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'action')"/>
						</label>

						<select name="action" id="action" class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'action')"/>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:apply-templates select="action_list/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'allrows')"/>
						</label>
						<input type="checkbox" name="allrows" value="1">
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'allrows')"/>
							</xsl:attribute>
						</input>
					</div>
					<xsl:if test="value_token != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'token')"/>
							</label>
							<xsl:value-of disable-output-escaping="yes" select="value_token"/>
						</div>
					</xsl:if>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'data')"/>
						</label>
						<xsl:value-of disable-output-escaping="yes" select="data_from_api"/>
					</div>

				</fieldset>
			</div>
		</div>
		<xsl:call-template name="submit_data"/>

	</form>
</xsl:template>

<!-- export_data -->
<xsl:template xmlns:php="http://php.net/xsl" match="export_data">
	<xsl:call-template name="jquery_phpgw_i18n"/>
	<xsl:variable name="form_action">
		<xsl:value-of select="form_action"/>
	</xsl:variable>
	<form class="pure-form pure-form-aligned" id="form" name="form" method="post" action="{$form_action}">
		<div id="tab-content">
			<xsl:value-of disable-output-escaping="yes" select="tabs"/>

			<div id="message" class='message'/>

			<div id="export_data">
				<fieldset>
					<div class="pure-control-group">
						<label>
							Helseforetak
						</label>
						<select name="helseforetak_id" id="helseforetak_id" class="pure-input-1-2" >
							<xsl:apply-templates select="helseforetak_list/options"/>
						</select>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'action')"/>
						</label>

						<select name="action" id="action" class="pure-input-1-2" >
							<xsl:attribute name="title">
								<xsl:value-of select="php:function('lang', 'action')"/>
							</xsl:attribute>
							<option value="">
								<xsl:value-of select="php:function('lang', 'select')"/>
							</option>
							<xsl:apply-templates select="action_list/options"/>
						</select>
					</div>
					<xsl:if test="value_token != ''">
						<div class="pure-control-group">
							<label>
								<xsl:value-of select="php:function('lang', 'token')"/>
							</label>
							<xsl:value-of disable-output-escaping="yes" select="value_token"/>
						</div>
					</xsl:if>

					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'data')"/>
						</label>
						<xsl:value-of disable-output-escaping="yes" select="data_for_export"/>
					</div>

				</fieldset>
			</div>
		</div>
		<xsl:call-template name="submit_data"/>

	</form>
</xsl:template>


<xsl:template xmlns:php="http://php.net/xsl" name="submit_data">
	<div class="proplist-col">
		<xsl:variable name="lang_send">
			<xsl:value-of select="php:function('lang', 'send')"/>
		</xsl:variable>
		<xsl:variable name="lang_cancel">
			<xsl:value-of select="php:function('lang', 'cancel')"/>
		</xsl:variable>
		<input class="pure-button pure-button-primary" type="submit" name="save" value="{$lang_send}">
			<xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'Save the entry and return to list')"/>
			</xsl:attribute>
		</input>
		<input class="pure-button pure-button-primary" type="button" name="cancel" value="{$lang_cancel}">
			<xsl:attribute name="title">
				<xsl:value-of select="php:function('lang', 'Back to the ticket list')"/>
			</xsl:attribute>
		</input>
	</div>


</xsl:template>

<!-- New template-->
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected"/>
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

