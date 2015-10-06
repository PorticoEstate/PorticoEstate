<xsl:template match="data" xmlns:php="http://php.net/xsl">

    <xsl:call-template name="msgbox"/>
    <!--xsl:call-template name="yui_booking_i18n"/-->
 
	<form id="panel1" method="POST">
		<xsl:attribute name="action"><xsl:value-of select="season/post_url"/></xsl:attribute>
		<div class="hd"><xsl:value-of select="php:function('lang', 'Allocations')" /></div>
		<div class="bd">
			<dl class="form-col">
				<dt><label for="field_org"><xsl:value-of select="php:function('lang', 'Organization')" /></label></dt>
				<dd>
					<input type="hidden" id="field_id" name="id"/>
					<div class="autocomplete">
						<input id="field_org_id" name="organization_id" type="hidden"/>
						<input id="field_org_name" name="organization_name" type="text"/>
						<div id="org_container"/>
					</div>
				</dd>
				<dt><label for="field_wday"><xsl:value-of select="php:function('lang', 'Day of the week')" /></label></dt>
				<dd>
					<select id="field_wday" name="wday">
						<option value="1"><xsl:value-of select="php:function('lang', 'Monday')" /></option>
						<option value="2"><xsl:value-of select="php:function('lang', 'Tuesday')" /></option>
						<option value="3"><xsl:value-of select="php:function('lang', 'Wednesday')" /></option>
						<option value="4"><xsl:value-of select="php:function('lang', 'Thursday')" /></option>
						<option value="5"><xsl:value-of select="php:function('lang', 'Friday')" /></option>
						<option value="6"><xsl:value-of select="php:function('lang', 'Saturday')" /></option>
						<option value="7"><xsl:value-of select="php:function('lang', 'Sunday')" /></option>
					</select>
				</dd>
				<dt><label for="field_from"><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
				<dd>
					<div class="time-picker">
					<input id="field_from" name="from_" type="text">
						<xsl:attribute name="value"><xsl:value-of select="season/from_"/></xsl:attribute>
					</input>
					</div>
				</dd>
				<dt><label for="field_to"><xsl:value-of select="php:function('lang', 'To')" /></label></dt>
				<dd>
					<div class="time-picker">
					<input id="field_to" name="to_" type="text">
						<xsl:attribute name="value"><xsl:value-of select="season/to_"/></xsl:attribute>
					</input>
					</div>
				</dd>
			</dl>
			<dl class="form-col">
				<dt><label for="field_cost"><xsl:value-of select="php:function('lang', 'Cost')" /></label></dt>
				<dd><input id="field_cost" name="cost" type="text"/></dd>
				<dt><label for="field_resources"><xsl:value-of select="php:function('lang', 'Resources')" /></label></dt>
				<dd>
					<div id="resources-container"/>
				</dd>
			</dl>
			<div class="clr"/>
		</div>
	</form>

</xsl:template>
