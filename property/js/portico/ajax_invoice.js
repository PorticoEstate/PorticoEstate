$(document).ready(function(){
	
	// When janitor is selected, vouchers are fetched from db and voucer select list is populated
	$("#janitor_lid").change(function () {
//		var janitor_lid = $(this).val();
		var janitor_lid = $("#janitor_lid").val();
		var supervisor_lid = $("#supervisor_lid").val();
		var budget_responsible_lid = $("#budget_responsible_lid").val();
		var query = $("#query").val();

		var oArgs = {menuaction:'property.uiinvoice2.get_vouchers'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&janitor_lid=" + janitor_lid + "&supervisor_lid=" + supervisor_lid + "&budget_responsible_lid=" + budget_responsible_lid + "&query=" + query,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<option>" + data.length + " bilag funnet</option>"
					var obj = data;

					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});

					$("#voucher_id_filter").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen bilag</option>"
					$("#voucher_id_filter").html( htmlString );
				}
			} 
			});
		
    });

	$("#supervisor_lid").change(function () {
//		var janitor_lid = $(this).val();
		var janitor_lid = $("#janitor_lid").val();
		var supervisor_lid = $("#supervisor_lid").val();
		var budget_responsible_lid = $("#budget_responsible_lid").val();
		var query = $("#query").val();

		var oArgs = {menuaction:'property.uiinvoice2.get_vouchers'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&janitor_lid=" + janitor_lid + "&supervisor_lid=" + supervisor_lid + "&budget_responsible_lid=" + budget_responsible_lid + "&query=" + query,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<option>" + data.length + " bilag funnet</option>"
					var obj = data;

					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});

					$("#voucher_id_filter").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen bilag</option>"
					$("#voucher_id_filter").html( htmlString );
				}
			} 
			});
		
    });

	$("#budget_responsible_lid").change(function () {
		var janitor_lid = $("#janitor_lid").val();
		var supervisor_lid = $("#supervisor_lid").val();
		var budget_responsible_lid = $("#budget_responsible_lid").val();
		var query = $("#query").val();

		var oArgs = {menuaction:'property.uiinvoice2.get_vouchers'};
		var requestUrl = phpGWLink('index.php', oArgs, true);
      
		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&janitor_lid=" + janitor_lid + "&supervisor_lid=" + supervisor_lid + "&budget_responsible_lid=" + budget_responsible_lid + "&query=" + query,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<option>" + data.length + " bilag funnet</option>"
					var obj = data;

					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});

					$("#voucher_id_filter").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen bilag</option>"
					$("#voucher_id_filter").html( htmlString );
				}
			} 
			});
		
    });


//	$("#queryForm").submit(function(e){
	$("#search").click(function(e){
		var janitor_lid = $("#janitor_lid").val();
		var supervisor_lid = $("#supervisor_lid").val();
		var budget_responsible_lid = $("#budget_responsible_lid").val();
		var query = $("#query").val();
		var oArgs = {menuaction:'property.uiinvoice2.get_vouchers'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		var htmlString = "";

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&janitor_lid=" + janitor_lid + "&supervisor_lid=" + supervisor_lid + "&budget_responsible_lid=" + budget_responsible_lid + "&query=" + query,
			success: function(data) {
				if( data != null)
				{
					htmlString  = "<option>" + data.length + " bilag funnet</option>"
					var obj = data;

					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});

					$("#voucher_id_filter").html( htmlString );
				}
				else
				{
					htmlString  += "<option>Ingen bilag</option>"
					$("#voucher_id_filter").html( htmlString );
				}
			}
			});
	});




	$("#voucher_id_filter").change(function () {
		var voucher_id = $(this).val();
		var oArgs = {menuaction:'property.uiinvoice2.get_single_voucher'};
		var requestUrl = phpGWLink('index.php', oArgs, true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&voucher_id=" + voucher_id,
			success: function(data) {
				var voucher = data['voucher'];
				if( voucher != null && voucher.length > 0)
				{
					$("#voucher_id").val( voucher_id );
		
					if( voucher[0].voucher_out_id )
					{
						$("#voucher_id_text").html( voucher[0].voucher_out_id );					
					}
					else
					{
						$("#voucher_id_text").html( voucher_id );
					}
					$("#order_id").val( voucher[0].order_id );

					if(voucher[0].order_id)
					{
						var oArgs_order = {menuaction:'property.uiinvoice.view_order', order_id: voucher[0].order_id};
						var requestUrl_order = phpGWLink('index.php', oArgs_order);
						var htmlString_order  =  " <a target= \"_blank\" href=\"" + requestUrl_order + "\" title=\"" + voucher[0].status + "\" > Bestilling</a>";
						$("#order_text").html( htmlString_order );
					}
					else
					{
						$("#order_text").html( 'Bestilling' );
					}

					$("#project_group").val( voucher[0].project_group );

					if(voucher[0].external_ref)
					{
						$("#invoice_id_text").html(voucher[0].external_ref );
					}
					else
					{
						$("#invoice_id_text").html('FakturaNr');
					}

					$("#invoice_id").html( voucher[0].invoice_id );
					$("#kid_nr").html( voucher[0].kid_nr );
					$("#vendor").html( voucher[0].vendor );
//					$("#janitor").html( voucher[0].janitor );
//					$("#supervisor").html( voucher[0].supervisor );
//					$("#budget_responsible").html( voucher[0].budget_responsible );
					$("#invoice_date").html( voucher[0].invoice_date );
					$("#payment_date").html( voucher[0].payment_date );
					$("#b_account_id").val( voucher[0].b_account_id );
					$("#dim_a").val( voucher[0].dim_a );
					$("#amount").html( data['generic'].amount );
					$("#approved_amount").html( data['generic'].approved_amount );
					$("#currency").html( voucher[0].currency );
		//			$("#oppsynsigndato").html( voucher[0].oppsynsigndato );
		//			$("#saksigndato").html( voucher[0].saksigndato );
		//			$("#budsjettsigndato").html( voucher[0].budsjettsigndato );
					if(voucher[0].merknad)
					{
						var oArgs_remark = {menuaction:'property.uiinvoice.remark', id: voucher[0].id};
						var requestUrl_remark = phpGWLink('index.php', oArgs_remark);

						var htmlString  =  " <a href=\"javascript:openwindow('" +requestUrl_remark + "','550','400')\" > Remark</a>";

						$("#remark").html( htmlString );
					}
//---------
					var checked_park_invoice = "";
					var park_invoice_status = "";
					if(voucher[0].parked)
					{
						checked_park_invoice = "checked = \"checked\"";
						var park_invoice_status = " X";
					}
					var htmlString_park_invoice = "<input type=\"checkbox\" name=\"values[park_invoice]\" value=\"1\" title=\"park invoice\"" + checked_park_invoice + "></input>" + park_invoice_status;
					$("#park_order").html( htmlString_park_invoice );
//---------
					var checked_close_order = "";
					var close_order_status = " " + voucher[0].status;
					if(voucher[0].closed)
					{
						checked_close_order = "checked = \"checked\"";
					}
					var htmlString_close_order = "<input type=\"checkbox\" name=\"values[close_order]\" value=\"1\" title=\"close order\"" + checked_close_order + "></input>" + close_order_status;
					$("#close_order").html( htmlString_close_order );
//---------

					if(data['generic']['dimb_list']['options'] != 'undefined')
					{
						var htmlString = "";

						var obj = data['generic']['dimb_list']['options'];

						$.each(obj, function(i) {
							var selected = '';
							if(obj[i].id == voucher[0].dim_b)
							{
								selected = ' selected';
							}
							htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
			    			});

						$("#dim_b").html( htmlString );
					}
					if(data['generic']['tax_code_list']['options'] != 'undefined')
					{
						var htmlString = "";

						htmlString  = "<option>Velg</option>"

						var obj = data['generic']['tax_code_list']['options'];

						$.each(obj, function(i) {
							var selected = '';
							if(obj[i].id == voucher[0].tax_code)
							{
								selected = ' selected';
							}
							htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
			    			});

						$("#tax_code").html( htmlString );
					}

					if(data['generic']['period_list']['options'] != 'undefined')
					{
						var htmlString = "";

						var obj = data['generic']['period_list']['options'];

						$.each(obj, function(i) {
							var selected = '';
							if(obj[i].id == voucher[0].period)
							{
								selected = ' selected';
							}
							htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
			    			});

						$("#period").html( htmlString );
					}
					if(data['generic']['periodization_list']['options'] != 'undefined')
					{
						var htmlString = "";

						var obj = data['generic']['periodization_list']['options'];

						$.each(obj, function(i) {
							var selected = '';
							if(obj[i].id == voucher[0].periodization)
							{
								selected = ' selected';
							}
							htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
			    			});

						$("#periodization").html( htmlString );
					}
					if(data['generic']['periodization_start_list']['options'] != 'undefined')
					{
						var htmlString = "";

						var obj = data['generic']['periodization_start_list']['options'];

						$.each(obj, function(i) {
							var selected = '';
							if(obj[i].id == voucher[0].periodization_start)
							{
								selected = ' selected';
							}
							htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
			    			});

						$("#periodization_start").html( htmlString );
					}

					if(data['generic']['process_code_list']['options'] != 'undefined')
					{
						var htmlString = "";

						var obj = data['generic']['process_code_list']['options'];

						$.each(obj, function(i) {
							var selected = '';
							if(obj[i].id == voucher[0].periodization)
							{
								selected = ' selected';
							}
							htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
			    			});

						$("#process_code").html( htmlString );
					}

					if(data['generic']['approved_list'] != 'undefined')
					{
						for ( var i = 0; i < data['generic']['approved_list'].length; ++i )
						{
							var role_sign = data['generic']['approved_list'][i].role_sign;
							var role_initials = data['generic']['approved_list'][i].initials;

							if( data['generic']['approved_list'][i].date )
							{
								var htmlString = role_initials + ": " + data['generic']['approved_list'][i].date;
							}
							else
							{
								var htmlString = "<select name=\"values[forward][" + role_sign + "]\">";
								var obj = data['generic']['approved_list'][i]['user_list'].options;
								$.each(obj, function(i) {
									var selected = '';
									if(obj[i].id == role_initials)
									{
										selected = ' selected';
									}
									htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
					    			});

								htmlString  += "</select>";
							}
							$("#" + role_sign).html( htmlString );
						}
					}

					if(data['generic']['approve_list']['options'] != 'undefined')
					{
						var htmlString = "";

						var obj = data['generic']['approve_list']['options'];

						$.each(obj, function(i) {
							var selected = '';
							if(obj[i].id == voucher[0].period)

							{
								selected = ' selected';
							}
							htmlString  += "<option value='" + obj[i].id + "'" + selected + ">" + obj[i].name + "</option>";
			    			});

						$("#approve_as").html( htmlString );
					}
				}
				else
				{
					$("#voucher_id").val( '' );
					$("#voucher_id_text").html( '' );
					$("#order_id").val( '' );
					$("#project_group").val( '' );
					$("#invoice_id").html( '' );
					$("#kid_nr").html( '' );
					$("#vendor").html('' );
//					$("#janitor").html( '' );
//					$("#supervisor").html( '' );
//					$("#budget_responsible").html( '' );
					$("#invoice_date").html( '' );
					$("#payment_date").html( '' );
					$("#b_account_id").val( '' );
					$("#amount").html( '' );
					$("#approved_amount").html( '' );
					$("#currency").html( '' );
					$("#oppsynsmannid").html( '' );
					$("#saksbehandlerid").html( '' );
					$("#budsjettansvarligid").html( '' );
					$("#remark").html( '' );
					$("#dim_a").val('' );
					$("#dim_b").html( "<option>Velg</option>" );
					$("#period").html( "<option>Velg</option>" );
					$("#periodization").html( "<option>Velg</option>" );
					$("#periodization_start").html( "<option>Velg</option>" );
					$("#process_code").html( "<option>Velg</option>" );
					$("#tax_code").html( "<option>0</option>" );
					$("#approve_as").html( "<option>Velg</option>" );
					$("#order_text").html( 'Bestilling' );
					$("#invoice_id_text").html('FakturaNr');
				}
			}
			});
    });


	// When control area is selected, controls are fetched from db and control select list is populated
	$("#control_area").change(function () {
		 var control_area_id = $(this).val();
		 if(control_area_id == '')
			 control_area_id = "all";
			
		var oArgs = {menuaction:'controller.uicontrol_group.get_control_groups_by_control_area', phpgw_return_as:'json'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);

		//var requestUrl = "index.php?menuaction=controller.uicontrol_group.get_control_groups_by_control_area&phpgw_return_as=json"
        
		var htmlString = "";
        
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&control_area_id=" + control_area_id,
			success: function(data) {
				if( data != null){
					htmlString  = "<option>Velg kontrollgruppe</option>"
					var obj = jQuery.parseJSON(data);
					
					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].group_name + "</option>";
		    			});
					 								 
					$("#control_group").html( htmlString );
					}else {
						htmlString  += "<option>Ingen kontrollgrupper</option>"
						$("#control_group").html( htmlString );
					}
			} 
			});
		
    });

	// When control area is selected, controls are fetched from db and control select list is populated
/*	$("#control_group").change(function () {
		 var control_group_id = $(this).val();
		var oArgs = {menuaction:'controller.uicontrol_group.get_control_area_by_control_group', phpgw_return_as:'json'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);

		//var requestUrl = "index.php?menuaction=controller.uicontrol_group.get_control_groups_by_control_area&phpgw_return_as=json"
        
		var htmlString = "";
        
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&control_group_id=" + control_group_id,
			success: function(data) {
				if( data != null){
					htmlString  = "<option>Ingen kontrollområde</option>"
					var obj = jQuery.parseJSON(data);
					
					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].group_name + "</option>";
		    			});
					 								 
					$("#control_group_id").html( htmlString );
					}else {
						htmlString  += "<option>Ingen kontrollområder</option>"
						$("#control_group_id").html( htmlString );
					}
			} 
			});
		
    });
*/

	// file: add_component_to_control.xsl
	// When component category is selected, corresponding component types are fetched from db and component type select list is populated
	$("#ifc").change(function () {
		 var ifc_id = $(this).val();
		
		 var oArgs = {menuaction:'controller.uicheck_list_for_component.get_component_types_by_category', phpgw_return_as:'json'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);
		//var requestUrl = "index.php?menuaction=controller.uicheck_list_for_component.get_component_types_by_category&phpgw_return_as=json"
        
		var htmlString = "";
        
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&ifc=" + ifc_id,
			success: function(data) {
				if( data != null){
					htmlString  = "<option>Velg type</option>"
					var obj = jQuery.parseJSON(data);
					
					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].name + "</option>";
		    			});
					 								 
					$("#bim_type_id").html( htmlString );
					}else {
						htmlString  += "<option>Ingen typer</option>"
						$("#bim_type_id").html( htmlString );
					}
			} 
			});
		
    });

	// file: control.xsl
	// When control area is selected, procedures are fetched from db and procedure select list is populated
	$("#control_area_id").change(function () {
		 var control_area_id = $(this).val();
		
		 var oArgs = {menuaction:'controller.uiprocedure.get_procedures'};
		 var requestUrl = phpGWLink('index.php', oArgs, true);
		//var requestUrl = "index.php?menuaction=controller.uiprocedure.get_procedures&phpgw_return_as=json"
        
		var htmlString = "";
        
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: requestUrl + "&control_area_id=" + control_area_id,
			success: function(data) {
				if( data != null){
					htmlString  = "<option>Velg prosedyre</option>"
					var obj = jQuery.parseJSON(data);
					
					$.each(obj, function(i) {
						htmlString  += "<option value='" + obj[i].id + "'>" + obj[i].title + "</option>";
		    			});
					 								 
					$("#procedure_id").html( htmlString );
					}
					else
					{
						htmlString  += "<option>Ingen prosedyrer</option>"
					$("#procedure_id").html( htmlString );			 
					}
			} 
			});
    });
		
	$("#frm_save_control_groups").submit(function(e){
		var thisForm = $(this);
		var num_checked = $(this).find("input:checked").length;
	
		if(num_checked == 0){
			e.preventDefault();		
			$(thisForm).before("<div style='margin: 10px 0;text-align: center;width: 200px;' class='input_error_msg'>Du må velge en eller flere grupper</div>");
		}
	});

	$("#frm_control_items").submit(function(e){
		var thisForm = $(this);
		var num_checked = $(this).find("input:checked").length;
	
		if(num_checked == 0){
			e.preventDefault();		
			$(thisForm).before("<div style='margin: 10px 0;text-align: center;width: 200px;' class='input_error_msg'>Du må velge en eller flere punkter</div>");
		}
	});

	$("#frm_save_control_details input").focus(function(e){
		$("#frm_save_control_details").find(".focus").removeClass("focus");
		$(this).addClass("focus");
	});

	$("#frm_save_control_details input").focus(function(e){
		$("#frm_save_control_details").find(".focus").removeClass("focus");
		$(this).addClass("focus");
	});

	$("#frm_save_control_details select").focus(function(e){
		$("#frm_save_control_details").find(".focus").removeClass("focus");
		$(this).addClass("focus");
	});

	$("#frm_save_control_details").submit(function(e){
	
		var thisForm = $(this);

		var $required_input_fields = $(this).find(".required");
		var status = true;
			
	    $required_input_fields.each(function() {
	    
	    	if($(this).val() == ''){
	    		var nextElem = $(this).next();
	    	
	    		if( !$(nextElem).hasClass("input_error_msg") )
	    			$(this).after("<div class='input_error_msg'>Du må fylle ut dette feltet</div>");
	    			  	
	    		status = false;
	    	}else{
	    		var nextElem = $(this).next();

	    		if( $(nextElem).hasClass("input_error_msg") )
	    			$(nextElem).remove();
	    	}
	    });

	    if( status ){
    		var saved_control_area_id = $(thisForm).find("input[name='saved_control_area_id']").val();
    		var new_control_area_id = $("#control_area_id").val();

    		if(saved_control_area_id != '' & saved_control_area_id != new_control_area_id)
    		{
    			var answer = confirm("Du har endret kontrollområde til kontrollen. " +
    								 "Hvis du lagrer vil kontrollgrupper og kontrollpunkter til kontrollen bli slettet.")
    			if (!answer){
    				e.preventDefault();
    			}
    		}
	    }else{
	    	e.preventDefault();
	    }
	    
	});

	// file: view_check_lists_for_location.xsl
	// Fetches info about a check list on hover status image icon
	$('a.view_check_list').bind('contextmenu', function(){
		var thisA = $(this);
		var divWrp = $(this).parent();
	
		var add_param = $(thisA).find("span").text();
	
		var oArgs = {menuaction:'controller.uicheck_list.get_cases_for_check_list'};
		var baseUrl = phpGWLink('index.php', oArgs, true);
		var requestUrl = baseUrl + add_param
	
		//var requestUrl = "http://portico/pe/index.php?menuaction=controller.uicheck_list.get_cases_for_check_list" + add_param;
	
		$.ajax({
			type: 'POST',
			url: requestUrl,
			dataType: 'json',
	    	  success: function(data) {
	    		  if(data){
	    			var obj = jQuery.parseJSON(data);

	    			// Show info box with info about check list
		    		  var infoBox = $(divWrp).find("#info_box");
		    		  $(infoBox).show();
		    		 
		    		  var htmlStr = "<h5>Åpne saker</h5><ul>";
		    	
		    		  $.each(obj, function(i) {
		    			htmlStr += "<li><label>" + (parseInt(i) + 1) + ": Tittel</label><span>" + obj[i].control_item.title + "</span>";
		    			htmlStr += "<ul>";
		    			 
		    			$(obj[i].cases_array).each(function(j) {
		    				htmlStr += "<li>" + "Sak " + (parseInt(j) + 1) + ":  " + obj[i].cases_array[j].descr + "</li>";
		    			});
		    			htmlStr += "</ul></li>";
		    			});
		    		 
		    		  htmlStr += "</ul>";
		    	
		    		  $(infoBox).html( htmlStr ); 
	    		  }
	    	  }
		   });
	
		return false;
	});

	$("a.view_check_list").mouseout(function(){
		var infoBox = $(this).parent().find("#info_box");
	
		$(infoBox).hide();
	});

	$(".frm_save_check_item").live("submit", function(e){
		e.preventDefault();
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");

		$.ajax({
			type: 'POST',
			url: requestUrl + "&phpgw_return_as=json&" + $(thisForm).serialize(),
			success: function(data) {
				if(data){
	    			var obj = jQuery.parseJSON(data);
		    	
		    		  if(obj.status == "saved"){
		    			var submitBnt = $(thisForm).find("input[type='submit']");
		    			$(submitBnt).val("Lagret");
		    				 
		    				// Changes text on save button back to original
		    				window.setTimeout(function() {
		    				$(submitBnt).val('Oppdater måling');
		    				$(submitBnt).addClass("not_active");
		    					 }, 1000);	   				 
					}
				}
				}
			});
	});

	$(".frm_save_control_item").live("click", function(e){
		e.preventDefault();
		var thisForm = $(this);
		var liWrp = $(this).parent();
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");
	
		$.ajax({
			type: 'POST',
			url: requestUrl + "&phpgw_return_as=json&" + $(thisForm).serialize(),
			success: function(data) {
				if(data){
	    			var obj = jQuery.parseJSON(data);
		    		 
		    		  if(obj.status == "saved"){
		    			$(liWrp).fadeOut('3000', function() {
		    				$(liWrp).addClass("hidden");
		    			});
					}
				}
				}
			});
	});

	$("#frm_update_check_list").live("submit", function(e){
		e.preventDefault();

		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");
	
		$.ajax({
			type: 'POST',
			url: requestUrl + "&phpgw_return_as=json&" + $(thisForm).serialize(),
			success: function(data) {
				if(data){
	    			var obj = jQuery.parseJSON(data);
		    	
	    			if(obj.status == "updated"){
		    			var submitBnt = $(thisForm).find("input[type='submit']");
		    			$(submitBnt).val("Lagret");
		    				 
		    			// Changes text on save button back to original
		    			window.setTimeout(function() {
							$(submitBnt).val('Lagre sjekkpunkt');
							$(submitBnt).addClass("not_active");
		    			}, 1000);
					}
				}
				}
		});
	});

	$(".frm_register_case").live("submit", function(e){
		e.preventDefault();

		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		var requestUrl = $(thisForm).attr("action");
	
		$.ajax({
			type: 'POST',
			url: requestUrl + "&" + $(thisForm).serialize(),
			success: function(data) {
				if(data){
	    			var jsonObj = jQuery.parseJSON(data);
		    	
	    			if(jsonObj.status == "saved"){
		    			var submitBnt = $(thisForm).find("input[type='submit']");
		    			$(submitBnt).val("Lagret");
		    			 
		    			clear_form( thisForm );
			    				 
		    			// Changes text on save button back to original
		    			window.setTimeout(function() {
							$(submitBnt).val('Registrer sak');
							$(submitBnt).addClass("not_active");
		    			}, 1000);
					}
				}
				}
		});
	});

	$(".frm_update_case").live("submit", function(e){
		e.preventDefault();

		var thisForm = $(this);
		var clickRow = $(this).closest("li");
		var checkItemRow = $(this).closest("li.check_item_case");
		var requestUrl = $(thisForm).attr("action");
			
		$.ajax({
			type: 'POST',
			url: requestUrl + "&" + $(thisForm).serialize(),
			success: function(data) {
				if(data){
	    			var jsonObj = jQuery.parseJSON(data);
		
	    			if(jsonObj.status == "saved"){
	    				var type = $(checkItemRow).find(".control_item_type").text();
	    			
		    			if(type == "control_item_type_1"){
	    				
	    				}else if(type == "control_item_type_2"){
	    					var measurement_text = $(thisForm).find("input[name='measurement']").val();
		    				$(clickRow).find(".case_info .measurement").text(measurement_text);
	    				}
		    		
		    			// Text from forms textarea
	    				var desc_text = $(thisForm).find("textarea").val();
	    				// Puts new text into description tag in case_info	    				 			
	    				$(clickRow).find(".case_info .case_descr").text(desc_text);
	    					  			
	    				$(clickRow).find(".case_info").show();
	    				$(clickRow).find(".frm_update_case").hide();
					}
				}
			}
		});
	});

	$("a.quick_edit").live("click", function(e){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
								
		$(clickRow).find(".case_info").hide();
		$(clickRow).find(".frm_update_case").show();
	
		return false;
	});

	$(".frm_update_case .cancel").live("click", function(e){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
			
	
		$(clickRow).find(".case_info").show();
		$(clickRow).find(".frm_update_case").hide();
	
		return false;
	});

	// Delete a case item from list
	$(".delete_case").live("click", function(){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");
	
		var url = $(clickElem).attr("href");

		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function(data) {
				var obj = jQuery.parseJSON(data);
		    	
   				if(obj.status == "deleted"){
	   				if( $(clickItem).children("li").length > 1){
	   					$(clickRow).fadeOut(300, function(){
	   						$(clickRow).remove();
	   					});
	   				
		   				var next_row = $(clickRow).next();
					
						// Updating order numbers for rows below deleted row 
						while( $(next_row).length > 0){
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
	   				}else{
		   				$(checkItemRow).fadeOut(300, function(){
	   						$(checkItemRow).remove();
	   					});
	   				}
   				}
			}
		});

		return false;
	});

	// Closes a case
	$(".close_case").live("click", function(){
		var clickElem = $(this);
		var clickRow = $(this).closest("li");
		var clickItem = $(this).closest("ul");
		var checkItemRow = $(this).parents("li.check_item_case");
	
		var url = $(clickElem).attr("href");

		// Sending request for deleting a control item list
		$.ajax({
			type: 'POST',
			url: url,
			success: function(data) {
				var obj = jQuery.parseJSON(data);
		    	
   				if(obj.status == "closed"){
	   				if( $(clickItem).children("li").length > 1){
	   					$(clickRow).fadeOut(300, function(){
	   						$(clickRow).remove();
	   					});
	   				
		   				var next_row = $(clickRow).next();
					
						// Updating order numbers for rows below deleted row 
						while( $(next_row).length > 0){
							update_order_nr_for_row(next_row, "-");
							next_row = $(next_row).next();
						}
	   				}else{
		   				$(checkItemRow).fadeOut(300, function(){
	   						$(checkItemRow).remove();
	   					});
	   				}
   				}
			}
		});

		return false;
	});

	$("#frm_update_check_list").live("click", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
	});

	$("#frm_add_check_list").live("click", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
	});

	$(".frm_save_check_item").live("click", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
	});

	$(".frm_register_case").live("click", function(e){
		var thisForm = $(this);
		var submitBnt = $(thisForm).find("input[type='submit']");
		$(submitBnt).removeClass("not_active");
	});

	$("#control_details input").focus(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeIn(300);
	});

	$("#control_details input").focusout(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeOut(300);
	});

	$("#control_details select").focus(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeIn(300);
	});

	$("#control_details select").focusout(function(e){
		var wrpElem = $(this).parents("dd");
		$(wrpElem).find(".help_text").fadeOut(300);
	});

});

function clear_form( form ){
	// Clear form
	$(form).find(':input').each(function() {
        switch(this.type) {
		   case 'select-multiple':
		   case 'select-one':
		   case 'text':
			  $(this).val('');
			  break;
		   case 'textarea':
			  $(this).val('');
			  break;
		   case 'checkbox':
		   case 'radio':
			  this.checked = false;
        }
    });
}

//Updates order number for hidden field and number in front of row
function update_order_nr_for_row(element, sign){

	var span_order_nr = $(element).find("span.order_nr");
	var order_nr = $(span_order_nr).text();

	if(sign == "+")
		var updated_order_nr = parseInt(order_nr) + 1;
	else
		var updated_order_nr = parseInt(order_nr) - 1;

	// Updating order number in front of row
	$(span_order_nr).text(updated_order_nr);
}
