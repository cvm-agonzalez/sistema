                    <div class="page page-profile">
                        <div class="panel panel-default">
                            <div class="panel-heading"><strong><span class="fa fa-group"></span> COMISIONES</strong></div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?
                                    foreach ($profesores as $profesor) {                                       
                                    ?>
                                    <tr>
                                        <td><?=$profesor->Id?></td>
                                        <td><span class="color-success"><?=$profesor->apellido?> <?=$profesor->nombre?></td>
                                        <td>
                                            <a href="<?=$baseurl?>admin/actividades/profesores/editar/<?=$profesor->Id?>"><i class="fa fa-gear"></i> Editar</a>  | 
                                            <a id="btn-eliminar-profesor" href="<?=$baseurl?>admin/actividades/profesores/eliminar/<?=$profesor->Id?>"><i class="fa fa-times"></i> Eliminar</a>
                                        </td>
                                    </tr>                                    
                                    <?
                                    }
                                    ?>                              

                                </tbody>
                            </table>
                        </div>
                    </div>
<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Agregar Comisión</strong></div>
        <div class="panel-body">
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/actividades/profesores/nuevo" method="post">

                <div class="form-group">
                    <label for="" class="col-sm-2">Nombre</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Apellido</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="apellido" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">DNI</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" name="dni" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Dirección</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="direccion">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Teléfono</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="telefono">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Celular</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="celular">
                    </div>
                </div> 
                <div class="form-group">
                    <label for="" class="col-sm-2">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" name="mail">
                    </div>
                </div> 
                <div class="form-group">
                    <label for="" class="col-sm-2">Contraseña</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="pass">
                    </div>
                </div>                                
                <button type="submit" class="btn btn-success">Agregar</button>
            </form>
        </div>
    </div>
</section>                    