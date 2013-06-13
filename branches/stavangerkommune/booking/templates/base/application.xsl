<func:function name="phpgw:conditional">
	<xsl:param name="test"/>
	<xsl:param name="true"/>
	<xsl:param name="false"/>

	<func:result>
		<xsl:choose>
			<xsl:when test="$test">
				<xsl:value-of select="$true"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$false"/>
			</xsl:otherwise>
		</xsl:choose>
	</func:result>
</func:function>

<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <div id="content">
        <ul class="pathway">
            <li>
                <a>
					<xsl:attribute name="href"><xsl:value-of select="application/applications_link"/></xsl:attribute>
					<xsl:value-of select="php:function('lang', 'Applications')" />
                </a>
            </li>
            <li>#<xsl:value-of select="application/id"/></li> 
			<li>
				<xsl:if test="frontend and application/status='ACCEPTED'">
						<form method="POST">
						<input type="hidden" name="print" value="ACCEPTED"/>
						<input type="submit" value="{php:function('lang', 'Print as PDF')}" />
					</form>
				</xsl:if>
			</li>
        </ul>

        <xsl:call-template name="msgbox"/>
		<xsl:call-template name="yui_booking_i18n"/>

			<xsl:if test="not(frontend)">
				<div style="border: 3px solid red; padding: 3px 4px 3px 4px">
					<xsl:choose>
						<xsl:when test="not(application/case_officer)">
							<xsl:value-of select="php:function('lang', 'In order to work with this application, you must first')"/>
							<xsl:text> </xsl:text><a href="#assign"><xsl:value-of select="php:function('lang', 'assign yourself')"/></a><xsl:text> </xsl:text>
							<xsl:value-of select="php:function('lang', 'as the case officer responsible for this application.')"/>
						</xsl:when>
						<xsl:when test="application/case_officer and not(application/case_officer/is_current_user)">
							<xsl:value-of select="php:function('lang', 'The user currently assigned as the responsible case officer for this application is')"/>'<xsl:text> </xsl:text><xsl:value-of select="application/case_officer/name"/>'.
							<br/>
							<xsl:value-of select="php:function('lang', 'In order to work with this application, you must therefore first')"/>
							<xsl:text> </xsl:text><a href="#assign"><xsl:value-of select="php:function('lang', 'assign yourself')"/></a><xsl:text> </xsl:text>
							<xsl:value-of select="php:function('lang', 'as the case officer responsible for this application.')"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="style">display:none</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
				</div>
			</xsl:if>
					
		<xsl:if test="not(frontend)">
	        <dl class="proplist-col">
	            <dt><xsl:value-of select="php:function('lang', 'Status')" /></dt>
	            <dd><xsl:value-of select="php:function('lang', string(application/status))"/></dd>
	        </dl>
	        <dl class="proplist-col">
	            <dt><xsl:value-of select="php:function('lang', 'Created')" /></dt>
	            <dd><xsl:value-of select="php:function('pretty_timestamp', application/created)"/></dd>
	        </dl>
	        <dl class="proplist-col">
	            <dt><xsl:value-of select="php:function('lang', 'Modified')" /></dt>
	            <dd><xsl:value-of select="php:function('pretty_timestamp', application/modified)"/></dd>
	        </dl>
		</xsl:if>
		<xsl:if test="frontend">
			<dl class="proplist">
			<span style="font-size: 110%; font-weight: bold;">Din søknad har status <xsl:value-of select="php:function('lang', string(application/status))"/></span><span class="text">, opprettet <xsl:value-of select="php:function('pretty_timestamp', application/created)"/>, sist endret <xsl:value-of select="php:function('pretty_timestamp', application/modified)"/></span>
			<span class="text"><br />Melding fra saksbehandler ligger under historikk, deretter vises kopi av din søknad.<br /> Skal du gi en melding til saksbehandler skriver du denne inn i feltet under "Legg til en kommentar"</span> 
			</dl>
		</xsl:if>

        <dl class="proplist">
            <dt class="heading"><xsl:value-of select="php:function('lang', 'Add a comment')" /></dt>
			<dd>
				<form method="POST">
					<textarea name="comment" style="width: 60%; height: 7em"></textarea><br/>
				    <input type="submit" value="{php:function('lang', 'Add comment')}" />
				</form>
			</dd>
        </dl>

		<dl class="proplist">
            <dt class="heading">1. <xsl:value-of select="php:function('lang', 'History and comments (%1)', count(application/comments/author))" /></dt>
			<xsl:for-each select="application/comments[author]">
				<dt>
					<xsl:value-of select="php:function('pretty_timestamp', time)"/>: <xsl:value-of select="author"/>
				</dt>
				<xsl:choose>
					<xsl:when test='contains(comment,"bookingfrontend.uidocument_building.download")'>				
						<dd><xsl:value-of select="comment" disable-output-escaping="yes"/></dd>
					</xsl:when>
					<xsl:otherwise>				
						<dd><div style="width: 80%;"><xsl:value-of select="comment"/></div></dd>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
		</dl>

        <dl class="proplist">
            <dt class="heading">2. <xsl:value-of select="php:function('lang', 'Why?')" /></dt>
            <dt><xsl:value-of select="php:function('lang', 'Activity')" /></dt>
            <dd><xsl:value-of select="application/activity_name"/></dd>
            <dt><xsl:value-of select="php:function('lang', 'Description')" /></dt>
			<dd><div style="width: 80%;"><xsl:value-of select="application/description"/></div></dd>
		</dl>
        <dl class="proplist-col">
            <dt class="heading">3. <xsl:value-of select="php:function('lang', 'Where?')" /></dt>
			<dt><xsl:value-of select="php:function('lang', 'Building')" /></dt>
            <dd><xsl:value-of select="application/building_name"/>
			(<a href="javascript: void(0)" 
				onclick="window.open('{application/schedule_link}', 
					     '', 
						   'width=1048, height=600, scrollbars=yes');
						      return false;"><xsl:value-of select="php:function('lang', 'Building schedule')" /></a>)</dd>
            <dd><div id="resources_container"/></dd>
        </dl>
        <dl class="proplist-col">
            <dt class="heading">4. <xsl:value-of select="php:function('lang', 'When?')" /></dt>
			<script type="text/javascript">
				var allocationParams = {};
				var bookingParams = {};
				var eventParams = {};
			</script>
			<xsl:for-each select="application/dates">
				<dd><xsl:value-of select="php:function('lang', 'From')" />: <xsl:value-of select="php:function('pretty_timestamp', from_)"/></dd>
				<dd><xsl:value-of select="php:function('lang', 'To')" />: <xsl:value-of select="php:function('pretty_timestamp', to_)"/></dd>
				<xsl:if test="../edit_link">
				<script type="text/javascript">
					allocationParams[<xsl:value-of select="id"/>] = <xsl:value-of select="allocation_params"/>;
					bookingParams[<xsl:value-of select="id"/>] = <xsl:value-of select="booking_params"/>;
					eventParams[<xsl:value-of select="id"/>] = <xsl:value-of select="event_params"/>;
				</script>
				<select name="create" onchange="if(this.selectedIndex==1) YAHOO.booking.postToUrl('index.php?menuaction=booking.uiallocation.add', allocationParams[{id}]); if(this.selectedIndex==2) YAHOO.booking.postToUrl('index.php?menuaction=booking.uibooking.add', eventParams[{id}]); if(this.selectedIndex==3) YAHOO.booking.postToUrl('index.php?menuaction=booking.uievent.add', eventParams[{id}]);">
					<xsl:if test="not(../case_officer/is_current_user)">
						<xsl:attribute name="disabled">disabled</xsl:attribute>		
					</xsl:if>
					
					<option><xsl:value-of select="php:function('lang', '- Actions -')" /></option>
					<option><xsl:value-of select="php:function('lang', 'Create allocation')" /></option>
					<option><xsl:value-of select="php:function('lang', 'Create booking')" /></option>
					<option><xsl:value-of select="php:function('lang', 'Create event')" /></option>
				</select>
				</xsl:if>
			</xsl:for-each>
        </dl>
        <dl class="proplist-col">
            <dt class="heading">5. <xsl:value-of select="php:function('lang', 'Who?')" /></dt>
            <dt><xsl:value-of select="php:function('lang', 'Target audience')" /></dt>
			<dd>
				<ul>
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
		<dl class="form-col">
			<div class="heading"><br />6. <xsl:value-of select="php:function('lang', 'Contact information')" /></div>
			<dt><label for="field_contact_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
			<dd>
					<xsl:value-of select="application/contact_name"/>
			</dd>
			<dt><label for="field_contact_email"><xsl:value-of select="php:function('lang', 'Email')" /></label></dt>
			<dd>
					<xsl:value-of select="application/contact_email"/>
			</dd>
			<dt><label for="field_contact_phone"><xsl:value-of select="php:function('lang', 'Phone')" /></label></dt>
			<dd>
					<xsl:value-of select="application/contact_phone"/>
			</dd>
		</dl>
		<dl class="form-col">
			<div class="heading">7. <xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" /></div>
			<xsl:if test="application/customer_identifier_type = 'organization_number'">
				<dt><label for="field_organization_number"><xsl:value-of select="php:function('lang', 'organization number')" /></label></dt>
				<dd><xsl:value-of select="application/customer_organization_number"/></dd>
			</xsl:if>
			<xsl:if test="application/customer_identifier_type = 'ssn'">
				<dt><label for="field_ssn_number"><xsl:value-of select="php:function('lang', 'Date of birth or SSN')" /></label></dt>
				<dd><xsl:value-of select="application/customer_ssn"/></dd>
			</xsl:if>
		</dl>
		<dl class="form-col">
			<div class="heading"><br />8. <xsl:value-of select="php:function('lang', 'Terms and conditions')" /></div>
			<p><xsl:value-of select="php:function('lang', 'All that borrow premises from Stavanger Kommune must verify that they have read the terms and conditions, this is usually fire regulations and house rules.')" /></p>
			<br />
			<div id='regulation_documents'>&nbsp;</div>
			<br />
			<p><xsl:value-of select="php:function('lang', 'To borrow premises you must verify that you have read terms and conditions')" /></p>
		</dl>

        <div class="clr"/>
		<xsl:if test="application/edit_link">
	        <button>
					<xsl:if test="application/case_officer/is_current_user">
	            	<xsl:attribute name="onclick">window.location.href='<xsl:value-of 				select="application/edit_link"/>'</xsl:attribute>
					</xsl:if>
					<xsl:if test="not(application/case_officer/is_current_user)">
						<xsl:attribute name="disabled">disabled</xsl:attribute>	
					</xsl:if>
	            <xsl:value-of select="php:function('lang', 'Edit')" />
	        </button>
		</xsl:if>
		<xsl:if test="not(frontend)">
			<dl class="proplist">
	            <dt class="heading"><xsl:value-of select="php:function('lang', 'Associated items')" /></dt>
				<dd><div id="associated_container"/></dd>
			</dl>
		</xsl:if>

		<xsl:if test="application/edit_link">
			<dl class="proplist">
				<dt class="heading"><xsl:value-of select="php:function('lang', 'Actions')" /></dt>
				
					<dt>
						<xsl:if test="application/case_officer/is_current_user">
							<form method="POST" style="display:inline">
								<input type="hidden" name="unassign_user"/>
								<input type="submit" value="{php:function('lang', 'Unassign me')}"/>
							</form>
							<form method="POST" style="display:inline">
								<input type="hidden" name="display_in_dashboard" value="{phpgw:conditional(application/display_in_dashboard='1', '0', '1')}"/>
								<input type="submit" value="{php:function('lang', phpgw:conditional(application/display_in_dashboard='1', 'Hide from my Dashboard until new activity occurs', 'Display in my Dashboard'))}"/>
							</form>
						</xsl:if>
							
							<xsl:if test="not(application/case_officer/is_current_user)">
								<a name="assign"/>
								<form method="POST">
									<input type="hidden" name="assign_to_user"/>
									<input type="hidden" name="status" value="PENDING"/>
									<input type="submit" value="{php:function('lang', phpgw:conditional(application/case_officer, 'Re-assign to me', 'Assign to me'))}"/>
							
									<xsl:if test="application/case_officer">
										<xsl:value-of select="php:function('lang', 'Currently assigned to user:')"/>
										<xsl:text> </xsl:text>					
										<xsl:value-of select="application/case_officer/name"/>
									</xsl:if>
								</form>
							</xsl:if>						
					</dt>
				
				<xsl:if test="application/status!='REJECTED'">
					<dt>
						<form method="POST">
							<input type="hidden" name="status" value="REJECTED"/>
							<input onclick="return confirm('{php:function('lang', 'Are you sure you want to delete?')}')" type="submit" value="{php:function('lang', 'Reject application')}">
								<xsl:if test="not(application/case_officer)">
									<xsl:attribute name="disabled">disabled</xsl:attribute>
								</xsl:if>
							</input>
						</form>
					</dt>
				</xsl:if>
				<xsl:if test="application/status='PENDING'">
					<xsl:if test="num_associations='0'">
						<input type="submit" disabled="" value="{php:function('lang', 'Accept application')}"/>
						<xsl:value-of select="php:function('lang', 'One or more bookings, allocations or events needs to be created before an application can be Accepted')"/>
					</xsl:if>
					<xsl:if test="num_associations!='0'">
						<dt>
							<form method="POST">
								<input type="hidden" name="status" value="ACCEPTED"/>
								<input type="submit" value="{php:function('lang', 'Accept application')}">
									<xsl:if test="not(application/case_officer)">
										<xsl:attribute name="disabled">disabled</xsl:attribute>
									</xsl:if>
								</input>
							</form>
						</dt>
					</xsl:if>
				</xsl:if>
				<dd><br/><a href="{application/dashboard_link}"><xsl:value-of select="php:function('lang', 'Back to Dashboard')" /></a></dd>
			</dl>
		</xsl:if>
    </div>

<script type="text/javascript">
    var resourceIds = '<xsl:value-of select="application/resource_ids"/>';
	 if (!resourceIds || resourceIds == "") {
		resourceIds = false;
	 }
	var lang = <xsl:value-of select="php:function('js_lang', 'Resources', 'Resource Type', 'ID', 'Type', 'From', 'To', 'Document')"/>;
	var app_id = <xsl:value-of select="application/id"/>;
	var building_id = <xsl:value-of select="application/building_id"/>;	
	var resources = <xsl:value-of select="application/resources"/>;
	
YAHOO.util.Event.addListener(window, "load", function() {
	<![CDATA[
	var url3 = 'index.php?menuaction=booking.uidocument_view.regulations&sort=name&phpgw_return_as=json&owner[]=building::' + building_id;		
		url3 += 'index.php?menuaction=booking.uidocument_view.regulations&sort=name&phpgw_return_as=json&owner[]=resource::'+ resources; 

	]]>
	var colDefs = [{key: 'name', label: lang['Document'], formatter: YAHOO.booking.formatLink}];
    YAHOO.booking.inlineTableHelper('regulation_documents', url3, colDefs);
	if (resourceIds) {
	    <![CDATA[
	    var url = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
	    var url2 = 'index.php?menuaction=booking.uiapplication.associated&sort=from_&dir=asc&phpgw_return_as=json&filter_application_id=' + app_id;
		]]>
	    var colDefs = [{key: 'name', label: lang['Resources'], formatter: YAHOO.booking.formatLink}, {key: 'type', label: lang['Resource Type']}];
	    YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
	    var colDefs = [
		{key: 'id', label: lang['ID'], formatter: YAHOO.booking.formatLink},
		{key: 'type', label: lang['Type']},
		{key: 'from_', label: lang['From']},
		{key: 'to_', label: lang['To']}];
	    YAHOO.booking.inlineTableHelper('associated_container', url2, colDefs);
    }

});
</script>
</xsl:template>
