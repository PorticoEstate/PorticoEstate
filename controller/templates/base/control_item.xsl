<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_booking_i18n"/>
<div class="identifier-header">
<h1><img src="{img_go_home}" /> 
		<xsl:value-of select="php:function('lang', 'Control_item')" />
</h1>
</div>

<ul class="check_list">
			<xsl:for-each select="check_list_array">
				<li>
				
			        <span>Tittel:</span><xsl:value-of select="title"/><span>Start dato:</span><xsl:value-of select="start_date"/>
				</li>
			</xsl:for-each>
		</ul>					
		
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
					<xsl:choose>
						<xsl:when test="editable">
							<input type="text" name="title" id="title" value="{control_item/title}" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="control_item/title"/>
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="required">Obligatorisk</label>
					</dt>
					<dd>
					<xsl:variable name="required_item"><xsl:value-of select="control_item/required" /></xsl:variable>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:choose>
								<xsl:when test="$required_item=1">
									<input type="checkbox" name="required" id="required" checked="true"/>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="required" id="required"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
								<xsl:when test="$required_item=1">
									<input type="checkbox" name="required" id="required" checked="true" disabled="true"/>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="required" id="required" disabled="true" />
								</xsl:otherwise>
							</xsl:choose>
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="what_to_do">Hva skal utføres</label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<textarea name="what_to_do" id="what_to_do" rows="5" cols="60"><xsl:value-of select="control_item/what_to_do" disable-output-escaping="yes" /></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="control_item/what_to_do" disable-output-escaping="yes" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="how_to_do">Utførelsesbeskrivelse</label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<textarea name="how_to_do" id="how_to_do" rows="5" cols="60"><xsl:value-of select="control_item/how_to_do" disable-output-escaping="yes" /></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="control_item/how_to_do" disable-output-escaping="yes" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="control_group">Kontrollgruppe</label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<select id="control_group" name="control_group">
								<option value="0">Ingen valgt</option>
								<xsl:apply-templates select="control_group/options"/>
							</select>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="control_item/control_group_name" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>
					<dt>
						<label for="control_area">Kontrollområde</label>
					</dt>
					<dd>
					<xsl:choose>
						<xsl:when test="editable">
							<select id="control_area" name="control_area">
								<xsl:apply-templates select="control_area/options"/>
							</select>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="control_item/control_area_name" />
						</xsl:otherwise>
					</xsl:choose>
					</dd>				
				</dl>
				
				<div class="form-buttons">
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
							<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
							<input type="submit" name="save_control_item" value="{$lang_save}" title = "{$lang_save}" />
							<input type="submit" name="cancel_control_item" value="{$lang_cancel}" title = "{$lang_cancel}" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
							<input type="submit" name="edit_control_item" value="{$lang_edit}" title = "{$lang_edit}" />
						</xsl:otherwise>
					</xsl:choose>
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

