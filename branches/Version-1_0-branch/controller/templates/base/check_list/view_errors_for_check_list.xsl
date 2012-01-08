<!-- $Id: edit_check_list.xsl 8513 2012-01-07 10:38:09Z vator $ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format">d/m-Y</xsl:variable>

<div id="main_content">
		
	<script>
	
		$(document).ready(function() {
				var requestUrl = $("#view_open_errors").attr("href");
			 	load_tab(requestUrl);
			});
			
		$(function() {
					
			$("#register_errors").live("click", function(){
				var requestUrl = $(this).attr("href");
				load_tab(requestUrl);
			
				return false;
			});
						
			$("#view_open_errors").live("click", function(){
				var requestUrl = $(this).attr("href");
				load_tab(requestUrl);
			
				return false;
			});
			
			$("#view_closed_errors").live("click", function(){
				var requestUrl = $(this).attr("href");
				load_tab(requestUrl);
			
				return false;
			});
			
			$("#view_measurements").live("click", function(){
				var requestUrl = $(this).attr("href");
				load_tab(requestUrl);
			
				return false;
			});
		});

		function load_tab(requestUrl){
			$.ajax({
				  type: 'POST',
				  url: requestUrl,
				  success: function(data) {
				  	$("#load_view_content").html(data);
				  }
			});
		}
	</script>
		
		<h1>Sjekkliste for <xsl:value-of select="location_array/loc1_name"/></h1>
		
		<div id="edit_check_list_menu" class="hor_menu">
			<a>
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.edit_check_list_for_location</xsl:text>
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="check_list/id"/>
				</xsl:attribute>
				Vis detaljer for sjekkliste
			</a>
			<a class="active">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.view_errors_for_check_list</xsl:text>
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="check_list/id"/>
				</xsl:attribute>
				Vis avvik/saker for sjekkliste
			</a>			
			<a>
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.view_control_info</xsl:text>
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="check_list/id"/>
				</xsl:attribute>
				Vis info om kontroll
			</a>
			
			<a style="background:#DD624B;border-bottom: 1px solid #CB563F;border-top: 1px solid #EE836F;box-shadow: 0 1px 0 #A9422E, 0 -1px 0 #A9422E;color: #FFFFFF;height: 18px;margin-left: 40px;margin-top: 1px;">
				<xsl:attribute name="href">
					<xsl:text>index.php?menuaction=controller.uicheck_list_for_location.register_error</xsl:text>
					<xsl:text>&amp;check_list_id=</xsl:text>
					<xsl:value-of select="check_list/id"/>
				</xsl:attribute>
				Registrer avvik/sak
			</a>
		</div>
		
		
		<a style="display:none;" id="view_open_errors">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_open_errors</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
			</xsl:attribute>
		</a>
	
		<div id="load_view_content"></div>	
</div>
</xsl:template>
