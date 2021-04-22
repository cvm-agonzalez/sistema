<!doctype html>
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?=$ent_nombre?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic" rel="stylesheet" type="text/css">
        <!-- needs images, font... therefore can not be part of ui.css -->
        <link rel="stylesheet" href="<?=$baseurl?>bower_components/font-awesome/css/font-awesome.min.css">

        <!-- end needs images -->

            <link rel="stylesheet" href="<?=$baseurl?>styles/ui.css"/>
            <link rel="stylesheet" href="<?=$baseurl?>styles/main.css">
            <link rel="stylesheet" href="<?=$baseurl?>styles/notifIt.css">
            <link rel="stylesheet" href="<?=$baseurl?>styles/jquery.fileupload.css">

            <?
            /*if($redirect){
            ?>
            <script type="text/javascript">document.location.href = '<?=$redirect?>'</script>
            <?
            }*/
            ?>
        <link rel="icon" href="<?=$baseurl?>images/favicon32.png" type="image/x-icon" />
    </head>
    <body data-ng-app="app" id="app" data-custom-background="" data-off-canvas-nav="">
        <!--[if lt IE 9]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div data-ng-controller="AppCtrl">
            <div data-ng-hide="isSpecificPage()" data-ng-cloak="">
                <section  id="header" class="top-header">
                    <header class="clearfix">
                        <a href="#/" data-toggle-min-nav
                                     class="toggle-min"
                                     ><i class="fa fa-bars"></i></a>

                        <!-- Logo -->
                        <div class="logo">
                            <a href="<?=$baseurl?>admin">
                                <img src="<?=$baseurl?>entidades/<?=$ent_directorio?>/g1.jpg" alt="" class="img-circle img30_30">
                            </a>
                        </div>

                        <!-- needs to be put after logo to make it working-->
                        <div class="menu-button" toggle-off-canvas>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </div>


                        <div class="top-nav">
			<div>
                    	  <ul class="nav-left pull-left">
				<?=$ent_nombre?>
			  </ul>
			</div>
			<div>
                    	  <ul class="nav-right pull-right">
				login: <?=$username?>
			  </ul>
			</div>
                    	  <ul class="nav-right pull-right list-unstyled">

                                <li class="dropdown text-normal nav-profile">
                                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                                        <img src="<?=$baseurl?>entidades/<?=$ent_directorio?>/g1.jpg" alt="" class="img-circle img30_30">
                                        <span class="hidden-xs">
                                            <span data-i18n=""></span>
                                        </span>
                                    </a>
                                    <ul class="dropdown-menu with-arrow pull-right">
					<? if ( $rango == 0 ) { ?>
                                        	<li>
                                            		<a href="<?=$baseurl?>admin/entidades">
                                                	<i class="fa fa-user"></i>
                                                	<span data-i18n="Entidades"></span>
                                            		</a>
                                        	</li>
                                        	<li>
                                            		<a href="<?=$baseurl?>admin/admins">
                                                	<i class="fa fa-user"></i>
                                                	<span data-i18n="Administradores"></span>
                                            		</a>
                                        	</li>
                                        	<li>
                                            		<a href="<?=$baseurl?>admin/profes">
                                                	<i class="fa fa-user"></i>
                                                	<span data-i18n="Acceso Comisiones"></span>
                                            		</a>
                                        	</li>
					<? } ?>
                                        <li>
                                            <a href="<?=$baseurl?>admin/admins/ult_movs">
                                                <i class="fa fa-key"></i>
                                                <span data-i18n="Ultimos Movimientos"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?=$baseurl?>admin/admins/chgpwd">
                                                <i class="fa fa-key"></i>
                                                <span data-i18n="Cambiar Contraseña"></span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?=$baseurl?>admin/logout">
                                                <i class="fa fa-sign-out"></i>
                                                <span data-i18n="Cerrar Sesión"></span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                            </ul>
                        </div>

                    </header>
                </section>


                <aside data-ng-include=" '<?=$baseurl?>views/nav.php?baseurl=<?=$baseurl?>&section=<?=$section?>&rango=<?=$rango?>' " id="nav-container"></aside>
            </div>
                <section id="content" class="gs_section">

                    <? include($section.".php"); ?>

                </section>

        </div>


        <script src="<?=$baseurl?>scripts/vendor.js" type="text/javascript" ></script>

        <script src="<?=$baseurl?>scripts/ui.js" type="text/javascript" ></script>

        <script src="<?=$baseurl?>scripts/jquery-ui-1.10.4.min.js" type="text/javascript" ></script>


        <script src="<?=$baseurl?>scripts/app.js" type="text/javascript" ></script>

        <script src="<?=$baseurl?>scripts/webcam.js" type="text/javascript" ></script>
        <script src="<?=$baseurl?>scripts/jquery.fileupload.js" type="text/javascript" ></script>

        <script src="<?=$baseurl?>scripts/jquery.autocomplete.js" type="text/javascript" ></script>
        <script src="<?=$baseurl?>scripts/bootstrap.min.js" type="text/javascript" ></script>
        <script src="<?=$baseurl?>scripts/jquery.ui.datepicker-es.js" type="text/javascript" ></script>
        <script src="<?=$baseurl?>scripts/tinymce/tinymce.min.js" type="text/javascript" ></script>


        <script>
        /*jslint unparam: true */
        /*global window, $ */
        $(function () {
            'use strict';
            // Change this to the location of your server-side upload handler:
	   // cambio?

            $('#fileupload').fileupload({
                url: '<?=base_url()?>admin/socios/subir_imagen',
                dataType: 'json',
                done: function (e, data) {
                    $.each(data.result.files, function (index, file) {
                        //$('<p/>').text(file.name).appendTo('#files');
                        $("#my_result").html('<img src="<?=base_url()?>images/temp/'+file.name+'" width="100%">');
                        $('#progress .progress-bar').hide();
                    });
                },
                progressall: function (e, data) {
                    $('#progress .progress-bar').show();
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css(
                        'width',
                        progress + '%'
                    );
                }
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
        });
        </script>

        <script>
        /*jslint unparam: true */
        /*global window, $ */
        $(function () {
            'use strict';
            // Change this to the location of your server-side upload handler:

            $('#fileupload_mail').fileupload({
                url: '<?=base_url()?>admin/envios/subir_imagen',
                dataType: 'json',
                done: function (e, data) {
                    $.each(data.result.files, function (index, file) {
                        $("#my_result").html('<img src="<?=$baseurl?>images/temp/'+file.name+'" width="100%">');
                        $('#progress .progress-bar').hide();
                    });
                },
                progressall: function (e, data) {
                    $('#progress .progress-bar').show();
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css(
                        'width',
                        progress + '%'
                    );
                }
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
        });
        </script>


        <script type="text/javascript">

            <? if ( $section == 'socios-editar' || $section == 'socios-nuevo' ) { ?>
        	$("#valid_dni").click(function(){
            	var nsocio = $("#nro_socio").val();
            	var ndni = $("#dni").val();
            	var sid = $("#sid").val();
            	$.get("<?=$baseurl?>autocomplete/buscar_socio/dni/"+ndni,function(data){
			if(data){
				var socio = $.parseJSON(data);
				if ( sid == socio[0].id ) {
                    			$("#result_dni").addClass('hidden');
                
					$.get("<?=$baseurl?>autocomplete/buscar_socio/nro_socio/"+nsocio,function(data){
                        		if(data){
                                		var socio = $.parseJSON(data);
                                		if ( sid == socio[0].id ) {
                                                        $("#valid_dni").addClass('hidden');
                                                        $("#result_dni").addClass('hidden');
                                        		$("#result_nsoc").addClass('hidden');
							$("#result_OK").removeClass('hidden');
							$("#resto_datos").removeClass('hidden');
							$("#nro_socio").addClass('disabled');
							$("#dni").addClass('disabled');
                                		} else {
                                        		$("#result_nsoc").removeClass('hidden');
							$("#nro_socio").addClass('disabled');
							$("#dni").addClass('disabled');
							$("#result_OK").addClass('hidden');
                                        		$("#dni").focus();
                                		}
                        		}
                			})

				} else {
                    			$("#result_dni").removeClass('hidden');
					$("#result_OK").addClass('hidden');
                   			$("#dni").focus();
				}
			} else {
                                        $.get("<?=$baseurl?>autocomplete/buscar_socio/nro_socio/"+nsocio,function(data){
                                        if(data){
                                                var socio = $.parseJSON(data);
                                                if ( sid == socio[0].id ) {
                                                        $("#valid_dni").addClass('hidden');
                                                        $("#result_dni").addClass('hidden');
                                                        $("#result_nsoc").addClass('hidden');
                                                        $("#result_OK").removeClass('hidden');
                                                        $("#resto_datos").removeClass('hidden');
							$("#nro_socio").addClass('disabled');
							$("#dni").addClass('disabled');
                                                } else {
                                                        $("#result_nsoc").removeClass('hidden');
                                                        $("#result_OK").addClass('hidden');
                                                        $("#nro_socio").focus();
                                                }
                                        } else {
                                                        $("#valid_dni").addClass('hidden');
                                        		$("#result_dni").addClass('hidden');
                                        		$("#result_ndoc").addClass('hidden');
							$("#result_OK").removeClass('hidden');
							$("#resto_datos").removeClass('hidden');
							$("#nro_socio").addClass('disabled');
							$("#dni").addClass('disabled');
					}
                                        })

			}
            	})

		if ( nsocio == 0 ) {
                	var agree = confirm("Seguro que desea dar de alta un NO SOCIO?");
                	if(agree) {	
				$("#result_OK").addClass('hidden');
				$("#result_OK0").removeClass('hidden');
			} else {
                                $("#nro_socio").focus();
			}
		}
		

        	});

	   <? } ?>


            <? if($section=='estadisticas-facturacion'){ ?>

            Morris.Line({
              element: 'facturacion-mensual',
              data: <?=$facturacion_mensual?>,
              xkey: 'y',
              ykeys: ['a','b'],
              labels: ['Facturación','Pagos']
            });

            Morris.Line({
              element: 'facturacion-anual',
              data: <?=$facturacion_anual?>,
              xkey: 'y',
              ykeys: ['a','b'],
              labels: ['Facturación','Pagos']
            });

            <? }else if($section=='estadisticas-actividades'){ ?>

            Morris.Bar({
                element: 'actividades-mensual',
                data: <?=$actividades_mensual['data']?>,
                xkey: 'y',
                ykeys: <?=$actividades_mensual['keys']?>,
                labels: <?=$actividades_mensual['labels']?>,
                stacked: true
            });

            Morris.Bar({
                element: 'actividades-anual',
                data: <?=$actividades_anual['data']?>,
                xkey: 'y',
                ykeys: <?=$actividades_anual['keys']?>,
                labels: <?=$actividades_anual['labels']?>,
                stacked: true
            });
            <? } ?>
            function check_eliminar_rifa(){
                var agree = confirm("Seguro que desea eliminar esta Rifa?");
                if(agree){return true}else{return false}
            }
            function check_eliminar_act(){
                var agree = confirm("Seguro que desea eliminar esta Actividad?");
                if(agree){return true}else{return false}
            }
            function check_eliminar_socio(){
                var agree = confirm("Seguro que desea eliminar este Socio?");
                if(agree){return true}else{return false}
            }
            $(document).ready(function()
            {
                $("div#socio_desc").each(function(){
                    var id = $(this).data('id');
                    if($(this).height() >= 24){
                        $(this).addClass('socios_desc');
                    }else{
                        $("a#ver_mas[data-id="+id+"]").addClass("hidden");
                    }
                })
                $("a#ver_mas").click(function(){
                    var id = $(this).data('id');
                    var toggle = $(this).data('toggle');
                    if(toggle == '0'){
                        $("div[data-id="+id+"]").removeClass('socios_desc');
                        $(this).data('toggle','1');
                        $(this).text('Ver Menos');
                    }else{
                        $("div[data-id="+id+"]").addClass('socios_desc');
                        $(this).data('toggle','0');
                        $(this).text('Ver Más');
                    }
                })
                $("a#btn-eliminar-socio").click(function(){
                    var agree = confirm("Seguro que desea eliminar este Socio?");
                    if(agree){return true}else{return false}
                })
                $("a#btn-eliminar-profesor").click(function(){
                    var agree = confirm("Seguro que desea eliminar este usuario de Comisión?");
                    if(agree){return true}else{return false}
                })
                $("a#btn-eliminar-lugar").click(function(){
                    var agree = confirm("Seguro que desea eliminar este Lugar?");
                    if(agree){return true}else{return false}
                })
                $("a#btn-eliminar-actividad").click(function(){
                    var agree = confirm("Seguro que desea eliminar esta Actividad?");
                    if(agree){return true}else{return false}
                })



                $('a#cliente_info_toogle').click(function(){
                   // $(this).parent().parent().next().toggle();
                    if($(this).hasClass("fa-plus-square-o")){
                        $(this).removeClass();
                        $(this).addClass("fa-minus-square-o");
                    }else{
                        $(this).removeClass();
                        $(this).addClass("fa-plus-square-o");
                    }
                });

                $('#btn-meses').click(function(){
                    $('#morosos-opt').hide(function(){
                    $('#morosos-meses').show();
                });



            })

                $('#btn-act').click(function(){
                    $('#morosos-opt').hide(function(){

                    $('#morosos-act').show();
                    });

                })

                $('button#morosos-cancel').click(function(){
                    $('#morosos-meses').hide();
                    $('#morosos-act').hide(function(){

                    $('#morosos-opt').show();
                    });

                })

                $("button#morosos-ver").click(function(){
                    $('#table-morosos').show();
                })

		$("#save_btn").click(function() {
            var fecha = $("#fechan").val();
            var nacimiento = new Date(fecha);
            var hoy = new Date();
			<?  $hoy=date('Y-m-d'); ?>
		    var edad = ( hoy - nacimiento ) / ( 365 * 24 * 60 * 60 * 1000 );

		    if ( fecha == 0 || fecha == "0000-00-00" ) {
			    alert ("Error en la fecha de nacimiento, no puede ser 0");
			    return false;
		    };
            if ( fecha > '<?=$hoy?>' ) {
                alert ("Error en la fecha de nacimiento, no puede ser mayor a hoy");
                return false;
            };
            var tipo_cat = $("#s_cate").find(':selected').attr('data-tipo');
            if ( tipo_cat == "0" ) {
                alert ("Tiene que seleccionar una categoria");
                return false;
	    }
            if ( tipo_cat == "m" && edad > 18 ) {
                alert ("Error no puede ser categoria menor, tiene mas de 18 años");
                return false;
            };
            if ( tipo_cat == "M" && edad < 18 ) {
                alert ("Error no puede ser categoria mayor, tiene menos de 18 años");
                return false;
            };
            var nsocio = $("#nro_socio").val();
	    if ( nsocio == 0 && tipo_cat != "N" ) {
                alert ("Tiene que seleccionar categoria NO SOCIO!!!!");
                return false;
	    }
            var mail = $("#mail").val();
            var accion = $(this).data('accion');
            var param = encodeURIComponent(mail);
            	   $.get("<?=$baseurl?>admin/socios/valid_mail/"+param,function(data){
			if ( data ) {
				var validmail = $.parseJSON(data);
				var vm = Object.values(validmail);
				if ( !vm || vm[1] == false ) {
                        		alert ("Direccion de correo no valida");
                        		return false;
				};
				if ( vm[1] == true ) {
                    if ( accion == "alta_socio" ) {
                        $("#form_alta").submit();
                    } else {
                        $("#edit_socio").submit();
                    }
				}
			} else {
                        	alert ("Direccion de correo no valida");
				return false;
			};
		   })


		});

                <?
                if($section == 'socios-editar'){
                ?>
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
                <?
                }
                ?>

                $('#conf-tab a').click(function (e) {
                  e.preventDefault()
                  $(this).tab('show')
                })
            })
        </script>

        <? if($section == 'socios-nuevo' || ($section == 'socios-editar' && $socio) ){ ?>
        <script language="JavaScript">
        Webcam.setSWFLocation("<?=$baseurl?>scripts/webcam.swf");
        Webcam.attach( '#my_camera' );

	<!-- Configure a few settings and attach camera -->
 	Webcam.set({
  		width: 320,
  		height: 240,
  		image_format: 'jpeg',
  		jpeg_quality: 90
 	});
 	Webcam.attach( '#my_camera' );

	<!-- Code to handle taking the snapshot and displaying it locally -->
	function take_snapshot() {
            $("#save_btn").attr("disabled", "disabled");
            $("#save_btn").html("Guardando imagen...");
 
 		// take snapshot and get image data
 		Webcam.snap( function(data_uri) {
  		// display results in page
  		document.getElementById('my_result').innerHTML = 
  		'<img id="imageprev" src="'+data_uri+'"/>';
  		} );

	var base64image = document.getElementById("imageprev").src;
        Webcam.upload( base64image, '<?=$baseurl?>admin/socios/agregar_imagen', function(code, text) {
                $("#save_btn").removeAttr("disabled");
                $("#save_btn").html("Guardar");
                // 'code' will be the HTTP response code from the server, e.g. 200
                // 'text' will be the raw response content
        } );

	}


        a= $('#nombre').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/search/socios-nombre' });
        a= $('#apellido').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/search/socios-apellido'  });
        a= $('#localidad').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/search/socios-localidad' });
        a= $('#nacionalidad').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/search/socios-nacionalidad' });

        $("a#r-buscar").click(function(){
            var id = $(this).data('id');
            $("#"+id+"-loading").removeClass('hidden');
            var dni = $("#"+id).val();
            $.get("<?=$baseurl?>autocomplete/buscar_socio/dni/"+dni,function(data){
                $("#"+id+"-loading").addClass('hidden');
                if(data){
                    var socio = $.parseJSON(data);
                    var close_link = '<a href="#" onclick="cleear(\''+id+'\')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>'
                    $("#"+id+"-result").html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+socio[0].nro_socio+'-'+socio[0].nombre+' '+socio[0].apellido+' ('+socio[0].dni+') '+close_link);
                    $("#"+id+"-data").addClass('hidden');
                    $("#"+id+"-result").removeClass('hidden');
                    $("#tutor_sid").val(socio[0].id);
                    $("#"+id+"-id").val(socio[0].id);
                }else{
                   angular.element("#modal_open").triggerHandler('click');
                   $("#tutor-dni").val($("#"+id).val());
                   $("#tutor-nombre").focus();
                   $("#form-tutor").data("id",id);
                }
            })
        })
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
                    $("#"+id+"-result").html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+socio.nro_socio+'-'+socio.nombre+' '+socio.apellido+' ('+socio.dni+') '+close_link);
                    $("#"+id+"-data").addClass("hidden");
                    $("#"+id+"-result").removeClass("hidden");
                    $("#"+id+"-id").val(socio.id);
                }
            })
        }
        function cleear(id){
            $("#"+id+"-data").removeClass('hidden');
            $("#tutor_sid").val(0);
            $("#tutor_dni").val(0);
            $("#"+id+"-result").addClass('hidden');
        }
        </script>
        <? } ?>
        <? if($section == 'actividades-asociar'){ ?>
        <script type="text/javascript">
        $(document).ready(function(){
            a= $('#r2').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/get/socios-dni|nombre|apellido',
                onSelect: function (suggestion) {
                $('#r2').val(suggestion.data);
                }
            });

        })
        a= $('#activ').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/search/socios-nombre' });


        $("#activ_asoc_form").submit(function(){
            var id = 'r2';
            $("#"+id+"-loading").removeClass('hidden');
            var dni = $("#"+id).val();
            $.get("<?=$baseurl?>autocomplete/buscar_socio/dni/"+dni,function(data){
                $("#"+id+"-loading").addClass('hidden');
                if(data){
                    var socio = $.parseJSON(data);
                    var close_link = '<a href="#" onclick="cleear(\''+id+'\')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>'
                    $("#"+id+"-result").html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+socio[0].nro_socio+'-'+socio[0].nombre+' '+socio[0].apellido+' ('+socio[0].dni+') '+close_link);
                    $("#"+id+"-data").addClass('hidden');
                    $("#"+id+"-result").removeClass('hidden');

                    $("#acceso_editar").attr('href','<?=$baseurl?>admin/socios/editar/'+socio[0].id);
                    $("#acceso_cupon").attr('href','<?=$baseurl?>admin/pagos/cupon/'+socio[0].id);
                    $("#acceso_ver_resumen").attr('href','<?=$baseurl?>admin/socios/resumen/'+socio[0].id);
                    $("#acceso_pago").attr('href','<?=$baseurl?>admin/pagos/registrar/'+socio[0].id);
                    $("#acceso_deuda").attr('href','<?=$baseurl?>admin/pagos/deuda/'+socio[0].id);
                    $("#acceso_resumen").attr('href','<?=$baseurl?>admin/socios/enviar_resumen/'+socio[0].id);
                    $("#accesos_directos").removeClass('hidden');


                    $("#"+id+"-id").val(socio[0].id);
                    get_actividades(socio[0].id);
                }else{
                   alert("El DNI ingresado no se encuentra en la Base de Datos.")
                }
            })
        })
        function get_actividades(id){
            $("#"+id+"-loading").removeClass('hidden');
            $.get( "<?=$baseurl?>admin/actividades/get/"+id ).done(function(data){
                $("#asociar-div").html(data);
                $("#asociar-div").slideDown();
                $("#"+id+"-loading").addClass('hidden');
            })

        }
        function cleear(id){
            $("#"+id+"-data").removeClass('hidden');
            $("#"+id+"-id").val('0');
            $("#"+id+"-result").addClass('hidden');
            $("#asociar-div").slideUp();
            $("#accesos_directos").addClass('hidden');
        }



        <? if($socio->id && $socio->id != 0){ ?> $("#asociar-div").slideDown(); get_actividades("<?=$socio->id?>"); <? } ?>
        </script>
        <? } ?>
        <? if($section == 'pagos-cupon'){ ?>
        <script type="text/javascript">
        $(document).ready(function(){
            a= $('#r2').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/get/socios-dni|nombre|apellido',
                onSelect: function (suggestion) {
                $('#r2').val(suggestion.data);
                }
            });
        })
        a= $('#activ').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/search/socios-nombre' });
        $("#gen_cupon_form").submit(function(){
            var id = 'r2';
            $("#"+id+"-loading").removeClass('hidden');
            var dni = $("#"+id).val();
            $.get("<?=$baseurl?>autocomplete/buscar_socio/dni/"+dni,function(data){
                $("#"+id+"-loading").addClass('hidden');
                if(data){
                    var socio = $.parseJSON(data);
                    var close_link = '<a href="#" onclick="cleear(\''+id+'\')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>'
                    $("#"+id+"-result").html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+socio[0].nro_socio+'-'+socio[0].nombre+' '+socio[0].apellido+' ('+socio[0].dni+') '+close_link);
                    $("#"+id+"-data").addClass('hidden');
                    $("#"+id+"-result").removeClass('hidden');
                    $("#"+id+"-id").val(socio[0].id);

                    $("#acceso_editar").attr('href','<?=$baseurl?>admin/socios/editar/'+socio[0].id);
                    $("#acceso_actividad").attr('href','<?=$baseurl?>admin/actividades/asociar/'+socio[0].id);
                    $("#acceso_ver_resumen").attr('href','<?=$baseurl?>admin/socios/resumen/'+socio[0].id);
                    $("#acceso_pago").attr('href','<?=$baseurl?>admin/pagos/registrar/'+socio[0].id);
                    $("#acceso_deuda").attr('href','<?=$baseurl?>admin/pagos/deuda/'+socio[0].id);
                    $("#acceso_resumen").attr('href','<?=$baseurl?>admin/socios/enviar_resumen/'+socio[0].id);
                    $("#accesos_directos").removeClass('hidden');

                    get_cupon(socio[0].id);
                }else{
                    alert("El DNI ingresado no se encuentra en la Base de Datos.")
                }
            })
        })
        function get_cupon(id){
             $("#cupon-div").html('<i class="fa fa-spinner fa-spin"></i> Cargando...');
            $.get( "<?=$baseurl?>admin/pagos/cupon/get/"+id ).done(function(data){
                $("#cupon-div").html(data);
                $("#cupon-div").slideDown();

            })

        }

        <? if($socio->id && $socio->id != 0){ ?> $("#cupon-div").slideDown(); get_cupon("<?=$socio->id?>"); <? } ?>
        function cleear(id){
            $("#"+id+"-data").removeClass('hidden');
            $("#"+id+"-id").val('0');
            $("#"+id+"-result").addClass('hidden');
            $("#cupon-div").slideUp();
             $("#accesos_directos").addClass('hidden');
        }
        </script>
        <? } ?>
        <? if($section == 'pagos-registrar'){ ?>

        <script language="JavaScript">

        $(document).ready(function(){
            a= $('#r2').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/get/socios-dni|nombre|apellido',
                onSelect: function (suggestion) {
                $('#r2').val(suggestion.data);
                }
            });

            $("#pagos_reg_form").submit(function(){
                var id = 'r2';
                $("#"+id+"-loading").removeClass('hidden');
                var dni = $("#"+id).val();
                $.get("<?=$baseurl?>autocomplete/buscar_socio/dni/"+dni,function(data){
                    $("#"+id+"-loading").addClass('hidden');
                    if(data){
                        var socio = $.parseJSON(data);
                        var close_link = '<a href="#" onclick="cliar(\''+id+'\')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>'
                        $("#"+id+"-result").html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+socio[0].nro_socio+'-'+socio[0].nombre+' '+socio[0].apellido+' ('+socio[0].dni+') '+close_link);
                        $("#"+id+"-data").addClass('hidden');
                        $("#"+id+"-result").removeClass('hidden');
                        $("#"+id+"-id").val(socio[0].id);

                        $("#acceso_editar").attr('href','<?=$baseurl?>admin/socios/editar/'+socio[0].id);
                        $("#acceso_actividad").attr('href','<?=$baseurl?>admin/actividades/asociar/'+socio[0].id);
                        $("#acceso_ver_resumen").attr('href','<?=$baseurl?>admin/socios/resumen/'+socio[0].id);
                        $("#acceso_cupon").attr('href','<?=$baseurl?>admin/pagos/cupon/'+socio[0].id);
                        $("#acceso_deuda").attr('href','<?=$baseurl?>admin/pagos/deuda/'+socio[0].id);
                        $("#acceso_resumen").attr('href','<?=$baseurl?>admin/socios/enviar_resumen/'+socio[0].id);
                        $("#accesos_directos").removeClass('hidden');

                        get_pago(socio[0].id);
                    }else{
                        alert("El DNI ingresado no se encuentra en la Base de Datos.")
                    }
                })
            })
            function get_pago(id){
                 $("#pago-div").html('<i class="fa fa-spinner fa-spin"></i> Cargando...');
                 $.get( "<?=$baseurl?>admin/pagos/registrar/get/"+id ).done(function(data){
                    $("#pago-div").html(data);
                    $("#pago-div").slideDown();

                })

            }

	})
         </script>
	<? } ?>

        <? if($section == 'debtarj-nuevo-get'){ ?>

        <script language="JavaScript">

        $(document).ready(function(){
            a= $('#r2').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/get/socios-dni|nombre|apellido',
                onSelect: function (suggestion) {
                $('#r2').val(suggestion.data);
                }
            });

            $("#debtarj_reg_form").submit(function(){
                var id = 'r2';
                $("#"+id+"-loading").removeClass('hidden');
                var dni = $("#"+id).val();
                $.get("<?=$baseurl?>autocomplete/buscar_socio/dni/"+dni,function(data){
                    $("#"+id+"-loading").addClass('hidden');
                    if(data){
                        var socio = $.parseJSON(data);
                        var close_link = '<a href="#" onclick="cliar(\''+id+'\')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>'
                        $("#"+id+"-result").html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+socio[0].nro_socio+'-'+socio[0].nombre+' '+socio[0].apellido+' ('+socio[0].dni+') '+close_link);
                        $("#"+id+"-data").addClass('hidden');
                        $("#"+id+"-result").removeClass('hidden');
                        $("#"+id+"-id").val(socio[0].id);

                        $("#acceso_editar").attr('href','<?=$baseurl?>admin/socios/editar/'+socio[0].id);
                        $("#acceso_actividad").attr('href','<?=$baseurl?>admin/actividades/asociar/'+socio[0].id);
                        $("#acceso_ver_resumen").attr('href','<?=$baseurl?>admin/socios/resumen/'+socio[0].id);
                        $("#acceso_cupon").attr('href','<?=$baseurl?>admin/pagos/cupon/'+socio[0].id);
                        $("#acceso_deuda").attr('href','<?=$baseurl?>admin/pagos/deuda/'+socio[0].id);
                        $("#acceso_resumen").attr('href','<?=$baseurl?>admin/socios/enviar_resumen/'+socio[0].id);
                        $("#accesos_directos").removeClass('hidden');

                        get_debtarj(socio[0].id);
                    }else{
                        alert("El DNI ingresado no se encuentra en la Base de Datos.")
                    }
                })
            })
            function get_debtarj(id){
                 $("#debtarj-div").html('<i class="fa fa-spinner fa-spin"></i> Cargando...');
                 $.get( "<?=$baseurl?>admin/debtarj/get/"+id ).done(function(data){
                    $("#debtarj-div").html(data);
                    $("#debtarj-div").slideDown();

                })

            }

        })

            <? if($socio->id && $socio->id != 0){ ?> $("#pago-div").slideDown(); get_pago("<?=$socio->id?>"); <? } ?>



        function cliar(id){
                $("#"+id+"-data").removeClass('hidden');
                $("#"+id+"-id").val('0');
                $("#"+id+"-result").addClass('hidden');
                $("#pago-div").slideUp();
                 $("#accesos_directos").addClass('hidden');
            }

            $("#cuotas").keyup(function(){
                if($("#cuotas").val() && $("#monto").val()){
                    if($.isNumeric($("#cuotas").val()) && $.isNumeric($("#monto").val())){
                        if($("#cuotas").val() <= 0){
                            alert("Ingrese un numero mayor que 0");
                            return false;
                        }
                            var valor_cuota = $("#monto").val()/$("#cuotas").val();

                                $("#valor-cuota").val(valor_cuota);
                    }else{
                        alert("Por Favor Ingrese solo Números en los campos Monto y Cantidad de Cuotas");
                    }
                }
            })
         </script>
        <? } ?>

        <? if($section == 'pagos-deuda'){ ?>

        <script language="JavaScript">

        $(document).ready(function(){
            a= $('#r2').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/get/socios-dni|nombre|apellido',
                onSelect: function (suggestion) {
                $('#r2').val(suggestion.data);
                }
            });

            $("#pagos_deuda_form").submit(function(){
                var id = 'r2';
                $("#"+id+"-loading").removeClass('hidden');
                var dni = $("#"+id).val();
                $.get("<?=$baseurl?>autocomplete/buscar_socio/dni/"+dni,function(data){
                    $("#"+id+"-loading").addClass('hidden');
                    if(data){
                        var socio = $.parseJSON(data);
                        var close_link = '<a href="#" onclick="cleear(\''+id+'\')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>'
                        $("#"+id+"-result").html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+socio[0].nro_socio+'-'+socio[0].nombre+' '+socio[0].apellido+' ('+socio[0].dni+') '+close_link);
                        $("#"+id+"-data").addClass('hidden');
                        $("#"+id+"-result").removeClass('hidden');
                        $("#"+id+"-id").val(socio[0].id);

                        $("#acceso_editar").attr('href','<?=$baseurl?>admin/socios/editar/'+socio[0].id);
                        $("#acceso_actividad").attr('href','<?=$baseurl?>admin/actividades/asociar/'+socio[0].id);
                        $("#acceso_ver_resumen").attr('href','<?=$baseurl?>admin/socios/resumen/'+socio[0].id);
                        $("#acceso_cupon").attr('href','<?=$baseurl?>admin/pagos/cupon/'+socio[0].id);
                        $("#acceso_pago").attr('href','<?=$baseurl?>admin/pagos/registrar/'+socio[0].id);
                        $("#acceso_resumen").attr('href','<?=$baseurl?>admin/socios/enviar_resumen/'+socio[0].id);
                        $("#accesos_directos").removeClass('hidden');

                        get_pago(socio[0].id);
                    }else{
                        alert("El DNI ingresado no se encuentra en la Base de Datos.")
                    }
                })
            })
            function get_pago(id){
                 $("#deuda-div").html('<i class="fa fa-spinner fa-spin"></i> Cargando...');
                 $.get( "<?=$baseurl?>admin/pagos/deuda/get/"+id ).done(function(data){
                    $("#deuda-div").html(data);
                    $("#deuda-div").slideDown();

                })

            }



            <? if($socio->id && $socio->id != 0){ ?> $("#pago-div").slideDown(); get_pago("<?=$socio->id?>"); <? } ?>


        })

        function cleear(id){
                $("#"+id+"-data").removeClass('hidden');
                $("#"+id+"-id").val('0');
                $("#"+id+"-result").addClass('hidden');
                $("#deuda-div").slideUp();
                 $("#accesos_directos").addClass('hidden');
            }


         </script>
        <? } ?>

        <? if($section == 'pagos-facturacion'){ ?>
        <script type="text/javascript" src="<?=$baseurl?>scripts/notifIt.js"></script>
            <script type="text/javascript">
                $("#generar").click(function(){
                    notif({
                        msg: "<b>Alerta :</b> Recuerde ingresar en la opción <a href='<?=$baseurl?>admin/pagos/facturacion'>Pagos -> Facturacion Mensual</a> para generar la facturación del mes actual.",
                        type: "warning",
                        width: "all",
                        opacity: "0.8",
                        color: "#FFF",
                        autohide: false
                    });
                })
            </script>
        <? } ?>
        <? if($section == 'socios-resumen'){ ?>
        <script type="text/javascript">
        $(document).ready(function(){
            a= $('#r2').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/get/socios-dni|nombre|apellido',
                onSelect: function (suggestion) {
                $('#r2').val(suggestion.data);
                }
            });

        })
        a= $('#activ').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/search/socios-nombre' });
        $("#buscar_resumen").submit(function(e){
        //$("a#r-buscar").click(function(){
            var id = 'r2';
            $("#"+id+"-loading").removeClass('hidden');
            var dni = $("#"+id).val();
            $.get("<?=$baseurl?>autocomplete/buscar_socio/dni/"+dni,function(data){
                $("#"+id+"-loading").addClass('hidden');
                if(data){
                    var socio = $.parseJSON(data);
                    var close_link = '<a href="#" onclick="cleear(\''+id+'\')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>'
                    $("#"+id+"-result").html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+socio[0].nro_socio+'-'+socio[0].nombre+' '+socio[0].apellido+' ('+socio[0].dni+') '+close_link);
                    $("#"+id+"-data").addClass('hidden');
                    $("#"+id+"-result").removeClass('hidden');

                    $("#acceso_editar").attr('href','<?=$baseurl?>admin/socios/editar/'+socio[0].id);
                    $("#acceso_cupon").attr('href','<?=$baseurl?>admin/pagos/cupon/'+socio[0].id);
                    $("#acceso_actividad").attr('href','<?=$baseurl?>admin/actividades/asociar/'+socio[0].id);
                    $("#acceso_pago").attr('href','<?=$baseurl?>admin/pagos/registrar/'+socio[0].id);
                    $("#acceso_deuda").attr('href','<?=$baseurl?>admin/pagos/deuda/'+socio[0].id);
                    $("#acceso_resumen").attr('href','<?=$baseurl?>admin/socios/enviar_resumen/'+socio[0].id);
                    $("#accesos_directos").removeClass('hidden');

                    $("#"+id+"-id").val(socio[0].id);
                    get_actividades(socio[0].id);
                }else{
                   alert("El DNI ingresado no se encuentra en la Base de Datos.")
                }
            })
            e.preventDefault();
            return false;
        })

        function get_actividades(id){
            $("#"+id+"-loading").removeClass('hidden');
            $.get( "<?=$baseurl?>admin/socios/resumen2/"+id ).done(function(data){
                $("#asociar-div").html(data);
                $("#asociar-div").slideDown();
                $("#"+id+"-loading").addClass('hidden');
            })

        }
        function cleear(id){
            $("#"+id+"-data").removeClass('hidden');
            $("#"+id+"-id").val('0');
            $("#"+id+"-result").addClass('hidden');
            $("#asociar-div").slideUp();
            $("#accesos_directos").addClass('hidden');
        }



        <? if($socio->id && $socio->id != 0){ ?> $("#asociar-div").slideDown(); get_actividades("<?=$socio->id?>"); <? } ?>
        </script>
        <? } ?>
<!--

-->

        <? if($section == 'pagos-editar'){ ?>

        <script language="JavaScript">

        $(document).ready(function(){
            a= $('#r2').autocomplete({ serviceUrl:'<?=$baseurl?>autocomplete/get/socios-dni|nombre|apellido',
                onSelect: function (suggestion) {
                $('#r2').val(suggestion.data);
                }
            });

            $("#pagos_deuda_form").submit(function(){
                var id = 'r2';
                $("#"+id+"-loading").removeClass('hidden');
                var dni = $("#"+id).val();
                $.get("<?=$baseurl?>autocomplete/buscar_socio/dni/"+dni,function(data){
                    $("#"+id+"-loading").addClass('hidden');
                    if(data){
                        var socio = $.parseJSON(data);
                        var close_link = '<a href="#" onclick="cleear(\''+id+'\')" title="Quitar" style="color:#F00"><i class="fa fa-times" ></i></a>'
                        $("#"+id+"-result").html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'+socio[0].nro_socio+'-'+socio[0].nombre+' '+socio[0].apellido+' ('+socio[0].dni+') '+close_link);
                        $("#"+id+"-data").addClass('hidden');
                        $("#"+id+"-result").removeClass('hidden');
                        $("#"+id+"-id").val(socio[0].id);

                        $("#acceso_editar").attr('href','<?=$baseurl?>admin/socios/editar/'+socio[0].id);
                        $("#acceso_actividad").attr('href','<?=$baseurl?>admin/actividades/asociar/'+socio[0].id);
                        $("#acceso_ver_resumen").attr('href','<?=$baseurl?>admin/socios/resumen/'+socio[0].id);
                        $("#acceso_cupon").attr('href','<?=$baseurl?>admin/pagos/cupon/'+socio[0].id);
                        $("#acceso_pago").attr('href','<?=$baseurl?>admin/pagos/registrar/'+socio[0].id);
                        $("#acceso_resumen").attr('href','<?=$baseurl?>admin/socios/enviar_resumen/'+socio[0].id);
                        $("#accesos_directos").removeClass('hidden');

                        get_pago(socio[0].id);
                    }else{
                        alert("El DNI ingresado no se encuentra en la Base de Datos.")
                    }
                })
            })
            function get_pago(id){
                 $("#deuda-div").html('<i class="fa fa-spinner fa-spin"></i> Cargando...');
                 $.get( "<?=$baseurl?>admin/pagos/get_pagos/"+id ).done(function(data){
                    $("#deuda-div").html(data);
                    $("#deuda-div").slideDown();

                })

            }

        })


            <? if($socio->id && $socio->id != 0){ ?> $("#pago-div").slideDown(); get_pago("<?=$socio->id?>"); <? } ?>



        function cleear(id){
                $("#"+id+"-data").removeClass('hidden');
                $("#"+id+"-id").val('0');
                $("#"+id+"-result").addClass('hidden');
                $("#deuda-div").slideUp();
                 $("#accesos_directos").addClass('hidden');
            }


         </script>
        <? } ?>

<script type="text/javascript">
function fecha_db_mostrar($fecha) {
        $anio = $fecha.substring(0,4);
        $mes = $fecha.substring(5,7);
        $dia = $fecha.substring(8,10);

	return $dia.concat("-",$mes,"-",$anio);
}

function fecha_mostrar_db($fecha) {
        $anio = $fecha.substring(6,10);
        $mes = $fecha.substring(3,5);
        $dia = $fecha.substring(0,2);

	return $anio.concat("-",$mes,"-",$dia);
}

$("#load-asoc-activ-form select").on('change',function(){
        switch ( $(this).val() ) {
                case 'txt':
                        $("#archivo-form").removeClass('hidden');
                        break;
                case 'bd':
                        $("#archivo-form").addClass('hidden');
                        break;
        }
})

$("#load-asoc-activ-form").submit(function(){
        var actividad = $("#actividad").val();
        var fuente = $("#fuente").val();
	if ( fuente == "" ) {
		alert("Debe elegir fuente");
		return false;
	}
	if ( fuente == "bd" ) {
        	var url = "<?=$baseurl?>admin/actividades/subearchivo" + "/" + actividad + "/" + fuente;
	} else {
        	var archivo = $("#userfile").val();
		if ( !archivo ) {
			alert("Debe elegir archivo");
			return false;
		}
        	var dato1col = $("#dato1col").val();
		if ( dato1col == "" ) {
			alert ("Debe seleccionar clave de 1 columna del archivo");
			return false;
		}
        	var url = "<?=$baseurl?>admin/actividades/subearchivo" + "/" + actividad + "/" + fuente + "/" + dato1col;
	}


        $("#load-asoc-activ-form").attr("action",url);

        $("#load-asoc-activ-form").submit();

	return true;

})

$("#estad-activ-form").submit(function(){
        var actividad = $("#actividad").val();
        var url = "<?=$baseurl?>admin/estadisticas/cobranza_act" + "/" + actividad;

        $("#estad-activ-form").attr("action",url);
        $("#estad-activ-form").submit();

        return true;
})
$("#estad-comi-form").submit(function(){
        var comision = $("#comision").val();
        var url = "<?=$baseurl?>admin/estadisticas/cobranza_comi" + "/" + comision;

        $("#estad-comi-form").attr("action",url);
        $("#estad-comi-form").submit();

        return true;
})

$("button#estad_ing_excel").click(function(){
	$("#arma_excel").val('1');
        var mes = $("#meses").val();
        var url = "<?=$baseurl?>admin/estadisticas/ingresos" + "/" + mes;

        $("#estad-ing-form").attr("action",url);
        $("#estad-ing-form").submit();
	return true;
})

$("button#estad_act_excel").click(function(){
	$("#arma_excel").val('1');
        var actividad = $("#actividad").val();
        var url = "<?=$baseurl?>admin/estadisticas/cobranza_act" + "/" + actividad;

        $("#estad-activ-form").attr("action",url);
        $("#estad-activ-form").submit();
	return true;
})

$("button#estad_comi_excel").click(function(){
	$("#arma_excel").val('1');
        var comision = $("#comision").val();
        var url = "<?=$baseurl?>admin/estadisticas/cobranza_comi" + "/" + comision;

        $("#estad-comi-form").attr("action",url);
        $("#estad-comi-form").submit();
	return true;
})

$("button#btn_profesor").click(function(){
	var mail = $("#mail").val();
	var param = encodeURIComponent(mail);
	var accion = $(this).data('accion');
        $("#reg-cargando").removeClass('hidden');
	$.get("<?=$baseurl?>admin/socios/valid_mail/"+param,function(data){
		if ( data ) {
			var validmail = $.parseJSON(data);
			var vm = Object.values(validmail);
			if ( !vm || vm[1] == false ) {
				alert ("Direccion de correo no valida");
        			$("#reg-cargando").addClass('hidden');
				return false;
			};
			if ( vm[1] == true ) {
				if ( accion == "alta_comi" ) {
					$("#altacomi").submit();
				} else {
					$("#edicomi").submit();
				}
			}
		} else {
			alert ("Direccion de correo no valida");
        		$("#reg-cargando").addClass('hidden');
			return false;
		};
	})
})

$("button#btn_admin").click(function(){
	var mail = $("#mail").val();
	var param = encodeURIComponent(mail);
	var accion = $(this).data('accion');
        $("#reg-cargando").removeClass('hidden');
	$.get("<?=$baseurl?>admin/socios/valid_mail/"+param,function(data){
		if ( data ) {
			var validmail = $.parseJSON(data);
			var vm = Object.values(validmail);
			if ( !vm || vm[1] == false ) {
				alert ("Direccion de correo no valida");
        			$("#reg-cargando").addClass('hidden');
				return false;
			};
			if ( vm[1] == true ) {
        			$("#reg-cargando").removeClass('hidden');
				if ( accion == 'agregar_admin' ) {
					$("#alta_admin").submit();
				} else {
					$("#edi_admin").submit();
				}
			}
		} else {
			alert ("Direccion de correo no valida");
        			$("#reg-cargando").addClass('hidden');
			return false;
		};
	})
})

$("button#btn_procesar").click(function(){
        $("#arma_excel").val('0');
        return true;
})

$("#comi-activ-form").submit(function(){
        var actividad = $("#actividad").val();
        var url = "<?=$baseurl?>comisiones/facturacion" + "/" + actividad;

        $("#comi-activ-form").attr("action",url);
        $("#comi-activ-form").submit();

        return true;
})



</script>


        <script type="text/javascript">
        <? /**




        **/ ?>


        $(document).on('click','#eliminar_pago',function(){
            var agree = confirm("Seguro que desea eliminar este pago?");
            if(!agree){return false;}
        })

        $("#filtro_morosos").click(function(){
            console.log('asd');
            var meses = $("#morosos_meses").val();
            var activ = $("#morosos_activ").val();
            document.location.href='<?=base_url()?>admin/pagos/'+meses+'/'+activ;
        })

        $(document).on("click","#valor_cuota",function(){
            $("#detalle_de_cuota").append('<i class="fa fa-spin fa-spinner"></i> Cargando...');
            angular.element("#detalle_de_cuota").append('click');
            angular.element("#modal_open").triggerHandler('click');
        })

        $("#imprimir_listado_morosos").click(function(){
            var meses = $(this).data('meses');
            var act = $(this).data('act');
            window.open('<?=base_url()?>imprimir/morosos/'+meses+'/'+act,'','width=800,height=600');
        })

        $(document).on("click","#imprimir_listado_actividades",function(){
            var act = $(this).data('act');
            window.open('<?=base_url()?>imprimir/listado/actividades/'+act,'','width=800,height=600');
        })

        $(document).on("click","#imprimir_carnet",function(){
            var id = $(this).data('id');
            window.open('<?=base_url()?>imprimir/carnet/'+id,'','menubar=yes,toolbar=yes,width=800,height=600');
        })

        $(document).on("click","#imprimir_carnets_lote",function(){
            var id = $(this).data('id');
            window.open('<?=base_url()?>imprimir/carnets/','','menubar=yes,toolbar=yes,width=800,height=600');
        })

        $("#grupo-select").change(function(){
            var grupo = $(this).val();
            $("#grupo-categorias").slideUp();
            $("#grupo-actividades").slideUp();
            $("#grupo-comisiones").slideUp();

		if ( grupo == "soccomision" || grupo == "titcomision" ) {
            		$("#grupo-comisiones").slideDown();
		}

            $("#grupo-"+grupo).slideDown();
        })
        <?
        if($this->uri->segment(3) == 'nuevo'){
        ?>
        $("#envios-step1").submit(function(){
            $("#envios-continuar").prop('disabled',true);
            $("#envios-continuar").html('Procesando <i class="fa fa-spin fa-spinner"></i>')
            var activ = $("#activ-select").val();
	    if ( activ == " " ) {
		alert ("Debe Elegir una condicion de estado");
            	$("#envios-continuar").prop('disabled',false);
            	$("#envios-continuar").html('Procesando <i class="fa fa-arrow-right"></i>')
		return false;
		}
            var grupo = $("#grupo-select").val();
            var data;
            var titulo = $("#envio-titulo").val();
	    if ( grupo == "soccomision" || grupo == "titcomision" ) {
			data = $("#comisiones-select").val();
		} else {
            		data = $("#"+grupo+"-select").val();
		}

            $.post("<?=base_url()?>admin/envios/agregar",{titulo:titulo,grupo:grupo,data:data,activ:activ})
            .done(function(data){
                if(data == 'no_mails'){
                    $("#step2").html('<div class="alert alert-danger">No se encontraron socios para el grupo seleccionado.</div>');
                    $("#envios-continuar").prop('disabled',false);
                    $("#envios-continuar").html('Continuar <i class="fa fa-arrow-right"></i>')
                }else{
                    $("#step2").html(data);
                    $("#envios-step1").slideUp();
                }
            })
        })
        $(document).on("submit","#envios-step2",function(e){
            var text = tinyMCE.activeEditor.getContent();
            var id = $("#envio_id").val();
            $.post("<?=base_url()?>admin/envios/guardar/"+id,{text:text})
            .done(function(){
                document.location.href = "<?=base_url()?>admin/envios/enviar/"+id;
            })
            e.preventDefault();
        })
        <?
        }
        ?>

        <?
        if($this->uri->segment(3) == 'editar'){
        ?>
        $("#envios-step1").submit(function(){
            $("#envios-continuar").prop('disabled',true);
            $("#envios-continuar").html('Procesando <i class="fa fa-spin fa-spinner"></i>')
            var grupo = $("#grupo-select").val();
            var data;
            var titulo = $("#envio-titulo").val();
            data = $("#"+grupo+"-select").val();
            $.post("<?=base_url()?>admin/envios/edicion/<?=$this->uri->segment(4)?>",{titulo:titulo,grupo:grupo,data:data})
            .done(function(data){
                if(data == 'no_mails'){
                    $("#step2").html('<div class="alert alert-danger">No se encontraron socios para el grupo seleccionado.</div>');
                    $("#envios-continuar").prop('disabled',false);
                    $("#envios-continuar").html('Continuar <i class="fa fa-arrow-right"></i>')
                }else{
                    $("#step2").html(data);
                    $("#envios-step1").slideUp();
                }
            })
        })
        $(document).on("submit","#envios-step2",function(e){
            var text = tinyMCE.activeEditor.getContent();
            var id = $("#envio_id").val();
            $.post("<?=base_url()?>admin/envios/guardar/"+id,{text:text})
            .done(function(){
                document.location.href = "<?=base_url()?>admin/envios/enviar/"+id;
            })
            e.preventDefault();
        })
        <?
        }
        ?>


        <?
        if($this->uri->segment(3) == 'enviar' && $this->uri->segment(3) == 'enviar' && $envio){
        ?>
            var pausa = false;
            $("#estado").html('Iniciando Envio...');
            enviar();

            function enviar(){
                if(pausa){ $("#estado").html('Envio Pausado'); return false; }
                $.post('<?=base_url()?>admin/envios/send/<?=$envio->id?>')
                .done(function(data){
                    if(data == 'end'){
                        $("#estado").html('Envio Finalizado.');
                    }else{
                        data = $.parseJSON(data);
                        $("#enviados").html(data.enviados);
                        $("#estado").html(data.estado);
                        enviar();
                    }
               })
            }

            $("#pausar_envio").click(function(){
                $("#pausar_envio").prop('disabled',true);
                $("#reanudar_envio").prop('disabled',false);
                pausa = true;
            })
            $("#reanudar_envio").click(function(){
                $("#estado").html('Reanudando Envio');
                $("#reanudar_envio").prop('disabled',true);
                $("#pausar_envio").prop('disabled',false);
                pausa = false;
                enviar();

            })
        <?
        }
        ?>
        $(document).on('click','#del_confirm',function(){
            var msj = $(this).data('msj');
            var agree = confirm(msj);
            if(!agree){return false;}
        })

        function hide_cambio(){
            $("#cambio_correcto").slideUp();
        }

        <? /**

        **/ ?>
        $(document).on("click",".actividad_beca",function(){
            var beca = $(this).data('beca');
            var id = $(this).data('id');
            angular.element("#modal_open").triggerHandler('click');
            $("#beca-porcien").val(beca);
            $("#beca-id").val(id);
            return false;
        })
        $(document).on('submit','#form-beca',function(e){
            var id = $("#beca-id").val();
            var beca = $("#beca-porcien").val();
            console.log(id,beca);
            e.preventDefault();
            $.post("<?=base_url()?>admin/actividades/becar",{id:id,beca:beca})
            .done(function(){
                angular.element("#modal_close").triggerHandler('click');
                $("#actividad_beca_"+id).data('beca',beca);
            })
        })

        function calcular_cuota(){
            var monto = $("#s_cate").find(':selected').attr('data-precio');
            var descuento = $("#descuento").val();
            var a_pagar = monto - ( monto*descuento/100 );
            $("#a_pagar").text(a_pagar);
        }
        <? if ($this->uri->segment(2) == 'socios' && ($this->uri->segment(3) == 'agregar' || $this->uri->segment(3) == 'editar') ) {
        ?>
        calcular_cuota();
        <?
        }
        ?>

        $("#s_cate").change(function(){calcular_cuota()});
        $("#descuento").keyup(function(){calcular_cuota()});

//Agregado AHG 
            $("#contra_reg_form").submit(function(){
alert("contra_reg");
                var marca = $("#marca").val();
                var periodo = $("#periodo").val();
                var url="<?=base_url()?>admin/debtarj/contracargo/getcab/"+marca+"/"+periodo;
alert("antes get url="+url);

            $.get("<?=$baseurl?>admin/debtarj/contracargo/getcab"+marca+"/"+periodo,function(data){
                if(data){
alert("encontro data");
                    	var cabecera = $.parseJSON(data);
            		var url="<?=base_url()?>admin/debtarj/contracargo/view/"+marca+"/"+periodo;
        		$("#contra_reg_form").attr("action",url);
                }else{
			alert("No existe ese Periodo / Tarjeta ");
                }
            })


	})

$("#load-debtarj-form").submit(function(){
        var marca = $("#marca").val();
        var fecha = $("#fecha").val();
        var url = "<?=$baseurl?>admin/debtarj/subearchivo" + "/" + marca + "/" + fecha

        $("#load-debtarj-form").attr("action",url);

        $("#load-debtarj-form").submit();

        return true;

})


$("#edit_debtarj_form").submit(function(){

        var url = "admin/debtarj/regrabar/";
        var msg = "Seguro que desea modificar este debito?";

        var agree = confirm(msg);
        if(!agree){return false;}
        $("#reg-cargando").removeClass('hidden');

        var id_debito = $("#id_debito").val();
        var id_marca = $("#id_marca").val();
        var nro_tarjeta = $("#nro_tarjeta").val();
        var largo = nro_tarjeta.length;
        var fecha_adhesion = $("#fecha_adhesion").val();
        var sid = $("#sid").val();

        if ( largo < 16 ) {
                var sigue = confirm("Seguro que el numero es tan corto?");
                if (!sigue){return false;}
        }

        $.post("<?=$baseurl?>"+url,{id_debito: id_debito, sid: sid, id_marca: id_marca, nro_tarjeta: nro_tarjeta, fecha_adhesion: fecha_adhesion })
        .done(function(data){
                alert("Debito correctamente actualizado");
                $("#reg-cargando").addClass('hidden');
        })

        return true;
})

$("#nvo_debtarj_form").submit(function(){

        var url = "admin/debtarj/grabar/";
        var msg = "Seguro que desea agregar este nuevo debito?";

        var agree = confirm(msg);
        if(!agree){return false;};
        $("#reg-cargando").removeClass('hidden');

        var id_marca = $("#id_marca").val();
        var nro_tarjeta = $("#nro_tarjeta").val();
        var largo = nro_tarjeta.length;
        var fecha_adhesion = $("#fecha_adhesion").val();
        var sid = $("#sid").val();

        if ( largo < 16 ) {
                var sigue = confirm("Seguro que el numero es tan corto?");
                if (!sigue){return false;};
        }

alert("boton nuevo");
alert(url);
        $.post("<?=$baseurl?>"+url,{sid: sid, id_marca: id_marca, nro_tarjeta: nro_tarjeta, fecha_adhesion: fecha_adhesion })
        .done(function(data){
                alert("Debito correctamente guardado");
                $("#reg-cargando").addClass('hidden');
        })

        return false;
})


$("#gen_debtarj_form select").on("change", function(){
    var id_marca = $(this).val();
    if ( id_marca > 1 ) {
        $("#btn_total").show();
    } else {
        $("#btn_total").hide();
    }

})
$("#gen_debtarj_form button").on("click", function(){
        var boton = $(this).data("text");

	var id_marca = $("#id_marca").val();
	var periodo = $("#periodo").val();
	var flag = $("#flag").val();
	var text = $(this).data("text");

        var agree = confirm("Seguro que desea "+boton+" el archivo ?");
        if(!agree){return false;}

	if ( text == "generar" ) {
		if ( flag == 1 ) {
        		var url = $(this).data("action") + "/" + id_marca + "/" + periodo + "/1";
		} else {
        		var url = $(this).data("action") + "/" + id_marca + "/" + periodo;
		}
	} else {
        		var url = $(this).data("action") ;
	}
        $("#gen_debtarj_form").attr("action",url);

        $("#gen_debtarj_form").submit();

        return true;
})

$("#debtarj_botones_form button").on("click", function(){
        var boton = $(this).data("text");
	
	switch ( boton ) {	
		case "excel":
			var url = $(this).data("action");
			break;
		case "nuevo":
			var url = $(this).data("action");
			break;
	}

        $("#debtarj_botones_form").attr("action",url);

        $("#debtarj_botones_form").submit();

        return true;
})


$("#contra-pago-form").submit(function(){
        var agree = confirm("Seguro que desea registrar este contracargo?");
        if(!agree){return false;}
        $("#reg-cargando").removeClass('hidden');
        var periodo = $("#periodo").val();
        var id_marca = $("#id_marca").val();
        var fecha_debito = $("#fechadeb").val();
        var nrotarjeta = $("#nrotarjeta").val();
        var importe = $("#importe").val();
        $.post("<?=$baseurl?>admin/debtarj/contracargo/do/"+id_marca+"/"+periodo,{fecha_debito: fecha_debito, nrotarjeta: nrotarjeta, importe: importe})
        return false;
})



        </script>
        <? if($this->uri->segment(3) == 'guardada' && $this->uri->segment(2) == 'configuracion'){ ?>
        <script type="text/javascript">setTimeout(hide_cambio,4000)</script>
        <? } ?>


	
    </body>
</html>
