<!-- $Id: activity_item.xsl 10096 2012-10-03 07:10:49Z vator $ -->
<!-- item  -->

<xsl:template name="requirement_details" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')"/></xsl:variable>

<div class="yui-navset yui-navset-top">
		<div class="yui-content" style="padding: 20px;">
		<div id="details">
			<form action="#" method="post">
				<input type="hidden" name="id" value = "{requirement/id}" />
				<input type="hidden" id="activity_id" name="activity_id" value="{activity/id}" />
							
				<dl class="proplist-col">
					<dt>
						<label for="start_date">Startdato</label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<input class="date" id="start_date" name="start_date" type="text">
						    	<xsl:if test="requirement/start_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(requirement/start_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>	
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="php:function('date', $date_format, number(requirement/start_date))"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
					<dt>
						<label for="end_date">Sluttdato</label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<input class="date" id="end_date" name="end_date" type="text">
						    	<xsl:if test="requirement/end_date != ''">
						      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(requirement/end_date))"/></xsl:attribute>
						    	</xsl:if>
					    	</input>	
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="php:function('date', $date_format, number(requirement/end_date))"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
				
				
					<dt>
						<label for="no_of_items">Antall</label>
					</dt>
					<dd>
						<xsl:choose>
							<xsl:when test="editable">
								<input style="width: 20px;" id="no_of_items" name="no_of_items" type="text" />
							</xsl:when>
							<xsl:otherwise>
							<span><xsl:value-of select="requirement/no_of_items"/></span>
							</xsl:otherwise>
						</xsl:choose>
					</dd>
				
					<xsl:choose>
						<xsl:when test="editable">
						<dt>
							<label>Velg hvilken kategori behovet gjelder</label>
						</dt>
							<dd>
								<select name="location_id" id="location_id">
									<option value="">Velg kategori</option>
									<xsl:for-each select="distict_locations">
										<option value="{location_id}">
											<xsl:value-of select="descr"/>
										</option>
									</xsl:for-each>
								</select>
								<div id="attributes"></div>
							</dd>					
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="location/descr" />
						</xsl:otherwise>
					</xsl:choose>
				</dl>
				
				<div class="form-buttons">
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save')" /></xsl:variable>
							<xsl:variable name="lang_cancel"><xsl:value-of select="php:function('lang', 'cancel')" /></xsl:variable>
							<input type="submit" name="save_requirement" value="{$lang_save}" title = "{$lang_save}" />
							<input type="submit" name="cancel_requirement" value="{$lang_cancel}" title = "{$lang_cancel}" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:variable name="lang_edit"><xsl:value-of select="php:function('lang', 'edit')" /></xsl:variable>
							<input type="submit" name="edit_requirement" value="{$lang_edit}" title = "{$lang_edit}" />
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</form>
		</div>
	</div>
</div>
</xsl:template>

<xsl:template match="options">
	<option value="{id}">
		<xsl:if test="selected">
			<xsl:attribute name="selected" value="selected" />
		</xsl:if>
		<xsl:value-of disable-output-escaping="yes" select="name"/>
	</option>
</xsl:template>
