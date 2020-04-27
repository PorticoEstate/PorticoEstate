<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="report">
			<xsl:apply-templates select="report"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>

<!-- new_component-->
<xsl:template match="report" xmlns:php="http://php.net/xsl">
	<html>
		<head>
			<!-- Required meta tags -->
			<!--<meta charset="utf-8"/>-->
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

			<!-- Bootstrap CSS -->
			<xsl:for-each select="stylesheets">
				<link rel="stylesheet" type="text/css">
					<xsl:attribute name="href">
						<xsl:value-of select="node()"/>
					</xsl:attribute>
				</link>
			</xsl:for-each>

			<xsl:for-each select="javascripts">
				<script>
					<xsl:attribute name="src">
						<xsl:value-of select="node()"/>
					</xsl:attribute>
				</script>
			</xsl:for-each>

			<title>
				<xsl:value-of select="control_area_name"/>
				<xsl:text> / </xsl:text>
				<xsl:value-of select="title"/>
				<xsl:text> #</xsl:text>
				<xsl:value-of select="check_list_id"/>
			</title>
			<style>

				@page {
				size: A4;
				}

				@media print {
				li {page-break-inside: avoid;}
				h1, h2, h3, h4, h5 {
				page-break-after: avoid;
				}

				table, figure {
				page-break-inside: avoid;
				}
				}


				@page:left{
				@bottom-left {
				content: "Page " counter(page) " of " counter(pages);
				}
				}
				@media print
				{
				.btn
				{
				display: none !important;
				}
				}

			</style>
		</head>

		<body>
						
			<button class="btn btn-secondary" onClick="window.print();">
				<xsl:value-of select="php:function('lang', 'print')" />
			</button>
		
			<div class="container small">
				<div class="mt-5 row">
					<div class="col-md-6 align-left">
						<xsl:choose>
							<xsl:when test="responsible_logo !=''">
								<img src="{responsible_logo}" width="200"/>
							</xsl:when>
						</xsl:choose>
					</div>
					<div class="col-md-6 text-right">
						<xsl:value-of select="responsible_organization"/>
					</div>
					<div class="col-md-12 align-right">
						<h2>
							<xsl:value-of select="title"/>
						</h2>
					</div>

				</div>

				<div class="row mt-3">
					<table class="table table-bordered">
						<tr>
							<td>
								<xsl:value-of select="php:function('lang', 'date')" />
							</td>
							<td>
								<xsl:value-of select="completed_date"/>
							</td>
							<td>
								Rapportnummer
							</td>
							<td>
								<xsl:value-of select="check_list_id"/>
							</td>
						</tr>

						<tr>
							<td>Inspektør</td>
							<td class="text-nowrap">
								<xsl:for-each select="inspectors">
									<xsl:value-of select="node()"/>
									<xsl:if test="last() != 1">
										<br/>
									</xsl:if>
								</xsl:for-each>
							</td>
							<td>Sted</td>
							<td>
								<xsl:value-of select="where"/>
							</td>
						</tr>
					</table>
				</div>
				<div class="row mt-2">
					<xsl:if test="location_image !=''">
						<xsl:choose>
							<xsl:when test="inline_images =1">
								<img src="data:image/jpg;base64,{image_data}" class="img-fluid"/>
							</xsl:when>
							<xsl:otherwise>
								<img src="{location_image}" class="img-fluid"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
				</div>

				<div class="row mt-2">

					<xsl:variable name="witdth">
						<xsl:value-of select="count(findings) * 2"/>
					</xsl:variable>

					<!--<div class="col-md-{$witdth}">-->
					<xsl:choose>
						<xsl:when test="findings/child::node()">
							<div class="col-sm-3">
								<table class="table table-bordered table-sm">
									<tr>
										<th class="text-left text-left border-bottom-0">
											<xsl:attribute name="colspan">
												<xsl:value-of select="count(findings)"/>
											</xsl:attribute>
											Funn - sammendrag
										</th>
									</tr>
									<tr>
										<xsl:for-each select="findings">
											<th class="text-left border-bottom-0 border-top-0">
												<xsl:value-of select="name"/>
											</th>
										</xsl:for-each>
									</tr>
									<tr>
										<xsl:for-each select="findings">
											<td>
												<table class="table table-sm">
													<xsl:for-each select="values">
														<tr>
															<td class="border-0">
																<xsl:value-of select="text"/>
															</td>
															<td class="border-0">
																<xsl:value-of select="value"/>
															</td>
														</tr>
													</xsl:for-each>
												</table>
											</td>
										</xsl:for-each>
									</tr>
								</table>
							</div>
						</xsl:when>
					</xsl:choose>
					<xsl:value-of disable-output-escaping="yes" select="report_intro"/>
				</div>

				<div class="row">

					<p>Kontrollen er gjennomført av:
					</p>
				</div>
				<div class="row">
					<xsl:for-each select="inspectors">
						<div class="col-md-6">
							<xsl:value-of select="node()"/>
						</div>
					</xsl:for-each>
				</div>

				<!-- DEL 1 START -->
				<!--				<xsl:if test= "component_child_data !=''">
					<div class="row mt-5">
						<h2>Del 1. Utstyr</h2>
					</div>
				</xsl:if>-->

				<xsl:for-each select="component_child_data">
					<div class="row mt-5">
						<h2>Del <xsl:value-of select="section"/>. <xsl:value-of select="section_descr"/></h2>
					</div>

					<xsl:for-each select="data">

						<div class="row mt-5">
							<div class="col-md-12 text-center bg-light">
								<h4>
									<xsl:value-of select="name"/>
								</h4>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<xsl:if test="image_link !=''">
									<a href="{image_link}">
										<xsl:choose>
											<xsl:when test="inline_images =1">
												<img src="data:image/jpg;base64,{image_data}" class="img-thumbnail img-fluid"/>
											</xsl:when>
											<xsl:otherwise>
												<img src="{image_link}" class="img-thumbnail img-fluid"/>
											</xsl:otherwise>
										</xsl:choose>
									</a>
								</xsl:if>
							</div>
							<div class="col-md-8">
								<table class="table">
									<xsl:for-each select="data">
										<tr>
											<td>
												<xsl:value-of select="text"/>
											</td>
											<td>
												<xsl:value-of select="value"/>
											</td>
										</tr>
									</xsl:for-each>
								</table>
							</div>
						</div>

						<xsl:if test="cases !=''">
							<div class="row">
								<div class="col-md-12 bg-light text-center">
									<h5>Saker</h5>
								</div>
							</div>
						</xsl:if>

						<xsl:for-each select="cases">
							<div class="row">

								<div class="col-md-4">
									<xsl:for-each select="files">
										<a href="{link}" title="{text}">
											<xsl:choose>
												<xsl:when test="inline_images =1">
													<img src="data:image/jpg;base64,{image_data}" class="img-thumbnail img-fluid"/>
												</xsl:when>
												<xsl:otherwise>
													<img src="{link}" class="img-thumbnail img-fluid"/>
												</xsl:otherwise>
											</xsl:choose>
										</a>
									</xsl:for-each>
								</div>
								<div class="col-md-8">
									<table class="table">
										<xsl:for-each select="data">
											<tr>
												<td>
													<xsl:value-of select="text"/>
												</td>
												<td>
													<xsl:value-of select="value"/>
												</td>
											</tr>
										</xsl:for-each>
									</table>
								</div>
							</div>
						</xsl:for-each>
					</xsl:for-each>
				</xsl:for-each>
				<xsl:if test= "stray_cases !=''">
					<div class="row mt-5">
						<div class="col-md-12 text-center bg-light">
							<h4>
								<xsl:choose>
									<xsl:when test="component_child_data !=''">
										Saker som ikke er knyttet til utstyr
									</xsl:when>
									<xsl:otherwise>
										Saker/registreringer
									</xsl:otherwise>
								</xsl:choose>
							</h4>
						</div>
						<div class="col-md-12">
						</div>
	
						<xsl:for-each select="stray_cases">
							<!--							<div class="col-md-4">
							</div>-->
							<div class="col-md-4">
								<xsl:for-each select="files">
									<a href="{link}" title="{text}">
										<xsl:choose>
											<xsl:when test="inline_images =1">
												<img src="data:image/jpg;base64,{image_data}" class="img-thumbnail img-fluid"/>
											</xsl:when>
											<xsl:otherwise>
												<img src="{link}" class="img-thumbnail img-fluid"/>
											</xsl:otherwise>
										</xsl:choose>
									</a>
								</xsl:for-each>
							</div>
							<div class="col-md-8">
								<table class="table">
									<xsl:for-each select="data">
										<tr>
											<td>
												<xsl:value-of select="text"/>
											</td>
											<td>
												<xsl:value-of select="value"/>
											</td>
										</tr>
									</xsl:for-each>
								</table>
							</div>
						</xsl:for-each>
					</div>
				</xsl:if>
			</div>
		</body>
	</html>

</xsl:template>

