  <div class="page page-table" data-ng-controller="tableCtrl">
    <div class="panel panel-default table-dynamic">
      <div class="panel-heading"><strong><span class="fa fa-user"></span> LISTADO DE MOROSOS</strong></div>

        
        <div id="table-morosos">
            <div style="padding:30px;">
                <div class="col-lg-2">
                    <label>Meses Adeudados</label>
                    <select class="form-control" id="morosos_meses">
                        <? for ($i=1; $i <= 12; $i++) { 
                        ?>
                        <option value="<?=$i?>" <? if($i == $meses){ echo 'selected'; } ?>><?=$i?></option>
                        <?
                        }
                        ?>
                    </select>
                </div>
                <div class="col-lg-2">
                    <label>Actividad</label>
                    <select class="form-control" id="morosos_activ">
                        <option value="">TODAS</option>
                        <?
                        foreach ($actividades as $actividad) {                        
                        ?>
                        <option value="<?=$actividad->Id?>" <? if($actividad->Id == $actividad_sel){ echo 'selected'; } ?>><?=$actividad->nombre?></option>
                        <?
                        }   
                        ?>
                    </select>                    
                </div>
                <?
                if(!$this->uri->segment(3)){
                    $meses = 6;
                }else{
                    $meses = $this->uri->segment(3);
                }
                ?>
                <div class="col-lg-3" style="margin-top:25px;">
                    <button class="btn btn-success" id="filtro_morosos"><i class="fa fa-filter"></i> Filtrar</button>
                    <button class="btn btn-warning" data-meses="<?=$meses?>" data-act="<?=$this->uri->segment(4)?>" id="imprimir_listado_morosos"><i class="fa fa-print"></i> Imprimir</button>
                </div>
                <div class="clearfix"></div>
            </div>            
            <table class="table table-bordered table-striped table-responsive" id="clientes" >
                <thead>
                    <tr>                        
                        <th><div class="th">
                            Nombre y Apellido                            
                        </div></th>
                        <th><div class="th">
                            Deuda                        
                        </div></th>                        
                        <th><div class="th">
                            Opciones                            
                        </div></th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    if($morosos){
                    foreach ($morosos as $moroso) {                                        
                    ?>
                    <tr>
                        <td><?=$moroso->nomb?></td>
                        <td>$ <?=$moroso->deuda*-1?></td>                        
                        <td><a href="<?=base_url()?>admin/socios/resumen/<?=$moroso->Id?>">Ver Resumen</a></td>
                    </tr>
                    <?
                    }
                    }
                    ?>
                </tbody>
            </table>            
        </div>
    </div>
  </div>