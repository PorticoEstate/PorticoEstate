var documents = null;
var documents2 = null;
var link_history = null;
var set_history_data = 0;

$(document).ready(function(){
	
	$("#treeDiv1").jstree({
		"core" : {
            "multiple" : false,
			"themes" : { "stripes" : true },
			"data" : documents
		},
        "plugins" : [ "themes","html_data","ui","state" ]
	});

	var count1 = 0;
	$("#treeDiv1").bind("select_node.jstree", function (event, data) {
		count1 += 1;
		var divd = data.instance.get_node(data.selected[0]).original['link']; 
		if(count1 > 1)
		{
			window.location.href = divd; 
		}
	});
        
	$('#collapse1').on('click',function(){
		$(this).attr('href','javascript:;');
		$('#treeDiv1').jstree('close_all');
	})

	$('#expand1').on('click',function(){
		$(this).attr('href','javascript:;');
		$('#treeDiv1').jstree('open_all');
	});

	if (documents2)
	{
		$("#treeDiv2").jstree({
			"core" : {
				"multiple" : false,
				"themes" : { "stripes" : true },
				"data" : documents2
			},
			"plugins" : [ "themes","html_data","ui","state" ]
		});

		var count2 = 0;
		$("#treeDiv2").bind("select_node.jstree", function (event, data) {
			count2 += 1;
			var divd = data.instance.get_node(data.selected[0]).original['link']; 
			if(count2 > 1)
			{
				window.location.href = divd; 
			}
		});

		$('#collapse2').on('click',function(){
			$(this).attr('href','javascript:;');
			$('#treeDiv2').jstree('close_all');
		})

		$('#expand2').on('click',function(){
			$(this).attr('href','javascript:;');
			$('#treeDiv2').jstree('open_all');
		});
	}
	
	get_history_data = function()
	{
		if (set_history_data  === 0)
		{
			JqueryPortico.updateinlineTableHelper(oTable1, link_history);
			set_history_data = 1;
		}
	};
});
