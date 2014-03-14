<!-- $Id: edit_check_list.xsl 8478 2012-01-03 12:36:37Z vator $ -->
<xsl:template match="data" name="view_check_list" xmlns:php="http://php.net/xsl">
<xsl:variable name="date_format"><xsl:value-of select="php:function('get_phpgw_info', 'user|preferences|common|dateformat')" /></xsl:variable>
<xsl:variable name="session_url">&amp;<xsl:value-of select="php:function('get_phpgw_session_url')" /></xsl:variable>

<div id="main_content" class="medium">
	<script>
	  
		$(document).ready(function() {
			var requestUrl = $("#view_control_details").attr("href");
			load_tab(requestUrl);
		});
	
		$(function() {
			
			$(".tab_menu a").click(function(){
				var thisTabA = $(this);
				var thisTabMenu = $(this).parent(".tab_menu");
								
				var showId = $(thisTabA).attr("href");
				var hideId = $(thisTabMenu).find("a.active").attr("href");
							
				$(thisTabMenu).find("a").removeClass("active");
				$(thisTabA).addClass('active');
												
				$(hideId).hide();
				$(hideId).removeClass("active")
				$(showId).fadeIn('10', function(){
					$(showId).addClass('active');
					
				});
			
				return false;
			});
			
			$("#view_control_details").click(function(){
				var requestUrl = $(this).attr("href");
				load_tab(requestUrl);
			
				return false;
			});
			
			$("#view_control_items").click(function(){
				var requestUrl = $(this).attr("href");
				load_tab(requestUrl);
			
				return false;
			});
			
			$("#view_procedures").click(function(){
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
				  	$("#tab_content").html(data);
				  }
			});
		}
				
	</script>
		
    <xsl:call-template name="check_list_top_section">
      <xsl:with-param name="active_tab">view_control_info</xsl:with-param>
    </xsl:call-template>
				
	<div class="tab_menu">
		<a id="view_control_details" class="active">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_control_details</xsl:text>
				<xsl:text>&amp;control_id=</xsl:text>
				<xsl:value-of select="control/id"/>
				<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
				<xsl:value-of select="$session_url"/>
			</xsl:attribute>
			Kontrolldetaljer
		</a>
		<a id="view_control_items">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uicheck_list.view_control_items</xsl:text>
				<xsl:text>&amp;check_list_id=</xsl:text>
				<xsl:value-of select="check_list/id"/>
				<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
				<xsl:value-of select="$session_url"/>
			</xsl:attribute>
			Kontrollpunkter
		</a>
		<a id="view_procedures">
			<xsl:attribute name="href">
				<xsl:text>index.php?menuaction=controller.uiprocedure.view_procedures_for_control</xsl:text>
				<xsl:text>&amp;control_id=</xsl:text>
				<xsl:value-of select="control/id"/>
				<xsl:text>&amp;location_code=</xsl:text>
				<xsl:value-of select="location_array/location_code"/>
				<xsl:text>&amp;phpgw_return_as=stripped_html</xsl:text>
				<xsl:value-of select="$session_url"/>
			</xsl:attribute>
			Prosedyrer
		</a>
	</div>
		
	<div id="tab_content" class="content_wrp"></div>
	
</div>
</xsl:template>
