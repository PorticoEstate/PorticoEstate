<!-- $Id$ -->

<xsl:template match="send" xmlns:php="http://php.net/xsl">
	<form ENCTYPE="multipart/form-data" name="form" method="post" action="{form_action}" class="pure-form pure-form-{form_type}" >
		<xsl:choose>
			<xsl:when test="msgbox_data != ''">
				<dl>
					<dt>
						<xsl:call-template name="msgbox"/>
					</dt>
				</dl>
			</xsl:when>
		</xsl:choose>
		<fieldset>
			<legend>
				<xsl:value-of select="php:function('lang', 'support')" />
			</legend>

			<div class="pure-control-group" style="display:none;">
				<label>
					<xsl:value-of select="php:function('lang', 'address')" />
				</label>
				<input type="text" name="values[address]" value="{support_address}" class="pure-input-3-4">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'address')" />
					</xsl:attribute>
				</input>
			</div>
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'from')" />
				</label>
				<xsl:value-of select="from_name"/>
			</div>
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'from adress')" />
				</label>
				<input type="email" name="values[from_address]" value="{from_address}" class="pure-input-3-4">
					<xsl:attribute name="required">
						<xsl:text>required</xsl:text>
					</xsl:attribute>
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'address')" />
					</xsl:attribute>
				</input>
			</div>
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'description')" />
				</label>
				<textarea name="values[details]" wrap="virtual" class="pure-input-3-4">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'details')" />
					</xsl:attribute>
					<xsl:attribute name="required">
						<xsl:text>required</xsl:text>
					</xsl:attribute>
					<xsl:value-of select="value_details"/>
				</textarea>
			</div>
			<div class="pure-control-group">
				<label>
					<xsl:value-of select="php:function('lang', 'file')" />
				</label>
				<input type="file" name="file" size="50" class="pure-input-3-4">
					<xsl:attribute name="title">
						<xsl:value-of select="php:function('lang', 'file')" />
					</xsl:attribute>
				</input>
			</div>
		</fieldset>
		<div class="pure-controls">
			<xsl:variable name="lang_send">
				<xsl:value-of select="php:function('lang', 'send')" />
			</xsl:variable>
			<input type="submit" name="values[save]" value="{$lang_send}" title='{$lang_send}' class="pure-button pure-button-primary">
			</input>
		</div>
	</form>
</xsl:template>

