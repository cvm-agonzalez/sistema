<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Agregar Actividad</strong></div>
        <div class="panel-body">
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/actividades/nueva" method="post">
                <div class="form-group">
                    <label for="" class="col-sm-2">Nombre</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Comisión</label>
                    <div class="col-sm-10">
                       
                        <select name="profesor" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                             <option value="">---------</option>
                        <?
                        foreach ($comisiones as $comision) {
                        ?>
                        <option value="<?=$comision->id?>" > <?=$comision->descripcion?> </option>
                        <?
                        }
                        ?>
                        </select>                        
                    </div>
                </div>    
                <div class="form-group">
                    <label for="" class="col-sm-2">Lugar</label>
                    <div class="col-sm-10">
                        
                        <select name="lugar" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                            <option value="">---------</option>
                        <?
                        foreach ($lugares as $lugar) {                                            
                        ?>
                        <option value="<?=$lugar->Id?>"><?=$lugar->nombre?> [<?=$lugar->direccion?>]</option>
                        <?
                        }
                        ?>
                        </select>
                    </div>
                </div>  

                <div class="form-group">
                    <label for="" class="col-sm-2">Precio</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="precio">
                    </div>
                </div>                               
                <div class="form-group">
                    <label for="" class="col-sm-2">Cuota Inicial</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="cuota_inicial">
                    </div>
                </div>                               
                <div class="form-group">
                    <label for="" class="col-sm-2">Seguro</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="seguro">
                    </div>
                </div>                               
                <div class="form-group">
                    <label for="" class="col-sm-2">Estado</label>
                    <div class="col-sm-10">
                        <select name="estado" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                        <option value="1" selected > ACTIVA </option>
                        <option value="2"  SUSPENDIDA </option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Solo Socios</label>
                    <div class="col-sm-10">
                        <select name="solo_socios" style="margin:0px; width:100%; border:1px solid #cbd5dd; padding:8px 15px 7px 10px;">
                        <option value="1" selected > SI </option>
                        <option value="0" > NO </option>
                        </select>
                    </div>
                </div>

                
                <button type="submit" class="btn btn-success">Agregar</button>
            </form>
        </div>
    </div>
</section>                    
