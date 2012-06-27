<!-- $Id$ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

<xsl:call-template name="yui_phpgw_i18n"/>

<div id="main_content">

<xsl:choose>
	<xsl:when test="editable">
		<h1><xsl:value-of select="php:function('lang', 'Register control item')" /></h1>
	</xsl:when>
	<xsl:otherwise>
		<h1><xsl:value-of select="php:function('lang', 'View control item')" /></h1>
	</xsl:otherwise>
</xsl:choose>
	
	<div id="control_item_details">
		<form action="index.php?menuaction=controller.uicontrol_item.save" method="post">
			<input type="hidden" name="id" value="{control_item/id}">
			</input>
			<dl class="proplist">
				<dt>
					<label for="title">Tittel</label>
					<xsl:choose>
						<xsl:when test="editable">
							<input type="text" name="title" id="title" value="{control_item/title}" size="80"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="control_item/title"/>
						</xsl:otherwise>
					</xsl:choose>
				</dt>
				<dt>
					<label for="required" class="line">Skal det være obligatorisk å sjekke kontrollpunktet</label>
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
				</dt>
				<dt>
					<label class="top" for="required">Velg hvordan kontrollpunktet skal sjekkes av kontrollør</label>
					<div class="styleWrp">
						<xsl:variable name="control_item_type"><xsl:value-of select="control_item/type" /></xsl:variable>
						<xsl:choose>
							<xsl:when test="view">
							
								<xsl:variable name="lang_type"><xsl:value-of select="control_item/type" /></xsl:variable>
								<xsl:value-of select="php:function('lang', $lang_type)" />
								
								<xsl:if test="control_item/options_array/child::node()">								
									<h3>Verdier i liste</h3>
									<ul>
									<xsl:for-each select="control_item/options_array">
										<li><xsl:value-of select="option_value" /></li>
									</xsl:for-each>
									</ul>
								</xsl:if>
							</xsl:when>
							<xsl:when test="editable">
							
								<!-- ==============  RADIOBUTTONS FOR CHOOSING CONTROL ITEM TYPE  ==============  -->
								<xsl:for-each select="control_item/control_item_types">
									
									<xsl:variable name="classes">
										<xsl:choose>
											<xsl:when test="position() = 1">
												btn active
											</xsl:when>
											<xsl:otherwise>
												btn
											</xsl:otherwise>
										</xsl:choose>
									</xsl:variable>
									
									<div class="control_item_type">
										<xsl:variable name="lang_type"><xsl:value-of select="." /></xsl:variable>
										<xsl:variable name="current_control_item_type"><xsl:value-of select="." /></xsl:variable>
										
										<input class="{$classes}" type="button" value="Velg" />
										<input type="radio" name="control_item_type" value="{$current_control_item_type}" />
										<xsl:value-of select="php:function('lang', $lang_type)" />
									</div>
								</xsl:for-each>
								
								<!-- ==============  FORM FOR SAVING OPTION VALUES FOR LIST  =============  -->
								<div id="add_control_item_option_panel">
									<hr />
									
									<h2 class="type"></h2>
									<h3>Legg til verdier som listen skal inneholde</h3>
									
									<input type="hidden" name="control_item_id">
										<xsl:attribute name="value"><xsl:value-of select="control_item/id"/></xsl:attribute>
									</input>
										
									<ul id="control_item_options"></ul>
									
									<div id="add_control_item_list_value" class="row">
										<label>Ny listeverdi</label>
										<input type="text" name="option_value" />
										<input class="btn" type="button" value="Legg til" />
									</div>
								</div>
							</xsl:when>
							<xsl:otherwise>
								<xsl:variable name="lang_type"><xsl:value-of select="control_item/type" /></xsl:variable>
								<xsl:value-of select="php:function('lang', $lang_type)" />
							</xsl:otherwise>
						</xsl:choose>
					</div>
				</dt>
				<dt>
					<label class="top" for="required">Velg hvilken kontrollgruppe kontrollpunktet skal tilhøre</label>
					<div class="styleWrp">
						<div class="row">
						<label for="control_area">Kontrollområde</label>
						<xsl:choose>
							<xsl:when test="editable">
								<select class="required" id="control_area" name="control_area">
								<option value="">Velg kontrollområde</option>
									<xsl:for-each select="control_areas">
									<xsl:value-of disable-output-escaping="yes" select="name"/>
										<xsl:choose>
											<xsl:when test="cat_id = //control_item/control_area_id">
												<option value="{cat_id}" selected="selected">
													<xsl:value-of disable-output-escaping="yes" select="name"/>
												</option>
											</xsl:when>
											<xsl:otherwise>
												<option value="{cat_id}">
													<xsl:value-of disable-output-escaping="yes" select="name"/>
												</option>
											</xsl:otherwise>
										</xsl:choose>								
									</xsl:for-each>
								</select>
								<span class="help_text">Angi hvilket kontrollområde kontrollen skal gjelde for</span>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="control_item/control_area_name" />
							</xsl:otherwise>
						</xsl:choose>
						</div>
						<div class="row">
						<label for="control_group">Kontrollgruppe</label>
						
						<xsl:choose>
							<xsl:when test="editable">
								<select id="control_group" name="control_group">
									<option value="0">Ingen valgt</option>
									<xsl:for-each select="control_groups">
										<xsl:choose>
											<xsl:when test="id = //control_item/control_group_id">
												<option value="{id}" selected="selected">
													<xsl:value-of disable-output-escaping="yes" select="group_name"/>
												</option>
											</xsl:when>
											<xsl:otherwise>
												<option value="{id}">
													<xsl:value-of disable-output-escaping="yes" select="group_name"/>
												</option>
											</xsl:otherwise>
										</xsl:choose>								
									</xsl:for-each>
								</select>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="control_item/control_group_name" />
							</xsl:otherwise>
						</xsl:choose>
						</div>
					</div>
				</dt>	
				<dt>
					<label for="what_to_do">Hva skal utføres</label>
					<xsl:choose>
						<xsl:when test="editable">
							<textarea name="what_to_do" id="what_to_do" rows="5" cols="60">
								<xsl:value-of select="control_item/what_to_do" disable-output-escaping="yes" />
							</textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="control_item/what_to_do" disable-output-escaping="yes" />
						</xsl:otherwise>
					</xsl:choose>
				</dt>
				<dt>
					<label for="how_to_do">Utførelsesbeskrivelse</label>
					<xsl:choose>
						<xsl:when test="editable">
							<textarea name="how_to_do" id="how_to_do" rows="5" cols="60"><xsl:value-of select="control_item/how_to_do" disable-output-escaping="yes" /></textarea>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="control_item/how_to_do" disable-output-escaping="yes" />
						</xsl:otherwise>
					</xsl:choose>
				</dt>
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
						<a class="btn">
							<xsl:attribute name="href">
								<xsl:text>index.php?menuaction=controller.uicontrol_item.edit</xsl:text>
								<xsl:text>&amp;id=</xsl:text>
								<xsl:value-of select="control_item/id"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'edit')" />
						</a>
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

