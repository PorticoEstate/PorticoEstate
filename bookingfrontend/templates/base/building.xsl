<xsl:template match="data" xmlns:php="http://php.net/xsl">

<div id="building-page-content">
	<div class="info-content">
	<div class="container wrapper">
		
		<div class="location">
			<span><a>
					<xsl:attribute name="href">
						<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
					</xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Home')" />
				</a>
			</span>
		</div>

		<div class="row p-3">
			<div class="col-lg-6">

				<div class="row">
					<div class="col-xl-4 col-lg-5 mb-4 col-item-img">
						<img class="img-fluid image-circle" id="item-main-picture" src=""/>
					</div>
					<div class="col-xl-6 col-lg-7 col-xs-12 building-place-info">
						<h3>
							<xsl:value-of select="building/name"/>
						</h3>
						<i class="fas fa-map-marker d-inline"> </i>
						<div class="building-place-adr">
							<span>
								<xsl:value-of select="building/street"/>
							</span>
							<span class="d-block">
								<xsl:value-of select="building/zip_code"/>
								<xsl:text> </xsl:text>
								<xsl:value-of select="building/city"/>
							</span>
						</div>
					</div>

							<xsl:if test="building/deactivate_calendar=0">
					<div class="col-auto">
							<div>
								<button class="btn btn-light goToCal">
									<i class="fa fa-calendar"></i>&#160;
									<xsl:value-of select="php:function('lang', 'Calendar')" />
								</button>
							</div>
					</div>
							</xsl:if>

					<div class="building-accordion">
								<xsl:if test="building/description and normalize-space(building/description)">
									<div class="building-card">
										<div class="building-card-header">
											<h5 class="mb-0">
												<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false">
													<xsl:value-of select="php:function('lang', 'Building information')" />
												</button>
												<button data-toggle="collapse" data-target="#collapseOne" class="btn fas fa-plus float-right"></button>
											</h5>
										</div>
										<div id="collapseOne" class="collapse">
											<div class="card-body">
												<xsl:value-of disable-output-escaping="yes" select="building/description"/>
											</div>
										</div>
									</div>
								</xsl:if>

						<div class="building-card card-img-thumbs">
							<div class="building-card-header">
								<h5 class="mb-0">
									<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false">
												<xsl:value-of select="php:function('lang', 'Pictures')" />
									</button>
									<button data-toggle="collapse" data-target="#collapseTwo" class="btn fas fa-plus float-right"></button>
								</h5>
							</div>
							<div id="collapseTwo" class="collapse">
								<div class="card-body building-images" id="list-img-thumbs">
								</div>
							</div>
						</div>

						<xsl:if test="building/opening_hours and normalize-space(building/opening_hours)">
							<div class="building-card">
								<div class="building-card-header">
									<h5 class="mb-0">
										<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false">
													<xsl:value-of select="php:function('lang', 'Opening hours')" />
										</button>
										<button data-toggle="collapse" data-target="#collapseThree" class="btn fas fa-plus float-right"></button>
									</h5>
								</div>
								<div id="collapseThree" class="collapse">
									<div class="card-body">
										<xsl:value-of disable-output-escaping="yes" select="building/opening_hours"/>
									</div>
								</div>
							</div>
						</xsl:if>

						<xsl:if test="building/contact_info and normalize-space(building/contact_info)">
							<div class="building-card">
								<div class="building-card-header">
									<h5 class="mb-0">
										<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false">
													<xsl:value-of select="php:function('lang', 'contact information')" />
										</button>
										<button data-toggle="collapse" data-target="#collapseFour" class="btn fas fa-plus float-right"></button>
									</h5>
								</div>
								<div id="collapseFour" class="collapse">
									<div class="card-body">
											<xsl:value-of disable-output-escaping="yes" select="building/contact_info"/>
												<xsl:if test="building/deactivate_sendmessage=0">
													<button class="btn btn-light" onclick="window.location.href='{building/message_link}'">
														<i class="fas fa-envelope"></i>&#160;
														<xsl:value-of select="php:function('lang', 'Send message')" />
													</button>
													- <xsl:value-of select="php:function('lang', 'Send message to case officer for building')" />
												</xsl:if>
								</div>
								</div>
							</div>
						</xsl:if>
					</div>
				</div>

			</div>

			<div class="col-lg-6 building-bookable">
				<h3 class="">
					<xsl:value-of select="php:function('lang', 'Bookable resources (2018)')" />
				</h3>
				<div data-bind="foreach: bookableResource">
					<div class="custom-card">
						<a class="bookable-resource-link-href" href="" data-bind="">
									<span data-bind="html: name"></span>
						</a>
						
						<div data-bind="foreach: activitiesList">							
									<span class="tagTitle" data-bind="if: $index() == 0">
										<xsl:value-of select="php:function('lang', 'Activities (2018)')"/>:
									</span>
									<span class="mr-2 textTagsItems" data-bind="html: $data"></span>
						</div>

								<div class="mt-2" data-bind="foreach: facilitiesList">
									<span class="tagTitle" data-bind="if: $index() == 0">
										<xsl:value-of select="php:function('lang', 'Facilities')"/>:
									</span>
									<span class="textTagsItems" data-bind="html: $data"></span>
								</div>

					</div>
				</div>

			</div>
		</div>
		</div>
		</div>

		<div class="container wrapper calendar-content">
			<xsl:if test="building/deactivate_application=0 and config_data/help_calendar_book and normalize-space(config_data/help_calendar_book)">
				<div class="row margin-top-and-bottom">
					<div class="col">
						<xsl:value-of select="config_data/help_calendar_book"/>
					</div>
				</div>
			</xsl:if>

		<div class="row margin-top-and-bottom">		
			<xsl:if test="building/deactivate_calendar=0">
				<div class="col-6">
					<div class="button-group dropdown calendar-tool invisible">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
								<xsl:value-of select="php:function('lang', 'Choose resources')"/>
								<span class="caret"></span>
							</button>

							<ul class="dropdown-menu px-2" data-bind="foreach: bookableResource">
								<li>
									<div class="form-check checkbox checkbox-primary">

										<label class="check-box-label">
											<input class="form-check-input choosenResource" type="checkbox"  checked="checked" data-bind="html: name"/>
											<span class="label-text" data-bind="html: name"></span>
										</label>
									</div>
								</li>
							</ul>

							<button class="btn btn-default datepicker-btn mr-1 mt-1 mb-1">
								<i class="far fa-calendar-alt"></i>&#160;
								<xsl:value-of select="php:function('lang', 'choose a date')"/>
							</button>

							<xsl:if test="building/deactivate_application=0">
								<a href="" class="btn btn-default bookBtnForward">
									<i class="fas fa-plus"></i>&#160;
										<xsl:value-of select="php:function('lang', 'Application')" />
								</a>
							</xsl:if>
						</div>						
				</div>

					<div class="col-6 col-md-3 offset-md-3 col-lg-3 offset-lg-3 col-xl-2 offset-xl-4 col-sm-5 offset-sm-1 col-12 mt-2">
						<div class="">
							<div class="square allocation"></div>							
							<span>
								<xsl:value-of select="php:function('lang', 'allocation')"/>
							</span>							
						</div>
						<div class="">
							<div class="square booking"></div>							
							<span>
								<xsl:value-of select="php:function('lang', 'Booking (2018)')"/>
							</span>							
						</div>
						<div class="">
							<div class="square event"></div>							
							<span>
								<xsl:value-of select="php:function('lang', 'event')"/>
							</span>							
						</div>
					</div>
			</xsl:if>



			<!--<div class="input-group date" id="datepicker" data-provide="datepicker">
				<input type="text" class="form-control" />
				<div class="input-group-addon">
					<span class="glyphicon glyphicon-th"></span>
				</div>
			</div>-->


			<xsl:if test="building/deactivate_calendar=0">
			<div id="myScheduler" class="d-none d-lg-block margin-top-and-bottom col-12"></div>
			<div id="mySchedulerSmallDeviceView" class="d-lg-none margin-top-and-bottom col-12"></div>
			</xsl:if>

		</div>


		<div class="push"></div>
	</div>


	<div id="lightbox" class="modal hide" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-body lightbox-body">
				<a href="#" class="close">&#215;</a>
				<img src="" alt="" />
			</div>
		</div>
	</div>

</div>
	<script type="text/javascript">
		var lang = <xsl:value-of select="php:function('js_lang', 'new application', 'Resource (2018)')" />;
		var deactivate_application = <xsl:value-of select="building/deactivate_application" />;
		var deactivate_calendar = <xsl:value-of select="building/deactivate_calendar" />;
		var script = document.createElement("script");
		script.src = strBaseURL.split('?')[0] + "bookingfrontend/js/base/building.js";

		document.head.appendChild(script);
	</script>
</xsl:template>
