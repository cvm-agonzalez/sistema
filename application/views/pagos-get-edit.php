<table class="table table-bordered">
	<thead>
		<tr>
			<td>Generado El</td>
			<td>Monto</td>
			<td>Pagado</td>
			<td>Tipo</td>
			<td></td>
		</tr>
	</thead>
	<tbody>
		<?
		foreach ($pagos as $pago) {	
			if($pago->tipo == 5){ continue; }
			?>
			<tr>
				<td><?=date('d/m/Y',strtotime($pago->generadoel))?></td>
				<td><?=$pago->monto?></td>
				<td><?=$pago->pagado?></td>
				<td>
					<? switch ($pago->tipo) {
						case 1:
							echo 'Cuota Social';
							break;
						
						case 2:
							echo 'Recargo por Mora';
							break;

						case 3:
							echo 'Financiación de deuda';
							break;

						case 4:
							echo 'Actividad: '.$pago->nombre;
							break;						
					}
					?>
				</td>
				<td>
					<a href="<?=base_url()?>admin/pagos/eliminar/<?=$pago->Id?>" class="btn btn-danger" id="eliminar_pago"><i class="fa fa-trash-o"></i></a>
				</td>
			</tr>

			<?
		}
		?>
	</tbody>
</table>