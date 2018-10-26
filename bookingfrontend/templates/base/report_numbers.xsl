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
						<p>
							<strong>
								<xsl:value-of select="php:function('lang', 'Please enter correct numbers for the event')"/>:</strong>
						</p>

						<table class="table">
							<tr>
								<th>
									<xsl:value-of select="php:function('lang', 'Where')" />:</th>
								<td>
									<xsl:value-of select="building/name" />
								</td>
							</tr>
							<tr>
								<th>
									<xsl:value-of select="php:function('lang', 'When')" />:</th>
								<td>
									<xsl:value-of select="php:function('pretty_timestamp', event_object/from_)" /> - <xsl:value-of select="php:function('pretty_timestamp', event_object/to_)" />
								</td>
							</tr>
							<xsl:if test="event_object/group_name">
								<tr>
									<th>
										<xsl:value-of select="php:function('lang', 'Who')" />:</th>
									<td>
										<xsl:value-of select="event_object/group_name" />
									</td>
								</tr>
							</xsl:if>
						</table>

					</div>
					<form action="" method="POST" id="event_form" name="form" class="col-md-8">
						<div class="form-group">
							<label class="text-uppercase"><xsl:value-of select="php:function('lang', 'Number of participants')" /></label>
							<table id="agegroup" class="table">
								<thead>
									<tr>
										<th/>
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
											<th>
												<xsl:value-of select="name"/>
											</th>
											<td>
												<input type="text" size="4">
													<xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
													<xsl:attribute name="value">
														<xsl:value-of select="../event_object/agegroups/male[../agegroup_id = $id]"/>
													</xsl:attribute>
												</input>
											</td>
											<td>
												<input type="text" size="4">
													<xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
													<xsl:attribute name="value">
														<xsl:value-of select="../event_object/agegroups/female[../agegroup_id = $id]"/>
													</xsl:attribute>
												</input>
											</td>
										</tr>
									</xsl:for-each>
								</tbody>
							</table>
						</div>
					
						<div class="form-group mt-5">
							<input type="submit" class="btn btn-light mr-4">
								<xsl:attribute name="value">
									<xsl:value-of select="php:function('lang', 'Save')"/>
								</xsl:attribute>
							</input>
						</div>
					</form>

					<xsl:if test="step = 2">
						<dl>
							<dt>
								<xsl:value-of select="php:function('lang', 'Thank you')"/>!</dt>
						</dl>
						<p>
							<dt>
								<xsl:value-of select="php:function('lang', 'The data was successfully updated')"/>!</dt>
						</p>
					</xsl:if>
				</div>
            
        	</div>
    	
	</div>
    <div class="push"></div>

</xsl:template>
