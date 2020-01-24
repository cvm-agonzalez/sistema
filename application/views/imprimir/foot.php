    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?=base_url()?>scripts/jquery.dataTables.min.js"></script>
    <script src="<?=base_url()?>scripts/bootstrap3.min.js"></script>
    <script src="<?=base_url()?>scripts/dataTables.bootstrap.js"></script>
    <script src="<?=base_url()?>scripts/moment.min.js"></script>
    <script src="<?=base_url()?>scripts/daterangepicker.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
    	$('#socios_table').DataTable({
    		"language": {
    			"url": "<?=base_url()?>scripts/ES_ar.txt"	 	   
    		},
    		"order": [[ 4, "desc" ]]       	

    	});

    $('#morosos_table').DataTable({
        "language": {
           "url": "<?=base_url()?>scripts/ES_ar.txt"           
        },
        "order": [[ 2, "desc" ]],
        "paging": false

    });

    $('#ingresos_table').DataTable({
        "language": {
           "url": "<?=base_url()?>scripts/ES_ar.txt"           
        },
        "order": [[ 0, "desc" ]],
        "paging": false

    });

    $('#actividades_cobros').DataTable({
        "language": {
           "url": "<?=base_url()?>scripts/ES_ar.txt"           
        },
        "order": [[ 0, "desc" ]],
        "paging": false

    });

    
    
        $("#becas_select").change(function(){
            var id = $(this).val();
            document.location.href = '<?=base_url()?>imprimir/listado/becas/'+id;
        })

    	$("#actividades_select").change(function(){
    		var id = $(this).val();
    		if(id == ''){return false;}
    		$("#listado_actividad").removeClass('hidden');
    		$("#listado_actividad").html('Generando Listado...')
    		$.post('<?=base_url()?>imprimir/generar/actividades/'+id)
    		.done(function(data){
    			$("#listado_actividad").html(data);
    			autoResize('iframe1');
    		})
    	})

        <?
        if( $this->uri->segment(2) == 'listado' && $this->uri->segment(3) == 'actividades' ){
            if($render_cat){
                ?>
                $("#listado_actividad").removeClass('hidden');
                $("#listado_actividad").html('Generando Listado...')
                $.post('<?=base_url()?>imprimir/generar/actividades/<?=$render_cat?>')
                .done(function(data){
                    $("#listado_actividad").html(data);
                    //autoResize('iframe1');
                    setTimeout(function(){window.print();},200);
                })
                <?
            }
        }
        ?>

    	$("#profesores_select").change(function(){
    		var id = $(this).val();
    		if(id == ''){return false;}
    		$("#listado_profesores").removeClass('hidden');
    		$("#listado_profesores").html('Generando Listado...')
    		$.post('<?=base_url()?>imprimir/generar/profesores/'+id)
    		.done(function(data){
    			$("#listado_profesores").html(data);
    			autoResize('iframe1');
    		})
    	})

    	<?
    	if($this->uri->segment(3) == 'socios'){
    		?>
    		$("#listado_socios").load('<?=base_url()?>imprimir/generar/socios/activos',function(){autoResize('iframe1')})
    		<?
    	}
    	?>

    	$("#socios_select").change(function(){
    		$("#listado_socios").html('<i class="fa fa-spinner fa-spin"></i> Generando Listado');
    		var type = $(this).val();
    		if(type== 'suspendidos'){
    			$("#listado_socios").load('<?=base_url()?>imprimir/generar/socios/suspendidos',function(){autoResize('iframe1');})
    		}else if(type == 'activos'){
    			$("#listado_socios").load('<?=base_url()?>imprimir/generar/socios/activos',function(){autoResize('iframe1');})
    		}
    	})

    	$("#categorias_select").change(function(){
    		$("#listado_categorias").html('<i class="fa fa-spinner fa-spin"></i> Generando Listado');
    		var id = $(this).val();
    		$("#listado_categorias").load('<?=base_url()?>imprimir/generar/categorias/'+id,function(){autoResize('iframe1');});    		
    	})
    	<? if($this->uri->segment(3) == 'ingresos'){ ?>
    		$('input[name="daterange"]').daterangepicker({
    			format: 'DD/MM/YYYY',
    			<?
    			if($fecha1 && $fecha2){
    				?>
    				startDate: '<?=date("d-m-Y",strtotime($fecha1))?>',
    				endDate: '<?=date("d-m-Y",strtotime($fecha2))?>'
    				<? } ?>	     	
    			},
    			function(start, end, label) {
    				document.location.href = '<?=base_url()?>imprimir/cobros/ingresos/'+start.format('YYYY-MM-DD')+'/'+end.format('YYYY-MM-DD');				
    			});
    		<? } ?>

        <? if($this->uri->segment(3) == 'cooperativa'){ ?>
            $('input[name="daterange"]').daterangepicker({
                format: 'DD/MM/YYYY',
                <?
                if($fecha1 && $fecha2){
                    ?>
                    startDate: '<?=date("d-m-Y",strtotime($fecha1))?>',
                    endDate: '<?=date("d-m-Y",strtotime($fecha2))?>'
                    <? } ?>
                },
                function(start, end, label) {
                    document.location.href = '<?=base_url()?>imprimir/cobros/cooperativa/'+start.format('YYYY-MM-DD')+'/'+end.format('YYYY-MM-DD');
                });
            <? } ?>

        <? if($this->uri->segment(3) == 'cuentadigital'){ ?>
            $('input[name="daterange"]').daterangepicker({
                format: 'DD/MM/YYYY',
                <?
                if($fecha1 && $fecha2){
                    ?>
                    startDate: '<?=date("d-m-Y",strtotime($fecha1))?>',
                    endDate: '<?=date("d-m-Y",strtotime($fecha2))?>'
                    <? } ?>         
                },
                function(start, end, label) {
                    document.location.href = '<?=base_url()?>imprimir/cobros/cuentadigital/'+start.format('YYYY-MM-DD')+'/'+end.format('YYYY-MM-DD');                
                });
            <? } ?>

    		<? if($this->uri->segment(3) == 'actividades' && $this->uri->segment(2) == 'cobros'){ ?>
    			$('input[name="daterange"]').daterangepicker({
    				format: 'YYYY-MM-DD',
    				<?
    				if($fecha1 && $fecha2){
    					?>
    					startDate: '<?=date("Y-m-d",strtotime($fecha1))?>',
    					endDate: '<?=date("Y-m-d",strtotime($fecha2))?>'
    					<? } ?>	     	
    				});
    			<? } ?>
    		} );
    </script>
    <script language="JavaScript">
    <!--
    function autoResize(id){
    	var newheight;

    	//if(window.getElementById){    		
    		newheight=$( document ).height()+200;
    	//}
    	console.log(newheight);
    	var elem = window.parent.document.getElementById('iframe1');
    	elem.style.height =  newheight + "px";    	
    }
	//-->
	$(document).ready(function(){
		var elem = window.parent.document.getElementById('iframe1');    	
		autoResize('iframe1');
	})
	</script>
</body>
</html>
