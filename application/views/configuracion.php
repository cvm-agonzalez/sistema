<div class="page page-charts ng-scope" data-ng-controller="morrisChartCtrl">
    <div class="row">
      <div class="col-md-12">
        <section class="panel panel-default">
          <div class="panel-heading">
            <span class="glyphicon glyphicon-th"></span> CONFIGURACION GENERAL          
          </div>
          <div class="panel-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="conf-tab">
              <li class="active"><a href="#gral" data-toggle="tab">Opciones Generales</a></li>
              <li><a href="#categorias" data-toggle="tab">Categorías de Socios</a></li>            
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
              <div class="tab-pane active" id="gral">               
                <div class="row">
                  <div class="col-lg-12"><br></div>
                      <? if($this->uri->segment(3) == 'guardada'){ ?>
                      <div id="cambio_correcto" class="col-xs-10 col-xs-offset-1 alert alert-success">Los cambios se guardaron correctamente.</div>
                      
                      <? } ?>
                  <form action="<?=base_url()?>admin/configuracion/guardar" method="post"> 
                    <div class="form-group col-lg-12">
                        <label for="" class="col-sm-2">Intereses por mora:</label>
                        <div class="col-sm-5">
                          <div class="input-group">
                            <input type="number" name="interes_mora" class="form-control" placeholder="0.00" value="<?=$config->interes_mora?>" aria-describedby="basic-addon1" required>
                            <span class="input-group-addon" id="basic-addon1">%</span>
                          </div>
                        </div>
                        <div class="col-sm-5">
                          El porcentaje de la cuota mensual que se sumará a la deuda de un socio cuando esta no haya sido abonada los 20 de cada mes.
                        </div>
                    </div>

                    <div class="clearfix" align="center"></div>
                    <div align="center">
                      <button class="btn btn-success">Guardar Cambios</button>                      
                    </div>                      
                  </form>
                </div>
              </div>
              <div class="tab-pane" id="categorias"><br>
                <table class="table table-bordered table-striped table-responsive">
                  <tbody>
                    <tr>

                      <? $cont = 0; foreach ($cats as $categoria) { if($categoria->nomb != 'Tutor'){ ?>                     
                      <th><?=$categoria->nomb?></th>                      
                      <? $cont ++; }} ?>
                    </tr>
                    <tr>   
                      <?foreach ($cats as $categoria) { if($categoria->nomb != 'Tutor'){ ?>                       
                      <td style="width:<?=100/$cont;?>%;">
                        <div class="input-group"><span class="input-group-addon">$</span>
                          <input type="text" class="form-control" id="cat-precio" data-id="<?=$categoria->Id?>" value="<?=$categoria->precio?>">
                          <? if($categoria->nomb == 'Grupo Familiar'){ ?>                         
                        </div>
                        Precio por Familiar Excedente
                        <div class="input-group"><span class="input-group-addon">$</span>
                          <input type="text" class="form-control" id="cat-precio_unit" data-id="<?=$categoria->Id?>" value="<?=$categoria->precio_unit?>">                          
                          <? } ?>
                        </div>
                      </td>
                      <? }  } ?>
                    </tr>
                  </tbody>
                </table>
                <button id="cats-conf-save" class="btn btn-success btn-block">Guardar Cambios</button>
              </div>              
            </div>
          </div>
        </section>
      </div>
    </div>
