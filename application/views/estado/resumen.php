<table class="table table-bordered table-striped table-responsive table-resumen">
	<thead>
		<tr>
			<th><div class="th-resumen"># ID</div></th>
			<th><div class="th-resumen">Fecha</div></th>
			<th><div class="th-resumen">Descripción</div></th>
			<th><div class="th-resumen">Debe</div></th>
			<th><div class="th-resumen">Haber</div></th>
			<th><div class="th-resumen">Total</div></th>
		</tr>
	</thead>
	<tbody>
		<?
		function mostrar_fecha($fecha)
		{
			$fecha = explode('-', $fecha);
			$fecha[2] = explode(' ',$fecha[2]);
			return $fecha[2][0].'/'.$fecha[1].'/'.$fecha[0];
		}	
		foreach ($facturacion as $ingreso) {				
			?>
			<tr class="<? if($ingreso->debe != 0){ echo 'danger'; }else{ echo 'success'; } ?>">
				<td><?=$ingreso->Id?></td>
				<td><?=mostrar_fecha($ingreso->date)?></td>
				<td>
					<div class="" id="socio_desc" data-id="<?=$ingreso->Id?>"><?=$ingreso->descripcion?></div>

				</td>
				<td class="debe">$ <?=$ingreso->debe?></td>
				<td class="haber">$ <?=$ingreso->haber?></td>
				<td class="<? if($ingreso->total < 0){ echo 'debe'; }else{ echo 'haber'; } ?>">$ <?=$ingreso->total?></td>
			</tr>
			<?
		}
		?>											
	</tbody>			
</table>