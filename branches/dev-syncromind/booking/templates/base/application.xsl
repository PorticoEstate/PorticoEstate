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
    <!--div id="content"-->
        <!--ul class="pathway">
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
        </ul-->
        
        <style type="text/css">
            .pure-form-contentTable {display: inline-block;}
        </style>

        <xsl:call-template name="msgbox"/>
		<!--xsl:call-template name="yui_booking_i18n"/-->
            <form class= "pure-form pure-form-aligned" action="" method="post" id="form" name="form">
                <input type="hidden" name="tab" value=""/>
                    <div id="tab-content">
                        <xsl:value-of disable-output-escaping="yes" select="application/tabs"/>
                        <div id="application">
                        <fieldset>
                            <div class="pure-g">
                                <div class="pure-u-1">
                                    <h1>
                                        <xsl:value-of select="application/id"/>
                                    </h1>

                                    <div class="pure-control-group">
                                        <xsl:if test="frontend and application/status='ACCEPTED'">
                                            <form method="POST">
                                                <input type="hidden" name="print" value="ACCEPTED"/>
                                                <input type="submit" value="{php:function('lang', 'Print as PDF')}" />
                                            </form>
                                        </xsl:if>
                                    </div>

                                    <div class="pure-control-group">
                                        <xsl:if test="not(frontend)">
                                            <div style="border: 3px solid red; padding: 3px 4px 3px 4px">
                                                <xsl:choose>
                                                    <xsl:when test="not(application/case_officer)">
                                                        <xsl:value-of select="php:function('lang', 'In order to work with this application, you must first')"/>
                                                        <xsl:text> </xsl:text><a href="#assign"><xsl:value-of select="php:function('lang', 'assign yourself')"/></a><xsl:text> </xsl:text>
                                                        <xsl:value-of select="php:function('lang', 'as the case officer responsible for this application.')"/>
                                                    </xsl:when>
                                                    <xsl:when test="application/case_officer and not(application/case_officer/is_current_user)">
                                                        <xsl:value-of select="php:function('lang', 'The user currently assigned as the responsible case officer for this application is')"/><xsl:text> </xsl:text>'<xsl:value-of select="application/case_officer/name"/>'.
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
                                    </div>
                                    <xsl:if test="not(frontend)">
                                        <div class="pure-control-group">
                                            <label>
                                                <h4><xsl:value-of select="php:function('lang', 'Status')" /></h4>
                                            </label>
                                            <span><xsl:value-of select="php:function('lang', string(application/status))"/></span>
                                        </div>
                                        <div class="pure-control-group">
                                            <label>
                                                <h4><xsl:value-of select="php:function('lang', 'Created')" /></h4>
                                            </label>
                                            <span><xsl:value-of select="php:function('pretty_timestamp', application/created)"/></span>
                                        </div>
                                        <div class="pure-control-group">
                                            <label>
                                                <h4><xsl:value-of select="php:function('lang', 'Modified')" /></h4>
                                            </label>
                                            <span><xsl:value-of select="php:function('pretty_timestamp', application/modified)"/></span>
                                        </div>
                                    </xsl:if>
                                    <xsl:if test="frontend">
                                            <dl class="proplist">
                                            <span style="font-size: 110%; font-weight: bold;">Din søknad har status <xsl:value-of select="php:function('lang', string(application/status))"/></span><span class="text">, opprettet <xsl:value-of select="php:function('pretty_timestamp', application/created)"/>, sist endret <xsl:value-of select="php:function('pretty_timestamp', application/modified)"/></span>
                                            <span class="text"><br />Melding fra saksbehandler ligger under historikk, deretter vises kopi av din søknad.<br /> Skal du gi en melding til saksbehandler skriver du denne inn i feltet under "Legg til en kommentar"</span> 
                                            </dl>
                                    </xsl:if>

                                    <form method="POST">
                                        <div class="pure-control-group">
                                            <label for="comment">
                                                <h4><xsl:value-of select="php:function('lang', 'Add a comment')" /></h4>
                                            </label>
                                            <textarea name="comment" id="comment" style="width: 60%; height: 7em"></textarea><br/>                                                   
                                        </div>
                                        <div class="pure-control-group">
                                            <label>&nbsp;</label>
                                            <input type="submit" value="{php:function('lang', 'Add comment')}" />
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="pure-g">
                                <div class="pure-u-1">
                                    <div class="heading">
                                        <legend><h3>1. <xsl:value-of select="php:function('lang', 'History and comments (%1)', count(application/comments/author))" /></h3></legend>
                                    </div>                                        
                                    <xsl:for-each select="application/comments[author]">
                                        <div class="pure-control-group">
                                            <label>
                                                <h4>
                                                    <xsl:value-of select="php:function('pretty_timestamp', time)"/>: <xsl:value-of select="author"/>
                                                </h4>
                                            </label>
                                            <xsl:choose>
                                                <xsl:when test='contains(comment,"bookingfrontend.uidocument_building.download")'>				
                                                    <span><xsl:value-of select="comment" disable-output-escaping="yes"/></span>
                                                </xsl:when>
                                                <xsl:otherwise>				
                                                    <span><xsl:value-of select="comment"/></span>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </div>
                                    </xsl:for-each>
                                </div>
                            </div>

                            <div class="pure-g">
                                <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                                    <div class="heading">
                                        <legend><h3>2. <xsl:value-of select="php:function('lang', 'Why?')" /></h3></legend>
                                    </div>
                                    <div class="pure-control-group">
                                        <label>
                                            <h4><xsl:value-of select="php:function('lang', 'Activity')" /></h4>
                                        </label>
                                        <span><xsl:value-of select="application/activity_name"/></span>
                                    </div>
                                    <div class="pure-control-group">
                                        <label>
                                            <h4><xsl:value-of select="php:function('lang', 'Description')" /></h4>
                                        </label>
                                        <span><xsl:value-of select="application/description"/></span>
                                    </div>
                                    <!--<div class="pure-control-group">
                                        <label>
                                            <h4><xsl:value-of select="config/application_equipment"/></h4>
                                        </label>
                                        <xsl:value-of select="application/equipment"/>
                                    </div>-->
                                </div>

                                <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                                    <div class="heading">
                                        <legend><h3>3. <xsl:value-of select="php:function('lang', 'Where?')" /></h3></legend>
                                    </div>
                                    <div class="pure-control-group">
                                        <label>
                                            <h4><xsl:value-of select="php:function('lang', 'Building')" /></h4>
                                        </label>
                                        <span>
                                            <xsl:value-of select="application/building_name"/>
                                            (<a href="javascript: void(0)" onclick="window.open('{application/schedule_link}', '', 'width=1048, height=600, scrollbars=yes');return false;">
                                                <xsl:value-of select="php:function('lang', 'Building schedule')" />
                                            </a>)
                                        </span>
                                    </div>
                                    <!--Revizar esta linea luego-->
                                    <div class="pure-control-group">
                                        <label>&nbsp;</label>
                                        <div id="resources_container" class="pure-form-contentTable"></div>
                                    </div>
                                </div>

                                <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                                    <div class="heading">
                                        <legend><h3>4. <xsl:value-of select="php:function('lang', 'When?')" /></h3></legend>
                                    </div>
                                    <script type="text/javascript">
                                        var allocationParams = {};
                                        var bookingParams = {};
                                        var eventParams = {};
                                        var applicationDate = {};
                                    </script>
                                    <xsl:variable name='assocdata'>
                                             <xsl:value-of select="assoc/data" />
                                    </xsl:variable>
                                    <xsl:variable name='collisiondata'>
                                        <xsl:value-of select="collision/data" />
                                    </xsl:variable>

                                    <script type="text/javascript">
                                        building_id = <xsl:value-of select="application/building_id"/>;
                                    </script>

                                    <xsl:for-each select="application/dates">
                                        <div class="pure-control-group">    
                                            <label>
                                                <h4><xsl:value-of select="php:function('lang', 'From')" />:</h4>
                                            </label> 
                                            <span><xsl:value-of select="php:function('pretty_timestamp', from_)"/></span>
                                            <xsl:if test="../case_officer/is_current_user">
                                                <xsl:if test="contains($collisiondata, from_)">
                                                    <xsl:if test="not(contains($assocdata, from_))">
                                                        <script type="text/javascript">
                                                            applicationDate[<xsl:value-of select="id"/>] = '<xsl:value-of select="substring(from_,0,11)"/>';
                                                        </script>
                                                        <a href="javascript: void(0)"
                                                            onclick="window.open('/bookingfrontend/index.php?menuaction=bookingfrontend.uibuilding.schedule&amp;id='+building_id+'&amp;backend=true&amp;date='+applicationDate[{id}], '', 'width=1048, height=600, scrollbars=yes');return false;">
                                                            <i class="fa fa-exclamation-circle"></i>
                                                        </a>
                                                    </xsl:if>
                                                </xsl:if>
                                            </xsl:if>
                                        </div>
                                        <div class="pure-control-group">
                                            <label>
                                                <h4><xsl:value-of select="php:function('lang', 'To')" />:</h4>
                                            </label> 
                                            <span><xsl:value-of select="php:function('pretty_timestamp', to_)"/></span>
                                        </div>
                                        <xsl:if test="../edit_link">
                                            <script type="text/javascript">
                                                    allocationParams[<xsl:value-of select="id"/>] = <xsl:value-of select="allocation_params"/>;
                                                    bookingParams[<xsl:value-of select="id"/>] = <xsl:value-of select="booking_params"/>;
                                                    eventParams[<xsl:value-of select="id"/>] = <xsl:value-of select="event_params"/>;
                                            </script>
                                            <div class="pure-control-group">
                                                <label>&nbsp;</label>
                                                    <select name="create" onchange="if(this.selectedIndex==1) YAHOO.booking.postToUrl('index.php?menuaction=booking.uiallocation.add', allocationParams[{id}]); if(this.selectedIndex==2) YAHOO.booking.postToUrl('index.php?menuaction=booking.uibooking.add', eventParams[{id}]); if(this.selectedIndex==3) YAHOO.booking.postToUrl('index.php?menuaction=booking.uievent.add', eventParams[{id}]);">
                                                        <xsl:if test="not(../case_officer/is_current_user)">
                                                            <xsl:attribute name="disabled">disabled</xsl:attribute>
                                                        </xsl:if>
                                                        <xsl:if test="not(contains($assocdata, from_))">
                                                            <option><xsl:value-of select="php:function('lang', '- Actions -')" /></option>
                                                            <option><xsl:value-of select="php:function('lang', 'Create allocation')" /></option>
                                                            <option><xsl:value-of select="php:function('lang', 'Create booking')" /></option>
                                                            <option><xsl:value-of select="php:function('lang', 'Create event')" /></option>
                                                        </xsl:if>
                                                        <xsl:if test="contains($assocdata, from_)">
                                                            <xsl:attribute name="disabled">disabled</xsl:attribute>
                                                            <option><xsl:value-of select="php:function('lang', '- Created -')" /></option>
                                                        </xsl:if>
                                                    </select>
                                            </div>
                                        </xsl:if>
                                    </xsl:for-each>
                                </div>

                                <div class="pure-u-1 pure-u-md-1-2 pure-u-lg-1-3">
                                    <div class="heading">
                                        <legend><h3>5. <xsl:value-of select="php:function('lang', 'Who?')" /></h3></legend>
                                    </div>
                                    <div class="pure-control-group">
                                        <label>
                                            <h4><xsl:value-of select="php:function('lang', 'Target audience')" /></h4>
                                        </label>    
                                        <label>
                                            <ul>
                                                <xsl:for-each select="audience">
                                                    <xsl:if test="../application/audience=id">
                                                        <li><xsl:value-of select="name"/></li>
                                                    </xsl:if>
                                                </xsl:for-each>
                                            </ul>
                                        </label>
                                    </div>
                                    <div class="pure-control-group">
                                        <label style="vertical-align: top;width: auto;">
                                            <h4><xsl:value-of select="php:function('lang', 'Number of participants')" /></h4>
                                        </label>
                                            <div class="pure-form-contentTable">
                                                <table id="agegroup" class="pure-table pure-table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th><xsl:value-of select="php:function('lang', 'Name')" /></th>
                                                            <th><xsl:value-of select="php:function('lang', 'Male')" /></th>
                                                            <th><xsl:value-of select="php:function('lang', 'Female')" /></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <xsl:for-each select="agegroups">
                                                            <xsl:variable name="id"><xsl:value-of select="id"/></xsl:variable>
                                                            <tr>
                                                                <td><xsl:value-of select="name"/></td>
                                                                <td><xsl:value-of select="../application/agegroups/male[../agegroup_id = $id]"/></td>
                                                                <td><xsl:value-of select="../application/agegroups/female[../agegroup_id = $id]"/></td>
                                                            </tr>
                                                        </xsl:for-each>
                                                    </tbody>
                                                </table>
                                            </div>
                                    </div>
                                </div>

                                <div class="pure-u-1  pure-u-md-1-2 pure-u-lg-1-3">
                                    <div class="heading">
                                        <legend><h3>6. <xsl:value-of select="php:function('lang', 'Contact information')" /></h3></legend>
                                    </div>
                                    <div class="pure-control-group">
                                        <label>
                                            <h4><xsl:value-of select="php:function('lang', 'Name')" /></h4>
                                        </label>
                                        <span><xsl:value-of select="application/contact_name"/></span>
                                    </div>
                                    <div class="pure-control-group">
                                        <label>
                                            <h4><xsl:value-of select="php:function('lang', 'Email')" /></h4>
                                        </label>
                                        <span><xsl:value-of select="application/contact_email"/></span>
                                    </div>
                                    <div class="pure-control-group">
                                        <label>
                                            <h4><xsl:value-of select="php:function('lang', 'Phone')" /></h4>
                                        </label>
                                        <span><xsl:value-of select="application/contact_phone"/></span>
                                    </div>
                                </div>

                                <div class="pure-u-1  pure-u-md-1-2 pure-u-lg-1-3">
                                    <div class="heading">
                                        <legend><h3>7. <xsl:value-of select="php:function('lang', 'responsible applicant')" /> / <xsl:value-of select="php:function('lang', 'invoice information')" /></h3></legend>
                                    </div>
                                    <div class="pure-control-group">
                                        <xsl:if test="application/customer_identifier_type = 'organization_number'">
                                            <label>
                                                <h4><xsl:value-of select="php:function('lang', 'organization number')" /></h4>
                                            </label>
                                            <span><xsl:value-of select="application/customer_organization_number"/></span>
                                        </xsl:if>
                                    </div>
                                    <div class="pure-control-group">
                                        <xsl:if test="application/customer_identifier_type = 'ssn'">
                                            <label>
                                                <h4><xsl:value-of select="php:function('lang', 'Date of birth or SSN')" /></h4>
                                            </label>
                                            <xsl:value-of select="application/customer_ssn"/>
                                        </xsl:if>
                                    </div>
                                </div>
                            </div>


                            <div class="pure-g">
                                <div class="pure-u-1">
                                    <div class="heading">
                                        <legend><h3>8. <xsl:value-of select="php:function('lang', 'Terms and conditions')" /></h3></legend>
                                    </div>
                                    <div class="pure-control-group">                                            
                                        <p><xsl:value-of select="php:function('lang', 'All that borrow premises from Stavanger Kommune must verify that they have read the terms and conditions, this is usually fire regulations and house rules.')" /></p>
                                        <br />
                                        <div id='regulation_documents'>&nbsp;</div>
                                        <br />
                                        <p><xsl:value-of select="php:function('lang', 'To borrow premises you must verify that you have read terms and conditions')" /></p>
                                    </div>
                                </div>
                            </div>

                            <xsl:if test="not(frontend)">
                                <div class="pure-g">
                                    <div class="pure-u-1">
                                        <div class="heading">
                                            <legend><h3><xsl:value-of select="php:function('lang', 'Associated items')" /></h3></legend>
                                        </div>
                                        <div class="pure-control-group"><div id="associated_container"/></div>                                            
                                    </div>
                                </div>
                            </xsl:if>

                            <xsl:if test="application/edit_link">
                                <div class="pure-g">
                                    <div class="pure-u-1">
                                        <div class="heading">
                                            <legend><h3><xsl:value-of select="php:function('lang', 'Actions')" /></h3></legend>
                                        </div>
                                        <div class="pure-control-group">
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
                                        </div>

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
                                        <!--dd><br/><a href="{application/dashboard_link}"><xsl:value-of select="php:function('lang', 'Back to Dashboard')" /></a></dd-->
                                    </div>
                                </div>
                            </xsl:if>
                        </fieldset>
                        </div>
                    </div>
                    <dl class="proplist-col">
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

                        <a href="{application/dashboard_link}"><xsl:value-of select="php:function('lang', 'Back to Dashboard')" /></a>
                    </dl>
            </form>
    <!--/div-->

<script type="text/javascript">
    var resourceIds = '<xsl:value-of select="application/resource_ids"/>';
    var currentuser = '<xsl:value-of select="application/currentuser"/>';
	 if (!resourceIds || resourceIds == "") {
		resourceIds = false;
	 }
	var lang = <xsl:value-of select="php:function('js_lang', 'Resources', 'Resource Type', 'No records found', 'ID', 'Type', 'From', 'To', 'Document', 'Active' ,'Delete', 'del')"/>;
	var app_id = <xsl:value-of select="application/id"/>;
	var building_id = <xsl:value-of select="application/building_id"/>;	
	var resources = <xsl:value-of select="application/resources"/>;

        <![CDATA[
        var resourcesURL = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&' + resourceIds;
        var associatedURL = 'index.php?menuaction=booking.uiapplication.associated&sort=from_&dir=asc&phpgw_return_as=json&filter_application_id='+app_id;
        var documentsURL = 'index.php?menuaction=booking.uidocument_view.regulations&sort=name&phpgw_return_as=json&owner[]=building::' + building_id;		
		documentsURL += 'index.php?menuaction=booking.uidocument_view.regulations&sort=name&phpgw_return_as=json&owner[]=resource::'+ resources; 
        ]]>
        
        
        $.get(resourcesURL, function(resourcesData){
            var resourcesBody = '';
            var resourcesTableClass = "pure-table";
            if (resourcesData.data.length === 0){
                resourcesBody = '<tr><td colspan="2">'+lang['No records found']+'</td></tr>';
            }else{
                resourcesTableClass = "pure-table pure-table-striped";
                $.each(resourcesData.data , function(index,value){
                    <![CDATA[
                    resourcesBody += '<tr><td><a href='+value.link+'>'+value.name+'</a></td><td>'+value.type+'</td></tr>';
                    ]]>
                });
            }
            <![CDATA[
                var resourcesTable = '<table class="'+resourcesTableClass+'"><thead><tr><th>'+lang['Resources']+'</th><th>'+lang['Resource Type']+'</th></tr></thead><tbody>'+resourcesBody+'</tbody></table>';
            ]]>
            $('#resources_container').html(resourcesTable);
        });      
        
        $.get(associatedURL, function(associatedData){
            var associatedBody = '';
            var associatedTableClass = "pure-table";
            <![CDATA[
                var associatedHead = '<th>'+lang['ID']+'</th><th>'+lang['Type']+'</th><th>'+lang['From']+'</th><th>'+lang['To']+'</th><th>'+lang['Active']+'</th>';
            ]]>
            if (currentuser == 1) {
                associatedColspan = 6;
                associatedHead += '<th>'+lang['Delete']+'</th>'
            } else {
                associatedColspan = 5;
            }
            if (associatedData.results.length === 0){
                associatedBody += '<tr><td colspan="'+associatedColspan+'">'+lang['No records found']+'</td></tr>';
            }else{
                associatedTableClass = "pure-table pure-table-striped";
                $.each(associatedData.results, function(index, value){                    
                    <![CDATA[
                    associatedBody += '<tr>';
                    associatedBody += '<td><a href="'+value.link+'">'+value.id+'</a></td><td>'+value.type+'</td><td>'+value.from_+'</td><td>'+value.to_+'</td><td>'+value.active+'</td>';
                    ]]>
                    if (currentuser == 1){
                        <![CDATA[
                        associatedBody += '<td><a onclick="return confirm(\'Er du sikker på at du vil slette denne?\';" href="'+value.dellink+'">slett</a></td>';
                        ]]>
                    }
                    <![CDATA[
                    associatedBody += '</tr>';
                    ]]>
                });
            }
            <![CDATA[
                var associatedTable = '<table class="'+associatedTableClass+'"><thead><tr>'+associatedHead+'</tr></thead><tbody>'+associatedBody+'</tbody></table>';
            ]]>
            $('#associated_container').html(associatedTable);
        });
        
        $.get(documentsURL, function(documentsData) {
            var documentsBody = '';
            var documentsTableClass = "pure-table";
            <![CDATA[
                var documentsHead = '<tr><th>'+lang['Document']+'</th><tr>';
            ]]>
            if (documentsData.data.length === 0){
                documentsBody += '<tr><td>'+lang['No records found']+'</td></tr>';
            }else{
                documentsTableClass = "pure-table pure-table-striped";
                $.each(documentsData.data, function(index, value) {
                    <![CDATA[
                    documentsBody += '<tr><td><a href='+value.link+'>'+value.name+'</a></td><tr>';
                    ]]>
                });
            };
            <![CDATA[
                var documentsTable = '<table class="'+documentsTableClass+'"><thead>'+documentsHead+'</thead><tbody>'+documentsBody+'</tbody></table>';
            ]]>
            $('#regulation_documents').html(documentsTable);
        });
        
        

        
        
        
            

        

/*	
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
	    var url2 = 'index.php?menuaction=booking.uiapplication.associated&sort=from_&dir=asc&phpgw_return_as=json&filter_application_id='+app_id;
		]]>
	    var colDefs = [{key: 'name', label: lang['Resources'], formatter: YAHOO.booking.formatLink}, {key: 'type', label: lang['Resource Type']}];
	    YAHOO.booking.inlineTableHelper('resources_container', url, colDefs);
		if (currentuser == 1) {
		    var colDefs = [
				{key: 'id', label: lang['ID'], formatter: YAHOO.booking.formatLink},
				{key: 'type', label: lang['Type']},
				{key: 'from_', label: lang['From']},
				{key: 'to_', label: lang['To']},
				{key: 'active', label: lang['Active']},
				{key: 'dellink', label: lang['Delete'], formatter: YAHOO.booking.formatLink2}];
		} else {
		    var colDefs = [
				{key: 'id', label: lang['ID'], formatter: YAHOO.booking.formatLink},
				{key: 'type', label: lang['Type']},
				{key: 'from_', label: lang['From']},
				{key: 'to_', label: lang['To']},
				{key: 'active', label: lang['Active']}];
		}
	    YAHOO.booking.inlineTableHelper('associated_container', url2, colDefs);
    }

});
*/
</script>
</xsl:template>
