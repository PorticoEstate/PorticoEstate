<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>
	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
				<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<style type="text/css">

		.card-img-top {
		width: 100%;
		height: 250px;
		object-fit: cover;
		}

		tr.clickable-row {
		cursor: pointer;
		color: #0275d8;
		}

		tr.clickable-row:hover {
		color: #224ac1;
		background-color: #f7f7f7;
		}


		* ==========================================
		* CUSTOM UTIL CLASSES
		* ==========================================
		*
		*/


		a:hover,a:focus{
		text-decoration: none;
		outline: none;
		}
		#accordion .panel{
		border: none;
		border-radius: 0;
		box-shadow: none;
		margin-bottom: 15px;
		position: relative;
		}
		#accordion .panel:before{
		content: "";
		display: block;
		width: 1px;
		height: 100%;
		border: 1px dashed #0275d8;
		position: absolute;
		top: 25px;
		left: 18px;
		}
		#accordion .panel:last-child:before{ display: none; }
		#accordion .panel-heading{
		padding: 0;
		border: none;
		border-radius: 0;
		position: relative;
		}
		#accordion .panel-title a{
		display: block;
		padding: 10px 30px 10px 60px;
		margin: 0;
		background: #fff;
		font-size: 18px;
		font-weight: 700;
		letter-spacing: 1px;
		color: #1d3557;
		border-radius: 0;
		position: relative;
		}
		#accordion .panel-title a:before,
		#accordion .panel-title a.collapsed:before{
		content: "\f107";
		font-family: "Font Awesome 5 Free";
		font-weight: 900;
		width: 40px;
		height: 100%;
		line-height: 40px;
		background: #0275d8;
		border: 1px solid #0275d8;
		border-radius: 3px;
		font-size: 17px;
		color: #fff;
		text-align: center;
		position: absolute;
		top: 0;
		left: 0;
		transition: all 0.3s ease 0s;
		}
		#accordion .panel-title a.collapsed:before{
		content: "\f105";
		background: #fff;
		border: 1px solid #0275d8;
		color: #000;
		}
		#accordion .panel-body{
		padding: 10px 30px 10px 30px;
		margin-left: 40px;
		background: #fff;
		border-top: none;
		font-size: 15px;
		color: #6f6f6f;
		line-height: 28px;
		letter-spacing: 1px;
		}

		.pure-form-contentTable {display: inline-block;}
	</style>

	<xsl:call-template name="msgbox"/>
	<!-- Begin Page Content -->
	<div class="container-fluid">
		<div class="row pl-3 pr-3">
			<div class="col-md-6 col-xs-12">
				<!-- HEADLINE -->
				<h1 class="h3 mb-1 text-gray-800">
					<i class="fas fa-hashtag mr-2"></i>
					<xsl:value-of select="application/id"/>&nbsp; - &nbsp;<xsl:value-of select="application/building_name"/>
				</h1> <!-- Saksnr + emne i e-post/melding/søknad -->
				<p class="small">
				
				</p>
			</div>
		

			<div class="col-md-6 col-xs-12 text-right">

				<!-- BUTTONS -->
				<ul class="list-inline">
					<li class="list-inline-item mb-2">
						<div class="dropdown">
							<button class="btn bg-white border rounded-pill" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

								<i class="fas fa-circle text-success" aria-hidden="true" title="Endre saksstatus"></i>
								Åpen

							</button>
							<div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
								<a class="dropdown-item" href="#">
									<i class="fas fa-circle mr-1 text-danger"></i>Endre status til <code>lukket</code>
								</a>
								<a class="dropdown-item" href="#">
									<i class="fas fa-fire-alt mr-1 text-warning"></i>Endre status til <code>eskalert</code>
								</a>
							</div>
						</div>


					</li>
					<li class="list-inline-item mb-2">
						<div class="dropdown">
							<button class="btn btn-outline-success btn-circle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

								<i class="fas fa-reply" aria-hidden="true" title="Send svar eller opprett notat i saken"></i>


							</button>
							<div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
								<a class="dropdown-item" href="#">
									<i class="fas fa-reply mr-1 text-success"></i>Send svar til innsender</a>
								<a class="dropdown-item" href="#">
									<i class="far fa-sticky-note mr-1 text-warning"></i>Opprett internt notat</a>
								<a class="dropdown-item" href="#">
									<i class="fas fa-reply mr-2 text-primary"></i>Send melding til saksbehandler</a>
							</div>
						</div>
					</li>
					<li class="list-inline-item mb-2">
						<div class="dropdown">
							<button class="btn btn-outline-warning btn-circle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

								<i class="fas fa-arrow-right" aria-hidden="true" title="Videresend sak"></i>


							</button>
							<div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
								<a class="dropdown-item" href="#">
									<i class="fas fa-arrow-right mr-1 text-warning"></i>Sett sak til en annen saksbehandler</a>
								<a class="dropdown-item" href="#">
									<i class="fas fa-angle-double-up mr-1 text-secondary"></i>Sett sak i en annen kategori</a>
							</div>
						</div>
					</li>
					<li class="list-inline-item mb-2">
						<div class="dropdown">
							<button class="btn btn-outline-primary btn-circle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

								<i class="fas fa-flag" aria-hidden="true" title="Flere handlinger"></i>


							</button>
							<div class="dropdown-menu animated--fade-in" aria-labelledby="dropdownMenuButton">
								<a class="dropdown-item" href="#">
									<i class="fas fa-flag mr-1 text-primary"></i>Overta sak</a>
								<a class="dropdown-item" href="#">
									<i class="fas fa-project-diagram mr-1 text-primary"></i>Knytt til prosjekt</a>
								<a class="dropdown-item" href="#">
									<i class="fas fa-building mr-1 text-primary"></i>Knytt til objekt</a>
							</div>
						</div>
					</li>
				</ul>
			</div>
		</div>


		<!-- TABS LINE START -->

		<div class="row">
			<div class="col-12">
				<ul class="nav nav-pills justify-content-center">
					<li class="nav-item mr-2">
						<a class="nav-link active border" data-toggle="tab" href="#booking">
							<i class="fas fa-calendar-alt fa-3x" aria-hidden="true" title="Søknad"></i>
						</a>

					</li>
					<li class="nav-item mr-2">
						<a class="nav-link border" data-toggle="tab" href="#dialogue">
							<i class="far fa-comments fa-3x" aria-hidden="true" title="Dialog"></i>
						</a>

					</li>
					<li class="nav-item mr-2">
						<a class="nav-link border" data-toggle="tab" href="#checklist">
							<i class="fas fa-clipboard-list fa-3x" aria-hidden="true" title="Sjekkliste"></i>
						</a>
					</li>
					<li class="nav-item mr-2">
						<a class="nav-link border" data-toggle="tab" href="#history">
							<i class="fas fa-history fa-3x" aria-hidden="true" title="Historikk"></i>
						</a>
					</li>
				</ul>
			</div>

		</div>
		<!-- TABS LINE END -->
		<div class="tab-content">

			<!-- FIRST PANE START -->
			<div class="tab-pane active" id="booking">

				<div class="row mt-2 float-right">
					<div class="container-fluid">


						<a href="#" class="btn btn-success btn-icon-split mr-2">
							<span class="icon text-white-50">
								<i class="fas fa-check"></i>
							</span>
							<span class="text">Godkjenn</span>
						</a>

						<a href="#" class="btn btn-danger btn-icon-split">
							<span class="icon text-white-50">
								<i class="fas fa-trash"></i>
							</span>
							<span class="text">Avslå</span>
						</a>
					</div>
				</div>

				<div class="clearfix"></div>
				
				<div class="row mt-3">
					<div class="d-flex w-100 justify-content-between">
						<p class="mb-1">
							<xsl:value-of select="php:function('lang', 'Status')" />
						</p>
					</div>
					<p>
						<xsl:value-of select="php:function('lang', string(application/status))"/>
					</p>
					<div class="d-flex w-100 justify-content-between">
						<p class="mb-1">
							<xsl:value-of select="php:function('lang', 'Created')" />
						</p>
					</div>
					<p>
						<xsl:value-of select="php:function('pretty_timestamp', application/created)"/>
					</p>
					<div class="d-flex w-100 justify-content-between">
						<p class="mb-1">
							<xsl:value-of select="php:function('lang', 'Modified')" />
						</p>
					</div>
					<p>
						<xsl:value-of select="php:function('pretty_timestamp', application/modified)"/>
					</p>
				</div>

				<!-- Accordian -->
				<div class="row mt-3">

					<div class="container-fluid">
						<div class="row">
							<div class="col-md-12">
								<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
									<div class="panel panel-default">
										<div class="panel-heading" role="tab" id="headingOne">
											<h4 class="panel-title">
												<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne" class="collapsed">
													Søker: <xsl:value-of select="application/contact_name"/>
												</a>
											</h4>
										</div>
										<div id="collapseOne" class="panel-collapse in collapse" role="tabpanel" aria-labelledby="headingOne" style="">
											<div class="panel-body">
												<div class="list-group">
													<div class="list-group-item flex-column align-items-start">
														<div class="d-flex w-100 justify-content-between">
															<h5 class="mb-1">
																<xsl:value-of select="php:function('lang', 'Name')" />
															</h5>
															<small></small>
														</div>
														<p class="mb-1 font-weight-bold">
															<xsl:value-of select="application/contact_name"/>
														</p>
														<small>Dette er søkers første søknad i Aktiv kommune.</small>
													</div>
													<div href="#" class="list-group-item flex-column align-items-start">
														<div class="d-flex w-100 justify-content-between">
															<h5 class="mb-1">
																<xsl:value-of select="php:function('lang', 'Email')" />
															</h5>
															<small class="text-muted"></small>
														</div>
														<p class="mb-1 font-weight-bold">
															<xsl:value-of select="application/contact_email"/>
														</p>
														<small class="text-muted"></small>
													</div>
													<div href="#" class="list-group-item flex-column align-items-start">
														<div class="d-flex w-100 justify-content-between">
															<h5 class="mb-1">
																<xsl:value-of select="php:function('lang', 'Phone')" />
															</h5>
															<small class="text-muted"></small>
														</div>
														<p class="mb-1 font-weight-bold">
															<xsl:value-of select="application/contact_phone"/>
														</p>
														<small class="text-muted"></small>
													</div>
													<!--div href="#" class="list-group-item flex-column align-items-start">
														<div class="d-flex w-100 justify-content-between">
															<h5 class="mb-1">Adresse</h5>
															<small class="text-muted"></small>
														</div>
														<p class="mb-1 font-weight-bold">Saksarlia 312</p>
														<small class="text-muted">5253 Sandsli</small>
													</div-->
												</div>
											</div>
										</div>
									</div>

									<xsl:if test="application/customer_organization_name != ''">
										<div class="panel panel-default">
											<div class="panel-heading" role="tab" id="headingTwo">
												<h4 class="panel-title">
													<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
														<xsl:value-of select="php:function('lang', 'Organization')" />: <xsl:value-of select="application/customer_organization_name"/>
													</a>
												</h4>
											</div>
											<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
												<div class="panel-body">

													<div class="list-group">
														<div class="list-group-item flex-column align-items-start">
															<div class="d-flex w-100 justify-content-between">
																<h5 class="mb-1">
																	<xsl:value-of select="php:function('lang', 'Organization')" />
																</h5>
																<small>Hentet fra enhetsregisteret</small>
															</div>
															<p class="mb-1 font-weight-bold">
																<xsl:value-of select="application/customer_organization_name"/>
															</p>
															<small>Dette er organisasjonens første søknad i Aktiv kommune.</small>
														</div>
														<xsl:if test="application/customer_identifier_type = 'organization_number'">
															<div href="#" class="list-group-item flex-column align-items-start">
																<div class="d-flex w-100 justify-content-between">
																	<h5 class="mb-1">
																		<xsl:value-of select="php:function('lang', 'organization number')" />
																	</h5>
																	<small class="text-muted"></small>
																</div>
																<p class="mb-1 font-weight-bold">
																	<xsl:value-of select="application/customer_organization_number"/>
																</p>
																<small class="text-muted"></small>
															</div>
														</xsl:if>
														<xsl:if test="application/customer_identifier_type = 'ssn'">

															<div href="#" class="list-group-item flex-column align-items-start">
																<div class="d-flex w-100 justify-content-between">
																	<h5 class="mb-1">
																		<xsl:value-of select="php:function('lang', 'Date of birth or SSN')" />
																	</h5>
																	<small class="text-muted">Hentet fra ID-Porten</small>
																</div>
																<p class="mb-1 font-weight-bold">
																	<xsl:value-of select="application/customer_ssn"/>
																</p>
																<small class="text-muted"></small>
															</div>
														</xsl:if>
														<div href="#" class="list-group-item flex-column align-items-start">
															<div class="d-flex w-100 justify-content-between">
																<h5 class="mb-1">
																	<xsl:value-of select="php:function('lang', 'Street')"/>
																</h5>
																<small class="text-muted">Hentet fra brukerinput</small>
															</div>
															<p class="mb-1 font-weight-bold">
																<xsl:value-of select="application/responsible_street"/>
															</p>
															<small class="text-muted"></small>
														</div>
														<div href="#" class="list-group-item flex-column align-items-start">
															<div class="d-flex w-100 justify-content-between">
																<h5 class="mb-1">
																	<xsl:value-of select="php:function('lang', 'Zip code')"/>
																</h5>
																<small class="text-muted">Hentet fra brukerinput</small>
															</div>
															<p class="mb-1 font-weight-bold">
																<xsl:value-of select="application/responsible_zip_code"/>
															</p>
															<small class="text-muted"></small>
														</div>
														<div href="#" class="list-group-item flex-column align-items-start">
															<div class="d-flex w-100 justify-content-between">
																<h5 class="mb-1">
																	<xsl:value-of select="php:function('lang', 'Postal City')"/>
																</h5>
																<small class="text-muted">Hentet fra brukerinput</small>
															</div>
															<p class="mb-1 font-weight-bold">
																<xsl:value-of select="application/responsible_city"/>
															</p>
															<small class="text-muted"></small>
														</div>
													</div>



												</div>
											</div>
										</div>
									</xsl:if>

									
									<xsl:if test="simple != 1">

										<div class="panel panel-default">
											<div class="panel-heading" role="tab" id="headingThree">
												<h4 class="panel-title">
													<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
														<xsl:value-of select="php:function('lang', 'Who?')" />
													</a>
												</h4>
											</div>
											<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
												<div class="panel-body">

													<div class="list-group">
														<div class="list-group-item flex-column align-items-start">
															<div class="d-flex w-100 justify-content-between">
																<h5 class="mb-1">
																	<xsl:value-of select="php:function('lang', 'Target audience')" />
																</h5>
																<small></small>
															</div>
															<p class="mb-1 font-weight-bold">
																<ul class="list-left">
																	<xsl:for-each select="audience">
																		<xsl:if test="../application/audience=id">
																			<li>
																				<xsl:value-of select="name"/>
																			</li>
																		</xsl:if>
																	</xsl:for-each>
																</ul>
															</p>
															<small></small>
														</div>
														<div class="list-group-item flex-column align-items-start">
															<div class="d-flex w-100 justify-content-between">
																<h5 class="mb-1">
																	<xsl:value-of select="php:function('lang', 'Number of participants')" />
																</h5>
																<small></small>
															</div>
															<p class="mb-1 font-weight-bold">
																<div class="pure-form-contentTable">
																	<table id="agegroup" class="pure-table pure-table-striped">
																		<thead>
																			<tr>
																				<th>
																					<xsl:value-of select="php:function('lang', 'Name')" />
																				</th>
																				<th>
																					<xsl:value-of select="php:function('lang', 'Male')" />
																				</th>
																				<th>
																					<xsl:value-of select="php:function('lang', 'Female')" />
																				</th>
																			</tr>
																		</thead>
																		<tbody>
																			<xsl:for-each select="agegroups">
																				<xsl:variable name="id">
																					<xsl:value-of select="id"/>
																				</xsl:variable>
																				<tr>
																					<td>
																						<xsl:value-of select="name"/>
																					</td>
																					<td>
																						<xsl:value-of select="../application/agegroups/male[../agegroup_id = $id]"/>
																					</td>
																					<td>
																						<xsl:value-of select="../application/agegroups/female[../agegroup_id = $id]"/>
																					</td>
																				</tr>
																			</xsl:for-each>
																		</tbody>
																	</table>
																</div>
															</p>
															<small></small>
														</div>
													</div>
												</div>
											</div>
										</div>
									</xsl:if>
									<xsl:if test="simple != 1">
										<div class="panel panel-default">
											<div class="panel-heading" role="tab" id="headingFour">
												<h4 class="panel-title">
													<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
														<xsl:value-of select="php:function('lang', 'Why?')" />
													</a>
												</h4>
											</div>
											<div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
												<div class="panel-body">
													<div class="list-group">
														<div class="list-group-item flex-column align-items-start">
															<div class="d-flex w-100 justify-content-between">
																<h5 class="mb-1">
																	<xsl:value-of select="php:function('lang', 'Activity')" />
																</h5>
															</div>
															<p class="mb-1 font-weight-bold">
																<xsl:value-of select="application/activity_name"/>
															</p>
														</div>
														<div class="list-group-item flex-column align-items-start">
															<div class="d-flex w-100 justify-content-between">
																<h5 class="mb-1">
																	<xsl:value-of select="php:function('lang', 'Event name')" />
																</h5>
															</div>
															<p class="mb-1 font-weight-bold">
																<xsl:value-of select="application/name" disable-output-escaping="yes"/>
															</p>
														</div>
														<div class="list-group-item flex-column align-items-start">
															<div class="d-flex w-100 justify-content-between">
																<h5 class="mb-1">
																	<xsl:value-of select="php:function('lang', 'Description')" />
																</h5>
															</div>
															<p class="mb-1 font-weight-bold">
																<xsl:value-of select="application/description" disable-output-escaping="yes"/>
															</p>
														</div>
														<div class="list-group-item flex-column align-items-start">
															<div class="d-flex w-100 justify-content-between">
																<h5 class="mb-1">
																	<xsl:value-of select="php:function('lang', 'Extra info')" />
																</h5>
															</div>
															<p class="mb-1 font-weight-bold">
																<xsl:value-of select="application/equipment" disable-output-escaping="yes"/>
															</p>
														</div>


														<div class="list-group-item flex-column align-items-start">
															<div class="d-flex w-100 justify-content-between">
																<h5 class="mb-1">
																	<xsl:value-of select="php:function('lang', 'Organizer')" />
																</h5>
															</div>
															<p class="mb-1 font-weight-bold">
																<xsl:value-of select="application/organizer" disable-output-escaping="yes"/>
															</p>
														</div>
														<div class="list-group-item flex-column align-items-start">
															<div class="d-flex w-100 justify-content-between">
																<h5 class="mb-1">
																	<xsl:value-of select="php:function('lang', 'Homepage')" />
																</h5>
															</div>
															<p class="mb-1 font-weight-bold">
																<xsl:if test="application/homepage and normalize-space(application/homepage)">
																	<a>
																		<xsl:attribute name="href">
																			<xsl:value-of select="application/homepage"/>
																		</xsl:attribute>
																		<xsl:value-of select="application/homepage"/>
																	</a>
																</xsl:if>
															</p>
														</div>
													</div>
												</div>
											</div>
										</div>
									</xsl:if>
									<div class="panel panel-default">
										<div class="panel-heading" role="tab" id="headingFive">
											<h4 class="panel-title">
												<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
													Ønsker ressurs: <i class="fas fa-redo-alt text-primary"></i>
												</a>
											</h4>
										</div>

										<div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
											<div class="panel-body">
												<div class="list-group">
													<div class="list-group-item flex-column align-items-start">
														<div class="d-flex w-100 justify-content-between">
															<h5 class="mb-1">
																<xsl:value-of select="php:function('lang', 'Building')" />
															</h5>
														</div>
														<p class="mb-1 font-weight-bold">
															<xsl:value-of select="application/building_name"/>
															(<a href="javascript: void(0)" onclick="window.open('{application/schedule_link}', '', 'width=1048, height=600, scrollbars=yes');return false;">
																<xsl:value-of select="php:function('lang', 'Building schedule')" />
															</a>)
														</p>
													</div>
												</div>
												<div class="list-group">
													<div id="resources_container" class="pure-form-contentTable"></div>
												</div>
											</div>
										</div>
									</div>

									<div class="panel panel-default">
										<div class="panel-heading" role="tab" id="headingSix">
											<h4 class="panel-title">
												<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
													<xsl:value-of select="php:function('lang', 'When?')" /> &nbsp;
													<i class="fas fa-clock text-primary"></i>
												</a>
											</h4>
										</div>

										<div id="collapseSix" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSix">
											<div class="panel-body">
												<p>
													<xsl:value-of select="php:function('lang', 'date format')" />:
													<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
												</p>
												<div class="list-group">
													<div class="list-group-item flex-column align-items-start">
														<script type="text/javascript">
															var allocationParams = {};
															var bookingParams = {};
															var eventParams = {};
															var applicationDate = {};
														</script>
														<xsl:variable name='assocdata'>
															<xsl:value-of select="assoc/data" />
														</xsl:variable>
														<xsl:variable name='collisiondata'>
															<xsl:value-of select="collision/data" />
														</xsl:variable>
														<script type="text/javascript">
															building_id = <xsl:value-of select="application/building_id"/>;
														</script>
														<xsl:for-each select="application/dates">
															<div class="pure-control-group">
																<label>
																	<xsl:value-of select="php:function('lang', 'From')" />:</label>
																<span>
																	<xsl:value-of select="php:function('pretty_timestamp', from_)"/>
																</span>
																<xsl:if test="../case_officer/is_current_user">
																	<xsl:if test="contains($collisiondata, from_)">
																		<xsl:if test="not(contains($assocdata, from_))">
																			<script type="text/javascript">
																				applicationDate[<xsl:value-of select="id"/>] = '<xsl:value-of select="substring(from_,0,11)"/>';
																				var oArgs = {menuaction:'bookingfrontend.uibuilding.schedule', id: building_id, backend: true, date: applicationDate[<xsl:value-of select="id"/>]};
																				var scheduleUrl = phpGWLink('bookingfrontend/', oArgs);
																			</script>
																			<a href="javascript: void(0)"
																			   onclick="window.open(scheduleUrl, '', 'width=1048, height=600, scrollbars=yes');return false;">
																				<i class="fa fa-exclamation-circle"></i>
																			</a>
																		</xsl:if>
																	</xsl:if>
																</xsl:if>
															</div>
															<div class="pure-control-group">
																<label>
																	<xsl:value-of select="php:function('lang', 'To')" />:</label>
																<span>
																	<xsl:value-of select="php:function('pretty_timestamp', to_)"/>
																</span>
															</div>
															<xsl:if test="../edit_link">
																<script type="text/javascript">
																	allocationParams[<xsl:value-of select="id"/>] = <xsl:value-of select="allocation_params"/>;
																	bookingParams[<xsl:value-of select="id"/>] = <xsl:value-of select="booking_params"/>;
																	eventParams[<xsl:value-of select="id"/>] = <xsl:value-of select="event_params"/>;
																</script>
																<div class="pure-control-group">
																	<label>&nbsp;</label>
																	<select name="create" onchange="if(this.selectedIndex==1) JqueryPortico.booking.postToUrl('index.php?menuaction=booking.uiallocation.add', allocationParams[{id}]); if(this.selectedIndex==2) JqueryPortico.booking.postToUrl('index.php?menuaction=booking.uibooking.add', eventParams[{id}]); if(this.selectedIndex==3) JqueryPortico.booking.postToUrl('index.php?menuaction=booking.uievent.add', eventParams[{id}]);">
																		<xsl:if test="not(../case_officer/is_current_user)">
																			<xsl:attribute name="disabled">disabled</xsl:attribute>
																		</xsl:if>
																		<xsl:if test="not(contains($assocdata, from_))">
																			<option>
																				<xsl:value-of select="php:function('lang', '- Actions -')" />
																			</option>
																			<option>
																				<xsl:value-of select="php:function('lang', 'Create allocation')" />
																			</option>
																			<option>
																				<xsl:value-of select="php:function('lang', 'Create booking')" />
																			</option>
																			<option>
																				<xsl:value-of select="php:function('lang', 'Create event')" />
																			</option>
																		</xsl:if>
																		<xsl:if test="contains($assocdata, from_)">
																			<xsl:attribute name="disabled">disabled</xsl:attribute>
																			<option>
																				<xsl:value-of select="php:function('lang', '- Created -')" />
																			</option>
																		</xsl:if>
																	</select>
																</div>
															</xsl:if>
														</xsl:for-each>

													</div>
												</div>
											</div>
										</div>
									</div>

									<div class="panel panel-default">
										<div class="panel-heading" role="tab" id="headingSeven">
											<h4 class="panel-title">
												<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
													<xsl:value-of select="php:function('lang', 'payments')" />
												</a>
											</h4>
										</div>
										<div id="collapseSeven" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSeven">
											<div class="panel-body">
												<div id="payments_container"/>
											</div>
										</div>
									</div>
									<div class="panel panel-default">
										<div class="panel-heading" role="tab" id="headingEight">
											<h4 class="panel-title">
												<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
													Booking-konflikter på ressurs: Ingen
												</a>
											</h4>
										</div>
										<div id="collapseEight" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingEight">
											<div class="panel-body">
												<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent nisl lorem, dictum id pellentesque at, vestibulum ut arcu. Curabitur erat libero, egestas eu tincidunt ac, rutrum ac justo. Vivamus condimentum laoreet lectus, blandit posuere tortor aliquam vitae. Curabitur molestie eros. </p>
											</div>
										</div>
									</div>
									<div class="panel panel-default">
										<div class="panel-heading" role="tab" id="headingNine">
											<h4 class="panel-title">
												<a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
													Kart og skisser
												</a>
											</h4>
										</div>
										<div id="collapseNine" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingNine">
											<div class="panel-body">
												<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent nisl lorem, dictum id pellentesque at, vestibulum ut arcu. Curabitur erat libero, egestas eu tincidunt ac, rutrum ac justo. Vivamus condimentum laoreet lectus, blandit posuere tortor aliquam vitae. Curabitur molestie eros. </p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>


				</div>
				<!-- END ACCORDIAN -->

			</div>
			<!-- /FIRST PANE END -->

			
			<!-- FOURTH PANE START -->
			<div class="tab-pane fade" id="history">
				<h3>
					<xsl:value-of select="php:function('lang', 'History and comments (%1)', count(application/comments/author))" />
				</h3>

				<xsl:for-each select="application/comments[author]">
					<div class="panel-body">
						<div class="list-group">
							<div href="#" class="list-group-item flex-column align-items-start">
								<div class="d-flex w-100 justify-content-between">
									<h5 class="mb-1">
										<xsl:value-of select="php:function('pretty_timestamp', time)"/>
									</h5>
									<small class="text-muted">
										<xsl:value-of select="author"/>
									</small>
								</div>
								<p class="mb-1 font-weight-bold">
									<xsl:value-of select="comment" disable-output-escaping="yes"/>
								</p>
								<small class="text-muted"></small>
							</div>
						</div>
					</div>
				</xsl:for-each>
			</div>

			<!-- FOURTH PANE END -->

			<!-- /END TABS CONTAINER -->
		</div>

	</div>


	<div class= "pure-form pure-form-stacked" id="form" name="form">
		<input type="hidden" name="tab" value=""/>
		<div id="application" class="booking-container">
			<fieldset>
				<div class="pure-g">
					<div class="pure-u-1">
						<div class="pure-control-group">
							<xsl:if test="frontend and application/status='ACCEPTED'">
								<form method="POST">
									<input type="hidden" name="print" value="ACCEPTED"/>
									<input type="submit" value="{php:function('lang', 'Print as PDF')}" />
								</form>
							</xsl:if>
						</div>
						<div class="pure-control-group">
							<xsl:if test="not(frontend)">
								<div style="border: 3px solid red; padding: 3px 4px 3px 4px">
									<xsl:choose>
										<xsl:when test="not(application/case_officer)">
											<xsl:value-of select="php:function('lang', 'In order to work with this application, you must first')"/>
											<xsl:text> </xsl:text>
											<a href="#assign">
												<xsl:value-of select="php:function('lang', 'assign yourself')"/>
											</a>
											<xsl:text> </xsl:text>
											<xsl:value-of select="php:function('lang', 'as the case officer responsible for this application.')"/>
										</xsl:when>
										<xsl:when test="application/case_officer and not(application/case_officer/is_current_user)">
											<xsl:value-of select="php:function('lang', 'The user currently assigned as the responsible case officer for this application is')"/>
											<xsl:text> </xsl:text>'<xsl:value-of select="application/case_officer/name"/>'.
											<br/>
											<xsl:value-of select="php:function('lang', 'In order to work with this application, you must therefore first')"/>
											<xsl:text> </xsl:text>
											<a href="#assign">
												<xsl:value-of select="php:function('lang', 'assign yourself')"/>
											</a>
											<xsl:text> </xsl:text>
											<xsl:value-of select="php:function('lang', 'as the case officer responsible for this application.')"/>
										</xsl:when>
										<xsl:otherwise>
											<xsl:attribute name="style">display:none</xsl:attribute>
										</xsl:otherwise>
									</xsl:choose>
								</div>
							</xsl:if>
						</div>
						<!--							<form method="POST">
							<div class="pure-control-group">
								<label for="comment">
									<xsl:value-of select="php:function('lang', 'Add a comment')" />
								</label>
								<textarea name="comment" id="comment" style="width: 60%; height: 7em"></textarea>
								<br/>
							</div>
							<div class="pure-control-group">
								<label>&nbsp;</label>
								<input type="submit" value="{php:function('lang', 'Add comment')}" />
							</div>
						</form>-->
					</div>
				</div>
				<div class="pure-g">
				</div>

				<div class="pure-g">
					<div class="pure-u-1">
						<div class="heading">
							<legend>
								<h3>1.1 <xsl:value-of select="php:function('lang', 'attachments')" /></h3>
							</legend>
						</div>
						<div id="attachments_container"/>
						<br/>
						<form method="POST" enctype='multipart/form-data' id='file_form'>
							<input name="name" id='field_name' type='file' >
								<xsl:attribute name='title'>
									<xsl:value-of select="document/name"/>
								</xsl:attribute>
								<xsl:attribute name="data-validation">
									<xsl:text>mime size</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-allowing">
									<xsl:text>jpg, jpeg, png, gif, xls, xlsx, doc, docx, txt, pdf, odt, ods</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-max-size">
									<xsl:text>2M</xsl:text>
								</xsl:attribute>
								<xsl:attribute name="data-validation-error-msg">
									<xsl:text>Max 2M:: jpg, jpeg, png, gif, xls, xlsx, doc, docx, txt , pdf, odt, ods</xsl:text>
								</xsl:attribute>
							</input>
							<br/>
							<br/>
							<input type="submit" value="{php:function('lang', 'Add attachment')}" />
						</form>

					</div>
				</div>

				<div class="pure-g">

					

				</div>
				<div class="pure-g">
					<div class="pure-u-1">
						<div class="heading">
							<legend>
								<h3>8. <xsl:value-of select="php:function('lang', 'Terms and conditions')" /></h3>
							</legend>
						</div>
						<div class="pure-control-group">
							<xsl:if test="config/application_terms">
								<p>
									<xsl:value-of select="config/application_terms"/>
								</p>
							</xsl:if>
							<br />
							<div id='regulation_documents'>&nbsp;</div>
							<br />
							<p>
								<xsl:value-of select="php:function('lang', 'To borrow premises you must verify that you have read terms and conditions')" />
							</p>
						</div>
					</div>
					<div class="pure-u-1">
						<div class="heading">
							<legend>
								<h4>
									<xsl:value-of select="php:function('lang', 'additional requirements')" />
								</h4>
							</legend>
						</div>
						<xsl:value-of disable-output-escaping="yes" select="application/agreement_requirements"/>
					</div>

				</div>
				<xsl:if test="not(frontend)">
					<div class="pure-g">
						<div class="pure-u-1">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'Associated items')" />
									</h3>
								</legend>
							</div>
							<div class="pure-control-group">
								<div id="associated_container"/>
							</div>
						</div>
						<div id="order_details" class="pure-u-1" style="display:none;">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'details')" />
									</h3>
								</legend>
							</div>
							<div class="pure-control-group">
								<div id="order_container"/>
							</div>
						</div>
					</div>
				</xsl:if>
				<xsl:if test="application/edit_link">
					<div class="pure-g">
						<div class="pure-u-1">
							<div class="heading">
								<legend>
									<h3>
										<xsl:value-of select="php:function('lang', 'Actions')" />
									</h3>
								</legend>
							</div>
							<form method="POST">
								<div class="pure-control-group">
									<label for="comment">
										<xsl:value-of select="php:function('lang', 'Add a comment')" />
									</label>
									<textarea name="comment" id="comment"></textarea>
									<br/>
								</div>
								<div class="pure-control-group">
									<label>&nbsp;</label>
									<input type="submit" value="{php:function('lang', 'Add comment')}" />
								</div>
							</form>
							<br/>
							<div id="return_after_action" class="pure-control-group">
								<xsl:if test="application/case_officer/is_current_user">
									<form method="POST" style="display:inline">
										<input type="hidden" name="unassign_user"/>
										<input type="submit" value="{php:function('lang', 'Unassign me')}" class="pure-button pure-button-primary" />
									</form>
									<form method="POST" style="display:inline">
										<input type="hidden" name="display_in_dashboard" value="{phpgw:conditional(application/display_in_dashboard='1', '0', '1')}"/>
										<input type="submit" value="{php:function('lang', phpgw:conditional(application/display_in_dashboard='1', 'Hide from my Dashboard until new activity occurs', 'Display in my Dashboard'))}" class="pure-button pure-button-primary" />
									</form>
								</xsl:if>
								<xsl:if test="not(application/case_officer/is_current_user)">
									<a name="assign"/>
									<form method="POST">
										<input type="hidden" name="assign_to_user"/>
										<input type="hidden" name="status" value="PENDING"/>
										<input type="submit" value="{php:function('lang', phpgw:conditional(application/case_officer, 'Re-assign to me', 'Assign to me'))}" class="pure-button pure-button-primary" />
										<xsl:if test="application/case_officer">
											<xsl:value-of select="php:function('lang', 'Currently assigned to user:')"/>
											<xsl:text> </xsl:text>
											<xsl:value-of select="application/case_officer/name"/>
										</xsl:if>
									</form>
								</xsl:if>
							</div>
							<xsl:if test="application/status!='REJECTED'">
								<div>
									<form method="POST">
										<input type="hidden" name="status" value="REJECTED"/>
										<input onclick="return confirm('{php:function('lang', 'Are you sure you want to delete?')}')" type="submit" value="{php:function('lang', 'Reject application')}" class="pure-button pure-button-primary">
											<xsl:if test="not(application/case_officer)">
												<xsl:attribute name="disabled">disabled</xsl:attribute>
											</xsl:if>
										</input>
									</form>
								</div>
							</xsl:if>
							<xsl:if test="application/status='PENDING'">
								<xsl:if test="num_associations='0'">
									<input type="submit" disabled="" value="{php:function('lang', 'Accept application')}" class="pure-button pure-button-primary" />
									<xsl:value-of select="php:function('lang', 'One or more bookings, allocations or events needs to be created before an application can be Accepted')"/>
								</xsl:if>
								<xsl:if test="num_associations!='0'">
									<div>
										<form method="POST">
											<input type="hidden" name="status" value="ACCEPTED"/>
											<input type="submit" value="{php:function('lang', 'Accept application')}" class="pure-button pure-button-primary" >
												<xsl:if test="not(application/case_officer)">
													<xsl:attribute name="disabled">disabled</xsl:attribute>
												</xsl:if>
											</input>
										</form>
									</div>
								</xsl:if>
							</xsl:if>
							<div>
								<xsl:choose>
									<xsl:when test="external_archive != '' and application/external_archive_key =''">
										<form method="POST" action ="{export_pdf_action}" >
											<input type="hidden" name="export" value="pdf"/>
											<input onclick="return confirm('{php:function('lang', 'transfer case to external system?')}')" type="submit" value="{php:function('lang', 'PDF-export to archive')}" class="pure-button pure-button-primary">
												<xsl:if test="not(application/case_officer/is_current_user)">
													<xsl:attribute name="disabled">disabled</xsl:attribute>
												</xsl:if>
											</input>
											<label for="preview">
												<input name="preview" type="checkbox" value="1" id="preview" />
												<xsl:value-of select="php:function('lang', 'preview')"/>
											</label>
										</form>
									</xsl:when>
									<xsl:when test="application/external_archive_key !=''">
										<div class="pure-control-group">
											<label>
												<xsl:value-of select="php:function('lang', 'external archive key')"/>
											</label>
											<xsl:value-of select="application/external_archive_key"/>
										</div>
									</xsl:when>
								</xsl:choose>
							</div>

							<!--dd><br/><a href="{application/dashboard_link}"><xsl:value-of select="php:function('lang', 'Back to Dashboard')" /></a></dd-->
						</div>
					</div>
				</xsl:if>
			</fieldset>
		</div>
		<div class="proplist-col">
			<xsl:if test="application/edit_link">
				<button class="pure-button pure-button-primary">
					<xsl:if test="application/case_officer/is_current_user">
						<xsl:attribute name="onclick">window.location.href='<xsl:value-of select="application/edit_link"/>'</xsl:attribute>
					</xsl:if>
					<xsl:if test="not(application/case_officer/is_current_user)">
						<xsl:attribute name="disabled">disabled</xsl:attribute>
					</xsl:if>
					<xsl:value-of select="php:function('lang', 'Edit')" />
				</button>
			</xsl:if>
			<a class="pure-button pure-button-primary" href="{application/dashboard_link}">
				<xsl:value-of select="php:function('lang', 'Back to Dashboard')" />
			</a>
		</div>
	</div>
	<script type="text/javascript">
		var resourceIds = '<xsl:value-of select="application/resource_ids"/>';
		var currentuser = '<xsl:value-of select="application/currentuser"/>';
		if (!resourceIds || resourceIds == "") {
		resourceIds = false;
		}
		var lang = <xsl:value-of select="php:function('js_lang', 'Resources', 'Resource Type', 'No records found', 'ID', 'Type', 'From', 'To', 'Document', 'Active' ,'Delete', 'del', 'Name', 'Cost', 'order id', 'Amount', 'currency', 'status', 'payment method', 'refund','refunded', 'Actions', 'cancel', 'created', 'article', 'Select', 'cost', 'unit', 'quantity', 'Selected', 'Delete', 'Sum', 'tax')"/>;
		var app_id = <xsl:value-of select="application/id"/>;
		var building_id = <xsl:value-of select="application/building_id"/>;
		var resources = <xsl:value-of select="application/resources"/>;

	    <![CDATA[
			var resourcesURL = phpGWLink('index.php', {menuaction:'booking.uiresource.index', sort:'name', length:-1}, true) +'&' + resourceIds;
			var associatedURL = phpGWLink('index.php', {menuaction:'booking.uiapplication.associated', sort:'from_',dir:'asc',filter_application_id:app_id, length:-1}, true);
			var documentsURL = phpGWLink('index.php', {menuaction:'booking.uidocument_view.regulations', sort:'name', length:-1}, true) +'&owner[]=building::' + building_id;
				documentsURL += '&owner[]=resource::'+ resources;
			var attachmentsResourceURL = phpGWLink('index.php', {menuaction:'booking.uidocument_application.index', sort:'name', no_images:1, filter_owner_id:app_id, length:-1}, true);
			var paymentURL = phpGWLink('index.php', {menuaction:'booking.uiapplication.payments', sort:'from_',dir:'asc',application_id:app_id, length:-1}, true);

		]]>

		var colDefsResources = [{key: 'name', label: lang['Resources'], formatter: genericLink}, {key: 'rescategory_name', label: lang['Resource Type']}];

		if (currentuser == 1) {
		var colDefsAssociated = [
		{key: 'id', label: lang['ID'], formatter: genericLink},
		{key: 'type', label: lang['Type']},
		{key: 'from_', label: lang['From']},
		{key: 'to_', label: lang['To']},
		{key: 'cost', label: lang['Cost']},
		{key: 'active', label: lang['Active']},
		{key: 'dellink', label: lang['Delete'], formatter: genericLink2}];
		} else {
		var colDefsAssociated = [
		{key: 'id', label: lang['ID'], formatter: genericLink},
		{key: 'type', label: lang['Type']},
		{key: 'from_', label: lang['From']},
		{key: 'to_', label: lang['To']},
		{key: 'active', label: lang['Active']}];
		}

		var colDefsDocuments = [{key: 'name', label: lang['Document'], formatter: genericLink}];

		createTable('resources_container',resourcesURL,colDefsResources, '', 'pure-table pure-table-bordered');
		createTable('associated_container',associatedURL,colDefsAssociated,'results', 'pure-table pure-table-bordered');
		createTable('regulation_documents',documentsURL,colDefsDocuments, '', 'pure-table pure-table-bordered');

		var colDefsAttachmentsResource = [{key: 'name', label: lang['Name'], formatter: genericLink}];
		createTable('attachments_container', attachmentsResourceURL, colDefsAttachmentsResource, '', 'pure-table pure-table-bordered');

		var colDefsPayment = [
		{
		label: lang['Select'],
		attrs: [{name: 'class', value: "align-middle"}],
		object: [
		{
		type: 'input',
		attrs: [
		{name: 'type', value: 'radio'},
		{name: 'onClick', value: 'show_order(this);'}
		]
		}
		], value: 'order_id'
		},
		{key: 'order_id', label: lang['order id']},
		{key: 'created_value', label: lang['created']},
		{key: 'amount', label: lang['Amount']},
		{key: 'refunded_amount', label: lang['refunded']},
		{key: 'currency', label: lang['currency']},
		{key: 'status_text', label: lang['status']},
		{key: 'payment_method', label: lang['payment method']},
		{key: 'actions', label: lang['Actions'], formatter: genericLink2({name: 'delete', label:lang['refund']},{name: 'edit', label:lang['cancel']})}
		];

		createTable('payments_container', paymentURL, colDefsPayment,'', 'pure-table pure-table-bordered');

	</script>
</xsl:template>
