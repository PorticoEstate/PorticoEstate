
<xsl:template match="data">
	<xsl:choose>
		<xsl:when test="start">
			<xsl:apply-templates select="start"/>
		</xsl:when>
		<xsl:when test="monthly">
			<xsl:apply-templates select="monthly"/>
		</xsl:when>
		<xsl:when test="notification">
			<xsl:apply-templates select="notification"/>
		</xsl:when>
	</xsl:choose>
</xsl:template>


<xsl:template match="start" xmlns:php="http://php.net/xsl">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>

	<script>

		$(document).ready(function($) {
		$(".table-data-link").click(function() {

		console.log($(this));
		//			window.document.location = $(this).data("data-target");

		});
		});

	</script>

	<div class="row">
		<div class="mt-5 container">
			<div class="text-center clearfix">
				<span class="float-left">
					<a href="#">
						<button type="button" class="btn btn-secondary"> 2018</button>
					</a>
				</span>

				<span class="float-right">
					<a href="#">
						<button type="button" class="btn btn-secondary">2020 ></button>
					</a>
				</span>
				<span class="float-none">
					<h4>2019</h4>
				</span>
			</div>
			<div class="mt-3 row">
				<table class="table table-bordered table-hover-cells">
					<thead>
						<tr>
							<th>
								<h5>Bydel</h5>
							</th>
							<th>
								<h5>

									<a>
										<xsl:attribute name="href" >
											<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicalendar_planner.monthly')" />
										</xsl:attribute>
										Januar
									</a>
								</h5>
							</th>
							<th>
								<h5>Februar</h5>
							</th>
							<th>
								<h5>Mars</h5>
							</th>
							<th>
								<h5>April</h5>
							</th>
							<th>
								<h5>Mai</h5>
							</th>
							<th>
								<h5>Juni</h5>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th scope="row">Åsane</th>
							<td class="table-data-link" data-toggle="modal" data-target="#myModal" style="cursor: pointer;">

								<span class="float-left">
									<kbd>F</kbd>
								</span>
								<span class="ml-3 float-left">24</span>
								<span class="float-right">
									<i class="fas fa-check float-right"></i>
								</span>
							</td>
							<div class="modal fade" id="myModal">
								<div class="modal-dialog modal-dialog-centered">
									<div class="modal-content">

										Modal Header
										<div class="mx-auto modal-header">
											<h4 class="modal-title">Velg kontrolltype</h4>
										</div>

										Modal body
										<div class="mx-auto modal-body">
											<div class="row">
												<button type="button" class="btn btn-primary" data-dismiss="modal">Funksjonsettersyn</button>
											</div>
											<div class="mt-3 row">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Kontrollettersyn</button>
											</div>
										</div>

									</div>
								</div>
							</div>

							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<th scope="row">Fyllingsdalen</th>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<th scope="row">Bergenhus</th>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<th scope="row">Laksevåg</th>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<th scope="row">Fana</th>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
					<thead>
						<tr>
							<th>
								<h5>Bydel</h5>
							</th>
							<th>
								<h5>Juli</h5>
							</th>
							<th>
								<h5>August</h5>
							</th>
							<th>
								<h5>September</h5>
							</th>
							<th>
								<h5>Oktober</h5>
							</th>
							<th>
								<h5>November</h5>
							</th>
							<th>
								<h5>Desember</h5>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th scope="row">Åsane</th>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<th scope="row">Fyllingsdalen</th>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<th scope="row">Bergenhus</th>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<th scope="row">Laksevåg</th>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<th scope="row">Fana</th>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="clearfix">
			<span class="float-left">
				<a href="#">
					<button type="button" class="btn btn-warning">Nullstill kalender</button>
				</a>
			</span>
		</div>
	</div>

</xsl:template>


<xsl:template match="monthly" xmlns:php="http://php.net/xsl">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<div class="mt-5 container">
		<div class="row">
			<div class="col">
				<h3>Januar</h3>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<p style="font-size: 14px">I januar er det satt opp befaring i følgende bydeler:
					<ul>
						<li style="font-size: 14px">Åsane - funksjonsettersyn</li>
						<li style="font-size: 14px">Laksevåg - funksjonsettersyn</li>
					</ul>
				</p>
			</div>
			<div class="col">
				<div class="clearfix">
					<span class="float-right" style="font-size: 14px">Legg til ny <i class="far fa-plus-square"></i></span>
				</div>
				<div class="mt-2 clearfix">
					<span class="float-right" style="font-size: 14px">Merk som inaktiv <i class="far fa-trash-alt"></i></span>
				</div>
			</div>
		</div>
		<div class="container">
			<table class="mt-2 table table-hover-cells">
				<thead>
					<tr>
						<th>
							<h5>#</h5>
						</th>
						<th>
							<h5>Mandag</h5>
						</th>
						<th>
							<h5>Tirsdag</h5>
						</th>
						<th>
							<h5>Onsdag</h5>
						</th>
						<th>
							<h5>Torsdag</h5>
						</th>
						<th>
							<h5>Fredag</h5>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th scope="row" style="writing-mode: vertical-rl;text-orientation: upright;">Uke 1</th>
						<td>
							<div class="clearfix">
								<span class="float-left">1</span>
							</div>
							<span class="badge badge-primary">Flaktveit barnehage</span>
							<br />
							<span class="badge badge-primary">Flaktveit skole</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">2</span>
							</div>
							<span class="badge badge-primary">Morvikbotn barnehage </span>
							<br />
							<span class="badge badge-primary">Salhus skole</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">3</span>
							</div>
							<span class="badge badge-primary">Eidsvåg skole</span>
							<br />
							<span class="badge badge-primary">Haukedalen skole</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">4</span>
							</div>
							<span class="badge badge-primary">Hordvik skole</span>
							<br />
							<span class="badge badge-primary">Kalvatræet skole</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">5</span>
							</div>
							<span class="badge badge-primary">Li skole</span>
							<br />
							<span class="badge badge-primary">Rolland skole</span>
							<br />
						</td>
					</tr>
					<tr>
						<th scope="row" style="writing-mode: vertical-rl;text-orientation: upright;">Uke 2</th>
						<td>
							<div class="clearfix">
								<span class="float-left">8</span>
							</div>
							<span class="badge badge-primary">Kyrkjekrinsen skole</span>
							<br />
							<span class="badge badge-primary">Mjølkeråen skole</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">9</span>
							</div>
							<span class="badge badge-primary">Moviksbotn skole</span>
							<br />
							<span class="badge badge-primary">Salhus skole</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">10</span>
							</div>
							<span class="badge badge-primary">Moviksbotn skole</span>
							<br />
							<span class="badge badge-primary">Salhus skole</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">11</span>
							</div>
							<span class="badge badge-primary">Li skole</span>
							<br />
							<span class="badge badge-primary">Rolland skole</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">12</span>
							</div>
							<span class="badge badge-primary">Langerinden barnehage</span>
							<br />
							<span class="badge badge-primary">Liakroken barnehage</span>
							<br />
						</td>
					</tr>
					<tr>
						<th scope="row" style="writing-mode: vertical-rl;text-orientation: upright;">Uke 3</th>
						<td>
							<div class="clearfix">
								<span class="float-left">15</span>
							</div>
							<span class="badge badge-primary">Blokkhaugen barnehage</span>
							<br />
							<span class="badge badge-primary">Ervik barnehage</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">16</span>
							</div>
							<span class="badge badge-primary">Blokkhaugen barnehage</span>
							<br />
							<span class="badge badge-primary">Ervik barnehage</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">17</span>
							</div>
							<span class="badge badge-primary">Moviksbotn skole</span>
							<br />
							<span class="badge badge-primary">Salhus skole</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">18</span>
							</div>
							<span class="badge badge-primary">Alvøen barnehage</span>
							<br />
							<span class="badge badge-primary">Damsgård barnehage</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">19</span>
							</div>
							<span class="badge badge-primary">Loddefjord barnehage</span>
							<br />
							<span class="badge badge-primary">Mathopen barnehage</span>
							<br />
						</td>
					</tr>
					<tr>
						<th scope="row" style="writing-mode: vertical-rl;text-orientation: upright;">Uke 4</th>
						<td>
							<div class="clearfix">
								<span class="float-left">22</span>
							</div>
							<span class="badge badge-primary">Blokkhaugen barnehage</span>
							<br />
							<span class="badge badge-primary">Ervik barnehage</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">23</span>
							</div>
							<span class="badge badge-primary">Blokkhaugen barnehage</span>
							<br />
							<span class="badge badge-primary">Ervik barnehage</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">24</span>
							</div>
							<span class="badge badge-primary">Moviksbotn skole</span>
							<br />
							<span class="badge badge-primary">Salhus skole</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">25</span>
							</div>
							<span class="badge badge-primary">Alvøen barnehage</span>
							<br />
							<span class="badge badge-primary">Damsgård barnehage</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">26</span>
							</div>
							<span class="badge badge-primary">Loddefjord barnehage</span>
							<br />
							<span class="badge badge-primary">Mathopen barnehage</span>
							<br />
						</td>
					</tr>
					<tr>
						<th scope="row" style="writing-mode: vertical-rl;text-orientation: upright;">Uke 5</th>
						<td>
							<div class="clearfix">
								<span class="float-left">29</span>
							</div>
							<span class="badge badge-primary">Blokkhaugen barnehage</span>
							<br />
							<span class="badge badge-primary">Ervik barnehage</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">30</span>
							</div>
							<span class="badge badge-primary">Blokkhaugen barnehage</span>
							<br />
							<span class="badge badge-primary">Ervik barnehage</span>
							<br />
						</td>
						<td>
							<div class="clearfix">
								<span class="float-left">31</span>
							</div>
							<span class="badge badge-primary">Moviksbotn skole</span>
							<br />
							<span class="badge badge-primary">Salhus skole</span>
							<br />
						</td>
						<td class="bg-secondary text-light">
							<div class="clearfix">
								<span class="float-left">1</span>
							</div>
						</td>
						<td class="bg-secondary text-light">
							<div class="clearfix">
								<span class="float-left">2</span>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="container">
			<div class="clearfix">
				<span class="float-left">
					<xsl:variable name="start_url">
						<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicalendar_planner.index')" />
					</xsl:variable>
					<a href="{$start_url}">
						<button type="button" class="btn btn-warning">Gå tilbake</button>
					</a>
				</span>
				<span class="ml-2 float-left">
					<a href="#">
						<button type="button" class="btn btn-warning">Nullstill kalender</button>
					</a>
				</span>
				<span class="float-right">
					<xsl:variable name="send_notification_url">
						<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicalendar_planner.send_notification')" />
					</xsl:variable>
					<a href="{$send_notification_url}">
						<button type="button" class="btn btn-success">Lagre og gå til utsending</button>
					</a>
				</span>
				<span class="mr-2 float-right">
					<a href="#">
						<button type="button" class="btn btn-success">Lagre</button>
					</a>
				</span>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="notification" xmlns:php="http://php.net/xsl">
	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<div class="mt-5 container">
		<div class="row">
			<div class="col">
				<h3>Send varsel</h3>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<p style="font-size: 14px">Det vil sendes varsel til følgende skoler og barnehager
				</p>
			</div>
		</div>
		<div class="container">
			<table class="mt-2 table table-hover">
				<thead>
					<tr>
						<th>
							<h5>#</h5>
						</th>
						<th>
							<h5>Enhet</h5>
						</th>
						<th>
							<h5>Epostadresse</h5>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<i class="far fa-trash-alt"></i>

						</td>
						<td>Alvøen skole
						</td>
						<td>postmottak.alvoenskole@bergen.kommune.no
						</td>
					</tr>
					<tr>
						<td>
							<i class="far fa-trash-alt"></i>
						</td>
						<td>Damsgård barnehage
						</td>
						<td>postmottak.damsgardbarnehage@bergen.kommune.no
						</td>
					</tr>
					<tr>
						<td>
							<i class="far fa-trash-alt"></i>
						</td>
						<td>Hordvik skole
						</td>
						<td>postmottak.hordvikskole@bergen.kommune.no
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="container">
			<div class="clearfix">
				<span class="float-left">
					<xsl:variable name="monthly_url">
						<xsl:value-of select="php:function('get_phpgw_link', '/index.php', 'menuaction:controller.uicalendar_planner.monthly')" />
					</xsl:variable>
					<a href="{$monthly_url}">
						<button type="button" class="btn btn-warning">Gå tilbake</button>
					</a>
				</span>
				<span class="float-right">
					<a href="">
						<button type="button" class="btn btn-success">Send varsel</button>
					</a>
				</span>
			</div>
		</div>
	</div>
</xsl:template>