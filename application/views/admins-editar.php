<section class="page page-profile">
	<div class="panel panel-default">
		<div class="panel-heading"><strong><span class="fa fa-plus"></span> Editar Administrador</strong></div>
		<div class="panel-body">
			<form autocomplete="off" class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/admins/guardar/<?=$admin->id?>" method="post">

				<div class="form-group">
					<label for="user" class="col-sm-2">Nombre de usuario</label>
					<div class="col-sm-10">
						<input type="text" value="<?=$admin->user?>" id="user" name="user" class="form-control" <?if ($action == "chgpwd") { echo ' disabled> '; } else { echo ' required>'; }?>
					</div>
				</div>
				<div class="form-group">
					<label for="mail" class="col-sm-2">E-Mail</label>
					<div class="col-sm-10">
						<input type="email" value="<?=$admin->mail?>" id="mail" name="mail" class="form-control" <?if ($action == "chgpwd") { echo ' disabled> '; } else { echo ' required>'; }?>
					</div>
				</div>    
				<div class="form-group">
					<label for="rango" class="col-sm-2">Tipo</label>
					<div class="col-sm-10">
						<select style="padding:5px;" id="rango" name="rango" class="form-control" <?if ($action == "chgpwd") { echo ' disabled> '; } else { echo ' >'; }?>
							<option value="0" <? if($admin->rango == 0){ echo 'selected'; } ?>>ROOT</option>
							<option value="1" <? if($admin->rango == 1){ echo 'selected'; } ?>>Administrador Principal</option>
							<option value="2" <? if($admin->rango == 2){ echo 'selected'; } ?>>Consultad</option>
							<option value="3" <? if($admin->rango == 3){ echo 'selected'; } ?>>Comisiones</option>
						</select>
					</div>
				</div>
				<?if ($action == "chgpwd") {?>
					<div class="form-group">
						<label for="pass_old" class="col-sm-2">Password Actual</label>
						<div class="col-sm-10">
							<input type="password" name="pass_old" id="pass_old" value="" class="form-control">
						</div>
					</div> 
				<? } else { ?>
					<div class="alert alert-info">
						Si no desea cambiar la contraseña, deje los siguientes campos en blanco.
					</div>
				<? } ?>
		
				<div class="form-group">
					<label for="pass1" class="col-sm-2">Password</label>
					<div class="col-sm-10">
						<input type="password" name="pass1" id="pass1" class="form-control">
					</div>
				</div> 
				<div class="form-group">
					<label for="pass2" class="col-sm-2">Reingresar Password</label>
					<div class="col-sm-10">
						<input type="password" name="pass2" id="pass2" class="form-control">
					</div>
				</div> 
				<button type="submit" class="btn btn-success">Agregar</button>
				<a href="<?=base_url()?>admin/admins" type="submit" class="btn btn-warning">Cancelar</a>
			</form>
		</div>
	</div>
</section>
