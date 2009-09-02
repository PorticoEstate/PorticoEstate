<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
					<xsl:attribute name="href"><xsl:value-of select="application/applications_link"/></xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Applications')" />
                </a>
            </li>
            <li><a href="">#<xsl:value-of select="application/id"/></a></li>
        </ul>

        <xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

        <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'Status')" /></dt>
            <dd><xsl:value-of select="php:function('lang', string(application/status))"/></dd>
        </dl>
        <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'Created')" /></dt>
            <dd><xsl:value-of select="application/created"/></dd>
        </dl>
        <dl class="proplist-col">
            <dt><xsl:value-of select="php:function('lang', 'Modified')" /></dt>
            <dd><xsl:value-of select="application/modified"/></dd>
        </dl>

        <dl class="proplist">
            <dt class="heading"><xsl:value-of select="php:function('lang', 'Why?')" /></dt>
            <dt><xsl:value-of select="php:function('lang', 'Activity')" /></dt>
            <dd><xsl:value-of select="application/activity_name"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
			<dd><pre><xsl:value-of select="application/description"/></pre></dd>
		</dl>
        <dl class="proplist-col">
            <dt class="heading"><xsl:value-of select="php:function('lang', 'Where?')" /></dt>
            <dt><xsl:value-of select="php:function('lang', 'Building')" /></dt>
            <dd><xsl:value-of select="application/building_name"/></dd>
            <dd><div id="resources_container"/></dd>
        </dl>
        <dl class="proplist-col">
            <dt class="heading"><xsl:value-of select="php:function('lang', 'When?')" /></dt>
			<xsl:for-each select="application/dates">
				<dd><xsl:value-of select="php:function('lang', 'From')" />: <xsl:value-of select="from_"/></dd>
				<dd><xsl:value-of select="php:function('lang', 'To')" />: <xsl:value-of select="to_"/>: <xsl:value-of select="id"/></dd>
				<form method="POST" >
					<input type="hidden" name="date_id" value="{id}"/>
					<select name="create" onchange="this.form.submit()">
						<option value=""><xsl:value-of select="php:function('lang', '- Actions -')" /></option>
						<option value="booking"><xsl:value-of select="php:function('lang', 'Create booking')" /></option>
						<option value="allocation"><xsl:value-of select="php:function('lang', 'Create allocation')" /></option>
						<option value="event"><xsl:value-of select="php:function('lang', 'Create event')" /></option>
					</select>
				</form>
			</xsl:for-each>
        </dl>
        <dl class="proplist-col">
            <dt class="heading"><xsl:value-of select="php:function('lang', 'Who?')" /></dt>
            <dt><xsl:value-of select="php:function('lang', 'Target audience')" /></dt>
			<dd>
				<ul>1
					<xsl:for-each select="audience">
						<xsl:if test="../application/audience=id">
							<li><xsl:value-of select="name"/></li>
						</xsl:if>
					</xsl:for-each>
				</ul>
			</dd>
            <dt><xsl:value-of select="php:function('lang', 'Number of participants')" /></dt>
			<dd>
				<table id="agegroup">
					<tr><th/><th><xsl:value-of select="php:function('lang', 'Male')" /></th>
					    <th><xsl:value-of select="php:function('lang', 'Female')" /></th></tr>
					<xsl:for-each select="agegroups">
						<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
						<tr>
							<th><xsl:value-of select="name"/></th>
							<td><xsl:value-of select="../application/agegroups/male[../agegroup_id = $id]"/></td>
							<td><xsl:value-of select="../application/agegroups/female[../agegroup_id = $id]"/></td>
						</tr>
					</xsl:for-each>
				</table>
			</dd>
        </dl>
        <div class="clr"/>
		<xsl:if test="application/edit_link">
	        <button>
	            <xsl:attribute name="onclick">window.location.href='<xsl:value-of select="application/edit_link"/>'</xsl:attribute>
	            <xsl:value-of select="php:function('lang', 'Edit')" />
	        </button>
		</xsl:if>
		<dl class="proplist">
            <dt class="heading"><xsl:value-of select="php:function('lang', 'History and comments (%1)', count(application/comments/author))" /></dt>
			<xsl:for-each select="application/comments[author]">
				<dt>
					<xsl:value-of select="time"/>: <xsl:value-of select="author"/>
				</dt>
				<dd><pre><xsl:value-of select="comment"/></pre></dd>
			</xsl:for-each>
		</dl>

        <dl class="proplist">
            <dt class="heading"><xsl:value-of select="php:function('lang', 'Add a comment')" /></dt>
			<dd>
				<form method="POST">
					<textarea name="comment" style="width: 60%; height: 7em"></textarea><br/>
				    <input type="submit" value="{php:function('lang', 'Add comment')}" />
				</form>
			</dd>
        </dl>

		<xsl:if test="application/edit_link">
		<dl class="proplist">
            <dt class="heading"><xsl:value-of select="php:function('lang', 'Actions')" /></dt>
			<xsl:if test="application/status!='REJECTED'">
			<dt>
				<form method="POST">
					<input type="hidden" name="status" value="REJECTED"/>
					<input type="submit" value="{php:function('lang', 'Reject application')}"/>
				</form>
			</dt>
			</xsl:if>
			<xsl:if test="application/status='NEW'">
			<dt>
				<form method="POST">
					<input type="hidden" name="status" value="ACCEPTED"/>
					<input type="submit" value="{php:function('lang', 'Accept application')}"/>
				</form>
			</dt>
			</xsl:if>
			<xsl:if test="application/status='ACCEPTED'">
			<dt>
				<form method="POST">
					<input type="hidden" name="status" value="CONFIRMED"/>
					<input type="submit" value="{php:function('lang', 'Confirm application')}"/>
				</form>
			</dt>
			</xsl:if>
			<dd><a href="{application/dashboard_link}"><xsl:value-of select="php:function('lang', 'Back to Dashboard')" /></a></dd>
		</dl>
		</xsl:if>
    </div>

<script type="text/javascript">
    var resourceIds = '<xsl:value-of select="application/resource_ids"/>';
	var lang = <xsl:value-of select="php:function('js_lang', 'Resources', 'Resource Type')"/>;
    <![CDATA[
YAHOO.util.Event.addListener(window, "load", function() {
    var url = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
]]>
    var colDefs = [{key: 'name', label: lang['Resources'], formatter: YAHOO.booking.formatLink}, {key: 'type', label: lang['Resource Type']}];
    YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
});
</script>

</xsl:template>
