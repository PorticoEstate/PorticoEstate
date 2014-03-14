<!-- $Id: procedure_item.xsl 8503 2012-01-06 08:13:27Z erikhl $ -->
<!-- item  -->

<xsl:template match="data" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" /></xsl:variable>

<div id="procedure">
		<h1><xsl:value-of select="procedure/title" /></h1>

		<div>
			<label for="revision_no"><xsl:value-of select="php:function('lang','Procedure revision')" /></label>
			<span style="display: inline-block;width: 600px;"><xsl:value-of select="procedure/revision_no" /></span>
		</div>
		<div>
			<label for="control_area"><xsl:value-of select="php:function('lang','Control area')" /></label>
			<span style="display: inline-block;width: 600px;"><xsl:value-of select="procedure/control_area_name" /></span>
		</div>
		<div>
			<label for="start_date"><xsl:value-of select="php:function('lang','Procedure valid from date')" /></label>
			<xsl:variable name="startdate"><xsl:value-of select="procedure/start_date" /></xsl:variable>
			<xsl:value-of select="php:function('date', $date_format, $startdate)" />
		</div>
		<div>
			<label for="revision_date"><xsl:value-of select="php:function('lang','Procedure revision date')" /></label>
			<xsl:if test="procedure/revision_date != 0">
				<xsl:variable name="revisiondate"><xsl:value-of select="procedure/revision_date" /></xsl:variable>
				<xsl:value-of select="php:function('date', $date_format, $revisiondate)" />
			</xsl:if>
		</div>
		<div>
			<xsl:if test="procedure/end_date != 0">
				<label for="end_date"><xsl:value-of select="php:function('lang','Procedure end date')" /></label>
			
				<xsl:variable name="enddate"><xsl:value-of select="procedure/end_date" /></xsl:variable>
				<xsl:value-of select="php:function('date', $date_format, $enddate)" />
			</xsl:if>
		</div>
		<div>
			<label for="purpose"><xsl:value-of select="php:function('lang','Procedure purpose')" /></label>
			<span style="display: inline-block;width: 600px;"><xsl:value-of select="procedure/purpose" disable-output-escaping="yes"/></span>
		</div>
		<div>
			<label for="responsibility"><xsl:value-of select="php:function('lang','Procedure responsibility')" /></label>
			<span style="display: inline-block;width: 600px;"><xsl:value-of select="procedure/responsibility" /></span>
		</div>
		<div>
			<label for="description"><xsl:value-of select="php:function('lang','Procedure description')" /></label>
			<span style="display: inline-block;width: 600px;"><xsl:value-of select="procedure/description" disable-output-escaping="yes"/></span>
		</div>
		<div>
			<label for="reference"><xsl:value-of select="php:function('lang','Procedure Reference')" /></label>
      <span style="display: inline-block;width: 600px;">
        <xsl:value-of select="procedure/reference" disable-output-escaping="yes"/>
      </span>
		</div>
		<a href="#print" class="btn" onClick="window.print()">Skriv ut</a>		
</div>
<style>

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
		display: inline-block;
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
    display: inline-block;
    margin-right: 10px;
    padding: 5px 10px;
    text-decoration: none;
}
	
	ol{
 		margin: 0;
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
		
</style>
</xsl:template>