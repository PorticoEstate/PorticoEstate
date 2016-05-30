
var oArgs = {menuaction: 'property.uigeneric.index', type: 'dimb', type_id:0};
var strURL = phpGWLink('index.php', oArgs, true);
JqueryPortico.autocompleteHelper(strURL, 'ecodimb_name', 'ecodimb', 'ecodimb_container', 'descr');

