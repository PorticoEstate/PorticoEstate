/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//Namespacing
;var JqueryPortico = {};

JqueryPortico.formatLink = function(key, oData) {
	var name = oData[key];
	var link = oData['link'];
	return '<a href="' + link + '">' + name + '</a>';
};
