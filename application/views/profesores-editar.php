                    
<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Editar Comisión</strong></div>
        <div class="panel-body">
            <?
            if(!$profesor){
            ?>
            La comisión que esta intentando editar no existe en nuestra base de datos.<br><br>
            <a href="<?=$baseurl?>admin/actividades/profesores" class="btn btn-primary">Volver a Profesores</a>
            <?
            }else{
            ?>
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
                <button type="submit" class="btn btn-success">Guardar Cambios</button>
            </form>
            <?
            }
            ?>
        </div>
    </div>
</section>                    