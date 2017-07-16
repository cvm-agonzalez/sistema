<form id="envios-step2" method="post">
	<fieldset>
		<h3 class="page-heading"><?=$titulo?></h3>
		<div class="form-group">
			<label>Envios</label>
			<input type="text" id="envio-titulo" class="form-control" value="Se enviarán <?=$total?> mensajes" disabled>
		</div>
		<div class="form-group">
			<label>Mensaje</label>			
			<textarea>
				<?
				if($body){
					echo $body;
				}else{
					?>
					<img src="http://clubvillamitre.com/images/vm-head.png">
					<?
				}
				?>

			</textarea>
		</div>
		<input type="hidden" value="<?=$id?>" id="envio_id">
		<button class="btn btn-success btn-block"><i class="fa fa-envelope"></i> Guardar y Comenzar Envío</button>
	</fieldset>
</form>
<script>tinymce.init({selector:'textarea',language : 'es_MX',height: 400});</script>