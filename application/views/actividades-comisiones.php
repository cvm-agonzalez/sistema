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
                                    foreach ($comisiones as $comision) {                                       
                                    ?>
                                    <tr>
                                        <td><?=$comision->cid?></td>
                                        <td><span class="color-success"><?=$comision->descripcion?> </td>
                                        <td>
                                            <a href="<?=$baseurl?>admin/actividades/comisiones/editar/<?=$comision->id?>"><i class="fa fa-gear"></i> Editar</a>  | 
                                            <a id="btn-eliminar-comision" href="<?=$baseurl?>admin/actividades/comisiones/eliminar/<?=$comision->id?>"><i class="fa fa-times"></i> Eliminar</a>
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
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Agregar Comision</strong></div>
        <div class="panel-body">
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/actividades/comisiones/nuevo" method="post">

                <div class="form-group">
                    <label for="" class="col-sm-2">Descripcion</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="descripcion" required>
                        <input type="hidden" name="id" value='0'>
                        <input type="hidden" name="estado" value='1'>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Agregar</button>
            </form>
        </div>
    </div>
</section>                    
