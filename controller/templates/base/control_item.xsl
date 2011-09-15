<!-- item  -->
<xsl:template match="item" xmlns:php="http://php.net/xsl">
	<xsl:variable name="form_action"><xsl:value-of select="form_action"/></xsl:variable>
<div class="identifier-header">
<h1><img src="{img_go_home}" /> 
		<xsl:value-of select="php:function('lang', 'Control_item')" />
</h1>
</div>

<div class="yui-content">
		<div id="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value = "{value_id}">
				</input>
				<dl class="proplist-col">
					<dt>
						<label for="title">Tittel</label>
					</dt>
					<dd>
						<input type="text" name="title" id="title" value="" />
					</dd>
					<dt>
						<label for="required">Obligatorisk</label>
					</dt>
					<dd>
						<input type="checkbox" value="" />
					</dd>
					<dt>
						<label for="what_to_do">Hva skal utføres</label>
					</dt>
					<dd>
						<textarea id="what_to_do" rows="5" cols="60"></textarea>
					</dd>
					<dt>
						<label for="how_to_do">Utførelsesbeskrivelse</label>
					</dt>
					<dd>
						<textarea id="how_to_do" rows="5" cols="60"></textarea>
					</dd>
					<dt>
						<label for="control_group">Kontrollgruppe</label>
					</dt>
					<dd>
						<select id="control_group" name="control_group">
							<xsl:apply-templates select="control_group/options"/>
						</select>
					</dd>
					<dt>
						<label for="control_type">Kontrolltype</label>
					</dt>
					<dd>
						<select id="control_type" name="control_type">
							<xsl:apply-templates select="control_type/options"/>
						</select>
					</dd>				
				</dl>
				
				<div class="form-buttons">
					<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
					<input type="submit" name="save_control" value="{$lang_save}" title = "{$lang_save}">
					</input>
				</div>
				
			</form>
						
		</div>
	</div>
</xsl:template>
	
<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected != 0">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>

