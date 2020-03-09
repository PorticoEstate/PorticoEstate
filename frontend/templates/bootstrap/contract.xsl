<xsl:template match="section" xmlns:php="http://php.net/xsl">
	<xsl:param name="template_set"/>
	
	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<xsl:call-template name="msgbox"/>
		</xsl:when>
	</xsl:choose>
	
	<div class="container mt-3">
		<div class="row p-3">
			<h4>Interleiekontrakter</h4>
		</div>

		<!-- Tab links -->

		<ul class="nav nav-pills" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" data-toggle="pill" href="#aktive">Aktive</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="pill" href="#inaktive">Inaktive</a>
			</li>
		</ul>
		<form action="{form_url}" method="post">
			<select name="contract_filter" onchange="this.form.submit()">
				<xsl:choose>
					<xsl:when test="//contract_filter = 'active'">
						<option value="active" selected="selected">
							<xsl:value-of select="php:function('lang', 'active')"/>
						</option>
					</xsl:when>
					<xsl:otherwise>
						<option value="active">
							<xsl:value-of select="php:function('lang', 'active')"/>
						</option>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="//contract_filter = 'not_active'">
						<option value="not_active" selected="selected">
							<xsl:value-of select="php:function('lang', 'not_active')"/>
						</option>
					</xsl:when>
					<xsl:otherwise>
						<option value="not_active">
							<xsl:value-of select="php:function('lang', 'not_active')"/>
						</option>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
					<xsl:when test="//contract_filter = 'all'">
						<option value="all" selected="selected">
							<xsl:value-of select="php:function('lang', 'all')"/>
						</option>
					</xsl:when>
					<xsl:otherwise>
						<option value="all">
							<xsl:value-of select="php:function('lang', 'all')"/>
						</option>
					</xsl:otherwise>
				</xsl:choose>
			</select>
		</form>

		<!-- Tab panes -->

		<!-- TAB #1 -->

		<div class="tab-content">
			<div id="aktive" class="container tab-pane active mt-2">
				<br/>
				<div class="row p-3">
					<div class="col-md-3" >
						<div class="card">
							<div class="card-header bg-light content-center">
								<h6 class="text-uppercase">Aktive kontrakter</h6>

							</div>
							<div class="card-body row text-center">

								<form>
									<div class="form-check">
										<label class="toggle">
											<input type="radio" name="toggle"/>
											<span class="label-text">K00001111</span>
										</label>
									</div>
									<div class="form-check">
										<label class="toggle">
											<input type="radio" name="toggle"/>
											<span class="label-text">K00002222</span>
										</label>
									</div>
									<div class="form-check">
										<label class="toggle">
											<input type="radio" name="toggle"/>
											<span class="label-text">K00003333</span>
										</label>
									</div>
									<div class="form-check">
										<label class="toggle">
											<input type="radio" name="toggle"/>
											<span class="label-text">K00004444</span>
										</label>
									</div>
									<div class="form-check">
										<label class="toggle">
											<input type="radio" name="toggle"/>
											<span class="label-text">K00005555</span>
										</label>
									</div>
									<div class="form-check">
										<label class="toggle">
											<input type="radio" name="toggle"/>
											<span class="label-text">K00006666</span>
										</label>
									</div>
									<div class="form-check">
										<label class="toggle">
											<input type="radio" name="toggle"/>
											<span class="label-text">K00007777</span>
										</label>
									</div>
									<div class="form-check">
										<label class="toggle">
											<input type="radio" name="toggle"/>
											<span class="label-text">K00008888</span>
										</label>
									</div>
								</form>
							</div>



						</div>
					</div>
					<div class="col-md-9">
						<div class="row">
							<div class="col-md-6">
								<div class="card">
									<div class="card-header bg-light content-center">
										<h6 class="text-uppercase">Kontraktsdetaljer</h6>

									</div>
									<div class="card-body row">
										<table class="table table-borderless">
											<tr>
												<td>Kontraktsnummer</td>
												<td>K00009999</td>
											</tr>
											<tr>
												<td>Kontraktsområde</td>
												<td>Interleie</td>
											</tr>
											<tr>
												<td>Startdato</td>
												<td>01.01.2000</td>
											</tr>
											<tr>
												<td>Sluttdato</td>
												<td> - </td>
											</tr>
											<tr>
												<td>Leid areal</td>
												<td>5,00 kvm</td>
											</tr>
											<tr>
												<td>Total pris</td>
												<td>99 999,99</td>
											</tr>
											<tr>
												<td>Tjenestested</td>
												<td>999999</td>
											</tr>
											<tr>
												<td>Ansvarssted</td>
												<td>888888</td>
											</tr>


										</table>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="card">
									<div class="card-header bg-light content-center">
										<h6 class="text-uppercase">Kontraktsparter</h6>

									</div>
									<div class="card-body row">
										<div class="col text-center">
											<div class="smallboxline"></div>
											<div class="text-value-xl">Rothaugen skole</div>
											<div class="text-uppercase text-muted small">Bergen kommune</div>
											<div class="text-uppercase text-muted small">Byrådsavd. for barnehage, skole og idrett</div>
											<div class="text-uppercase text-muted small">Etat for skole</div>
										</div>

									</div>
								</div>

								<div class="card mt-4">
									<div class="card-header bg-light content-center">
										<h6 class="text-uppercase">Leieobjekt</h6>

									</div>
									<div class="card-body row">
										<div class="col text-center">
											<div class="smallboxline"></div>
											<div class="text-value-xl">Rothaugen skole - Hovedbygg</div>
											<div class="text-uppercase text-muted small">Rotthaugsgaten 10</div>
										</div>

									</div>
								</div>


							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div id="accordion4" class="col-md-6">
						<div class="card">
							<div class="card-header" id="subMenuHeading4">
								<h5 class="mb-0">
									<button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#collapseSubMenu4" aria-expanded="true" aria-controls="collapseSubMenu4">
										<h6 class="text-uppercase">Se kommentarer</h6>
									</button>
								</h5>
							</div>

							<div id="collapseSubMenu4" class="collapse" aria-labelledby="subMenuHeading4" data-parent="#accordion4">

								<div class="card-body row">
									<div class="col text-center">
										<div class="smallboxline"></div>
										<div class="text-value-xl">Kunnskapsløftet investering 2020: kr. 777 777,-</div>
										<div class="text-muted small">Ole Nilsen, 22.03.2020</div>
									</div>

								</div>
								<div class="card-body row">
									<div class="col text-center">
										<div class="smallboxline"></div>
										<div class="text-value-xl">Kunnskapsløftet investering 2017: kr. 555 555,-</div>
										<div class="text-muted small">Ole Nilsen, 28.02.2017</div>
									</div>

								</div>


							</div>

						</div>
					</div>



					<div id="accordion5" class="col-md-6">
						<div class="card">
							<div class="card-header" id="subMenuHeading5">
								<h5 class="mb-0">
									<button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#collapseSubMenu5" aria-expanded="true" aria-controls="collapseSubMenu5">
										<h6 class="text-uppercase">Send melding</h6>
									</button>
								</h5>
							</div>

							<div id="collapseSubMenu5" class="collapse p-3" aria-labelledby="subMenuHeading5" data-parent="#accordion5">
								<div class="row px-3">
									<div class="form-group w-100">
										<label for="exampleFormControlTextarea6">Tilbakemelding eller spørsmål angående kontrakten?</label>
										<textarea class="form-control" id="exampleFormControlTextarea6" rows="3" width="32" placeholder="Skriv inn din melding her" style="border-width: 0px 0px 1px 0px;"></textarea>
									</div>
									<div class="form-group w-100">
										<button class="btn btn-info float-right w-50">
											<h6 class="text-uppercase">Send melding</h6>
										</button>
									</div>


								</div>
							</div>

						</div>
					</div>





				</div>




			</div>


			<!-- TAB #2 -->

			<div id="inaktive" class="container tab-pane fade">

				<div class="row p-3">
					<div class="col-md-3" >
						<div class="card">
							<div class="card-header bg-light content-center">
								<h6 class="text-uppercase">Inktive kontrakter</h6>

							</div>
							<div class="card-body row text-center">
								<form>

									<div class="form-check">
										<label class="toggle">
											<input type="radio" name="toggle"/>
											<span class="label-text">K00001111</span>
										</label>
									</div>
									<div class="form-check">
										<label class="toggle">
											<input type="radio" name="toggle"/>
											<span class="label-text">K00002222</span>
										</label>
									</div>
									<div class="form-check">
										<label class="toggle">
											<input type="radio" name="toggle"/>
											<span class="label-text">K00003333</span>
										</label>
									</div>
								</form>
							</div>



						</div>
					</div>
					<div class="col-md-9">
						<div class="row">
							<div class="col-md-6">
								<div class="card">
									<div class="card-header bg-light content-center">
										<h6 class="text-uppercase">Kontraktsdetaljer</h6>

									</div>
									<div class="card-body row">
										<table class="table table-borderless">
											<tr>
												<td>Kontraktsnummer</td>
												<td>K00009999</td>
											</tr>
											<tr>
												<td>Kontraktsområde</td>
												<td>Interleie</td>
											</tr>
											<tr>
												<td>Startdato</td>
												<td>01.01.2000</td>
											</tr>
											<tr>
												<td>Sluttdato</td>
												<td>01.01.2018</td>
											</tr>
											<tr>
												<td>Leid areal</td>
												<td>5,00 kvm</td>
											</tr>
											<tr>
												<td>Total pris</td>
												<td>99 999,99</td>
											</tr>
											<tr>
												<td>Tjenestested</td>
												<td>999999</td>
											</tr>
											<tr>
												<td>Ansvarssted</td>
												<td>888888</td>
											</tr>


										</table>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="card">
									<div class="card-header bg-light content-center">
										<h6 class="text-uppercase">Kontraktsparter</h6>

									</div>
									<div class="card-body row">
										<div class="col text-center">
											<div class="smallboxline"></div>
											<div class="text-value-xl">Rothaugen skole</div>
											<div class="text-uppercase text-muted small">Bergen kommune</div>
											<div class="text-uppercase text-muted small">Byrådsavd. for barnehage, skole og idrett</div>
											<div class="text-uppercase text-muted small">Etat for skole</div>
										</div>

									</div>
								</div>

								<div class="card mt-4">
									<div class="card-header bg-light content-center">
										<h6 class="text-uppercase">Leieobjekt</h6>

									</div>
									<div class="card-body row">
										<div class="col text-center">
											<div class="smallboxline"></div>
											<div class="text-value-xl">Rothaugen skole - Hovedbygg</div>
											<div class="text-uppercase text-muted small">Rotthaugsgaten 10</div>
										</div>

									</div>
								</div>


							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div id="accordion4" class="col-md-6">
						<div class="card">
							<div class="card-header" id="subMenuHeading4">
								<h5 class="mb-0">
									<button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#collapseSubMenu4" aria-expanded="true" aria-controls="collapseSubMenu4">
										<h6 class="text-uppercase">Se kommentarer</h6>
									</button>
								</h5>
							</div>

							<div id="collapseSubMenu4" class="collapse" aria-labelledby="subMenuHeading4" data-parent="#accordion4">

								<div class="card-body row">
									<div class="col text-center">
										<div class="smallboxline"></div>
										<div class="text-value-xl">Kunnskapsløftet investering 2020: kr. 777 777,-</div>
										<div class="text-muted small">Ole Nilsen, 22.03.2020</div>
									</div>

								</div>
								<div class="card-body row">
									<div class="col text-center">
										<div class="smallboxline"></div>
										<div class="text-value-xl">Kunnskapsløftet investering 2017: kr. 555 555,-</div>
										<div class="text-muted small">Ole Nilsen, 28.02.2017</div>
									</div>

								</div>


							</div>

						</div>
					</div>

					<div id="accordion5" class="col-md-6">
						<div class="card">
							<div class="card-header" id="subMenuHeading5">
								<h5 class="mb-0">
									<button class="btn btn-light w-100 text-left" data-toggle="collapse" data-target="#collapseSubMenu5" aria-expanded="true" aria-controls="collapseSubMenu5">
										<h6 class="text-uppercase">Send melding</h6>
									</button>
								</h5>
							</div>

							<div id="collapseSubMenu5" class="collapse p-3" aria-labelledby="subMenuHeading5" data-parent="#accordion5">
								<div class="row px-3">
									<div class="form-group w-100">
										<label for="exampleFormControlTextarea6">Tilbakemelding eller spørsmål angående kontrakten?</label>
										<textarea class="form-control" id="exampleFormControlTextarea6" rows="3" width="32" placeholder="Skriv inn din melding her" style="border-width: 0px 0px 1px 0px;"></textarea>
									</div>
									<div class="form-group w-100">
										<button class="btn btn-info float-right w-50">
											<h6 class="text-uppercase">Send melding</h6>
										</button>
									</div>

								</div>
							</div>

						</div>
					</div>

				</div>
			</div>

		</div>
	</div>
</xsl:template>
 
<xsl:template match="contract">
	<xsl:copy-of select="."/>
	
</xsl:template>


