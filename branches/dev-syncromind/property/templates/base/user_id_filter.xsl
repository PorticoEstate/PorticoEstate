  <!-- $Id$ -->
	<xsl:template name="user_id_filter">
		<xsl:variable name="select_action">
			<xsl:value-of select="select_action"/>
		</xsl:variable>
		<xsl:variable name="select_user_name">
			<xsl:value-of select="select_user_name"/>
		</xsl:variable>
		<xsl:variable name="lang_submit">
			<xsl:value-of select="lang_submit"/>
		</xsl:variable>
			<select name="{$select_user_name}">
				<xsl:attribute name="title">
					<xsl:value-of select="lang_user_statustext"/>
				</xsl:attribute>
				<option value="">
					<xsl:value-of select="lang_no_user"/>
				</option>
				<xsl:apply-templates select="user_list"/>
			</select>
		<script>
			$(document).ready(function(){
				$('select[name="<xsl:value-of select="$select_user_name"/>"]').change( function( e ) {
					var strURL = "<xsl:value-of select="$select_action"/>";
					user_id = $(this).val();
					strURL += '&amp;' + "<xsl:value-of select="$select_user_name"/>=" + user_id;
					window.location.replace(strURL);
				});
			});
		</script>
	</xsl:template>

	<!-- New template-->
	<xsl:template match="user_list">
		<xsl:variable name="user_id">
			<xsl:value-of select="user_id"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="selected = 1">
				<option value="{$user_id}" selected="selected">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</option>
			</xsl:when>
			<xsl:otherwise>
				<option value="{$user_id}">
					<xsl:value-of disable-output-escaping="yes" select="name"/>
				</option>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
