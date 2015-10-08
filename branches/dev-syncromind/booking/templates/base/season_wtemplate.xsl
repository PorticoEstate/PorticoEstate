<xsl:template match="data" xmlns:php="http://php.net/xsl">
    <style typ="text/css" rel="stylesheet">
        #week-selector {list-style: outside none none;}
        #week-selector li {display: inline-block;}
        #cal_container {margin: 0 20px;}
        #cal_container #datepicker {width: 2px;opacity: 0;position: absolute;display:none;}
        #cal_container #numberWeek {width: 20px;display: inline-block;}
    </style>
    <!--div id="content">
        <ul class="pathway">
                <li><a href="{season/buildings_link}"><xsl:value-of select="php:function('lang', 'Buildings')" /></a></li>
                <li><a href="{season/building_link}"><xsl:value-of select="season/building_name"/></a></li>
                <li><xsl:value-of select="php:function('lang', 'Season')" /></li>
                <li><a href="{season/season_link}"><xsl:value-of select="season/name"/></a></li>
        </ul-->

    <xsl:call-template name="msgbox"/>
    <!--xsl:call-template name="yui_booking_i18n"/-->
    
    <div class="pure-form">
        <input type="hidden" name="tab" value="" />
        <div id="tab-content">
            <xsl:value-of disable-output-escaping="yes" select="season/tabs" />
            <div id="season_wtemplate">
                <fieldset>
                    <div class="pure-g">
                        <div class="pure-u-1">
                            <div class="heading"></div>
                            <div clas="pure-control-group">
                                <!--ul id="week-selector">
                                    <li><span class="pure-button pure-button-primary" onclick="schedule.prevWeek(); return false"><xsl:value-of select="php:function('lang', 'Previous week')"/></span></li>
                                    <li id="cal_container">
                                        <div>
                                            <span><xsl:value-of select="php:function('lang', 'Week')" />: </span>
                                            <label id="numberWeek"></label>
                                            <input type="text" id="datepicker" />
                                            <img id="pickerImg" src="/portico/phpgwapi/templates/base/images/cal.png" />
                                        </div>
                                    </li>
                                    <li><span class="pure-button pure-button-primary" onclick="schedule.nextWeek(); return false"><xsl:value-of select="php:function('lang', 'Next week')"/></span></li>
                                </ul-->
                                <div id="schedule_container"></div>
                            </div>                            

                            <form action="" method="POST" id="form" class="pure-form pure-form-aligned" name="form"></form>
                            
                                
                            
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        <xsl:if test="season/permission/write">
            <div class="pure-control-group">	
                <div class="form-buttons">
					<!--
                    <button onclick="YAHOO.booking.dialog.newAllocation(); return false;"><xsl:value-of select="php:function('lang', 'New allocation')" /></button>
                    <button>
                        <xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/generate_url"/>"</xsl:attribute>
                        <xsl:value-of select="php:function('lang', 'Generate allocations')" />
                    </button>
					-->
					<input type="button" class="pure-button pure-button-primary" name="new" onclick="schedule.newAllocationForm('')">
						<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'New allocation')" /></xsl:attribute>	
					</input>
					<input type="button" class="pure-button pure-button-primary" name="generate_allocations">
						<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/generate_url"/>"</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Generate allocations')" /></xsl:attribute>	
					</input>						
					<input type="button" class="pure-button pure-button-primary" name="cancel">
						<xsl:attribute name="onclick">window.location.href="<xsl:value-of select="season/cancel_link"/>"</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="php:function('lang', 'Cancel')" /></xsl:attribute>	
					</input>
                </div>
            </div>
        </xsl:if>
        
    </div>


		
		
		
		<!--form id="panel1" method="POST">
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
		</form-->

	<!--/div-->

    <script type="text/javascript">
        var season_id = <xsl:value-of select="season/id"/>;
        var resource_ids = <xsl:value-of select="season/resources_json"/>;
        var r = [{n: 'ResultSet'},{n: 'Result'}];
        <![CDATA[
            var weekUrl = 'index.php?menuaction=booking.uiseason.wtemplate_json&id=' + season_id + '&phpgw_return_as=json&';
        ]]>
        $(window).load(function() {
            var colDefs = [
                {key: 'time', label: '<xsl:value-of select="php:function('lang', 'Time')" />'}, 
                {key: 'resource', label: '<xsl:value-of select="php:function('lang', 'Resources')" />', formatter: 'scheduleResourceColumn'},
                {key: '1', label: '<xsl:value-of select="php:function('lang', 'Monday')" />', formatter: 'seasonDateColumn'},
                {key: '2', label: '<xsl:value-of select="php:function('lang', 'Tuesday')" />', formatter: 'seasonDateColumn'},
                {key: '3', label: '<xsl:value-of select="php:function('lang', 'Wednesday')" />', formatter: 'seasonDateColumn'},
                {key: '4', label: '<xsl:value-of select="php:function('lang', 'Thursday')" />', formatter: 'seasonDateColumn'},
                {key: '5', label: '<xsl:value-of select="php:function('lang', 'Friday')" />', formatter: 'seasonDateColumn'},
                {key: '6', label: '<xsl:value-of select="php:function('lang', 'Saturday')" />', formatter: 'seasonDateColumn'},
                {key: '7', label: '<xsl:value-of select="php:function('lang', 'Sunday')" />', formatter: 'seasonDateColumn'}
            ];
            createTableSchedule('schedule_container', weekUrl, colDefs, r, 'pure-table' );
        });
    </script>

<!--script type="text/javascript">
var season_id = <xsl:value-of select="season/id"/>;
var resource_ids = <xsl:value-of select="season/resources_json"/>;
<![CDATA[
	var weekUrl = 'index.php?menuaction=booking.uiseason.wtemplate_json&id=' + season_id + '&phpgw_return_as=json&';
    var resourceUrl = 'index.php?menuaction=booking.uiresource.index&sort=name&phpgw_return_as=json&';
    for(var i=0; i< resource_ids.length; i++) {
		resourceUrl += 'filter_id[]=' + resource_ids[i] + '&';
	}
	var orgUrl = 'index.php?menuaction=booking.uiorganization.index&phpgw_return_as=json&';
]]>
Dom = YAHOO.util.Dom;

YAHOO.booking.AllocationDialog = function(container) {
	this._container = container;
	YAHOO.booking.AllocationDialog.superclass.constructor.call(this, container, { 
		width:"580px", 
		visible:false, 
		constraintoviewport:true,
		hideaftersubmit: false,
		effect: {effect:YAHOO.widget.ContainerEffect.FADE,duration:0.25}
	});
	YAHOO.booking.checkboxTableHelper('resources-container', resourceUrl, 'resources[]');
	YAHOO.booking.autocompleteHelper(orgUrl, 'field_org_name', 'field_org_id', 'org_container');

	var myButtons = [{text:"<xsl:value-of select="php:function('lang', 'Save')" />", handler: this.submit, isDefault:true},
                  	 {text:"<xsl:value-of select="php:function('lang', 'Delete')" />", handler: this._delete},
                  	 {text:"<xsl:value-of select="php:function('lang', 'Cancel')" />", handler: this.hide}];
	this.cfg.queueProperty("buttons", myButtons);
	this.callback.success = this.onSuccess;
	this.callback.failure = this.onFailure;
	this.callback.argument = this;
	this.render();
};
YAHOO.lang.extend(YAHOO.booking.AllocationDialog, YAHOO.widget.Dialog); 

YAHOO.booking.AllocationDialog.prototype._delete = function (e) {
	var postData = 'id=' + YAHOO.booking.currentAlloc;
	var url = '<xsl:value-of select="season/delete_wtemplate_alloc_url"/>';
	YAHOO.util.Connect.asyncRequest('POST', url, 
	{
		success: function(o) {
			YAHOO.booking.updateSchedule();
			var panel = o.argument;
	        panel.hide();
		},
		failure: function(o) {alert('nay' + o)},
		argument: this
	}, postData);
}

YAHOO.booking.AllocationDialog.prototype.onFailure = function (o) {
	alert('Operation failed');
}

YAHOO.booking.AllocationDialog.prototype.onSuccess = function (o) {
	var errors = eval('x='+o.responseText);
	var panel = o.argument;
	var msg = '';
	for(e in errors) {
		msg += errors[e] + '\n';
	}
	if(!msg) {
		YAHOO.booking.updateSchedule();
		panel.hide();
	}
	else {
		alert(msg);
	}
}

YAHOO.booking.AllocationDialog.prototype.editAllocation = function (id) {
	YAHOO.booking.currentAlloc = id;
	var url = '<xsl:value-of select="season/get_url"/>';
<![CDATA[
	url += '&id=' + id;
	YAHOO.util.Connect.asyncRequest('GET', url, 
	{
		success: function(o) {
			var alloc = eval('x='+o.responseText);
			var panel = o.argument;
			panel.updateForm(alloc);
	        panel.show();
		},
		failure: function(o) {alert('nay' + o)},
		argument: this
	});
}

YAHOO.booking.AllocationDialog.prototype.updateForm = function (alloc) {
    Dom.get('field_id').value = alloc.id || '';
    Dom.get('field_org_name').value = alloc.organization_name;
    Dom.get('field_org_id').value = alloc.organization_id;
    Dom.get('field_cost').value = alloc.cost;
    Dom.get('field_from').value = alloc.from_;
    Dom.get('field_to').value = alloc.to_;
    Dom.get('field_from')._update(); // Update the time-picker UI
    Dom.get('field_to')._update();
    var resources = Dom.getElementsBy(function(){return true;}, 'input', 'resources-container');
    var wday = Dom.get('field_wday');
    for(var i=0; i< wday.options.length; i++) {
        if(wday.options[i].value*1 == alloc.wday) {
            wday.selectedIndex = i;
            break;
        }
    }
    for(var i=0; i< resources.length; i++) {
        res = resources[i];
		res.checked = false;
		for (var j=0; j < alloc.resources.length; j++) {
			if((alloc.resources[j] * 1) == (res.value * 1)) {
				res.checked = true;
				break;
			}
		}
        /*res.checked = alloc.resources.indexOf(res.value*1) != -1;*/
    }
}
]]>

YAHOO.booking.AllocationDialog.prototype.newAllocation = function (wday, from_, to_, resources) {
    resources = resources || [];
    this.updateForm({resources: resources, organization_name: '', 
                     cost: 0, wday: (wday*1), from_: from_, to_: to_});
	this.show();
}

YAHOO.booking.AllocationDialog.prototype.cellFormatter = function(elCell, oRecord, oColumn, text) { 
	YAHOO.booking.scheduleColorFormatter(elCell, oRecord, oColumn, text);
	var id = oRecord.getData(oColumn.field) ? oRecord.getData(oColumn.field).id : null;
	if(id)
		elCell.onclick = function () { YAHOO.booking.dialog.editAllocation(id); };
	else {
	    var resource_id = oRecord.getData('resource_id')*1;
	    var from_ = oRecord.getData('_from');
	    var to_ = oRecord.getData('_to');
		elCell.ondblclick = function () { YAHOO.booking.dialog.newAllocation(oColumn.field, from_, to_, [resource_id]); };
	}
}


YAHOO.booking.updateSchedule = function() {
	var colDefs = [
				{key: 'time', label: '<xsl:value-of select="php:function('lang', 'Time')" />'}, 
				{key: 'resource', label: '<xsl:value-of select="php:function('lang', 'Resources')" />', formatter: YAHOO.booking.scheduleResourceColFormatter},
				{key: '1', label: '<xsl:value-of select="php:function('lang', 'Monday')" />', formatter: YAHOO.booking.dialog.cellFormatter},
				{key: '2', label: '<xsl:value-of select="php:function('lang', 'Tuesday')" />', formatter: YAHOO.booking.dialog.cellFormatter},
				{key: '3', label: '<xsl:value-of select="php:function('lang', 'Wednesday')" />', formatter: YAHOO.booking.dialog.cellFormatter},
				{key: '4', label: '<xsl:value-of select="php:function('lang', 'Thursday')" />', formatter: YAHOO.booking.dialog.cellFormatter},
				{key: '5', label: '<xsl:value-of select="php:function('lang', 'Friday')" />', formatter: YAHOO.booking.dialog.cellFormatter},
				{key: '6', label: '<xsl:value-of select="php:function('lang', 'Saturday')" />', formatter: YAHOO.booking.dialog.cellFormatter},
				{key: '7', label: '<xsl:value-of select="php:function('lang', 'Sunday')" />', formatter: YAHOO.booking.dialog.cellFormatter}
	];

	YAHOO.booking.inlineTableHelper('schedule_container', weekUrl, colDefs, {
		formatRow: YAHOO.booking.scheduleRowFormatter
	}, true);
}


YAHOO.util.Event.addListener(window, "load", function() {
	YAHOO.booking.dialog = new YAHOO.booking.AllocationDialog('panel1');
	YAHOO.booking.updateSchedule();
});
</script-->

<xsl:if test="not(season/permission/write)">
	<script type="text/javascript">
		YAHOO.booking.AllocationDialog.prototype.newAllocation = function() { }
		YAHOO.booking.AllocationDialog.prototype.editAllocation = function (id) { }
	</script>
</xsl:if>

</xsl:template>
