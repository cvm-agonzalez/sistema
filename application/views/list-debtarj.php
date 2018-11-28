  <div class="page page-table" data-ng-controller="tableDebTarj">
    <div class="panel panel-default table-dynamic">
      <div class="panel-heading"><strong><span class="fa fa-user"></span> DEBITOS</strong></div>        
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
                <div class="col-sm-3 col-xs-7 filter-result-info">
                    <span>
                        Mostrando {{filteredStores.length}}/{{stores.length}} debitos
                    </span>
                </div>
		<form id="debtarj_botones_form" method="post">
                        <? if ( $rango < 2 ) { ?>
                		<div class="col-sm-2 col-xs-3 " id="debtarj_nuevo" align="left">
                        		<button class="btn-success fa fa-plus" data-text="nuevo" data-action="<?=base_url()?>admin/debtarj/0" >Nuevo Debito <i class="fa fa-spin fa-spinner hidden"></i></button>
                		</div>
			<? } ?>
                <div class="col-sm-2 col-xs-3" id="debtarj_excel" align="right">
                        <button class="btn-success fa fa-cloud-download" data-text="excel" data-action="<?=base_url()?>admin/debtarj/list-debtarj/excel" >Excel <i class="fa fa-spin fa-spinner hidden"></i></button>
                </div>
		</form>
            </div>
        </div>

        <table class="table table-bordered table-striped table-responsive">
            <thead>
                <tr>
                    <th><div class="th">
                        ID
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('id') "
                              data-ng-class="{active: row == 'id'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-id') "
                              data-ng-class="{active: row == '-id'}"></span>
                    </div></th>
                    <th><div class="th">
                        Socio
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('sid') "
                              data-ng-class="{active: row == 'sid'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-sid') "
                              data-ng-class="{active: row == '-sid'}"></span>
                    </div></th>                    
                    <th><div class="th">
                        DNI
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('dni') "
                              data-ng-class="{active: row == 'dni'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-dni') "
                              data-ng-class="{active: row == '-dni'}"></span>
                    </div></th>                    
                    <th><div class="th">
                        Apellido y Nombre
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('name') "
                              data-ng-class="{active: row == 'name'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-name') "
                              data-ng-class="{active: row == '-name'}"></span>
                    </div></th>                    
                    <th><div class="th">
                        Ult Debito
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('fecha') "
                              data-ng-class="{active: row == 'fecha'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-fecha') "
                              data-ng-class="{active: row == '-fecha'}"></span>
                    <th><div class="th">
                        Tarjeta
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('tarjeta') "
                              data-ng-class="{active: row == 'tarjeta'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-tarjeta') "
                              data-ng-class="{active: row == '-tarjeta'}"></span>
                    <th><div class="th">
                        Nro Tarjeta
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('nro_tarjeta) "
                              data-ng-class="{active: row == 'nro_tarjeta}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-nro_tarjeta) "
                              data-ng-class="{active: row == '-nro_tarjeta}"></span>
                    <th><div class="th">
                        Importe
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('price') "
                              data-ng-class="{active: row == 'price'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-price') "
                              data-ng-class="{active: row == '-price'}"></span>
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
                        Acciones
                    </div></th>
                </tr>
            </thead>
            <tbody>
                <tr data-ng-repeat="store in currentPageStores">                    
                    <td>{{store.id}}</td>
                    <td>{{store.sid}}</td>
                    <td>{{store.dni}}</td>
                    <td>{{store.name}}</td>
                    <td>{{store.fecha}}</td>
                    <td>{{store.tarjeta}}</td>
                    <td>{{store.nro_tarjeta}}</td>
                    <td>{{store.price}}</td>
                    <td>{{store.estado}}</td>
                    <td>
                        <? if ( $rango < 2 ) { ?>
                        	<a href="<?=base_url()?>admin/debtarj/{{store.sid}}">Editar</a> | 
                        	<a href="<?=base_url()?>admin/debtarj/eliminar/{{store.id}}" >Eliminar</a> |
                        	<a href="<?=base_url()?>admin/debtarj/stopdebit/{{store.id}}" >Stop DB</a> |
                        <? } ?>
                        <a href="<?=base_url()?>admin/socios/resumen/{{store.sid}}" >Ver Resumen</a>

                    </td>
                </tr>
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
                        Debitos por página
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
