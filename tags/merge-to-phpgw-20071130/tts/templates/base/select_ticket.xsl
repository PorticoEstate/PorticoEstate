<!-- $Id: select_ticket.xsl 17712 2006-12-17 23:29:29Z skwashd $ -->

<xsl:template match="select_ticket">
	<form action="{url_form_action}" method="post">
		<label for="ticket_type"><xsl:value-of select="lang/ticket_type" /></label>: 
		<select name="ticket_type" id="type_id">
			<xsl:for-each select="ticket_type">
				<option value="{id}"><xsl:value-of disable-output-escaping="yes" select="value" /></option>
			</xsl:for-each>
		</select><br />
		<div class="btngrp">
			<input type="submit" name="cancel" value="{lang/cancel}" class="button" />
			<input type="submit" name="next" value="{lang/next}" class="button" />
		</div>
	</form>
</xsl:template>
