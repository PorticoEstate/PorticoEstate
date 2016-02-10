<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">

	<dl class="form">
    	<dt class="heading"><xsl:value-of select="php:function('lang', 'Delete Booking')"/></dt>
	</dl>
    <xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

	<!-- <xsl:call-template name="xmlsource"/> -->

	    <form action="" method="POST">
			<input type="hidden" name="booking_id" value="{booking/id}"/>
			<input type="hidden" name="season_id" value="{booking/season_id}"/>
			<input type="hidden" name="building_id" value="{booking/building_id}"/>
			<input type="hidden" name="building_name" value="{booking/building_name}"/>
			<input type="hidden" name="organization_id" value="{booking/organization_id}"/>
			<input type="hidden" name="organization_name" value="{booking/organization_name}"/>
			<input type="hidden" name="allocation_id" value="{booking/allocation_id}"/>
			<input type="hidden" name="noallocation" value="{noallocation}" />
			<input type="hidden" name="delete_allocation" value="{delete_allocation}" />
			<input type="hidden" name="step" value="{step}" />
			<input type="hidden" name="from_" value="{from_date}" />
			<input type="hidden" name="to_" value="{to_date}" />
			<input type="hidden" name="cost" value="{booking/cost}" />
			<input type="hidden" name="repeat_until" value="{repeat_until}" />
			<input type="hidden" name="field_interval" value="{interval}" />
			<input type="hidden" name="recurring" value="{recurring}" />
			<input type="hidden" name="outseason" value="{outseason}" />
			<input type="hidden" name="message" value="{message}" />
			<input type="hidden" name="activity_id" value="{booking/activity_id}" />
			<input type="hidden" name="group_id" value="{booking/group_id}" />
			<xsl:for-each select="booking/audience">
				<input type="hidden" name="audience[]" value="{.}" />
			</xsl:for-each>
			<xsl:for-each select="booking/resources">
				<input type="hidden" name="resources[]" value="{.}" />
			</xsl:for-each>
			<xsl:for-each select="booking/agegroups">
				<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
				<input type="hidden">
					<xsl:attribute name="name">male[<xsl:value-of select="agegroup_id"/>]</xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="male"/></xsl:attribute>
				</input>
				<input type="hidden">
					<xsl:attribute name="name">female[<xsl:value-of select="agegroup_id"/>]</xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="female"/></xsl:attribute>
				</input>
			</xsl:for-each>


			<h4><xsl:value-of select="php:function('lang', 'Bookings to be deleted')" /></h4>
			<div class="allocation-list">
				<xsl:for-each select="valid_dates">
					<li>
						<xsl:value-of select="from_"/> - <xsl:value-of select="to_"/>
					</li>
				</xsl:for-each>
			</div>
            <xsl:if test="delete_allocation='on'">
			<h4><xsl:value-of select="php:function('lang', 'Allocations that is going to be deleted')" /></h4>
			<div class="allocation-list">
				<xsl:for-each select="allocation_delete">
					<li>
						<xsl:value-of select="from_"/> - <xsl:value-of select="to_"/>
					</li>
				</xsl:for-each>
			</div>

			<h4><xsl:value-of select="php:function('lang', 'Allocations with still existing bookings (%1)', count(allocation_keep[from_]))" /></h4>
			<div class="allocation-list">
				<xsl:for-each select="allocation_keep">
					<li>
						<xsl:value-of select="from_"/> - <xsl:value-of select="to_"/>
					</li>
				</xsl:for-each>
			</div>
            </xsl:if>
	        <div class="form-buttons">
	            <input type="submit" name="delete">
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Delete')" /></xsl:attribute>
				</input>
	            <a class="cancel">
	                <xsl:attribute name="href"><xsl:value-of select="season/wtemplate_link"/></xsl:attribute>
	                <xsl:value-of select="php:function('lang', 'Cancel')" />
	            </a>
	        </div>
		</form>

    </div>
    <script type="text/javascript">
        YAHOO.booking.season_id = '<xsl:value-of select="booking/season_id"/>';
        YAHOO.booking.group_id = '<xsl:value-of select="booking/group_id"/>';
        YAHOO.booking.initialSelection = <xsl:value-of select="booking/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'Resource Type')"/>;
    </script>
</xsl:template>
<xsl:template name="xmlsource">
  NODE <xsl:value-of select="name()"/>
  ATTR { <xsl:for-each select="attribute::*"><xsl:value-of select="name()"/>=<xsl:value-of select="."/> </xsl:for-each> }
  CHILDREN: { <xsl:for-each select="*"><xsl:call-template name="xmlsource"/></xsl:for-each> }
  TEXT <xsl:value-of select="text()"/>
  <br/>
</xsl:template>
