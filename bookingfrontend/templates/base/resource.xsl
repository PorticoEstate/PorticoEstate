<xsl:template match="data" xmlns:php="http://php.net/xsl">

    
    <div class="container my-container-top-fix wrapper">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a>
                    <xsl:attribute name="href">
                        <xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
                    </xsl:attribute>
                    <xsl:value-of select="php:function('lang', 'Home')" />
                </a>
            </li>
			<li class="breadcrumb-item active">
				<a>
					<xsl:attribute name="href">
						<xsl:value-of select="building/link" />
					</xsl:attribute>
					<xsl:value-of select="building/name" />
				</a>
			</li>
        </ol>

		<div class="row p-3">
            <div class="col-lg-6">
                
                <div class="row">
                    <div class="px-2 p-3">
						<h3>
							<xsl:value-of select="resource/name"/>
						</h3>
						<span>
							<xsl:value-of select="building/name"/>
						</span>
						<br />
                        <i class="fas fa-map-marker d-inline"> </i>
                        <div class="building-place-adr">
							<span>
								<xsl:value-of select="building/street"/>
							</span>
							<span>
								<xsl:value-of select="building/zip_code"/>
								<xsl:text> </xsl:text>
								<xsl:value-of select="building/city"/>
							</span>
                        </div>
                    </div>
					<div class="px-2 p-3" id="item-description">
						<xsl:value-of disable-output-escaping="yes" select="resource/description"/>
					</div>

					<div>
						<ul>
							<xsl:for-each select="resource/activities_list">
								<li>
									<xsl:value-of select="name"/>
								</li>
							</xsl:for-each>
						</ul>
					</div>

					<div class="building-accordion">
						<xsl:if test="count(resource/facilities_list) &gt; 0">
							<div class="building-card">
								<div class="building-card-header">
									<h5 class="mb-0">
										<button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false">
											Fasiliteter
										</button>
										<button data-toggle="collapse" data-target="#collapseOne" class="btn fas fa-plus float-right"></button>
									</h5>
								</div>

								<div id="collapseOne" class="collapse">
									<div class="card-body">
										<ul>
											<xsl:for-each select="resource/facilities_list">
												<li>
													<xsl:value-of select="name"/>
												</li>
											</xsl:for-each>
										</ul>
									</div>
								</div>
							</div>
						</xsl:if>

						<xsl:if test="resource/opening_hours and normalize-space(resource/opening_hours)">
							<div class="building-card">
								<div class="building-card-header">
									<h5 class="mb-0">
										<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false">
											Åpningstider
										</button>
										<button data-toggle="collapse" data-target="#collapseTwo" class="btn fas fa-plus float-right"></button>
									</h5>
								</div>
								<div id="collapseTwo" class="collapse">
									<div class="card-body">
										<xsl:value-of disable-output-escaping="yes" select="resource/opening_hours"/>
									</div>
								</div>
							</div>
						</xsl:if>

						<xsl:if test="resource/contact_info and normalize-space(resource/contact_info)">
							<div class="building-card">
								<div class="building-card-header">
									<h5 class="mb-0">
										<button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false">
											Kontaktinformasjon
										</button>
										<button data-toggle="collapse" data-target="#collapseThree" class="btn fas fa-plus float-right"></button>
									</h5>
								</div>
								<div id="collapseThree" class="collapse">
									<div class="card-body">
										<xsl:value-of disable-output-escaping="yes" select="resource/contact_info"/>
									</div>
								</div>
							</div>
						</xsl:if>

                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 resource-picture">
                <img class="img-fluid rounded m-1 main-picture" src=""/>
                <div id="list-img-thumbs">
                </div>  
            </div>
        </div>
        
        
        <div class="row margin-top-and-bottom">
            
            <div class="button-group dropdown">

                <button class="btn btn-default datepicker-btn"><i class="far fa-calendar-alt"></i> Velg dato</button>
                
                <button class="btn btn-default ml-1"><i class="fas fa-plus"></i> Book</button>
            </div>
            
            
                
                <!--<div class="input-group date" id="datepicker" data-provide="datepicker">
                    <input type="text" class="form-control" />
                    <div class="input-group-addon">
                        <span class="glyphicon glyphicon-th"></span>
                    </div>
                </div>-->
                

            
            <div id="myScheduler" class="d-none d-sm-block d-xs-block margin-top-and-bottom"></div>

            <div id="mySchedulerSmallDeviceView" class="d-block d-sm-none d-xs-none margin-top-and-bottom"></div>

        </div>
        
        
        <div class="push"></div>
    </div>

    
    <script type="text/javascript">
            var script = document.createElement("script"); 
//            script.src = document.location.origin + "/" + window.location.pathname.split('/')[1] + "/bookingfrontend/" + "/js/base/resource.js";
			script.src = strBaseURL.split('?')[0] + "bookingfrontend/js/base/resource.js";

            document.head.appendChild(script);			
        </script>
</xsl:template>
