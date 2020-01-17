<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Editar Actividad</strong></div>
        <div class="panel-body">
                    <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/actividades/guardar/<?=$actividad->id?>" method="post">

                <div class="form-group">
                    <label for="" class="col-sm-2">Nombre</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="nombre" value="<?=$actividad->nombre?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Comisión</label>
                    <div class="col-sm-10">
                        <select name="comision" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                        <?
                        foreach ($comisiones as $comision) {                                            
                        ?>
                        <option value="<?=$comision->id?>" <? if($comision->id == $actividad->comision){ echo 'selected';} ?>>
                            <?=$comision->descripcion?> 
                        </option>
                        <?
                        }
                        ?>
                        </select>
                    </div>
                </div>    
                <div class="form-group">
                    <label for="" class="col-sm-2">Precio</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?=$actividad->precio?>" name="precio">
                    </div>
                </div>                                 
                <div class="form-group">
                    <label for="" class="col-sm-2">Cuota Inicial</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?=$actividad->cuota_inicial?>" name="cuota_inicial">
                    </div>
                </div>                                 
                <div class="form-group">
                    <label for="" class="col-sm-2">Seguro</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?=$actividad->seguro?>" name="seguro">
                    </div>
                </div>                                 
                <div class="form-group">
                    <label for="" class="col-sm-2">Estado</label>
                    <div class="col-sm-10">
                        <select name="estado" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                        <option value="1" <? if($actividad->estado == 1){ echo 'selected';} ?>> ACTIVA </option>
                        <option value="2" <? if($actividad->estado == 2){ echo 'selected';} ?>> SUSPENDIDA </option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Solo Socios</label>
                    <div class="col-sm-10">
                        <select name="solo_socios" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                        <option value="1" <? if($actividad->solo_socios == 1){ echo 'selected';} ?>> SI </option>
                        <option value="0" <? if($actividad->solo_socios == 0){ echo 'selected';} ?>> NO </option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-success">Guardar Cambios</button>
            </form>
        </div>
    </div>
</section>                    
