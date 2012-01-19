<!-- $Id: procedure_item.xsl 8503 2012-01-06 08:13:27Z erikhl $ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="dateformat"><xsl:value-of select="dateformat" /></xsl:variable>

<div id="control_info" style="margin:40px 0 0 40px;">

</div>

<div id="procedure" style="margin:40px 0 0 40px;">
		<div>
			<label for="title"><xsl:value-of select="php:function('lang','Procedure title')" /></label>
			<xsl:value-of select="procedure/title" />
		</div>
		<div>
			<label for="revision_no"><xsl:value-of select="php:function('lang','Procedure revision')" /></label>
			<xsl:value-of select="procedure/revision_no" />
		</div>
		<div>
			<label for="control_area"><xsl:value-of select="php:function('lang','Control area')" /></label>
			<xsl:value-of select="procedure/control_area_name" />
		</div>
		<div>
			<label for="start_date"><xsl:value-of select="php:function('lang','Procedure start date')" /></label>
			<xsl:variable name="startdate"><xsl:value-of select="procedure/start_date" /></xsl:variable>
			<xsl:value-of select="php:function('date', $dateformat, $startdate)" />
		</div>
		<div>
			<label for="revision_date"><xsl:value-of select="php:function('lang','Procedure revision date')" /></label>
			<xsl:if test="procedure/revision_date != 0">
				<xsl:variable name="revisiondate"><xsl:value-of select="procedure/revision_date" /></xsl:variable>
				<xsl:value-of select="php:function('date', $dateformat, $revisiondate)" />
			</xsl:if>
		</div>
		<div>
			<xsl:if test="procedure/end_date != 0">
				<label for="end_date"><xsl:value-of select="php:function('lang','Procedure end date')" /></label>
			
				<xsl:variable name="enddate"><xsl:value-of select="procedure/end_date" /></xsl:variable>
				<xsl:value-of select="php:function('date', $dateformat, $enddate)" />
			</xsl:if>
		</div>
		<div>
			<label for="purpose"><xsl:value-of select="php:function('lang','Procedure purpose')" /></label>
			<xsl:value-of select="procedure/purpose" disable-output-escaping="yes"/>
		</div>
		<div>
			<label for="responsibility"><xsl:value-of select="php:function('lang','Procedure responsibility')" /></label>
			<xsl:value-of select="procedure/responsibility" />
		</div>
		<div>
			<label for="description"><xsl:value-of select="php:function('lang','Procedure description')" /></label>
			<xsl:value-of select="procedure/description" disable-output-escaping="yes"/>
		</div>
		<div>
			<label for="reference"><xsl:value-of select="php:function('lang','Procedure Reference')" /></label>
			<xsl:value-of select="procedure/reference" />
		</div>
		<div>
			<label for="attachment"><xsl:value-of select="php:function('lang','Procedure Attachment')" /></label>
			<xsl:value-of select="procedure/attachment" />
		</div>		
</div>
<style>

	#procedure{
		font-family: arial;
		font-size:16px;
	}
	#procedure div{
		margin:10px 0;
	 }
	label{ 
		font-weight: bold;
		display: inline-block;
		width: 200px;
	}

	.btn {
	    background: none repeat scroll 0 0 #2647A0;
	    border: 1px solid #173073;
	    color: #FFFFFF;
	    cursor: pointer;
	    display: inline-block;
	    font-family: arial;
	    margin-right: 5px;
	    padding: 5px 20px;
	    text-decoration: none;
	}
	
	ul{
		list-style: none outside none;
	}
	
	li{
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
		
</style>
<a style="margin:20px 0 0 40px;" href="#print" class="btn" onClick="window.print()">Skriv ut</a>
</xsl:template>