	<!-- $Id$ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>
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
		<xsl:variable name="action_url"><xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicontrol_item.save')" /></xsl:variable>
		<form action="{$action_url}" method="post">
			<input type="hidden" name="id" value="{control_item/id}" />
			<dl class="proplist">
				<dt>
					<label for="title">Tittel</label>
					<xsl:choose>
						<xsl:when test="editable">
							<xsl:if test="control_item/error_msg_array/title != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="control_item/error_msg_array/title" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<input type="text" name="title" id="title" size="80"  class="required">
								<xsl:attribute name="value"><xsl:value-of select="control_item/title"/></xsl:attribute>
							</input>
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
								<xsl:when test="$required_item = 1">
									<input type="checkbox" name="required" id="required" checked="true"/>
								</xsl:when>
								<xsl:otherwise>
									<input type="checkbox" name="required" id="required"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
								<xsl:when test="$required_item = 1">
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
								<h4 class="option-list-heading"><xsl:value-of select="php:function('lang', $lang_type)" /></h4>
								
								<xsl:if test="control_item/options_array/child::node()">								
									<h4 class="option-list-heading">Verdier i liste</h4>
									<ul class="option-list">
									<xsl:for-each select="control_item/options_array">
										<li><xsl:value-of select="option_value" /></li>
									</xsl:for-each>
									</ul>
								</xsl:if>
							</xsl:when>
							<xsl:when test="editable">
								<!-- ==============  RADIOBUTTONS FOR CHOOSING CONTROL ITEM TYPE  ==============  -->
								<xsl:choose>
								<xsl:when test="control_item/type = ''">
										<xsl:for-each select="control_item/control_item_types">
												<xsl:choose>
													<xsl:when test="position() = 1">
														<div class="control_item_type">
															<xsl:variable name="lang_type"><xsl:value-of select="." /></xsl:variable>
															<xsl:variable name="current_control_item_type"><xsl:value-of select="." /></xsl:variable>
															
															<input class="btn active" type="button" value="Velg" />
															<input type="radio" name="control_item_type" value="{$current_control_item_type}" checked="checked"/>
															<xsl:value-of select="php:function('lang', $lang_type)" />
														</div>
													</xsl:when>
													<xsl:otherwise>
														<div class="control_item_type">
															<xsl:variable name="lang_type"><xsl:value-of select="." /></xsl:variable>
															<xsl:variable name="current_control_item_type"><xsl:value-of select="." /></xsl:variable>
															
															<input class="btn" type="button" value="Velg" />
															<input type="radio" name="control_item_type" value="{$current_control_item_type}" />
															<xsl:value-of select="php:function('lang', $lang_type)" />
														</div>
													</xsl:otherwise>
												</xsl:choose>
									</xsl:for-each>
								</xsl:when>
								<xsl:otherwise>
										<xsl:for-each select="control_item/control_item_types">
											<xsl:variable name="current_type"><xsl:value-of select="." /></xsl:variable>
												<xsl:choose>
													<xsl:when test="//control_item/type = $current_type">
														<div class="control_item_type">
															<xsl:variable name="lang_type"><xsl:value-of select="." /></xsl:variable>
															<xsl:variable name="current_control_item_type"><xsl:value-of select="." /></xsl:variable>
															
															<input class="btn active" type="button" value="Velg" />
															<input type="radio" name="control_item_type" value="{$current_control_item_type}" checked="checked"/>
															<xsl:value-of select="php:function('lang', $lang_type)" />
														</div>
													</xsl:when>
													<xsl:otherwise>
														<div class="control_item_type">
															<xsl:variable name="lang_type"><xsl:value-of select="." /></xsl:variable>
															<xsl:variable name="current_control_item_type"><xsl:value-of select="." /></xsl:variable>
															
															<input class="btn" type="button" value="Velg" />
															<input type="radio" name="control_item_type" value="{$current_control_item_type}" />
															<xsl:value-of select="php:function('lang', $lang_type)" />
														</div>
													</xsl:otherwise>
												</xsl:choose>
									</xsl:for-each>
								</xsl:otherwise>
								</xsl:choose>
								
								
								<!-- ==============  FORM FOR SAVING OPTION VALUES FOR LIST  =============  -->
								<xsl:choose>
								<xsl:when test="control_item/options_array/child::node()">
									<div id="add_control_item_option_panel"  style="display:block;">
										<hr />
										<xsl:choose>
											<xsl:when test="//control_item/type = 'control_item_type_3'">
												<h2 class="type">Nedtrekksliste</h2>	
											</xsl:when>
											<xsl:otherwise>
												<h2 class="type">Radioknapper</h2>
											</xsl:otherwise>
										</xsl:choose>
										
										<h3>Legg til verdier som listen skal inneholde</h3>
	
										<input type="hidden" name="control_item_id">
											<xsl:attribute name="value"><xsl:value-of select="control_item/id"/></xsl:attribute>
										</input>
										
										<ul id="control_item_options">
										
											<xsl:for-each select="control_item/options_array">
												<li>
													<label>Listeverdi<span class="order_nr"><xsl:number /></span></label>
													<xsl:variable name="option_value"><xsl:value-of select="option_value" /></xsl:variable>
													<input type="text" name="option_values[]" value="{$option_value}" />
													<span class="btn delete">Slett</span>
												</li>
											</xsl:for-each>
										</ul>
	
										<div id="add_control_item_list_value" class="row">
											<label>Ny listeverdi</label>
											<input type="text" name="option_value" />
											<input class="btn" type="button" value="Legg til" />
										</div>
									</div>
								</xsl:when>
								<xsl:otherwise>
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
								</xsl:otherwise>
								</xsl:choose>
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
								<xsl:if test="control_item/error_msg_array/control_area_id != ''">
									<xsl:variable name="error_msg"><xsl:value-of select="control_item/error_msg_array/control_area_id" /></xsl:variable>
									<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
								</xsl:if>
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
							<xsl:if test="control_item/error_msg_array/what_to_do != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="control_item/error_msg_array/what_to_do" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<textarea name="what_to_do" id="what_to_do" rows="5" cols="60" class="required">
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
							<xsl:if test="control_item/error_msg_array/how_to_do != ''">
								<xsl:variable name="error_msg"><xsl:value-of select="control_item/error_msg_array/how_to_do" /></xsl:variable>
								<div class='input_error_msg'><xsl:value-of select="php:function('lang', $error_msg)" /></div>
							</xsl:if>
							<textarea name="how_to_do" id="how_to_do" rows="5" cols="60" class="required">
								<xsl:value-of select="control_item/how_to_do" disable-output-escaping="yes" />
							</textarea>
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
								<xsl:>index.php?menuaction=controller.uicontrol_item.edit</xsl:>
								<xsl:>&amp;id=</xsl:>
								<xsl:value-of select="control_item/id"/>
								<xsl:value-of select="$session_url"/>
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

