
$(document).ready(function ()
{
    function populateTableChkArticles(selection, resources, application_id, reservation_type, reservation_id)
    {

        var oArgs = {
            menuaction: 'bookingfrontend.uiarticle_mapping.get_articles',
            sort: 'name',
            application_id: application_id,
            reservation_type: reservation_type,
            reservation_id: reservation_id
        };
        var url = phpGWLink('bookingfrontend/', oArgs, true);

        for (var r in resources)
        {
            url += '&resources[]=' + resources[r];
        }

        var container = 'articles_container';
        var colDefsRegulations = [
             {
                /**
                 * Hidden field for holding article id
                 */
                attrs: [{name: 'style', value: "display:none;"}],
                object: [
                    {type: 'input', attrs: [
                            {name: 'type', value: 'hidden'}
                        ]
                    }
                ], value: 'id'},
            {
                key: 'name',
                label: lang['article'],
                attrs: [{name: 'class', value: "align-middle"}],
            },
            {
                key: 'unit',
                label: lang['unit'],
                attrs: [{name: 'class', value: "align-middle"}],
            },
            {
                key: 'ex_tax_price',
                label: lang['unit cost'],
                attrs: [
					{name: 'class', value: "text-right align-middle"}
				]
            },
            {
                key: 'tax',
                label: lang['tax'],
                attrs: [{name: 'class', value: "text-right align-middle"}],
            },
            {
                key: 'selected_quantity',
                label: lang['quantity'],
                attrs: [{name: 'class', value: "text-right align-middle"}]
            },
            {
                key: 'selected_sum',
                label: lang['Sum'],
                attrs: [
                    {name: 'class', value: "text-right align-middle selected_sum"}
                ]
            }

        ];

        populateTableArticles(url, container, colDefsRegulations);

    }
	var resources = initialSelection;
	if (resources.length > 0)
	{
        if(typeof(application_id)  === 'undefined')
        {
            application_id  = '';
        }
        if(typeof(reservation_type) === 'undefined')
        {
            reservation_type = '';
        }
        if(typeof(reservation_id) === 'undefined')
        {
            reservation_id = '';
        }

        populateTableChkArticles([], resources, application_id, reservation_type,reservation_id );

	}

});

var post_handle_table = function()
{

	var tr = $('#articles_container').find('tr')[1];

	if(!tr || typeof(tr) == 'undefined')
	{
		return;
	}

	var xTable = tr.parentNode.parentNode;

	set_sum(xTable);
};



function set_sum(xTable)
{
	var xTableBody = xTable.childNodes[1];
	var selected_sum = xTableBody.getElementsByClassName('selected_sum');

	var temp_total_sum = 0;
	for (var i = 0; i < selected_sum.length; i++)
	{
		if (selected_sum[i].innerHTML)
		{
			temp_total_sum = parseFloat(temp_total_sum) + parseFloat(selected_sum[i].innerHTML);
			selected_sum[i].innerHTML = parseFloat(selected_sum[i].innerHTML).toFixed(2);
		}
	}

	var tableFooter = document.createElement('tfoot');
	tableFooter.id = 'tfoot'
	var tableFooterTr = document.createElement('tr');
	var tableFooterTrTd = document.createElement('td');

	tableFooterTrTd.setAttribute('colspan', 5);
	tableFooterTrTd.innerHTML = "Sum:";
	tableFooterTr.appendChild(tableFooterTrTd);
	var tableFooterTrTd2 = document.createElement('td');
	tableFooterTrTd2.setAttribute('id', 'sum_price_table');
	tableFooterTrTd2.classList.add("text-right");

	tableFooterTrTd2.innerHTML = temp_total_sum.toFixed(2);

	tableFooterTr.appendChild(tableFooterTrTd2);

	tableFooter.appendChild(tableFooterTr);
	xTable.appendChild(tableFooter);

}


function populateTableArticles(url, container, colDefs)
{
	createTable(container, url, colDefs, '', 'table table-bordered table-hover table-sm table-responsive', null, post_handle_table);
}
