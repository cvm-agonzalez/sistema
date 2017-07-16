$(document).ready(function() 
{
	//asociar actividad
	a= $('#r2').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/get/socios-dni|nombre|apellido',
		onSelect: function (suggestion) {        
	        $('#r2').val(suggestion.data);
	    } 
	});


}