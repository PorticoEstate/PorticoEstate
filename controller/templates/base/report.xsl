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
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

				<title>Barnas Byrom - rapportnummer <!-- INSERT VARIABEL --> </title>
			</link>
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

			<div class="container">
				<div class="mt-5 row">
					<div class="col-md-6 align-left">
						<img src="logo bk.png" width="200"/>
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
					<img src="{location_image}" class="img-fluid"/>
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

				<!-- START LEKEUTSTYR -->
				<div class="row mt-5">
					<div class="col-md-12 text-center bg-light">
						<h4>Lekeutstyr nr 1: Huske</h4>
					</div>
					<div class="col-md-4">
						<a href="images/playground.jpg">
							<img src="images/playground.jpg" class="img-thumbnail img-fluid"/>
						</a>
					</div>
					<div class="col-md-8">
						<table class="table">
							<tr>
								<td>Produsent</td>
								<td>Kompan</td>
							</tr>
							<tr>
								<td>Oppført</td>
								<td>2018</td>
							</tr>
							<tr>
								<td>Tilstandsvurdering sist</td>
								<td>2</td>
							</tr>
							<tr>
								<td>Tilstandsvurdering nå</td>
								<td>3</td>
							</tr>
						</table>
					</div>




					<!-- START AVVIK -->
					<div class="col-md-12 bg-light text-center">
						<h5>Avvik</h5>
					</div>
					<div class="col-md-4">
						<a href="images/playground.jpg">
							<img src="images/playground.jpg" class="img-thumbnail img-fluid"/>
						</a>
					</div>
					<div class="col-md-8">
						<table class="table">
							<tr>
								<td>Nummer</td>
								<td>1</td>
							</tr>
							<tr>
								<td>Avvik</td>
								<td>B</td>
							</tr>
							<tr>
								<td>Henvisning til NS</td>
								<td>NS 1-4.2.8.3.5</td>
							</tr>
							<tr>
								<td>Beskrivelse</td>
								<td>Enkelte lenger er åpnet og kan løsne ved stress</td>
							</tr>
							<tr>
								<td>Forslag til tiltak</td>
								<td>Bør byttes</td>
							</tr>
						</table>
					</div>

					<!-- END AVVIK-->

				</div>

				<!-- END LEKEUTSTYR -->



				<div class="row mt-5">
					<div class="col-md-12 text-center bg-light">
						<h4>Lekeutstyr nr 1: Huske</h4>
					</div>
					<div class="col-md-4">
						<a href="images/playground.jpg">
							<img src="images/playground.jpg" class="img-thumbnail img-fluid"/>
						</a>
					</div>
					<div class="col-md-8">
						<table class="table">
							<tr>
								<td>Produsent</td>
								<td>Kompan</td>
							</tr>
							<tr>
								<td>Oppført</td>
								<td>2018</td>
							</tr>
							<tr>
								<td>Tilstandsvurdering sist</td>
								<td>2</td>
							</tr>
							<tr>
								<td>Tilstandsvurdering nå</td>
								<td>3</td>
							</tr>
						</table>
					</div>
					<div class="col-md-12 bg-light text-center">
						<h5>Avvik</h5>
					</div>
					<div class="col-md-4">
						<a href="images/playground.jpg">
							<img src="images/playground.jpg" class="img-thumbnail img-fluid"/>
						</a>
					</div>
					<div class="col-md-8">
						<table class="table">
							<tr>
								<td>Nummer</td>
								<td>1</td>
							</tr>
							<tr>
								<td>Avvik</td>
								<td>B</td>
							</tr>
							<tr>
								<td>Henvisning til NS</td>
								<td>NS 1-4.2.8.3.5</td>
							</tr>
							<tr>
								<td>Beskrivelse</td>
								<td>Enkelte lenger er åpnet og kan løsne ved stress</td>
							</tr>
							<tr>
								<td>Forslag til tiltak</td>
								<td>Bør byttes</td>
							</tr>
						</table>
					</div>

      

				</div>




			</div>


		</body>
	</html>

</xsl:template>

