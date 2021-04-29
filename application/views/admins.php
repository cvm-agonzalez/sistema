<div class="page page-profile">
	<div class="panel panel-default">
		<div class="panel-heading"><strong><span class="fa fa-user"></span> ADMINISTRADORES</strong></div>
		<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>Nombre de Usuario</th>
					<th>E-Mail</th>
					<th>Última conexión</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody>
				<? foreach($listaAdmin as $aa){ ?>
				<tr>
					<td><?=$aa->id?></td>
					<td><span class="color-success"><?=$aa->user?></td>
					<td><?=$aa->mail?></td>
					<td><span class="label label-info"><?=$aa->lCon?></span></td>
					<td>
						<a href="<?=base_url()?>admin/admins/editar/<?=$aa->id?>"><i class="fa fa-gear"></i> Editar</a>  | 
						<a onclick="return confirm('Seguro que desea eliminar este Administrador?');" href="<?=base_url()?>admin/admins/eliminar/<?=$aa->id?>"><i class="fa fa-times"></i> Eliminar</a>
					</td>
				</tr>                                    
				<? } ?>
			</tbody>
		</table>
	</div>
</div>
<section class="page page-profile">
	<div class="panel panel-default">
		<div class="panel-heading"><strong><span class="fa fa-plus"></span> Agregar Administrador</strong></div>
		<div class="panel-body">
			<form class="form-horizontal ng-pristine ng-valid" id="alta_admin" action="<?=$baseurl?>admin/admins/agregar" method="post">

				<div class="form-group">
					<label for="entidad" class="col-sm-2">Entidad</label>
					<div class="col-sm-10">
					<select style="padding:5px;" id="select_ent" name="select_ent" class="form-control">							
                                                        <? if ( $grupo_user == 0 ) { ?>
								<option value="0">Todas las Entidades del Grupo</option>
							<? } ?>
						<?
                           				foreach ($entidades as $entidad) {
                           			?>
                           				<option value="<?=$entidad->id?>"><?=$entidad->descripcion?></option>
                           			<?
                           				}
                           			?>
					</select>
					</div>
				</div>
				<div class="form-group">
					<label for="user" class="col-sm-2">Nombre de usuario</label>
					<div class="col-sm-10">
						<input type="text" id="user" name="user" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<label for="mail" class="col-sm-2">E-Mail</label>
					<div class="col-sm-10">
						<input type="email" id="mail" name="mail" class="form-control" required>
					</div>
				</div>    
				<div class="form-group">
					<label for="pass" class="col-sm-2">Password</label>
					<div class="col-sm-10">
						<input type="password" name="pass" id="pass" class="form-control" required>
					</div>
				</div> 
				<div class="form-group">
					<label for="rango" class="col-sm-2">Tipo</label>
					<div class="col-sm-10">
						<select style="padding:5px;" id="rango" name="rango" class="form-control">							
							<option value="0">ROOT</option>
							<option value="1">Administrador Principal</option>
							<option value="2">Consultas/Pagos</option>
						</select>
					</div>
				</div>
				<button type="button" data-accion="agregar_admin" id="btn_admin" class="btn btn-success">Agregar</button> <i id="reg-cargando" class="fa fa-spinner fa-spin hidden"></i>
			</form>
		</div>
	</div>
</section>                    
