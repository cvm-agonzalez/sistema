<meta charset="utf-8">
<style>
@media print {
    #actividades_print,#actividades_table_length,#actividades_table_filter,#actividades_table_info,#actividades_table_paginate{display:none;}
    td{ font-size: 12px;}
}
</style>
<div class="container" style="margin-top:80px;">
<div class="pull-left">
    <h3>Planes de Financiación | <?=date('d/m/Y H:i')?></h3>
</div>
<div class="pull-right hidden-print">
    <button class="btn btn-info" onclick="print()"><i class="fa fa-print"></i> Imprimir</button>
    <a href="<?=base_url()?>imprimir/financiacion_excel/" class="btn btn-success"><i class="fa fa-cloud-download"></i> Excel</a>
</div>
<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="socios_table">
    <thead>
        <tr>
            <th>Nombre y Apellido</th>
            <th>Socio</th>
            <th>Detalle</th>
            <th>Cuotas</th>
            <th>Cuota Actual</th>        
            <th>Monto</th>
            <th>Inicio - Fin</th>
            <th class="hidden-print">Resumen</th>
        </tr>
    </thead>
	        
    <tbody>
    	<?
        if($socios){
    	foreach ($socios as $socio) {    	
    	?>
        <tr>
            <td><?=$socio->nombre?> <?=$socio->apellido?></td>
            <td><?=$socio->sid?></td>
            <td><?=$socio->detalle?></td>
            <td><?=$socio->cuotas?></td>
            <td><?=$socio->actual?></td>
            <td>$ <?=$socio->monto?></td>
            <td><?=$socio->inicio?> | <?=$socio->fin?></td>
            <td class="hidden-print"><a href="<?=base_url()?>admin/socios/resumen/<?=$socio->sid?>" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-external-link"></i> Ver Resumen</a></td>
        </tr>
        <? } } ?>
    </tbody>
</table>
</div>