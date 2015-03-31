<xsl:template match="data" xmlns:php="http://php.net/xsl">
	<div id="content">

	<h3><xsl:value-of select="php:function('lang', 'Application')"/> (<xsl:value-of select="application/id"/>)</h3>
	<xsl:call-template name="msgbox"/>
	<xsl:call-template name="yui_booking_i18n"/>

	<form action="" method="POST" id='application_form'>
		<dl class="form">
            <dt><label for="field_active"><xsl:value-of select="php:function('lang', 'Active')"/></label></dt>
            <dd>
                <select id="field_active" name="active">
                    <option value="1">
                    	<xsl:if test="application/active=1">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="php:function('lang', 'Active')"/>
                    </option>
                    <option value="0">
                    	<xsl:if test="application/active=0">
                    		<xsl:attribute name="selected">checked</xsl:attribute>
                    	</xsl:if>
                        <xsl:value-of select="php:function('lang', 'Inactive')"/>
                    </option>
                </select>
            </dd>
		</dl>
		<dl class="form">
			<div class="heading">1. <xsl:value-of select="php:function('lang', 'Why?')" /></div>
			<dt><label for="field_activity"><xsl:value-of select="php:function('lang', 'Activity')" /></label></dt>
			<dd>
				<select name="activity_id" id="field_activity">
					<option value=""><xsl:value-of select="php:function('lang', '-- select an activity --')" /></option>
					<xsl:for-each select="activities">
						<option>
							<xsl:if test="../application/activity_id = id">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
							<xsl:value-of select="name"/>
						</option>
					</xsl:for-each>
				</select>
			</dd>
			<dt><label for="field_description"><xsl:value-of select="php:function('lang', 'Description')" /></label></dt>
			<dd>
				<textarea id="field_description" class="full-width" name="description"><xsl:value-of select="application/description"/></textarea>
			</dd>
            <dt><label for="field_equipment"><xsl:value-of select="php:function('lang', 'Equipment')" /></label></dt>
            <dd>
                <textarea id="field_equipment" class="full-width" name="equipment"><xsl:value-of select="application/equipment"/></textarea>
            </dd>
		</dl>
		<div class="clr"/>
		<dl class="form-col">
			<div class="heading">2. <xsl:value-of select="php:function('lang', 'Where?')" /></div>
			<dt><label for="field_building"><xsl:value-of select="php:function('lang', 'Building')" /></label></dt>
			<dd>
				<div class="autocomplete">
					<input id="field_building_id" name="building_id" type="hidden">
						<xsl:attribute name="value"><xsl:value-of select="application/building_id"/></xsl:attribute>
					</input>
					<input id="field_building_name" name="building_name" type="text">
						<xsl:attribute name="value"><xsl:value-of select="application/building_name"/></xsl:attribute>
					</input>
					<div id="building_container"/>
				</div>
			</dd>
			<dt><label for="field_resources"><xsl:value-of select="php:function('lang', 'Resources')" /></label></dt>
			<dd>
				<div id="resources_container"><xsl:value-of select="php:function('lang', 'Select a building first')" /></div>
			</dd>
		</dl>
		<dl class="form-col">
			<div class="heading">3. <xsl:value-of select="php:function('lang', 'When?')" /></div>
			<div id="dates-container">
				<xsl:for-each select="application/dates"><div class="date-container">
					<a href="#" class="close-btn">-</a>
					<dt><label for="field_{position()}_from"><xsl:value-of select="php:function('lang', 'From')" /></label></dt>
					<dd class="datetime-picker">
						<input id="field_{position()}_from" name="from_[]" type="text">
							<xsl:attribute name="value"><xsl:value-of select="from_"/></xsl:attribute>
						</input>
					</dd>
					<dt><label for="field_{position()}_to"><xsl:value-of select="php:function('lang', 'To')" /></label></dt>
					<dd class="datetime-picker">
						<input id="field_{position()}_to" name="to_[]" type="text">
							<xsl:attribute name="value"><xsl:value-of select="to_"/></xsl:attribute>
						</input>
					</dd>
				</div></xsl:for-each>
			</div>
			<dt><a href="#" id="add-date-link"><xsl:value-of select="php:function('lang', 'Add another date')" /></a></dt>
		</dl>
		<dl class="form-col">
			<div class="heading">4. <xsl:value-of select="php:function('lang', 'Who?')" /></div>
			<dt><label for="field_from"><xsl:value-of select="php:function('lang', 'Target audience')" /></label></dt>
			<dd>
				<ul>
					<xsl:for-each select="audience">
						<li>
							<input type="checkbox" name="audience[]">
								<xsl:attribute name="value"><xsl:value-of select="id"/></xsl:attribute>
								<xsl:if test="../application/audience=id">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</input>
							<label><xsl:value-of select="name"/></label>
						</li>
					</xsl:for-each>
				</ul>
			</dd>
			<dt><label for="field_from"><xsl:value-of select="php:function('lang', 'Number of participants')" /></label></dt>
			<dd>
				<table id="agegroup">
					<tr><th/><th><xsl:value-of select="php:function('lang', 'Male')" /></th>
					    <th><xsl:value-of select="php:function('lang', 'Female')" /></th></tr>
					<xsl:for-each select="agegroups">
						<xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
						<tr>
							<th><xsl:value-of select="name"/></th>
							<td>
								<input type="text">
									<xsl:attribute name="name">male[<xsl:value-of select="id"/>]</xsl:attribute>
									<xsl:attribute name="value"><xsl:value-of select="../application/agegroups/male[../agegroup_id = $id]"/></xsl:attribute>
								</input>
							</td>
							<td>
								<input type="text">
									<xsl:attribute name="name">female[<xsl:value-of select="id"/>]</xsl:attribute>
									<xsl:attribute name="value"><xsl:value-of select="../application/agegroups/female[../agegroup_id = $id]"/></xsl:attribute>
								</input>
							</td>
						</tr>
					</xsl:for-each>
				</table>
			</dd>
		</dl>
		<div class="clr"/>
		<dl class="form-col">
			<div class="heading"><br /><xsl:value-of select="php:function('lang', 'Contact information')" /></div>
			<dt><label for="field_contact_name"><xsl:value-of select="php:function('lang', 'Name')" /></label></dt>
			<dd>
				<input id="field_contact_name" name="contact_name" type="text">
					<xsl:attribute name="value"><xsl:value-of select="application/contact_name"/></xsl:attribute>
				</input>
			</dd>
			<dt><label for="field_contact_email"><xsl:value-of select="php:function('lang', 'Email')" /></label></dt>
			<dd>
				<input id="field_contact_email" name="contact_email" type="text">
					<xsl:attribute name="value"><xsl:value-of select="application/contact_email"/></xsl:attribute>
				</input>
			</dd>
			<dt><label for="field_contact_phone"><xsl:value-of select="php:function('lang', 'Phone')" /></label></dt>
			<dd>
				<input id="field_contact_phone" name="contact_phone" type="text">
					<xsl:attribute name="value"><xsl:value-of select="application/contact_phone"/></xsl:attribute>
				</input>
			</dd>
		</dl>
		<dl class="form-col">
			<div class="heading"><xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" /></div>
			<xsl:copy-of select="phpgw:booking_customer_identifier(application, '')"/>
		</dl>
		<dl class="form-col">
			<div class="heading"><br /><xsl:value-of select="php:function('lang', 'Terms and conditions')" /></div>
			<br/>
			<div id='regulation_documents'/>
		</dl>
		<div class="form-buttons">
			<input type="submit">
				<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Create')"/></xsl:attribute>
			</input>
			<a class="cancel">
				<xsl:attribute name="href"><xsl:value-of select="application/cancel_link"/></xsl:attribute>
				<xsl:value-of select="php:function('lang', 'Cancel')" />
			</a>
		</div>
	</form>
	</div>
	<script type="text/javascript">
		YAHOO.booking.initialDocumentSelection = <xsl:value-of select="application/accepted_documents_json"/>;
		YAHOO.booking.initialAcceptAllTerms = true;
		YAHOO.booking.initialSelection = <xsl:value-of select="application/resources_json"/>;
		var lang = <xsl:value-of select="php:function('js_lang', 'From', 'To', 'Resource Type', 'Name', 'Accepted', 'Document', 'You must accept to follow all terms and conditions of lease first.')"/>;
	</script>
</xsl:template>
