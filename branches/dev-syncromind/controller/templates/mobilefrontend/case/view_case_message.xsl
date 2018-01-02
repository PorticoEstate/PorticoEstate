<!-- $Id: edit_check_list.xsl 8374 2011-12-20 07:45:04Z vator $ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">

	<div id="main_content" class="medium">
	
		<div id="check-list-heading">
			<div class="box-1">
				<h1>Kontroll: <xsl:value-of select="control/title"/></h1>
				<xsl:choose>
					<xsl:when test="type = 'component'">
						<h2>
							<xsl:value-of select="component_array/xml_short_desc"/>
						</h2>
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="location_level = 1">
								<h2>Eiendom: <xsl:value-of select="location_array/loc1_name"/></h2>
							</xsl:when>
							<xsl:otherwise>
								<h2>Bygg: <xsl:value-of select="location_array/loc2_name"/></h2>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:otherwise>
				</xsl:choose>
			</div>
			<!-- ==================  CHECKLIST TAB MENU  ===================== -->
			<div class="pure-menu pure-menu-horizontal pure-menu-scrollable">
				<ul class="pure-menu-list">
					<xsl:call-template name="check_list_menu" />
					<xsl:choose>
						<xsl:when test="type = 'component'">
							<li class="pure-menu-item">

								<a class="pure-menu-link bigmenubutton">
									<xsl:attribute name="href">
										<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicomponent.index' )" />
										<xsl:text>&amp;year=</xsl:text>
										<xsl:value-of select="current_year"/>
										<xsl:text>&amp;month=</xsl:text>
										<xsl:value-of select="current_month_nr"/>
										<xsl:text>&amp;location_id=</xsl:text>
										<xsl:value-of select="component_array/location_id"/>
										<xsl:text>&amp;component_id=</xsl:text>
										<xsl:value-of select="component_array/id"/>
										<xsl:text>&amp;get_locations=</xsl:text>
										<xsl:value-of select="get_locations"/>
									</xsl:attribute>
									<i class="fa fa-calendar" aria-hidden="true"></i>
									<xsl:text> </xsl:text>
									Kontrollplan for komponent (år)
								</a>
							</li>
						</xsl:when>
						<xsl:otherwise>
							<li class="pure-menu-item">
								<a class="pure-menu-link bigmenubutton">
									<xsl:attribute name="href">
										<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicalendar.view_calendar_for_year' )" />
										<xsl:text>&amp;year=</xsl:text>
										<xsl:value-of select="current_year"/>
										<xsl:text>&amp;location_code=</xsl:text>
										<xsl:value-of select="location_array/location_code"/>
									</xsl:attribute>
									<i class="fa fa-calendar" aria-hidden="true"></i>
									<xsl:text> </xsl:text>
									Kontrollplan for bygg/eiendom (år)
								</a>
							</li>
							<li class="pure-menu-item">

								<a class="pure-menu-link bigmenubutton">
									<xsl:attribute name="href">
										<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicalendar.view_calendar_for_month' )" />
										<xsl:text>&amp;year=</xsl:text>
										<xsl:value-of select="current_year"/>
										<xsl:text>&amp;month=</xsl:text>
										<xsl:value-of select="current_month_nr"/>
										<xsl:text>&amp;location_code=</xsl:text>
										<xsl:value-of select="location_array/location_code"/>
									</xsl:attribute>
									<i class="fa fa-calendar" aria-hidden="true"></i>
									<xsl:text> </xsl:text>
									Kontrolplan for bygg/eiendom (måned)
								</a>
							</li>
						</xsl:otherwise>
					</xsl:choose>
				</ul>
			</div>		
		
		</div>
	
		<!-- =======================  INFO ABOUT MESSAGE  ========================= -->
		<h3 class="box_header ext">Registrert melding</h3>
		<div id="caseMessage" class="box ext">
		
			<xsl:variable name="show_ticket_params">
				<xsl:text>menuaction:property.uitts.view, id:</xsl:text>
				<xsl:value-of select="message_ticket_id" />
			</xsl:variable>
			<xsl:variable name="show_ticket_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $show_ticket_params )" />
			</xsl:variable>
			<a id="showMessage" target="_blank" href="{$show_ticket_url}">
				<xsl:value-of select="php:function('lang', 'Show message')" />
			</a>
		
			<!-- === TITLE === -->
			<div class="row">
				<label>Tittel på melding:</label>
				<span>
					<xsl:value-of select="message_ticket/subject"/>
				</span>
			</div>
			<!-- === CATEGORY === -->
			<div class="row">
				<label>Kategori</label>
				<span>
					<xsl:value-of select="category"/>
				</span>
			</div>
			<!-- === UPLOAD FILE === -->
			<div class="row">
				<label>Filvedlegg:</label>
				<xsl:for-each select="message_ticket/files">
					<li>
						<xsl:value-of select="."/>
					</li>
				</xsl:for-each>
			</div>
		
			<h3>Meldingen inneholder disse sakene</h3>
			<ul class="cases">
				<xsl:for-each select="check_items_and_cases">
					<xsl:choose>
						<xsl:when test="cases_array/child::node()">
							<li class="check_item">
								<h4>
									<span>
										<xsl:value-of select="control_item/title"/>
									</span>
								</h4>
								<ul>
									<xsl:for-each select="cases_array">
										<xsl:variable name="cases_id">
											<xsl:value-of select="id"/>
										</xsl:variable>
										<li>
											<xsl:value-of select="descr"/>
										</li>
									</xsl:for-each>
								</ul>
							</li>
						</xsl:when>
					</xsl:choose>
				</xsl:for-each>
			</ul>
		
			<xsl:variable name="new_ticket_params">
				<xsl:text>menuaction:controller.uicase.create_case_message, check_list_id:</xsl:text>
				<xsl:value-of select="check_list/id" />
			</xsl:variable>
			<xsl:variable name="new_ticket_url">
				<xsl:value-of select="php:function('get_phpgw_link', '/index.php', $new_ticket_params)" />
			</xsl:variable>
			<a class="pure-button pure-button-primary" href="{$new_ticket_url}">
				<xsl:value-of select="php:function('lang', 'Register new message')" />
			</a>
		</div>
	</div>
</xsl:template>
