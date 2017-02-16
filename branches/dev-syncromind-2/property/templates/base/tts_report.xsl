
<!-- $Id$ -->

<!-- New template-->
<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.js"></script>
	<fieldset>

		<div class="pure-control-group">
			<label>
				<xsl:value-of select="php:function('lang', 'from')"/>
			</label>
			<input type="text" id="filter_start_date" name="values[start_date]" size="10" value="{filter_start_date}" readonly="readonly" onMouseout="window.status='';return true;">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_finnish_date_statustext"/>
				</xsl:attribute>
			</input>
		</div>
					
		<div class="pure-control-group">
			<label>
				<xsl:value-of select="php:function('lang', 'to')"/>
			</label>
			<input type="text" id="filter_end_date" name="values[end_date]" size="10" value="{filter_end_date}" readonly="readonly" onMouseout="window.status='';return true;">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_finnish_date_statustext"/>
				</xsl:attribute>
			</input>
		</div>
					
		<div id="canvas-holder" style="width:40%">
			<canvas id="chart-area" />
		</div>
	</fieldset>
    <script>

var data = {
    labels: [
		"Open",
		"Ny Melding",
		"Closed",
		"Hos teknisk pers. på bygget",
		"Utføres av EBE",
		"Utført av EBE og Avsluttet",
		"Saksbehandler hos EBE (A)",
		"I bestilling/ under utføring (B)",
		"Utført av leverandør",
		"Bestilling til godkjenning",
		"Bestilling godkjent",
		"Avsluttet og fakturert (C)",
		"Avvist",
		"Overført behovsliste",
		"Garantioppfølging",
		"Brukers ansvar"
    ],
    datasets: [
        {
            data: [300,50,100,24,45,78,122,13,45,23,88,124,33,44,55,66],
            backgroundColor: [
                "#FF6384",
                "#36A2EB",
                "#FFCA56",
				"#FFCE51",
				"#FFCE52",
				"#FFCE53",
				"#FFCE54",
				"#FFCE56",
				"#FFCE57",
				"#FFCE58",
				"#FFCE59",
				"#36A222",
				"#FFCE32",
				"#FFCE45",
				"#F5CE77",
				"#4FCE10"
            ],
            hoverBackgroundColor: [
                "#FF6384",
                "#36A2EB",
                "#FFCA56",
				"#FFCE51",
				"#FFCE52",
				"#FFCE53",
				"#FFCE54",
				"#FFCE56",
				"#FFCE57",
				"#FFCE58",
				"#FFCE59",
				"#XFCE21",
				"#FFCE32",
				"#FFCE45",
				"#F5CE77",
				"#4FCE10"
            ]
        }]
};
		
var ctx = document.getElementById("chart-area");
var myPieChart = new Chart(ctx, {
	type: 'pie',
	data: data,
	options:  {
            responsive: true
        }
});
    </script>
</xsl:template>
