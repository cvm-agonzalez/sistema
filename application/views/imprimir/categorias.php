<div class="container" style="margin-top:50px;">
    <div class="starter-template">
        <h1>Categorías</h1>
        <div id="actividades_print">
	        <div class="col-xs-6">
	        	<label>Seleccione Categoría:</label>
	        	<select class="form-control" id="categorias_select"> 
	        		<option value="">--</option>
	        		<?
	        		foreach ($actividades as $actividad) {        		
	        		?>
	        		<option value="<?=$actividad->Id?>"><?=$actividad->nomb?></option>
	        		<?
	        		}
	        		?>
	    		</select>
	    	</div>
	    	<div class="clearfix"></div>
	    </div>
    	<div id="listado_categorias">
    		
	        
		</div>
  	</div>

</div><!-- /.container -->