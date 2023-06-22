<xsl:template match="section" xmlns:php="http://php.net/xsl">
	<xsl:param name="template_set"/>
	<xsl:variable name="form_url">
		<xsl:value-of select="form_url"/>
	</xsl:variable>

	<xsl:choose>
		<xsl:when test="msgbox_data != ''">
			<xsl:call-template name="msgbox"/>
		</xsl:when>
	</xsl:choose>


	<!-- Tab links -->
	<form action="{$form_url}" method="post">
		<select class="form-select" name="contract_filter" onchange="this.form.submit()">
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

	<div class="row mt-3">
		<div class="col-md-3" >

			<xsl:choose>
				<xsl:when test="not(normalize-space(select)) and (count(select) &lt;= 1)">
					<div class="pure-u-1">
						<xsl:value-of select="php:function('lang', 'no_contracts')"/>
					</div>
				</xsl:when>
				<xsl:otherwise>
					<div class="card">
						<div class="card-header bg-light content-center">
							<h6 class="text-uppercase">
								<xsl:value-of select="php:function('lang', 'contracts')"/>

							</h6>
						</div>
						<div class="card-body row text-center">
							<form action="{$form_url}" method="post">
								<xsl:for-each select="select">
									<div class="form-check">
										<label class="toggle">
											<input name="contract_id" type="radio" value="{id}" onclick	="this.form.submit();">
												<xsl:if test="id = //selected_contract">
													<xsl:attribute name="checked">
														<xsl:text>true</xsl:text>
													</xsl:attribute>
												</xsl:if>
											</input>
											<span class="label-text">
												<xsl:value-of select="old_contract_id"/> (<xsl:value-of select="contract_status"/>)
											</span>
										</label>
									</div>
								</xsl:for-each>
							</form>
						</div>
					</div>
				</xsl:otherwise>
			</xsl:choose>
		</div>
		<xsl:for-each select="contract">
			<div class="col-md-9">
				<div>
					<div>
						<div class="row">
							<div class="col-md-6">
								<div class="card">
									<div class="card-header bg-light content-center">
										<h6 class="text-uppercase">Kontraktsdetaljer</h6>
									</div>
									<div class="card-body row">

										<table class="table table-borderless">
											<tr>
												<td>
													<xsl:value-of select="php:function('lang', 'old_contract_id')"/>
												</td>
												<td>
													<xsl:value-of select="old_contract_id"/>
												</td>
											</tr>
											<tr>
												<td>
													<xsl:value-of select="php:function('lang', 'contract_type')"/>
												</td>
												<td>
													<xsl:value-of select="type"/>
												</td>
											</tr>
											<tr>
												<td>
													<xsl:value-of select="php:function('lang', 'contract_status')"/>
												</td>
												<td>
													<xsl:value-of select="contract_status"/>
												</td>
											</tr>
											<tr>
												<td>
													<xsl:value-of select="php:function('lang', 'date_start')"/>
												</td>
												<td>
													<xsl:value-of select="date_start"/>
												</td>
											</tr>
											<tr>
												<td>
													<xsl:value-of select="php:function('lang', 'date_end')"/>
												</td>
												<td>
													<xsl:choose>
														<xsl:when test="date_end != ''">
															<xsl:value-of select="date_end"/>
														</xsl:when>
														<xsl:otherwise >
															<xsl:value-of select="php:function('lang', 'no_end_date')"/>
														</xsl:otherwise>
													</xsl:choose>
												</td>
											</tr>
											<tr>
												<td>
													<xsl:value-of select="php:function('lang', 'rented_area')"/>
												</td>
												<td>
													<xsl:value-of select="rented_area"/>
												</td>
											</tr>
											<tr>
												<td>
													<xsl:value-of select="php:function('lang', 'total_price')"/>
												</td>
												<td>
													<xsl:value-of select="total_price"/>
												</td>
											</tr>
											<tr>
												<td>
													<xsl:value-of select="php:function('lang', 'service_id')"/>
												</td>
												<td>
													<xsl:value-of select="service_id"/>
												</td>
											</tr>
											<tr>
												<td>
													<xsl:value-of select="php:function('lang', 'responsibility_id')"/>
												</td>
												<td>
													<xsl:value-of select="responsibility_id"/>
												</td>
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

									<xsl:for-each select="../party">
										<div class="card-body row">
											<div class="col text-center">
												<div class="smallboxline"></div>
												<div class="text-value-xl">
													<xsl:value-of select="name"/>
												</div>

												<xsl:choose>
													<xsl:when test="normalize-space(address)">
														<div class="text-uppercase text-body-secondary small">
															<xsl:value-of select="address"/>
														</div>
													</xsl:when>
													<xsl:when test="normalize-space(address1)">
														<div class="text-uppercase text-body-secondary small">
															<xsl:value-of select="address1"/>
															<br/>
															<xsl:value-of select="address2"/>
															<br/>
															<xsl:value-of select="postal_code"/>&nbsp;
															<xsl:value-of select="place"/>
														</div>
													</xsl:when>
													<xsl:when test="normalize-space(department)">
														<div class="text-uppercase text-body-secondary small">
															<xsl:value-of select="department"/>
														</div>
													</xsl:when>
												</xsl:choose>
											</div>
										</div>
									</xsl:for-each>

								</div>

								<div class="card mt-4">
									<div class="card-header bg-light content-center">
										<h6 class="text-uppercase">Leieobjekt</h6>

									</div>
									<xsl:for-each select="../composite">

										<div class="card-body row">
											<div class="col text-center">
												<div class="smallboxline"></div>
												<div class="text-value-xl">
													<xsl:value-of select="name" />
												</div>

												<xsl:if test="normalize-space(address)">
													<div class="text-uppercase text-body-secondary small">
														<xsl:value-of select="address" disable-output-escaping="yes"/>
													</div>
												</xsl:if>
											</div>
										</div>
									</xsl:for-each>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="row">
					<xsl:choose>
						<xsl:when test="publish_comment = 1">
							<div id="accordion4" class="col-md-12 mt-3">
								<div class="card">
									<div class="card-header" id="subMenuHeading4">
										<h5 class="mb-0">
											<button class="btn btn-light w-100 text-start" data-bs-toggle="collapse" data-bs-target="#collapseSubMenu4" aria-expanded="true" aria-controls="collapseSubMenu4">
												<h6 class="text-uppercase">
													<xsl:value-of select="php:function('lang', 'remark')"/>
												</h6>
											</button>
										</h5>
									</div>
									<div id="collapseSubMenu4" class="collapse" aria-labelledby="subMenuHeading4" data-parent="#accordion4">
										<div class="card-body row">
											<div class="col text-start">
												<div class="smallboxline"></div>
												<div class="text-value-xl">
													<xsl:value-of select="comment" disable-output-escaping="yes"/>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</xsl:when>
					</xsl:choose>

					<div id="accordion5" class="col-md-12 mt-3">
						<div class="card">
							<div class="card-header" id="subMenuHeading5">
								<h5 class="mb-0">
									<button class="btn btn-light w-100 text-start" data-bs-toggle="collapse" data-bs-target="#collapseSubMenu5" aria-expanded="true" aria-controls="collapseSubMenu5">
										<h6 class="text-uppercase">
											<xsl:value-of select="php:function('lang', 'send_contract_message')"/>
										</h6>
									</button>
								</h5>
							</div>

							<div id="collapseSubMenu5" class="collapse p-3" aria-labelledby="subMenuHeading5" data-parent="#accordion5">
								<div class="row px-3">
									<form action="{$form_url}" method="post" class="w-100">
										<input type="hidden" name="contract_id" value="{//selected_contract}"/>

										<div class="form-group w-100">
											<label for="exampleFormControlTextarea6">
												<xsl:value-of select="php:function('lang', 'send_contract_message')"/>

											</label>
											<textarea name="contract_message" class="form-control" id="exampleFormControlTextarea6" rows="3" width="32" placeholder="Skriv inn din melding her" style="border-width: 0px 0px 1px 0px;">
											</textarea>
										</div>
										<div class="form-group w-100">
											<button class="btn btn-info float-end w-50" type="submit" name="send">
												<h6 class="text-uppercase">
													<xsl:value-of select="php:function('lang', 'btn_send')"/>
												</h6>
											</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</xsl:for-each>
	</div>
</xsl:template>

<xsl:template match="contract">
	<xsl:copy-of select="."/>

</xsl:template>


