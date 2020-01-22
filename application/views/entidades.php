<div class="page page-profile">
	<div class="panel panel-default">
		<div class="panel-heading"><strong><span class="fa fa-user"></span> ENTIDADES</strong></div>
		<table class="table">
			<thead>
				<tr>
					<th>#</th>
					<th>Abreviatura</th>
					<th>Descripcion</th>
					<th>CUIT</th>
					<th>Cuenta Digital ID</th>
					<th>Cuenta Digital Control</th>
					<th>Nro Prov COL</th>
					<th>Email Origen</th>
					<th>Opciones</th>
				</tr>
			</thead>
			<tbody>
				<? foreach($listaEntidades as $aa){ ?>
				<tr>
					<td><?=$aa->id?></td>
					<td><?=$aa->abreviatura?></td>
					<td><?=$aa->descripcion?></td>
					<td><?=$aa->cuit?></td>
					<td><?=$aa->cd_id?></td>
					<td><?=$aa->cd_control?></td>
					<td><?=$aa->nprov_col?></td>
					<td><?=$aa->email_sistema?></td>
					<td>
						<a href="<?=base_url()?>admin/entidades/editar/<?=$aa->id?>"><i class="fa fa-gear"></i> Editar</a>  | 
					</td>
				</tr>                                    
				<? } ?>
			</tbody>
		</table>
	</div>
</div>
<section class="page page-profile">
	<div class="panel panel-default">
		<div class="panel-heading"><strong><span class="fa fa-plus"></span> Agregar Entidad</strong></div>
		<div class="panel-body">
			<form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/entidades/agregar" method="post">

				<div class="form-group">
					<label for="user" class="col-sm-2">Abreviatura</label>
					<div class="col-sm-10">
						<input type="text" id="abreviatura" name="abreviatura" class="form-control" required>
					</div>
				</div>
				<div class="form-group">
					<label for="mail" class="col-sm-2">Descripcion</label>
					<div class="col-sm-10">
						<input type="text" id="descripcion" name="descripcion" class="form-control" required>
					</div>
				</div>    
				<div class="form-group">
					<label for="pass" class="col-sm-2">CUIT</label>
					<div class="col-sm-10">
						<input type="text" name="cuit" id="cuit" class="form-control" required>
					</div>
				</div> 
				<div class="form-group">
					<label for="pass" class="col-sm-2">ID Cuenta Digital</label>
					<div class="col-sm-10">
						<input type="text" name="cd_id" id="cd_id" class="form-control" required>
					</div>
				</div> 
				<div class="form-group">
					<label for="pass" class="col-sm-2">Control Cuenta Digital</label>
					<div class="col-sm-10">
						<input type="text" name="cd_control" id="cd_control" class="form-control" required>
					</div>
				</div> 
				<div class="form-group">
					<label for="pass" class="col-sm-2">Nro Prov Cooperativa</label>
					<div class="col-sm-10">
						<input type="text" name="nprov_col" id="nprov_col" class="form-control" required>
					</div>
				</div> 
				<div class="form-group">
					<label for="pass" class="col-sm-2">Email Origen</label>
					<div class="col-sm-10">
						<input type="email" name="email_sistema" id="email_sistema" class="form-control" required>
					</div>
				</div> 
				<button type="submit" class="btn btn-success">Agregar</button>
			</form>
		</div>
	</div>
</section>                    
