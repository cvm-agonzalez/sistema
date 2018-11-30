  <div class="page page-table" data-ng-controller="tableCtrl2">
    <div class="panel panel-default table-dynamic">
      <div class="panel-heading"><strong><span class="fa fa-user"></span> ACTIVIDADES</strong></div>        
        <div class="table-filters">
            <div class="row">
                <div class="col-sm-4 col-xs-6">
                    <form>
                        <input type="text"
                               placeholder="Buscar"
                               class="form-control"
                               data-ng-model="searchKeywords"
                               data-ng-keyup="search()">
                    </form>
                </div>
                <div class="col-sm-3 col-xs-6 filter-result-info">
                    <span>
                        Mostrando {{filteredStores.length}}/{{stores.length}} actividades
                    </span>              
                </div>
                <div class="col-sm-3 col-xs-12 filter-result-info" id="cargando_acts" align="right">
                    <i class="fa fa-spinner fa-spin"></i> <strong>Cargando Listado de Actividades...</strong>
                </div>
            </div>
        </div>                
        <table class="table table-bordered table-striped table-responsive">
            <thead>
                <tr>
                    <th><div class="th">
                        Código
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('name') "
                              data-ng-class="{active: row == 'name'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-name') "
                              data-ng-class="{active: row == '-name'}"></span>
                    </div></th>
                    <th><div class="th">
                        Actividad
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('name') "
                              data-ng-class="{active: row == 'name'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-name') "
                              data-ng-class="{active: row == '-name'}"></span>
                    </div></th>                    
                    <th><div class="th">
                        Precio
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('price') "
                              data-ng-class="{active: row == 'price'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-price') "
                              data-ng-class="{active: row == '-price'}"></span>
                    </div></th>
                    <th><div class="th">
                        Cuota Inicial
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('cta_inic') "
                              data-ng-class="{active: row == 'cta_inic'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-cta_inic') "
                              data-ng-class="{active: row == '-cta_inic'}"></span>
                    </div></th>
                    <th><div class="th">
                        Seguro
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('seguro') "
                              data-ng-class="{active: row == 'seguro'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-seguro') "
                              data-ng-class="{active: row == '-seguro'}"></span>
                    </div></th>
                    <th><div class="th">
                        Solo Socios
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('solo_socios') "
                              data-ng-class="{active: row == 'solo_socios'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-solo_socios') "
                              data-ng-class="{active: row == '-solo_socios'}"></span>
                    </div></th>
                    <th><div class="th">
                        Estado
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('estado') "
                              data-ng-class="{active: row == 'estado'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-estado') "
                              data-ng-class="{active: row == '-estado'}"></span>
                    </div></th>
                    <th><div class="th">
                        Opciones                        
                    </div></th>
                </tr>
            </thead>
            <tbody>
                <tr data-ng-repeat="store in currentPageStores">                    
                    <td>{{store.id}}</td>
                    <td>{{store.name}}</td>
                    <td>{{store.price}}</td>
                    <td>{{store.cta_inic}}</td>
                    <td>{{store.seguro}}</td>
                    <td>{{store.solo_socios}}</td>
                    <td>{{store.estado}}</td>
                    <td>
                        <a href="#" id="imprimir_listado_actividades" data-act="{{store.id}}">Imprimir Listado</a>  
			<? if ( $rango < 2 ) { ?>
                        	<a href="<?=base_url()?>admin/actividades/editar/{{store.id}}">| Editar</a>  
                        	<a href="<?=base_url()?>admin/actividades/eliminar/{{store.id}}" onclick="return check_eliminar_act()">| Eliminar</a>
			<? } ?>
                    </td>
                </tr>
                <?
                foreach ($actividades as $actividad) {              
                ?>
                <!--<tr>
                    <td><?=$actividad->Id?></td>
                    <td><?=$actividad->nombre?></td>
                    
                    <td><?=$actividad->precio?></td>
                    <td><a href="<?=base_url()?>admin/actividades/editar/<?=$actividad->Id?>">Editar</a> | 
                      <a id="btn-eliminar-actividad" href="<?=base_url()?>admin/actividades/eliminar/<?=$actividad->Id?>">Eliminiar</a></td>
                </tr>-->
                <?
                }
                ?>                              
            </tbody>
        </table>
        <footer class="table-footer">
            <div class="row">
                <div class="col-md-6 page-num-info">
                    <span>
                        Mostrar 
                        <select data-ng-model="numPerPage"
                                data-ng-options="num for num in numPerPageOpt"
                                data-ng-change="onNumPerPageChange()">
                        </select> 
                        actividades por página
                    </span>
                </div>
                <div class="col-md-6 text-right pagination-container">
                    <pagination class="pagination-sm"
                                page="currentPage"
                                total-items="filteredStores.length"
                                max-size="4"
                                on-select-page="select(page)"
                                items-per-page="numPerPage"
                                rotate="false"
                                boundary-links="true"></pagination>
                </div>
            </div>
        </footer>      
    </div>
  </div>
