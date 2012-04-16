<!-- $Id$ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content" class="medium">
		
	<!--  =======================  CALENDAR  ================================== -->
	<script>
		$(function() {
			$( "#planned_date" ).datepicker({ 
				monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
				dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
				dateFormat: 'dd/mm-yy',
				changeMonth: true,
				changeYear: true
			});
			$( "#completed_date" ).datepicker({ 
				monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
				dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
				dateFormat: 'dd/mm-yy',
				changeMonth: true,
				changeYear: true 
			});
			$( "#deadline_date" ).datepicker({ 
				monthNames: ['Januar','Februar','Mars','April','Mai','Juni','Juli','August','September','Oktober','November','Desember'],
				dayNamesMin: ['Sø', 'Ma', 'Ti', 'On', 'To', 'Fr', 'Lø'],
				dateFormat: 'dd/mm-yy',
				changeMonth: true,
				changeYear: true 
			});
			
		});
	</script>
	
	<!-- ==================  EDIT CHECKLIST  ========================= -->
	
	<div>
		<h1>Kontroll: <xsl:value-of select="control/title"/></h1>
		<h2>Bygg: <xsl:value-of select="location_array/loc1_name"/></h2>
		
		<!-- ==================  CHECKLIST TAB MENU  ===================== -->
		<xsl:call-template name="check_list_tab_menu">
	 		<xsl:with-param name="active_tab">view_details</xsl:with-param>
		</xsl:call-template>
	</div>
	
	<!-- ==================  CHECKLIST DETAILS  ===================== -->
	<div id="check_list_details">
		<h3 class="box_header">Sjekklistedetaljer</h3>
			<form id="frm_update_check_list" action="index.php?menuaction=controller.uicheck_list.update_check_list" method="post">	
			<xsl:variable name="check_list_id"><xsl:value-of select="check_list/id"/></xsl:variable>
			<input id="check_list_id" type="hidden" name="check_list_id" value="{$check_list_id}" />
			
			<fieldset class="col_1">
			<div class="row">
				<label>Status</label>
				<xsl:variable name="status"><xsl:value-of select="check_list/status"/></xsl:variable>
				<select id="status" name="status">
					<xsl:choose>
						<xsl:when test="check_list/status = 0">
							<option value="1">Utført</option>
							<option value="0" SELECTED="SELECTED" >Ikke utført</option>
						</xsl:when>
						<xsl:when test="check_list/status = 1">
							<option value="1" SELECTED="SELECTED">Utført</option>
							<option value="0">Ikke utført</option>
						</xsl:when>
					</xsl:choose>
				</select>
			</div>
			<div class="row">
				<label>Skal utføres innen</label>
				<input class="date">
			      <xsl:attribute name="id">deadline_date</xsl:attribute>
			      <xsl:attribute name="name">deadline_date</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
			      <xsl:if test="check_list/deadline != 0">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/deadline))"/></xsl:attribute>
				  </xsl:if>
			    </input>
			</div>
			<div class="row">
				<label>Planlagt dato</label>
				<input class="date">
			      <xsl:attribute name="id">planned_date</xsl:attribute>
			      <xsl:attribute name="name">planned_date</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
			      <xsl:if test="check_list/planned_date != 0">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/planned_date))"/></xsl:attribute>
			      </xsl:if>
			    </input>
		    </div>
		    <div class="row">
				<label>Utført dato</label>
				<input class="date">
			      <xsl:attribute name="id">completed_date</xsl:attribute>
			      <xsl:attribute name="name">completed_date</xsl:attribute>
			      <xsl:attribute name="type">text</xsl:attribute>
				  <xsl:if test="check_list/completed_date != 0">
			      	<xsl:attribute name="value"><xsl:value-of select="php:function('date', $date_format, number(check_list/completed_date))"/></xsl:attribute>
			      </xsl:if>
			    </input>
		    </div>
		    </fieldset>
		    <fieldset class="col_2">
			    <div class="row">
					<label>Antall åpne saker</label>
				     <xsl:value-of select="check_list/num_open_cases"/>
			    </div>
			    <div class="row">
					<label>Antall ventende saker</label>
				     <xsl:value-of select="check_list/num_pending_cases"/>
			    </div>
		    </fieldset>
		    
			<div class="comment">
				<label>Kommentar</label>
				<textarea>
				  <xsl:attribute name="name">comment</xsl:attribute>
				  <xsl:value-of select="check_list/comment"/>
				</textarea>
			</div>
			
			<div class="form-buttons">
				<xsl:variable name="lang_save"><xsl:value-of select="php:function('lang', 'save_check_list')" /></xsl:variable>
				<input class="btn not_active" type="submit" name="save_control" value="Lagre detaljer" />
			</div>
			</form>
		</div>
	</div>
</xsl:template>
