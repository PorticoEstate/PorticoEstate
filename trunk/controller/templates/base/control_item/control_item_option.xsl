<!-- $Id: control_item.xsl 8913 2012-02-17 10:14:42Z erikhl $ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:call-template name="yui_phpgw_i18n"/>

<div id="main_wrp">

<h1>Legg verdier til liste</h1>
	
<div class="yui-content">
	<div id="details">
		<xsl:variable name="action_url"><xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicontrol_item_option.save')" /></xsl:variable>
		<form id="frm_add_control_item_option" action="{$action_url}" method="post">
			<input type="hidden" name="control_item_id">
				<xsl:attribute name="value"><xsl:value-of select="control_item/id"/></xsl:attribute>
			</input>
			
			<ul id="control_item_options"></ul>
			
			<div class="row">
				<label>Valgverdi</label>
				<input type="text" name="label" />
				<input type="submit" value="Lagre" />
			</div>
		</form>
	</div>
</div>
</div>
</xsl:template>
