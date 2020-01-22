<section class="page page-profile">
	<div class="panel panel-default">
		<div class="panel-heading"><strong><span class="fa fa-plus"></span> Editar Entidad</strong></div>
		<div class="panel-body">
			<form autocomplete="off" class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/entidades/guardar/<?=$entidad->id?>" method="post">

                                <div class="form-group">
                                        <label for="user" class="col-sm-2">Abreviatura</label>
                                        <div class="col-sm-10">
                                                <input type="text" id="abreviatura" name="abreviatura" class="form-control" value="<?=$entidad->abreviatura?>" required>
                                        </div>
                                </div>
                                <div class="form-group">
                                        <label for="mail" class="col-sm-2">Descripcion</label>
                                        <div class="col-sm-10">
                                                <input type="text" id="descripcion" name="descripcion" class="form-control" value="<?=$entidad->descripcion?>" required>
                                        </div>
                                </div>
                                <div class="form-group">
                                        <label for="pass" class="col-sm-2">CUIT</label>
                                        <div class="col-sm-10">
                                                <input type="text" name="cuit" id="cuit" class="form-control" value="<?=$entidad->cuit?>" required>
                                        </div>
                                </div>
                                <div class="form-group">
                                        <label for="pass" class="col-sm-2">ID Cuenta Digital</label>
                                        <div class="col-sm-10">
                                                <input type="text" name="cd_id" id="cd_id" class="form-control" value="<?=$entidad->cd_id?>" required>
                                        </div>
                                </div>
                                <div class="form-group">
                                        <label for="pass" class="col-sm-2">Control Cuenta Digital</label>
                                        <div class="col-sm-10">
                                                <input type="text" name="cd_control" id="cd_control" class="form-control" value="<?=$entidad->cd_control?>" required>
                                        </div>
                                </div>
                                <div class="form-group">
                                        <label for="pass" class="col-sm-2">Nro Prov Cooperativa</label>
                                        <div class="col-sm-10">
                                                <input type="text" name="nprov_col" id="nprov_col" class="form-control" value="<?=$entidad->nprov_col?>" required>
                                        </div>
                                </div>
                                <div class="form-group">
                                        <label for="pass" class="col-sm-2">Email Origen</label>
                                        <div class="col-sm-10">
                                                <input type="email" name="email_sistema" id="email_sistema" class="form-control" value="<?=$entidad->email_sistema?>" required>
                                        </div>
                                </div>
				<button type="submit" class="btn btn-success">Agregar</button>
				<a href="<?=base_url()?>admin/entidades" type="submit" class="btn btn-warning">Cancelar</a>
			</form>
		</div>
	</div>
</section>
