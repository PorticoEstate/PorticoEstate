<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">

<div id="main_content">
		
	  <!-- ===========================  SHOWS CONTROL ITEMS RECEIPT   =============================== -->
		<xsl:variable name="control_id"><xsl:value-of select="control_id"/></xsl:variable>	
		<input type="hidden" id="control_id" name="control_id" value="{control_id}" />
		
		<h3>Detaljer for kontroll</h3>
		<fieldset>
			<label>Tittel</label><xsl:value-of select="control_as_array/title"/><br/>
			<label>Startdato</label><xsl:value-of select="control_as_array/start_date"/><br/>
			<label>Sluttdato</label><xsl:value-of select="control_as_array/end_date"/><br/>
			<label>Syklustype</label><xsl:value-of select="control_as_array/repeat_type"/><br/>
			<label>Syklusfrekvens</label><xsl:value-of select="control_as_array/repeat_interval"/><br/>
		</fieldset>
		
		<h3>Sjekklister</h3>
		<ul class="check_list">
			<xsl:choose>
				<xsl:when test="check_list_array/child::node()">
					<xsl:for-each select="check_list_array">
						<li>
					       <span><xsl:number/></span>. <xsl:value-of select="id"/><xsl:value-of select="control_id"/><xsl:value-of select="status"/><xsl:value-of select="comment"/>
					    </li>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					Ingen sjekklister for denne kontrollen
				</xsl:otherwise>
			</xsl:choose>
			
			
		</ul>
</div>
</xsl:template>