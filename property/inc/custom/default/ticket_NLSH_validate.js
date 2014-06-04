
function validate_save()
{
	alert($("#id_feilkoder").val());
}

$(document).ready(function(){

//id_feilkoder
	$("#form").on("submit", function(e){

		var error = false;
		if( !$("#id_konf_1").prop('checked') && (!$("#id_konf_2").prop('checked') && !$("#id_konf_3").prop('checked') && !$("#id_konf_4").prop('checked')))
		{
			error = true;
			alert('Du må angi kriterie for Konfidensialitetsvurdering');
		}

		if( !$("#id_integritet_1").prop('checked') && (!$("#id_integritet_2").prop('checked') && !$("#id_integritet_3").prop('checked') && !$("#id_integritet_4").prop('checked')))
		{
			error = true;
			alert('Du må angi kriterie for Integritetsvurdering');
		}

		if( !$("#id_tilgjengelighet_1").prop('checked') && (!$("#id_tilgjengelighet_2").prop('checked') && !$("#id_tilgjengelighet_3").prop('checked') && !$("#id_tilgjengelighet_4").prop('checked')))
		{
			error = true;
			alert('Du må angi kriterie for Tilgjengelighetsvurdering');
		}

		if(error)
		{
			e.preventDefault();
			return;
		}
	});

});
