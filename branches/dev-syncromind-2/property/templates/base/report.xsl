
<xsl:template match="data" xmlns:php="http://php.net/xsl">

	<div id="document_edit_tabview">
		<xsl:value-of select="validator"/>
		
		<xsl:variable name="form_action">
			<xsl:value-of select="form_action"/>
		</xsl:variable>		
		<form name="form" class="pure-form pure-form-aligned" id="form" action="{$form_action}" method="post">	
			<div id="tab-content">					
				<xsl:value-of disable-output-escaping="yes" select="tabs"/>						
				<div id="generic">
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Choose columns')" />
						</label>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Group by')" />
						</label>
					</div>
					<div class="pure-control-group">
						<label>
							<xsl:value-of select="php:function('lang', 'Sort by')" />
						</label>
					</div>												
				</div>
			</div>
			<div class="proplist-col">
				<input type="submit" class="pure-button pure-button-primary" name="save">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'save')" />
					</xsl:attribute>						
					<xsl:attribute name="title">
						<xsl:value-of select="lang_save_statustext"/>
					</xsl:attribute>
				</input>
				<xsl:variable name="cancel_action">
					<xsl:value-of select="cancel_action"/>
				</xsl:variable>
				<input type="button" class="pure-button pure-button-primary" name="cancel"  onclick="location.href='{$cancel_action}'">
					<xsl:attribute name="value">
						<xsl:value-of select="php:function('lang', 'cancel')" />
					</xsl:attribute>
				</input>
			</div>	
		</form>
	</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected = 'selected' or selected = 1">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:attribute name="title" value="description" />
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
