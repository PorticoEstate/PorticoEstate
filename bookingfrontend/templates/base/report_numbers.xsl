<xsl:template match="data" xmlns:php="http://php.net/xsl">
<div id="report-numbers-page-content" class="margin-top-content">
        	<div class="container wrapper">
				<div class="location">
					<span>
						<a><xsl:attribute name="href">
								<xsl:value-of select="php:function('get_phpgw_link', '/bookingfrontend/index.php', 'menuaction:bookingfrontend.uisearch.index')"/>
							</xsl:attribute>
							<xsl:value-of select="php:function('lang', 'Home')" />
						</a>
					</span>
					<span><xsl:value-of select="php:function('lang', 'Report numbers')"/></span>															
				</div>

            	<div class="row">					
					<div class="col-md-8">
						<h1 class="font-weight-bold">
							<xsl:value-of select="php:function('lang', 'Please enter correct numbers for the event')"/>
						</h1>

						<xsl:if test="step = 2">
							<div class="alert alert-success">
								<dl>
									<xsl:value-of select="php:function('lang', 'Thank you')"/>! <xsl:value-of select="php:function('lang', 'The data was successfully updated')"/>!
								</dl>
							</div>
						</xsl:if>

						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Where')" /></label>
							<xsl:value-of select="building/name" />
						</div>

						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'When')" /></label>
							<xsl:value-of select="event_object/when" />
						</div>

						<xsl:if test="event_object/group_name">
							<div class="form-group">
								<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Who')" /></label>
								<xsl:value-of select="event_object/group_name" />
							</div>
						</xsl:if>
					</div>

					<form action="" method="POST" id="event_form" name="form" class="col-md-8">
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Estimated number of participants')" /></label>
							<div class="p-2 border">
								<div class="row mb-2">
									<div class="col-3">
										<span class="span-label mt-2"></span>
									</div>
									<div class="col-4">
										<span><xsl:value-of select="php:function('lang', 'Male')" /></span>
									</div>
									<div class="col-4">
										<xsl:value-of select="php:function('lang', 'Female')" />
									</div>
								</div>
								
								<xsl:for-each select="agegroups">
										<xsl:variable name="id">
											<xsl:value-of select="id"/>
										</xsl:variable>
								
										<div class="row mb-2">
											<span data-bind="text: id, visible: false"/>
											<div class="col-3">
												<span class="mt-2"><xsl:value-of select="name"/></span>
											</div>
											<div class="col-4">
												<input type="text" size="4" class="form-control sm-input">
													<xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
													<xsl:attribute name="value">
														<xsl:value-of select="../event_object/agegroups/male[../agegroup_id = $id]"/>
													</xsl:attribute>
												</input>
											</div>
											<div class="col-4">
												<input type="text" size="4" class="form-control sm-input">
													<xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
													<xsl:attribute name="value">
														<xsl:value-of select="../event_object/agegroups/female[../agegroup_id = $id]"/>
													</xsl:attribute>
												</input>
											</div>
										</div>

								</xsl:for-each>

																						
							</div>
						</div>						
					
						<div class="form-group mt-5">
							<input type="submit" class="btn btn-light mr-4">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('lang', 'Save')"/>
								</xsl:attribute>
							</input>
						</div>
					</form>				
				</div>		

        	</div>
    	
	</div>
    <div class="push"></div>

</xsl:template>
