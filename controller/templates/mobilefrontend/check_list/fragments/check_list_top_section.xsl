<xsl:template name="check_list_top_section" xmlns:php="http://php.net/xsl">
	<xsl:param name="active_tab" />
	<xsl:variable name="session_url">
		<xsl:text>&amp;</xsl:text>
		<xsl:value-of select="php:function('get_phpgw_session_url')" />
	</xsl:variable>

	<!-- ==================  CHECKLIST DETAILS INFO  ========================= -->
	<div id="check-list-heading">
		<div class="box-1">
			<h1>Kontroll: <xsl:value-of select="control/title"/></h1>

			<xsl:if test="last_completed_checklist_date != ''">
				<xsl:value-of select="php:function('lang', 'last inspection')" />
				<xsl:text>: </xsl:text>
				<xsl:value-of select="last_completed_checklist_date"/>
			</xsl:if>

			<xsl:if test="inspectors != ''">
				<br/>
				<xsl:value-of select="php:function('lang', 'performed by')" />
				<xsl:text>: </xsl:text>
				<xsl:for-each select="inspectors">
					<div class="custom-control custom-checkbox custom-control-inline">
						<input type="checkbox" class="custom-control-input inspectors" id="inspector{id}" value="{id}">
							<xsl:if test="selected = 1">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:if>
						</input>
						<label class="custom-control-label" for="inspector{id}">
							<xsl:value-of select="name"/>
						</label>
					</div>
				</xsl:for-each>
			</xsl:if>
			<xsl:if test="administrator_list != ''">
				<br/>
				<xsl:value-of select="php:function('lang', 'administrator')" />
				<xsl:text>: </xsl:text>
				<xsl:value-of select="administrator_list"/>
			</xsl:if>
			<xsl:if test="supervisor_name != ''">
				<br/>
				<xsl:value-of select="php:function('lang', 'supervisor')" />
				<xsl:text>: </xsl:text>
				<xsl:value-of select="supervisor_name"/>
			</xsl:if>

			<xsl:choose>
				<xsl:when test="type = 'component'">
					<h2>
						<xsl:value-of select="component_array/xml_short_desc"/>
					</h2>
					<button id = "submit_parent_component" type="button" class="mb-3 btn btn-info btn-block" onclick="show_parent_component_information({component_array/location_id}, {component_array/id});">
						<xsl:value-of select="php:function('lang', 'details')" />
					</button>
					<form ENCTYPE="multipart/form-data" method="post" id="frm_add_picture_parent">
						<xsl:attribute name="action">
							<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicase.add_component_image, phpgw_return_as:json')" />
						</xsl:attribute>

						<input type="hidden" name="component" value="{component_array/location_id}_{component_array/id}" />

						<div id="new_picture_parent" class="container" style="display:none">

							<div class="form-group">
								<!--								<label>
									<xsl:value-of select="php:function('lang', 'picture')" />
								</label>-->
								<div  id="equipment_parent_picture_container"/>
							</div>
							<div class="form-group">

								<div class="input-group">
									<div class="custom-file">
										<input type="file" id="component_parent_picture_file" name="file" class="custom-file-input" aria-describedby="submit_update_component_parent" onchange="show_picture_parent_submit();">
											<xsl:attribute name="accept">image/*</xsl:attribute>
											<xsl:attribute name="capture">camera</xsl:attribute>
										</input>
										<label class="custom-file-label">
											<xsl:value-of select="php:function('lang', 'new picture')" />
										</label>
									</div>
								</div>
								<button id = "submit_update_component_parent" type="submit" class="btn btn-primary btn-lg mr-3 mt-3" style="display:none">
									<xsl:value-of select="php:function('lang', 'add picture')" />
								</button>
							</div>
						</div>

					</form>

					<div id = "form_parent_component_2">
					</div>
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
		<nav class="navbar bg-dark navbar-dark">

			<!-- LOGO -->
			<a class="navbar-brand" href="#" data-toggle="collapse" data-target="#collapsibleNavbar">
				<xsl:value-of select="php:function('lang', $active_tab)"/>
			</a>
			<button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#collapsibleNavbar" aria-expanded="false">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="navbar-collapse collapse" id="collapsibleNavbar" style="">

				<ul class="navbar-nav">
					<xsl:choose>
						<xsl:when test="count(check_list_type) = 0 or check_list_type != 'add_check_list'">
							<xsl:call-template name="check_list_menu">
								<xsl:with-param name="active_tab">
									<xsl:value-of select="$active_tab" />
								</xsl:with-param>
							</xsl:call-template>
							<xsl:call-template name="nav_control_plan" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="add_check_list_menu" />
							<xsl:call-template name="nav_control_plan" />
						</xsl:otherwise>
					</xsl:choose>
				</ul>
			</div>
		</nav>
	</div>

</xsl:template>
