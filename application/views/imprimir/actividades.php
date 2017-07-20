<div class="container" style="margin-top:50px;">
    <div class="starter-template">
        <h1>Actividades</h1>
        <div id="actividades_print">
	        <div class="col-xs-6">
	        	<label>Seleccione Actividad:</label>
	        	<select class="form-control" id="actividades_select"> 
	        		<option value="">--</option>
	        		<?
	        		foreach ($actividades as $actividad) {        		
	        		?>
	        		<option value="<?=$actividad->Id?>"><?=$actividad->nombre?></option>
	        		<?
	        		}
	        		?>
	    		</select>
	    	</div>
	    	<div class="clearfix"></div>
	    </div>
    	<div id="listado_actividad" class="hidden">
    		       
		</div>
  	</div>
</div><!-- /.container -->