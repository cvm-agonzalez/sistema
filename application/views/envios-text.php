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
                			$img_path=$baseurl."entidades/".$ent_directorio;
					$imagen_default=$img_path."/email_head.png";
						?>
							<img src="<?=$imagen_default?>">
							<br>
                    			        <?
                    			if ( $img_attach ) {
                        			?>
                            			<img src="<?=$img_path?>/emails/<?=$img_attach?>" >
	
						<?
					}
				}
				?>

			</textarea>
		</div>
		<input type="hidden" value="<?=$id?>" id="envio_id">
		<button class="btn btn-success btn-block"><i class="fa fa-envelope"></i> Guardar y Comenzar Envío</button>
	</fieldset>
</form>
<script>tinymce.init({selector:'textarea',language : 'es_MX',height: 400, relative_urls: false, remove_script_host : false, force_br_newlines : false,
      force_p_newlines : false, forced_root_block : ''});</script>
