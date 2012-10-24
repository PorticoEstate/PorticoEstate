<!-- $Id: control.xsl 9951 2012-08-31 10:14:12Z vator $ -->
<xsl:template match="data" name="control_details" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" /></xsl:variable>
	<div id="show-control-details-box">
	 <h3>Detaljer for kontroll</h3>
	 	
	 	<ul class='elem-wrp'>
		  <li><label>Kontrollområde</label><xsl:value-of select="control/control_area_name" /></li>
		  <li><label>Prosedyre</label><xsl:value-of select="control/procedure_name" /></li>
	   	<li><label for="start_date">Startdato</label>
	  			<xsl:value-of select="php:function('date', $date_format, number(control/start_date))"/>
	  	</li>
		  <li>
		  	<label for="end_date">Sluttdato</label>
		  	<xsl:choose>
				<xsl:when test="control/end_date != 0">
					<xsl:value-of select="php:function('date', $date_format, number(control/end_date))"/>
				</xsl:when>
				<xsl:otherwise>
					Løpende
				</xsl:otherwise>
		  	</xsl:choose>
		  </li>
		  <li><label>Frekvens</label>
		  		<xsl:choose>
	 			<xsl:when test="control/repeat_interval = 1">
	     		<span class="pre">Hver</span>
	     	</xsl:when>
	     	<xsl:when test="control/repeat_interval = 2">
	     		<span class="pre">Annenhver</span>
	     	</xsl:when>
	     	<xsl:when test="control/repeat_interval > 2">
	     		<span class="pre">Hver</span><span><xsl:value-of select="control/repeat_interval"/>.</span>
	     	</xsl:when>
	     </xsl:choose>
	     <span class="val"><xsl:value-of select="control/repeat_type_label"/></span>
		  </li>
	</ul>
	</div>
</xsl:template>
