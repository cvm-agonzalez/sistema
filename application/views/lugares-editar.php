                    
<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Editar Lugar</strong></div>
        <div class="panel-body">
            <?
            if(!$lugar){
            ?>
            El Lugar que esta intentando editar no existe en nuestra base de datos.<br><br>
            <a href="<?=$baseurl?>admin/actividades/lugares" class="btn btn-primary">Volver a Lugares</a>
            <?
            }else{
            ?>
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/actividades/lugares/guardar/<?=$lugar->Id?>" method="post">

                <div class="form-group">
                    <label for="" class="col-sm-2">Nombre</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="nombre" value="<?=$lugar->nombre?>" required>
                    </div>
                </div>                
                <div class="form-group">
                    <label for="" class="col-sm-2">Dirección</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="direccion" value="<?=$lugar->direccion?>">
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