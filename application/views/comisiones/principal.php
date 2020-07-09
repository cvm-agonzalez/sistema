<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-question-circle"></span> ACCESO SUB-COMISION <?=$nombre_comision?> </strong></div>
        <div class="panel-body">
                <div class="form-group col-lg-12">
		    <label > Presidente: XXXXXX - estado OK </label>
		</div>
                <div class="form-group col-lg-12">
		    <label > Tesorero  : ZZZZZZ - estado OK </label>
		</div>
	<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="activos">
	<thead>
	        <tr>
	            <th>SOCIOS ACTIVOS</th>
	        </tr>
	</thead>
	</table>
	<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="comisiones_table1">
		<thead>
	        <tr>
	            <th>Id Actividad</th>
	            <th>Descripcion</th>
	            <th>Socios</th>
	            <th>Becados</th>	                      
	            <th>Morosos Cuota Socia</th>
	            <th>Mora Cuota Social</th>
	            <th>Morosos Actividad</th>
	            <th>Mora Actividad Actual</th>
	            <th>Mora Actividad Anterior</th>
	            <th>Mora Act Desrel</th>
	            <th>Opciones</th>
	        </tr>
	    	</thead>
	    	<tbody>
	    	<?
	    	if($resumen1){
	    		foreach ($resumen1 as $actividad) {	    	
	    	?>
			<tr>				
				<td><?=$actividad->aid?></td>
				<td><?=$actividad->descr_activ?></td>
				<td align="right"><?=$actividad->socios_rel?></td>
				<td align="right"><?=$actividad->socios_becados?></td>
				<td align="right"><?=$actividad->socio_deuda_cs?></td>
				<td align="right"><?=$actividad->deuda_cs?></td>
				<td align="right"><?=$actividad->socio_deuda_act_rel?></td>
				<td align="right"><?=$actividad->deuda_act_rel_hoy?></td>
				<td align="right"><?=$actividad->deuda_act_rel_corte?></td>
				<td align="right"><?=$actividad->deuda_act_desrel+=$actividad->deuda_act_rel_ant?></td>
				<td>Opciones</td>
			</tr>
			<?
				}
			}
			?>
		</tbody>
	</table>


        <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="suspendidos">
        <thead>
                <tr>
                    <th>SOCIOS SUSPENDIDOS</th>
                </tr>
        </thead>
        </table>
        <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="comisiones_table2">
                <thead>
                <tr>
                    <th>Id Actividad</th>
                    <th>Descripcion</th>
                    <th>Socios</th>
                    <th>Becados</th>
                    <th>Morosos Cuota Socia</th>
                    <th>Mora Cuota Social</th>
                    <th>Morosos Actividad</th>
                    <th>Mora Actividad Actual</th>
                    <th>Mora Actividad Anterior</th>
                    <th>Mora Act Desrel</th>
                    <th>Opciones</th>
                </tr>
                </thead>
                <tbody>
                <?
                if($resumen2){
                        foreach ($resumen2 as $actividad) {
                ?>
                        <tr>
                                <td><?=$actividad->aid?></td>
                                <td><?=$actividad->descr_activ?></td>
                                <td align="right"><?=$actividad->socios_rel?></td>
                                <td align="right"><?=$actividad->socios_becados?></td>
                                <td align="right"><?=$actividad->socio_deuda_cs?></td>
                                <td align="right"><?=$actividad->deuda_cs?></td>
                                <td align="right"><?=$actividad->socio_deuda_act_rel?></td>
                                <td align="right"><?=$actividad->deuda_act_rel_hoy?></td>
                                <td align="right"><?=$actividad->deuda_act_rel_corte?></td>
                                <td align="right"><?=$actividad->deuda_act_desrel+=$actividad->deuda_act_rel_ant?></td>
                                <td>Opciones</td>
                        </tr>
                        <?
                                }
                        }
                        ?>
                </tbody>
        </table>

            </form>
       	</div>
    </div>
</section>
