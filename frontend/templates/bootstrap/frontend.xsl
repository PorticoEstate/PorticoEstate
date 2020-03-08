
<xsl:template match="data">
	<xsl:apply-templates select="header"/>
	<xsl:apply-templates select="section">
		<xsl:with-param name="template_set">
			<xsl:text>bootstrap</xsl:text>
		</xsl:with-param>
	</xsl:apply-templates>
	<xsl:call-template name="jquery_phpgw_i18n"/>
</xsl:template>


<xsl:template match="header" xmlns:php="http://php.net/xsl">
	<xsl:variable name="messages_url">
		<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:frontend.uimessages.index')" />
	</xsl:variable>

	<style>

		@keyframes check {0% {height: 0;width: 0;}
		25% {height: 0;width: 10px;}
		50% {height: 20px;width: 10px;}
		}
		.checkbox{background-color:#fff;display:inline-block;height:28px;margin:0 .25em;width:28px;border-radius:4px;border:1px solid #ccc;float:right}
		.checkbox span{display:block;height:28px;position:relative;width:28px;padding:0}
		.checkbox span:after{-moz-transform:scaleX(-1) rotate(135deg);-ms-transform:scaleX(-1) rotate(135deg);-webkit-transform:scaleX(-1) rotate(135deg);transform:scaleX(-1) rotate(135deg);-moz-transform-origin:left top;-ms-transform-origin:left top;-webkit-transform-origin:left top;transform-origin:left top;border-right:4px solid #fff;border-top:4px solid #fff;content:'';display:block;height:20px;left:3px;position:absolute;top:15px;width:10px}
		.checkbox span:hover:after{border-color:#999}
		.checkbox input{display:none}
		.checkbox input:checked + span:after{-webkit-animation:check .8s;-moz-animation:check .8s;-o-animation:check .8s;animation:check .8s;border-color:#555}
		.checkbox input:checked + .default:after{border-color:#444}
		.checkbox input:checked + .primary:after{border-color:#2196F3}
		.checkbox input:checked + .success:after{border-color:#8bc34a}
		.checkbox input:checked + .info:after{border-color:#3de0f5}
		.checkbox input:checked + .warning:after{border-color:#FFC107}
		.checkbox input:checked + .danger:after{border-color:#f44336}


		p
		{
		line-height: 0.7em;
		}

		.topHeader:after {
		content: "";
		background: #5bc0de;
		position: absolute;
		bottom: 0;
		left: 0;
		height: 4px;
		width: 20%;
		}

		.smallboxline
		{
		width: 100%;
		background: #5bc0de;
		height: 5px;
		}

	</style>

	<!-- NAVIGATION START -->

	<nav class="navbar navbar-expand-sm bg-light navbar-light">

		<!-- LOGO -->

		<div class="container">
			<div class="float-left">
				<a href="#">
					<img src="{logo_path}" width="200"/>
				</a>
			</div>
			<div class="btn-group float-right">

				<a href="{home_url}"   class="btn btn-light pt-4 pb-4">
					<xsl:value-of select="php:function('lang', 'home')"/>
				</a>
				<a href="{contact_url}" class="btn btn-light pt-4 pb-4">
					<xsl:value-of select="php:function('lang', 'contact_BKB')"/>
				</a>
				<a href="{help_url}" class="btn btn-light pt-4 pb-4">
					<xsl:value-of select="php:function('lang', 'help')"/>
				</a>
				<a href="{$messages_url}" class="btn btn-light pt-4 pb-4">
					<i class="far fa-bell"></i>
					<span class="badge badge-info badge-pill ml-2">
						<xsl:if test="total_messages > 0">
							<xsl:value-of select="total_messages"/>
						</xsl:if>
					</span>
				</a>
				<div class="btn-group">
					<button type="button" class="btn btn-light dropdown-toggle pt-4 pb-4" data-toggle="dropdown">
						<i class="fas fa-user-cog"></i>
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="#">Min profil</a>
						<a class="dropdown-item" href="logout.php" >
							<xsl:value-of select="php:function('lang', 'logout')"/>
						</a>
					</div>
				</div>
			</div>
		</div>

	</nav>

	<!-- NAVIGATION END -->

	<!-- ACCORDION START -->

	<div class="container mt-3">
		<div id="accordion1">
			<div class="card">
				<div class="card-header" id="headingOne">
					<button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						<h5>
							<xsl:value-of select="php:function('lang', 'organisational_units')"/>
							<span class="badge badge-info badge-pill float-right mt-2">
								<xsl:value-of select="number_of_org_units"/>
							</span>
						</h5>
					</button>
				</div>

				<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion1">
					<div class="card-body">
						<div class="row">
							<!-- RESULTATENHET -->
							<div class="col-md-6">

								<div class="card">
									<div class="card-header">
										<h6 class="text-uppercase">
											<xsl:value-of select="php:function('lang', 'select organisational unit')"/>
										</h6>
									</div>

									<form action="{form_action}" method="post">

										<ul class="list-group list-group-flush">

											<xsl:for-each select="org_unit">
												<xsl:sort select="ORG_NAME"/>
												<li class="list-group-item">
													<xsl:value-of disable-output-escaping="yes" select="ORG_NAME"/>
													<label class="checkbox">
														<input type="radio" value="{ORG_UNIT_ID}" name="org_unit_id" onchange="this.form.submit()">
															<xsl:if test="ORG_UNIT_ID = //header/selected_org_unit">
																<xsl:attribute name="checked" value="checked"/>
															</xsl:if>
														</input>
														<span class="success"></span>
													</label>
												</li>
											</xsl:for-each>
										</ul>

									</form>
								</div>
							</div>
							<!-- RESULTATENHET INFO -->
							<div class="col-md-6">

								<div class="card">
									<div class="card-header bg-light content-center">
										<h6 class="text-uppercase">Informasjon resultatenhet</h6>
									</div>
									<div class="card-body row text-center">
										<div class="col">
											<div class="smallboxline"></div>
											<div class="text-value-xl">
												<xsl:value-of select="number_of_locations"/>
											</div>
											<div class="text-uppercase text-muted small">
												<xsl:value-of select="php:function('lang', 'number_of_units')"/>
											</div>
										</div>
										<div class="vr"></div>
										<div class="col">
											<div class="smallboxline"></div>
											<div class="text-value-xl">
												<xsl:value-of select="total_area"/>
											</div>
											<div class="text-uppercase text-muted small">
												<xsl:value-of select="php:function('lang', 'total_area_internal')"/>
											</div>
										</div>
										<div class="vr"></div>
										<div class="col">
											<div class="smallboxline"></div>
											<div class="text-value-xl">
												<xsl:value-of select="total_price"/>
											</div>
											<div class="text-uppercase text-muted small">
												<xsl:value-of select="php:function('lang', 'total_price_internal')"/>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="footer-copyright bg-info" style="height: 10px;">
					</div>
				</div>
			</div>
		</div>
		<div id="accordion">
			<div class="card mt-1">
				<div class="card-header" id="headingTwo">

					<button class="btn collapsed btn-light w-100 text-left" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
						<h5>
							Bygninger
							<xsl:for-each select="locations">
								<xsl:if test="location_code = //header/selected_location">
									<span class="text-lowercase">
										(
										<xsl:choose>
											<xsl:when test="name != ''">
												<xsl:value-of select="name"/>, <xsl:value-of select="location_code"/>
											</xsl:when>
											<xsl:otherwise>
												<xsl:value-of select="$lang_no_name_unit"/> (<xsl:value-of select="location_code"/>)
											</xsl:otherwise>
										</xsl:choose>
										)
									</span>
								</xsl:if>
							</xsl:for-each>
							<span class="badge badge-info badge-pill float-right mt-2">
								<xsl:value-of select="count(locations)"/>
							</span>
						</h5>
					</button>
				</div>

				<div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion">
					<div class="card-body">

						<div class="row">
							<!-- BYGG -->
							<div class="col-md-6">

								<div class="card">
									<div class="card-header">
										<h6 class="text-uppercase">Velg Bygg</h6>
									</div>
									<form action="{form_action}" method="post">
										<ul class="list-group list-group-flush">

											<xsl:for-each select="locations">
												<xsl:sort select="location_code"/>
												<li class="list-group-item">
													<xsl:choose>
														<xsl:when test="name != ''">
															<xsl:value-of select="name"/>
														</xsl:when>
														<xsl:otherwise>
															<xsl:value-of select="$lang_no_name_unit"/> (<xsl:value-of select="location_code"/>)
														</xsl:otherwise>
													</xsl:choose>
													<label class="checkbox">
														<input type="radio" value="{location_code}" name="location" onchange="this.form.submit()">
															<xsl:if test="location_code = //header/selected_location">
																<xsl:attribute name="checked" value="checked"/>
															</xsl:if>
														</input>
														<span class="success"></span>
													</label>
												</li>
											</xsl:for-each>
										</ul>
									</form>
								</div>


							</div>
							<!-- BYGG INFO -->
							<div class="col-md-6">

								<div class="card">
									<div class="card-header bg-light content-center">
										<h6 class="text-uppercase">
											<xsl:value-of select="php:function('lang', 'chosen_unit')"/>
										</h6>

									</div>
									<div class="card-body row text-center">
										<div class="col">
											<div class="smallboxline"></div>
											<div class="text-value-xl">
												<xsl:value-of select="selected_total_area"/>
											</div>
											<div class="text-uppercase text-muted small">
												<xsl:value-of select="php:function('lang', 'total_area_internal')"/>
											</div>
										</div>
										<div class="vr"></div>
										<div class="col">
											<div class="smallboxline"></div>
											<div class="text-value-xl">
												<xsl:value-of select="selected_total_price"/>
											</div>
											<div class="text-uppercase text-muted small">
												<xsl:value-of select="php:function('lang', 'total_price_internal')"/>
											</div>

										</div>
										<div class="vr"></div>
										<div class="col">
											<img  class="img-fluid">
												<xsl:attribute name="src">
													<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:frontend.uifrontend.objectimg')" />
													<xsl:text>&amp;loc_code=</xsl:text>
													<xsl:value-of select="//header/selected_location"/>
												</xsl:attribute>
											</img>
										</div>
									</div>
								</div>

							</div>
						</div>

					</div>
					<div class="footer-copyright bg-info" style="height: 10px;">
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- ACCORDION END -->

	<!-- CONTENT START -->

	<div class="container mt-1">
		<div id="accordion_">
			<div class="card">
				<div class="card-header" id="subMenuHeading">
					<h5 class="mb-0">
						<button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#collapseSubMenu" aria-expanded="true" aria-controls="collapseSubMenu">
							<h5>
								Innholdsmeny
								<xsl:for-each select="tabs_data">
									<xsl:for-each select="node()">
										<span>
											<xsl:if test="location_id = //section/tab_selected">
												(<xsl:value-of select="label"/>)
											</xsl:if>
										</span>
									</xsl:for-each>
								</xsl:for-each>
							</h5>
						</button>
					</h5>
				</div>

				<div id="collapseSubMenu" class="collapse" aria-labelledby="subMenuHeading" data-parent="#accordion_">

					<div class="row p-3">

						<xsl:for-each select="tabs_data">
							<div class="col-md-4">

								<div class="list-group content-left;">
									<button type="button" class="list-group-item list-group-item-action text-uppercase topHeader disabled" style="border: 0px;">
										<h6 class="text-uppercase">Informasjon...</h6>
									</button>
									<xsl:for-each select="node()">
										<a href="{link}" class="list-group-item list-group-item-action font-weight-light" style="border: 0px;">
											<span>
												<xsl:if test="location_id = //section/tab_selected">
													<xsl:attribute name="class">font-weight-bold</xsl:attribute>
												</xsl:if>
												<xsl:value-of select="label"/>
											</span>
										</a>
									</xsl:for-each>
								</div>
							</div>
						</xsl:for-each>
					</div>
				</div>
			</div>
		</div>
	</div>

</xsl:template>
