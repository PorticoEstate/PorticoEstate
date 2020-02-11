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

			<title>Barnas Byrom - rapportnummer <!-- INSERT VARIABEL --> </title>
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
		
			<div class="container">
				<div class="mt-5 row">
					<div class="col-md-6 align-left">
						<!--<img src="logo bk.png" width="200"/>-->
					</div>
					<div class="col-md-6 align-right">
						<xsl:value-of select="title"/>
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
							<td>
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
					<table class="table table-bordered">
						<tr>
							<td colspan="2" class="text-center">Antall avvik</td>
							<td rowspan="5"></td>
							<td colspan="2" class="text-center">Tilstandsvurdering - forklaring</td>
						</tr>
						<tr>
							<td class="small">
								<b>A</b> - kan medføre fare for barnets liv</td>
							<td class="small bg-danger text-white text-center">0</td>
							<td class="small">Lekeutstyr med mer enn 10 års levetid med jevnlig vedlikehold</td>
							<td class="small">1 - Svært bra</td>
						</tr>
						<tr>
							<td class="small">
								<b>B</b> - kan medføre fare for livsvarig skade hos barnet</td>
							<td class="small bg-warning text-dark text-center">1</td>

							<td class="small">Lekeutstyr med 5 - 10 års levetid med jevnlig vedlikehold</td>
							<td class="small">2 - Bra</td>
						</tr>
						<tr>
							<td class="small">
								<b>C</b> - kan medføre mindre alvorlig skade</td>
							<td class="small bg-success text-white text-center">2</td>

							<td class="small">Lekeutstyr med 1 - 5 års levetid med jevnlig vedlikehold</td>
							<td class="small">3 - Dårlig</td>
						</tr>
						<tr>
							<td class="small">
								<b>M</b> - merknad</td>
							<td class="small text-center">4</td>

							<td class="small">Lekeutstyr som bør fjernesinnen 1 år</td>
							<td class="small">1 - Svært bra</td>
						</tr>
					</table>
				</div>
				<div class="row">

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
				<div class="row mt-5">
					<h2>Del 1. Lekeplassutstyr</h2>
				</div>

				<xsl:for-each select="component_child_data">
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
								<h5>Avvik</h5>
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

				<xsl:if test= "stray_cases !=''">
					<div class="row mt-5">
						<div class="col-md-12 text-center bg-light">
							<h4>
								Avvik ikke knyttet til utstyr
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

