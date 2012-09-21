<!-- $Id$ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" /></xsl:variable>

<div id="main_content" class="medium">
		
	  <!-- ===========================  SHOWS CONTROL ITEMS RECEIPT   =============================== -->
				
		<h1>Sjekkliste</h1>
		<fieldset class="control_details">
			<div class="row"><label>Tittel</label><xsl:value-of select="check_list/status"/></div>
			<div class="row"><label>Kommentar</label><xsl:value-of select="check_list/comment"/></div>
			<div class="row"><label>Skal utføres innen</label>
				<xsl:if test="check_list/deadline != ''">
					<xsl:value-of select="php:function('date', $date_format, number(check_list/deadline))"/><br/>
				</xsl:if>
			</div>
		</fieldset>
				
		<h2>Sjekkpunkter</h2>
		<ul class="check_list">
			<li class="heading">
				<div class="status">Status</div>
				<div class="title">Tittel for kontrollpunkt</div>
				<div>Kommentar</div>
			</li>
			
			<xsl:choose>
				<xsl:when test="check_list/check_item_array/child::node()">
					<xsl:for-each select="check_list/check_item_array">
						<li>
						   <div class="order_nr"><xsl:number/>.</div>
						   <div class="status">
						   	 <xsl:variable name="status"><xsl:value-of select="status"/></xsl:variable>	
							 <xsl:choose>
								<xsl:when test="status = 1">
									<img height="15" src="controller/images/status_icon_light_green.png" />	
								</xsl:when>
								<xsl:otherwise>
									<img height="15" src="controller/images/status_icon_red.png" />
								</xsl:otherwise>
							</xsl:choose>
						   </div>
						   <div class="title"><xsl:value-of select="control_item/title"/></div>
						   <div><xsl:value-of select="comment"/></div>
						</li>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					Ingen sjekklister for denne kontrollen
				</xsl:otherwise>
			</xsl:choose>
		</ul>
		
		<a>
			<xsl:attribute name="href">
			<xsl:text>index.php?menuaction=controller.uicheck_list.edit_check_list</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
			</xsl:attribute>
			<div>Registrer sjekkliste</div>
		</a>
		
</div>
</xsl:template>
