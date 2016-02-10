<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

	<dl class="form">
    	<dt class="heading">
			<xsl:if test="not(system_message/id)">
				<xsl:value-of select="php:function('lang', 'New System Message')" />
			</xsl:if>
			<xsl:if test="system_message/id">
				<xsl:value-of select="php:function('lang', 'Edit System Message')" />
			</xsl:if>
		</dt>
	</dl>

    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

	<form action="" method="POST">
		<dl class="form-col">
			<dt><label for="field_title"><xsl:value-of select="php:function('lang', 'Title')" /></label></dt>
			<dd><input name="title" type="text" value="{system_message/title}" /></dd>

			<dt><label for="field_message"><xsl:value-of select="php:function('lang', 'Message')" /></label></dt>
			<dd class="yui-skin-sam">
			<textarea id="field-message" name="message" type="text"><xsl:value-of select="system_message/message"/></textarea>
			</dd>
			<dt><label for="field_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
			<dd><input name="name" type="text" value="{system_message/name}" /></dd>
			<dt><label for="field_phone"><xsl:value-of select="php:function('lang', 'Phone')" /></label></dt>
			<dd><input name="phone" type="text" value="{system_message/phone}" /></dd>
			<dt><label for="field_email"><xsl:value-of select="php:function('lang', 'Email')" /></label></dt>
			<dd><input name="email" type="text" value="{system_message/email}" /></dd>

			<dt><label for="field_time"><xsl:value-of select="php:function('lang', 'Created')" /></label></dt>
			<dd>
   			    <input id="inputs" name="created" readonly="true" type="text">
		            <xsl:attribute name="value"><xsl:value-of select="system_message/created"/></xsl:attribute>
		        </input>
			</dd>
		</dl>
		
		<div class="form-buttons">
			<xsl:if test="not(system_message/id)"><input type="submit" value="{php:function('lang', 'Save')}"/></xsl:if>
			<xsl:if test="system_message/id"><input type="submit" value="{php:function('lang', 'Save')}"/></xsl:if>
			<a class="cancel" href="{system_message/cancel_link}">
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
</div>

<script type="text/javascript">
<![CDATA[
var descEdit = new YAHOO.widget.SimpleEditor('field-message', {
    height: '300px',
    width: '522px',
    dompath: true,
    animate: true,
	handleSubmit: true,
        toolbar: {
            titlebar: '',
            buttons: [
               { group: 'textstyle', label: ' ',
                    buttons: [
                        { type: 'push', label: 'Bold', value: 'bold' },
                        { type: 'separator' },
                        { type: 'push', label: 'HTML Link CTRL + SHIFT + L', value: 'createlink'}
                    ]
                }
            ]
        }
});
descEdit.render();
]]>
</script>
</xsl:template>

