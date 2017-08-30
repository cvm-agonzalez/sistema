<div class="page page-table" data-ng-controller="tableCtrl">
    <section class="panel panel-default table-dynamic">
      <div class="panel-heading"><strong><span class="fa fa-user"></span> SOCIOS</strong></div>

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
                        Mostrando {{filteredStores.length}}/{{stores.length}} socios
                    </span>        
                </div>
                <div class="col-sm-3 col-xs-12 filter-result-info" id="cargando_socios" align="right">
                    <i class="fa fa-spinner fa-spin"></i> <strong>Cargando Listado de Socios...</strong>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped table-responsive" id="clientes-table">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th><div class="th">
                        Nombre y Apellido (DNI)
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('name') "
                              data-ng-class="{active: row == 'name'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-name') "
                              data-ng-class="{active: row == '-name'}"></span>
                    </div></th>
                    <th><div class="th">
                        Cuota
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
                        Deuda Cta Social
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('deuda') "
                              data-ng-class="{active: row == 'deuda'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-deuda') "
                              data-ng-class="{active: row == '-deuda'}"></span>
                    </div></th>
                    <th><div class="th">
                        Actividades asociadas
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('actividades') "
                              data-ng-class="{active: row == 'actividades'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-actividades') "
                              data-ng-class="{active: row == '-actividades'}"></span>
                    </div></th>
                    <th><div class="th">
                        Opciones                        
                    </div></th>
                </tr>
            </thead>            
            <tbody>

                <div>
                <tr data-ng-repeat="store in currentPageStores">
                    <td class="fa"><a href="#" id="td_socio_{{store.id}}" ng-click="showInfo(this, store.id,'<?=$baseurl?>')" class="fa-plus-square-o"></a></td>
                    <td><a href="<?=base_url()?>admin/socios/resumen/{{store.id}}">{{store.name}} ({{store.dni}})</a></td>
                    <td align="right">$ {{store.price}}</td>
                    <td align="center">{{store.estado}}</td>
                    <td align="right">$ {{store.deuda}}</td>
                    <td>{{store.actividades}}</td>
                    <td>
                        <a href="<?=base_url()?>admin/socios/editar/{{store.id}}">Editar</a> |
                        <a href="<?=base_url()?>admin/socios/resumen/{{store.id}}">Ver Resumen</a>
                    </td>
                </tr>                
                </div>

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
                        socios por página
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

    </section>
  </div>
    
