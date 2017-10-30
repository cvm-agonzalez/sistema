var info_show = false;
var socio_input;
var btn = $("#s_btn");
$(document).ready(function(){
	$(document).on('submit',"#search_form",function(e){
		btn.button('loading');
		socio_input = $("#socio_input").val();
		$.post(base_url+'estado/get_socio',{socio_input:socio_input})
		.done(function(data){
			$("#socio_info").html(data);
			$("#socio_info").slideDown();
			info_show = true;
			btn.button('reset');
		})
		e.preventDefault();
	})
	$(document).on('keyup',"#socio_input",function(){
		if(info_show && $("#socio_input").val() != socio_input){
			$("#socio_info").slideUp();
			info_show = false;
		}
	})
})
