
<xsl:template match="data">
	<xsl:apply-templates select="header"/>
	<div class="container mt-3">
		<xsl:apply-templates select="section">
			<xsl:with-param name="template_set">
				<xsl:text>bootstrap</xsl:text>
			</xsl:with-param>
		</xsl:apply-templates>
	</div>
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
		background: #17a2b8;
		position: absolute;
		bottom: 0;
		left: 0;
		height: 4px;
		width: 20%;
		}

		.smallboxline
		{
		width: 100%;
		background: #17a2b8;
		height: 5px;
		}

		label{
		position: relative;
		cursor: pointer;
		color: #000;
		font-size: 18px;
		}

		input[type="checkbox"], input[type="radio"]{
		position: absolute;
		right: 9000px;
		}


		.toggle input[type="radio"] + .label-text:before{
		content: "\f204";
		font-family: "Font Awesome 5 Free";
		speak: none;
		font-style: normal;
		font-weight: 900;
		font-variant: normal;
		text-transform: none;
		line-height: 1;
		-webkit-font-smoothing:antialiased;
		width: 1em;
		display: inline-block;
		margin-right: 10px;
		}

		.toggle input[type="radio"]:checked + .label-text:before{
		content: "\f205";
		color: #17a2b8;
		animation: effect 250ms ease-in;
		}

		.toggle input[type="radio"]:disabled + .label-text{
		color: #aaa;
		}

		.toggle input[type="radio"]:disabled + .label-text:before{
		content: "\f204";
		color: #ccc;
		}


		@keyframes effect{
		0%{transform: scale(0);}
		25%{transform: scale(1.3);}
		75%{transform: scale(1.4);}
		100%{transform: scale(1);}
		}

		.nav-pills > li > a.active {
		background-color: #17a2b8!important;
		}

		a.footerlink:link {color:#17a2b8;}
		a.footerlink:visited {color:#17a2b8;}
		a.footerlink:hover {color:#FFFFFF;}

		p.footertext {
		line-height: 21px;
		}

		.menubtnactive {
		background: #17a2b8;
		color: #FFFFFF;
		}

		.menubtnactive:hover {
		background: #17a2b8;
		color: #000000;
		}

		ul {
		list-style-type: none;
		}


	</style>

	<!-- NAVIGATION START -->

	<nav class="navbar navbar-expand-sm bg-light navbar-light">

		<!-- LOGO -->

		<div class="container">
			<div class="float-start">
				<a href="#">
					<img src="{logo_path}" width="200"/>
				</a>
			</div>
			<div class="btn-group float-end">

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
					<span class="badge rounded-pill bg-success ms-2">
						<xsl:if test="total_messages > 0">
							<xsl:value-of select="total_messages"/>
						</xsl:if>
					</span>
				</a>
				<div class="btn-group">
					<button type="button" class="btn btn-light dropdown-toggle pt-4 pb-4" data-bs-toggle="dropdown">
						<i class="fas fa-user-cog"></i>
					</button>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#myProfile">Min profil</a>
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
					<button class="btn btn-light w-100 text-start" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
						<h5>
							<xsl:value-of select="php:function('lang', 'organisational_units')"/>
							<span class="badge rounded-pill bg-success float-end mt-2">
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
																<xsl:attribute name="checked">
																	<xsl:text>true</xsl:text>
																</xsl:attribute>
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

					<button class="btn collapsed btn-light w-100 text-start" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
						<h5>
							Bygninger
							<xsl:if test="locations !=''">
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
								<span class="badge rounded-pill bg-success float-end mt-2">
									<xsl:value-of select="count(locations)"/>
								</span>
							</xsl:if>
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
											<xsl:if test="locations !=''">
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
																	<xsl:attribute name="checked">
																		<xsl:text>true</xsl:text>
																	</xsl:attribute>
																</xsl:if>
															</input>
															<span class="success"></span>
														</label>
													</li>
												</xsl:for-each>
											</xsl:if>
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
						<button class="btn btn-light w-100 text-start" data-bs-toggle="collapse" data-bs-target="#collapseSubMenu" aria-expanded="true" aria-controls="collapseSubMenu">
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
													<xsl:attribute name="class">fw-bold</xsl:attribute>
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
	<!-- MODAL PROFILE START -->
	<div class="modal fade" id="myProfile">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Modal Header -->
				<div class="modal-header">
					<h4 id="inspection_title" class="modal-title">Min profil</h4>
				</div>
				<!-- Modal body -->
				<div class="modal-body">
					<xsl:variable name="action_url">
						<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:frontend.uifrontend.save_profile,phpgw_return_as:json')" />
					</xsl:variable>
					<form ENCTYPE="multipart/form-data" name="myProfile_form" id="myProfile_form" method="post" action="{$action_url}" class="was-validated">
						<fieldset class="border p-2">
							<legend  class="w-auto">
								<xsl:value-of select="profile/name" />
							</legend>

							<div class="mb-1">
								<label class="form-label">
									<xsl:value-of select="php:function('lang', 'phone')" />
								</label>
								<input type="text" name="values[cellphone]" value="{profile/cellphone}" required="required" class="form-control">
								</input>
							</div>
							<div class="mb-1">
								<label class="form-label">
									<xsl:value-of select="php:function('lang', 'email')" />
								</label>
								<input type="Email" name="values[email]" value="{profile/email}" required="required" class="form-control">
								</input>
							</div>
							<div class="mb-1">
								<xsl:variable name="lang_send">
									<xsl:value-of select="php:function('lang', 'save')" />
								</xsl:variable>
								<label class="form-label">
									<input type="submit" class="btn btn-primary" name="values[save]" value="{$lang_send}" title='{$lang_send}'/>
								</label>
							</div>
						</fieldset>
					</form>
				</div>

				<!-- Modal footer -->
			</div>
		</div>
	</div>
	<!-- MODAL PROFILE END -->

</xsl:template>
