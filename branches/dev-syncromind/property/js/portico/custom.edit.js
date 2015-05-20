/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
this.local_DrawCallback1 = function(oTable)
{       
	var api = oTable.api(); 
        api.columns( '.sorting' ).order( 'asc' );
};
