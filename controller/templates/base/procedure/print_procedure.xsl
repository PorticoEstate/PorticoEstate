<!-- $Id: procedure_item.xsl 8503 2012-01-06 08:13:27Z erikhl $ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">

	<xsl:variable name="date_format">
		<xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" />
	</xsl:variable>
	<html>
		<body>
			<div id="procedure" style="width: 600px;">
				<h1>
					<xsl:value-of select="procedure/title" />
				</h1>
				<h4>
					<xsl:value-of select="php:function('lang','Procedure revision')" />
				</h4>
				<xsl:value-of select="procedure/revision_no" />
				<h4>
					<xsl:value-of select="php:function('lang','Control area')" />
				</h4>
				<xsl:value-of select="procedure/control_area_name" />
				<h4>
					<xsl:value-of select="php:function('lang','Procedure valid from date')" />
				</h4>
				<xsl:if test="procedure/start_date != 0">
					<xsl:variable name="startdate">
						<xsl:value-of select="procedure/start_date" />
					</xsl:variable>
					<xsl:value-of select="php:function('date', $date_format, $startdate)" />
				</xsl:if>
				<xsl:if test="procedure/revision_date != 0">
					<h4>
						<xsl:value-of select="php:function('lang','Procedure revision date')" />
					</h4>
					<xsl:variable name="revisiondate">
						<xsl:value-of select="procedure/revision_date" />
					</xsl:variable>
					<xsl:value-of select="php:function('date', $date_format, $revisiondate)" />
				</xsl:if>
				<xsl:if test="procedure/end_date != 0">
					<h4>
						<xsl:value-of select="php:function('lang','Procedure end date')" />
					</h4>
					<xsl:variable name="enddate">
						<xsl:value-of select="procedure/end_date" />
					</xsl:variable>
					<xsl:value-of select="php:function('date', $date_format, $enddate)" />
				</xsl:if>
				<h4>
					<xsl:value-of select="php:function('lang','Procedure purpose')" />
				</h4>
				<xsl:value-of select="procedure/purpose" disable-output-escaping="yes"/>
				<h4>
					<xsl:value-of select="php:function('lang','Procedure responsibility')" />
				</h4>
				<xsl:value-of select="procedure/responsibility" disable-output-escaping="yes"/>
				<h4>
					<xsl:value-of select="php:function('lang','Procedure description')" />
				</h4>
				<xsl:value-of select="procedure/description" disable-output-escaping="yes"/>
				<h4>
					<xsl:value-of select="php:function('lang','Procedure Reference')" />
				</h4>
				<xsl:value-of select="procedure/reference" disable-output-escaping="yes"/>
			</div>
			<br/>
			<a href="#print" class="btn" onClick="window.print()">Skriv ut</a>
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
				#procedure {
				font-family: arial;
				font-size: 15px;
				padding: 5px 25px;
				}
				#procedure h1{
				font-size: 24px;
				margin-bottom: 25px;
				}
				#procedure div {
				margin: 15px 0;
				}
				label{
				
				font-weight: bold;
				vertical-align: top;
				width: 200px;
				}

				.btn {
				background: none repeat scroll 0 0 #4F9AEA;
				border: 1px solid #428AD7;
				border-radius: 4px 4px 4px 4px;
				color: #FFFFFF;
				cursor: pointer;
				margin-right: 10px;
				padding: 5px 10px;
				text-decoration: none;
				}
	
				ol{
				margin: 15px;
				padding: 0 20px;
				}
	
				ul{
				list-style: none outside none;
				}
	
				ul.groups li {
				padding: 3px 0;
				}
	
				ul.groups li.odd{
				background: none repeat scroll 0 0 #DBE7F5;
				}
	
				ul.groups h3 {
				font-size: 18px;
				margin: 0 0 5px;
				}
    
				#procedure ul li{
				list-style: disc;
				}
		
			</style>
		</body>
	</html>
</xsl:template>