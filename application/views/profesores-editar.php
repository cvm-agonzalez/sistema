                    
<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Editar Profesor</strong></div>
        <div class="panel-body">
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/actividades/profesores/guardar/<?=$profesor->Id?>" method="post">

                <div class="form-group">
                    <label for="" class="col-sm-2">Nombre</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="nombre" value="<?=$profesor->nombre?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Apellido</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="apellido" value="<?=$profesor->apellido?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">DNI</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="dni" value="<?=$profesor->dni?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">SID</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="sid-select" name="sid" value="<?=$profesor->sid?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Dirección</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="direccion" value="<?=$profesor->direccion?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Teléfono</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="telefono" value="<?=$profesor->telefono?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Celular</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="celular" value="<?=$profesor->celular?>">
                    </div>
                </div> 

                <div class="form-group">
                    <label for="" class="col-sm-2">Comisión</label>
                    <div class="col-sm-10">
                        <select name="comision" id="comision" >
                                <?
                                foreach ($comisiones as $comision) {
                                ?>
					<option value="<?=$comision->id?>"  <? if($comision->id == $profesor->comision){echo 'selected';} ?>><?=$comision->descripcion?></option>

                                <? } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="" class="col-sm-2">Puesto en la Comisión</label>
                    <div class="col-sm-10">
                        <select name="puesto" id="puesto" >
                                <option value="0" >Operador</option>
                                <option value="1" >Presidente</option>
                                <option value="2" >VicePresidente</option>
                                <option value="3" >Tesorero</option>
                                <option value="4" >Secretario</option>
                                <option value="5" >Otro</option>
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label for="" class="col-sm-2">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" name="mail" value="<?=$profesor->mail?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Contraseña</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="pass" value="<?=$profesor->pass?>">
                    </div>
                </div>                              
                <button type="submit" id="btn_profesor" class="btn btn-success">Guardar Cambios</button>
            </form>
        </div>
    </div>
</section>                    
