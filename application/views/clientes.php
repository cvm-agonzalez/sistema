  <div class="page page-table" data-ng-controller="tableCtrl">
    <div class="panel panel-default table-dynamic">
      <div class="panel-heading"><strong><span class="fa fa-user"></span> CLIENTES</strong></div>

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
                        Mostrando {{filteredStores.length}}/{{stores.length}} clientes
                    </span>              
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped table-responsive" id="clientes">
            <thead>
                <tr>
                    <th width="10"></th>
                    <th><div class="th">
                        Nombre y Apellido
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
                        Actividades asociadas
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('sales') "
                              data-ng-class="{active: row == 'sales'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-sales') "
                              data-ng-class="{active: row == '-sales'}"></span>
                    </div></th>
                    <th><div class="th">
                        Opciones
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('rating') "
                              data-ng-class="{active: row == 'rating'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-rating') "
                              data-ng-class="{active: row == '-rating'}"></span>
                    </div></th>
                </tr>
            </thead>
            <tbody>
                <tr> 
                    <td class="fa"><a href="#" id="cliente_info_toogle" class="fa-plus-square-o"></a></td>
                    <td>Carlos Alvarez</td>
                    <td>Al Día</td>
                    <td>Baquet, Futbol, Karate</td>
                    <td><a href="<?=base_url()?>admin/clientes/editar">Editar</a> | <a href="<?=base_url()?>admin/clientes/resumen">Ver Resumen</a></td>

                </tr>
                <tr dynarow="1" class="cliente_info">
                    <td></td>
                    <td colspan="4">
                        Generar Cupón | Enviar Resumen | Asociar Actividad | Eliminar
                    </td>
                </tr>
                <tr>
                    <td class="fa"><a href="#" id="cliente_info_toogle" class="fa-plus-square-o"></a></td>
                    <td>Julian Alonso</td>
                    <td style="background-color:#f33; color:#FFF;">Moroso</td>
                    <td>Futbol, Karate</td>
                    <td><a href="<?=base_url()?>admin/clientes/editar">Editar</a> | <a href="<?=base_url()?>admin/clientes/resumen">Ver Resumen</a></td>
                </tr>  
                <tr dynarow="1" class="cliente_info">
                    <td></td>
                    <td colspan="4">
                        Generar Cupón | Enviar Resumen | Asociar Actividad | Eliminar
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
                        clientes por página
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