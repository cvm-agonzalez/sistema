                    <div class="page page-profile">
                        <div class="panel panel-default">
                            <div class="panel-heading"><strong><span class="fa fa-group"></span> LUGARES</strong></div>
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
                                    foreach ($lugares as $lugar) {                                       
                                    ?>
                                    <tr>
                                        <td><?=$lugar->id?></td>
                                        <td><span class="color-success"><?=$lugar->nombre?></td>
                                        <td>
                                            <a href="<?=$baseurl?>admin/actividades/lugares/editar/<?=$lugar->id?>"><i class="fa fa-gear"></i> Editar</a>  | 
                                            <a id="btn-eliminar-lugar" href="<?=$baseurl?>admin/actividades/lugares/eliminar/<?=$lugar->id?>"><i class="fa fa-times"></i> Eliminar</a>
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
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Agregar Lugar</strong></div>
        <div class="panel-body">
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/actividades/lugares/nuevo" method="post">

                <div class="form-group">
                    <label for="" class="col-sm-2">Nombre</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                </div>                
                <div class="form-group">
                    <label for="" class="col-sm-2">Dirección</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="direccion">
                    </div>
                </div>                                            
                <button type="submit" class="btn btn-success">Agregar</button>
            </form>
        </div>
    </div>
</section>                    
