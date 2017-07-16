<div class="container" style="margin-top:50px;">
    <div class="starter-template">
        <h1>Profesores</h1>
        <div id="profesores_print">
	        <div class="col-xs-6">
	        	<label>Seleccione Profesor:</label>
	        	<select class="form-control" id="profesores_select"> 
	        		<option value="">--</option>
	        		<?
	        		foreach ($profesores as $profesor) {        		
	        		?>
	        		<option value="<?=$profesor->Id?>"><?=$profesor->nombre?> <?=$profesor->apellido?></option>
	        		<?
	        		}
	        		?>
	    		</select>
	    	</div>
	    	<div class="clearfix"></div>
	    </div>
    	<div id="listado_profesores" class="hidden">
    		
	        
		</div>
  	</div>

</div><!-- /.container -->