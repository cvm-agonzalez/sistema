<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Editar Categoria Socio</strong></div>
        <div class="panel-body">
                    <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/socios/categorias/editar-do/<?=$categoria->id?>" method="post">

                <div class="form-group">
                    <label for="" class="col-sm-2">Nombre</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="nombre" value="<?=$categoria->nombre?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Precio</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?=$categoria->precio?>" name="precio">
                    </div>
                </div>                                 
                <div class="form-group">
                    <label for="" class="col-sm-2">Precio Unitario Adicional</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?=$categoria->precio_unit?>" name="precio_unit">
                    </div>
                </div>                                 
                <div class="form-group">
                    <label for="" class="col-sm-2">Estado</label>
                    <div class="col-sm-10">
                        <select name="estado" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                        <option value="1" <? if($categoria->estado == 1){ echo 'selected';} ?>> ACTIVA </option>
                        <option value="2" <? if($categoria->estado == 2){ echo 'selected';} ?>> SUSPENDIDA </option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Guardar Cambios</button>
            </form>
        </div>
    </div>
</section>                    
