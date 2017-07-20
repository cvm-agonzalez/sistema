$(document).ready(function() 
{
    //comprobacion de fecha de nacimiento para mostrar o no los campos de contacto
    $( "#domicilio" ).focus(function(){
        var fecha = $("#fechan").val();
        var res = fecha.split("-");
        res[1]--;
        var d = new Date(res[2],res[1],res[0]);                    
        var n = d.getTime();                    
        if($.now()-d > 567648000000){                
            $("#menor").hide();
        }else{
            $("#menor").show();
        }
                  
    })  

    //webcam attach
    Webcam.setSWFLocation("<?=$baseurl?>scripts/webcam.swf");
    Webcam.attach( '#my_camera' );
    //tomar foto
    function take_snapshot() {
        $("#save_btn").attr("disabled", "disabled");
        $("#save_btn").html("Guardando imagen...");
        var data_uri = Webcam.snap();
        document.getElementById('my_result').innerHTML = '<img src="'+data_uri+'"/>';
        //subir imagen on take_snapshot
        Webcam.upload( data_uri, '<?=$baseurl?>admin/socios/agregar_imagen', function(code, text) {
            $("#save_btn").removeAttr("disabled");
            $("#save_btn").html("Guardar");
        } );
    }
    //iniciamos varios autocomplete                       
    a= $('#nombre').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/search/socios-nombre' });
    a= $('#apellido').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/search/socios-apellido'  });
    a= $('#r1').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/get/socios-dni|nombre|apellido',
    onSelect: function (suggestion) {
            $('#r1').val(suggestion.data);
        } });
    a= $('#r2').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/get/socios-dni|nombre|apellido',
    onSelect: function (suggestion) {
            $('#r2').val(suggestion.data);
        }  });
    a= $('#r3').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/get/socios-apellido|nombre|dni',
        onSelect: function (suggestion) {
            $('#r3').val(suggestion.data);
        }
    });
    //funciones para buscar si existen los usuarios ingresados en los campos de 
    //contacto si es menor o en caso de ingresar un tutor de grupo familiar
    //y lo carga en el form
    $("a#r-buscar").click(function(){
        var id = $(this).data('id');
        $("#"+id+"-loading").removeClass('hidden');
        var dni = $("#"+id).val();
        $.get("<?=$baseurl?>autocomplete/buscar_socio/dni/"+dni,function(data){
            $("#"+id+"-loading").addClass('hidden');
            if(data){
                var socio = $.parseJSON(data);                                        
                var close_link = '<a href="#" onclick="cleear(\''+id+'\')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>'
                $("#"+id+"-result").html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+socio[0].nombre+' '+socio[0].apellido+' ('+socio[0].dni+') '+close_link);
                $("#"+id+"-data").addClass('hidden');
                $("#"+id+"-result").removeClass('hidden');
                $("#"+id+"-id").val(socio[0].Id);
            }else{
               angular.element("#modal_open").triggerHandler('click');
               $("#tutor-dni").val($("#"+id).val());
               $("#tutor-nombre").focus();
               $("#form-tutor").data("id",id);
               
            }
        })                            
    })           
    //la siguiente funcion da de alta al usuario nuevo en caso de no existir y lo carga en el form            
    function submit_tutor(){ 
        var tutor = $( "#form-tutor" ).serialize();
        $.get("<?=$baseurl?>admin/socios/nuevo-tutor?"+tutor, function(data){
            if(data == 'DNI'){
                alert("El DNI Ingresado ya existe o no es valido.")
            }else{
                angular.element("#modal_close").triggerHandler('click');
                var socio = $.parseJSON(data);
                var id = $("#form-tutor").data('id');  
                var close_link = '<a href="#" onclick="cleear(\''+id+'\')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>'                                     
                $("#"+id+"-result").html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+socio.nombre+' '+socio.apellido+' ('+socio.dni+') '+close_link);
                $("#"+id+"-data").addClass("hidden");
                $("#"+id+"-result").removeClass("hidden");
                $("#"+id+"-id").val(socio.Id);                                        
            }
        })
    }  
    function cleear(id){                                                     
        $("#"+id+"-data").removeClass('hidden');
        $("#"+id+"-id").val('0');
        $("#"+id+"-result").addClass('hidden');                                 
    }

}