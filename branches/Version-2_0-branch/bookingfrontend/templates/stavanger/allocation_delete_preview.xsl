<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

	<dl class="form">
    	<dt class="heading"><xsl:value-of select="php:function('lang', 'Delete Allocations')"/></dt>
	</dl>
    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

	<!-- <xsl:call-template name="xmlsource"/> -->

	    <form action="" method="POST">
			<input type="hidden" name="allocation_id" value="{allocation/id}"/>
			<input type="hidden" name="organization_name" value="{allocation/organization_name}"/>
			<input type="hidden" name="organization_id" value="{allocation/organization_id}"/>
			<input type="hidden" name="building_name" value="{allocation/building_name}"/>
			<input type="hidden" name="building_id" value="{allocation/building_id}"/>
			<input type="hidden" name="from_" value="{from_date}"/>
			<input type="hidden" name="to_" value="{to_date}"/>
			<input type="hidden" name="building_id" value="{allocation/building_id}"/>
			<input type="hidden" name="cost" value="{allocation/cost}"/>
			<input type="hidden" name="season_id" value="{allocation/season_id}"/>
			<input type="hidden" name="field_building_id" value="{allocation/building_id}"/>
			<input type="hidden" name="step" value="{step}" />
			<input type="hidden" name="repeat_until" value="{repeat_until}" />
			<input type="hidden" name="field_interval" value="{interval}" />
			<input type="hidden" name="outseason" value="{outseason}" />
			<input type="hidden" name="recurring" value="{recurring}" />
			<input type="hidden" name="message" value="{message}" />
			<xsl:for-each select="allocation/resources">
				<input type="hidden" name="resources[]" value="{.}" />
			</xsl:for-each>

			<h4><xsl:value-of select="php:function('lang', 'Allocations that will be deleted')" /></h4>
			<div class="allocation-list">
				<xsl:for-each select="valid_dates">
					<li>
						<xsl:value-of select="from_"/> - <xsl:value-of select="to_"/>
					</li>
				</xsl:for-each>
			</div>

			<h4><xsl:value-of select="php:function('lang', 'Allocations  with existing bookings (%1)', count(result/invalid[from_]))" /></h4>
			<div class="allocation-list">
				<xsl:for-each select="invalid_dates">
					<li>
						<xsl:value-of select="from_"/> - <xsl:value-of select="to_"/>
					</li>
				</xsl:for-each>
			</div>
	        <div class="form-buttons">
	            <input type="submit" name="create">
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Delete')" /></xsl:attribute>
				</input>
	            <a class="cancel">
	                <xsl:attribute name="href"><xsl:value-of select="allocation/cancel_link"/></xsl:attribute>
	                <xsl:value-of select="php:function('lang', 'Cancel')" />
	            </a>
	        </div>
		</form>

    </div>
    <script type="text/javascript">
        YAHOO.booking.initialSelection = <xsl:value-of select="allocation/resources_json"/>;
    </script>
</xsl:template>
<xsl:template name="xmlsource">
  NODE <xsl:value-of select="name()"/>
  ATTR { <xsl:for-each select="attribute::*"><xsl:value-of select="name()"/>=<xsl:value-of select="."/> </xsl:for-each> }
  CHILDREN: { <xsl:for-each select="*"><xsl:call-template name="xmlsource"/></xsl:for-each> }
  TEXT <xsl:value-of select="text()"/>
  <br/>
</xsl:template>
