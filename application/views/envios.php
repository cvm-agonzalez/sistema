<div class="page page-charts ng-scope" data-ng-controller="morrisChartCtrl">
   <div class="row">
      <div class="col-md-12">
         <section class="panel panel-default">
            <div class="panel-heading">
               <span class="glyphicon glyphicon-th"></span> ENVIOS
               <div class="pull-right" style="margin-top:-7px;">
                  <a href="<?=base_url()?>admin/envios/nuevo" class="btn btn-success"><i class="fa fa-plus"></i> Nuevo Envio</a>
               </div>
            </div>
            <div class="panel-body">
               <table class="table table-bordered">
                  <thead>
                     <tr>
                        <th>Nombre</th>
                        <th>Fecha</th>
                        <th>Enviados</th>
                        <th>Opciones</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?
                     if($envios){
                     foreach ($envios as $envio) {                     
                     ?>
                     <tr>
                        <td><span class="color-success"><?=$envio->titulo?></span></td>
                        <td><?=$envio->enviados?>/<?=$envio->total?></td>
                        <td><?=date('d/m/Y H:i:s',strtotime($envio->creado_el))?></td>
                        <td>
                           <?
                           if($envio->enviados < $envio->total){
                           ?>
                           <a href="<?=base_url()?>admin/envios/enviar/<?=$envio->Id?>"><i class="fa fa-play"></i> Continuar Envio </a>  | 
                           <?
                           }
                           ?>
                           <a href="<?=base_url()?>admin/envios/editar/<?=$envio->Id?>"><i class="fa fa-pencil"></i> Editar </a>  | 
                           <a id="del_confirm" data-msj="Seguro que desea eliminar este envio?" href="<?=base_url()?>admin/envios/eliminar/<?=$envio->Id?>"><i class="fa fa-trash-o"></i> Eliminar</a>
                        </td>
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
   </div>
</div>