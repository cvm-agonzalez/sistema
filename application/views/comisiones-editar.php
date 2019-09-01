                    
<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Editar Comisión</strong></div>
        <div class="panel-body">
            <?
            if(!$comision){
            ?>
            La comisión que esta intentando editar no existe en nuestra base de datos.<br><br>
            <a href="<?=$baseurl?>admin/actividades/comisiones" class="btn btn-primary">Volver a Comisiones</a>
            <?
            }else{
            ?>
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/actividades/comisiones/guardar/<?=$comision->id?>" method="post">

                <div class="form-group">
                    <label for="" class="col-sm-2">Descripcion</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="descripcion" value="<?=$comision->descripcion?>" required>
                        <input type="hidden" name="id" value="<?=$comision->id?>" >
                        <input type="hidden" name="estado" value="<?=$comision->estado?>" >
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
