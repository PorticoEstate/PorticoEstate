<xsl:template name="check_list_top_section" xmlns:php="http://php.net/xsl">
	<xsl:param name="active_tab" />
	<xsl:variable name="session_url">
		<xsl:text>&amp;</xsl:text>
		<xsl:value-of select="php:function('get_phpgw_session_url')" />
	</xsl:variable>

	<!-- ==================  CHECKLIST DETAILS INFO  ========================= -->
	<div id="check-list-heading">
		<h1>Kontroll: <xsl:value-of select="control/title"/></h1>

		<xsl:if test="last_completed_checklist_date != ''">
			<xsl:value-of select="php:function('lang', 'last inspection')" />
			<xsl:text>: </xsl:text>
			<xsl:value-of select="last_completed_checklist_date"/>
		</xsl:if>
		<xsl:if test="categories != ''">
			<br/>
			<xsl:value-of select="php:function('lang', 'category')" />
			<xsl:text>: </xsl:text>
			<select id ="categories" class="pure-form pure-form-stacked pure-input-1">
				<option>
					<xsl:value-of select="php:function('lang', 'select')"/>
				</option>
				<xsl:for-each select="categories">
					<option value="{id}">
						<xsl:if test="selected = 1">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="name"/>
					</option>
				</xsl:for-each>
			</select>
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
							<!--							<button id = "submit_update_component_parent" type="submit" class="btn btn-primary btn-lg me-3 mt-3" style="display:none">
								<xsl:value-of select="php:function('lang', 'add picture')" />
							</button>-->
						</div>
					</div>

				</form>

				<button id = "update_geolocation" type="button" class="mb-3 btn btn-info btn-block" onclick="update_geolocation({component_array/location_id}, {component_array/id});">
					<xsl:value-of select="php:function('lang', 'update geolocation')" />
				</button>
    <div id="map" style="width: 600px; height: 400px;"></div>
    <div id="popup" class="ol-popup">
        <a href="#" id="popup-closer" class="ol-popup-closer"></a>
        <div id="popup-content"></div>
    </div>
   <style>
        .ol-attribution.ol-logo-only,
        .ol-attribution.ol-uncollapsible {
            max-width: calc(100% - 3em) !important;
            height: 1.5em !important;
        }

        .ol-control button,
        .ol-attribution,
        .ol-scale-line-inner {
            font-family: 'Lucida Grande', Verdana, Geneva, Lucida, Arial, Helvetica, sans-serif !important;
        }

        .ol-popup {
            font-family: 'Lucida Grande', Verdana, Geneva, Lucida, Arial, Helvetica, sans-serif !important;
            font-size: 12px;
            position: absolute;
            background-color: white;
            -webkit-filter: drop-shadow(0 1px 4px rgba(0, 0, 0, 0.2));
            filter: drop-shadow(0 1px 4px rgba(0, 0, 0, 0.2));
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #cccccc;
            bottom: 12px;
            left: -50px;
            min-width: 100px;
        }

        .ol-popup:after,
        .ol-popup:before {
            top: 100%;
            border: solid transparent;
            content: " ";
            height: 0;
            width: 0;
            position: absolute;
            pointer-events: none;
        }

        .ol-popup:after {
            border-top-color: white;
            border-width: 10px;
            left: 48px;
            margin-left: -10px;
        }

        .ol-popup:before {
            border-top-color: #cccccc;
            border-width: 11px;
            left: 48px;
            margin-left: -11px;
        }

        .ol-popup-closer {
            text-decoration: none;
            position: absolute;
            top: 2px;
            right: 8px;
        }

        .ol-popup-closer:after {
            content: "✖";
            color: #c3c3c3;
        }
.ol-box{box-sizing:border-box;border-radius:2px;border:2px solid #00f}.ol-mouse-position{top:8px;right:8px;position:absolute}.ol-scale-line{background:rgba(0,60,136,.3);border-radius:4px;bottom:8px;left:8px;padding:2px;position:absolute}.ol-scale-line-inner{border:1px solid #eee;border-top:none;color:#eee;font-size:10px;text-align:center;margin:1px;will-change:contents,width}.ol-overlay-container{will-change:left,right,top,bottom}.ol-unsupported{display:none}.ol-unselectable,.ol-viewport{-webkit-touch-callout:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;-webkit-tap-highlight-color:transparent}.ol-selectable{-webkit-touch-callout:default;-webkit-user-select:text;-moz-user-select:text;-ms-user-select:text;user-select:text}.ol-grabbing{cursor:-webkit-grabbing;cursor:-moz-grabbing;cursor:grabbing}.ol-grab{cursor:move;cursor:-webkit-grab;cursor:-moz-grab;cursor:grab}.ol-control{position:absolute;background-color:rgba(255,255,255,.4);border-radius:4px;padding:2px}.ol-control:hover{background-color:rgba(255,255,255,.6)}.ol-zoom{top:.5em;left:.5em}.ol-rotate{top:.5em;right:.5em;transition:opacity .25s linear,visibility 0s linear}.ol-rotate.ol-hidden{opacity:0;visibility:hidden;transition:opacity .25s linear,visibility 0s linear .25s}.ol-zoom-extent{top:4.643em;left:.5em}.ol-full-screen{right:.5em;top:.5em}@media print{.ol-control{display:none}}.ol-control button{display:block;margin:1px;padding:0;color:#fff;font-size:1.14em;font-weight:700;text-decoration:none;text-align:center;height:1.375em;width:1.375em;line-height:.4em;background-color:rgba(0,60,136,.5);border:none;border-radius:2px}.ol-control button::-moz-focus-inner{border:none;padding:0}.ol-zoom-extent button{line-height:1.4em}.ol-compass{display:block;font-weight:400;font-size:1.2em;will-change:transform}.ol-touch .ol-control button{font-size:1.5em}.ol-touch .ol-zoom-extent{top:5.5em}.ol-control button:focus,.ol-control button:hover{text-decoration:none;background-color:rgba(0,60,136,.7)}.ol-zoom .ol-zoom-in{border-radius:2px 2px 0 0}.ol-zoom .ol-zoom-out{border-radius:0 0 2px 2px}.ol-attribution{text-align:right;bottom:.5em;right:.5em;max-width:calc(100% - 1.3em)}.ol-attribution ul{margin:0;padding:0 .5em;font-size:.7rem;line-height:1.375em;color:#000;text-shadow:0 0 2px #fff}.ol-attribution li{display:inline;list-style:none;line-height:inherit}.ol-attribution li:not(:last-child):after{content:" "}.ol-attribution img{max-height:2em;max-width:inherit;vertical-align:middle}.ol-attribution button,.ol-attribution ul{display:inline-block}.ol-attribution.ol-collapsed ul{display:none}.ol-attribution:not(.ol-collapsed){background:rgba(255,255,255,.8)}.ol-attribution.ol-uncollapsible{bottom:0;right:0;border-radius:4px 0 0;height:1.1em;line-height:1em}.ol-attribution.ol-uncollapsible img{margin-top:-.2em;max-height:1.6em}.ol-attribution.ol-uncollapsible button{display:none}.ol-zoomslider{top:4.5em;left:.5em;height:200px}.ol-zoomslider button{position:relative;height:10px}.ol-touch .ol-zoomslider{top:5.5em}.ol-overviewmap{left:.5em;bottom:.5em}.ol-overviewmap.ol-uncollapsible{bottom:0;left:0;border-radius:0 4px 0 0}.ol-overviewmap .ol-overviewmap-map,.ol-overviewmap button{display:inline-block}.ol-overviewmap .ol-overviewmap-map{border:1px solid #7b98bc;height:150px;margin:2px;width:150px}.ol-overviewmap:not(.ol-collapsed) button{bottom:1px;left:2px;position:absolute}.ol-overviewmap.ol-collapsed .ol-overviewmap-map,.ol-overviewmap.ol-uncollapsible button{display:none}.ol-overviewmap:not(.ol-collapsed){background:rgba(255,255,255,.8)}.ol-overviewmap-box{border:2px dotted rgba(0,60,136,.7)}.ol-overviewmap .ol-overviewmap-box:hover{cursor:move}
    </style>

    <script src="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/build/ol.js"></script>
   <link rel="stylesheet" href="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/css/ol.css"
        type="text/css"/>




				<p id="demo"></p>

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
		<!-- ==================  CHECKLIST TAB MENU  ===================== -->
		<nav class="navbar bg-dark" data-bs-theme="dark">

			<!-- LOGO -->
			<a class="navbar-brand" href="#" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
				<xsl:value-of select="php:function('lang', $active_tab)"/>
			</a>
			<button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar" aria-expanded="false">
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
	<xsl:choose>
		<xsl:when test="buildings_on_property/child::node() and not(component_children/child::node())">
			<div id="choose-building-wrp" class="row mt-3">
				<xsl:call-template name="select_buildings_on_property" />
				<xsl:if test="$active_tab != 'view_details'">
					<div class="row mt-2">
						<div class="container">
							<h5 class="ms-5">Kontrollert lokasjon</h5>
							<ul class="ms-2">
								<xsl:for-each select="completed_list">
									<li style="display: block;">
										<a href="#">
											<img src="{//img_undo}" width="16" class="me-2" onClick="undo_completed({completed_id})"/>
										</a>
										<img src="{//img_green_check}" width="16" class="me-2"/>
										<xsl:value-of select="name" />
									</li>
								</xsl:for-each>
							</ul>
						</div>
					</div>
				</xsl:if>
			</div>
		</xsl:when>
	</xsl:choose>
	<xsl:choose>
		<xsl:when test="component_children/child::node() and $active_tab != 'view_cases'">
			<div id="choose-building-wrp">
				<xsl:call-template name="select_component_children">
					<xsl:with-param name="template_set">
						<xsl:text>bootstrap</xsl:text>
					</xsl:with-param>
				</xsl:call-template>
				<xsl:if test="$active_tab != 'view_details'">
					<div class="row mt-2">
						<div class="container">
							<h5 class="ms-5">Kontrollert utstyr</h5>
							<ul class="ms-2">
								<xsl:for-each select="completed_list">
									<li style="display: block;">
										<a href="#">
											<img src="{//img_undo}" width="16" class="me-2" onClick="undo_completed({completed_id})"/>
										</a>
										<img src="{//img_green_check}" width="16" class="me-2"/>
										<xsl:value-of select="short_description" />
									</li>
								</xsl:for-each>
							</ul>
						</div>
					</div>
				</xsl:if>
			</div>
		</xsl:when>
	</xsl:choose>
</xsl:template>