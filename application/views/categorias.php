  <div class="page page-table" data-ng-controller="tableCategoria">
    <div class="panel panel-default table-dynamic">
      <div class="panel-heading"><strong><span class="fa fa-user"></span> CATEGORIAS</strong></div>        
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
                        Mostrando {{filteredStores.length}}/{{stores.length}} categorias
                    </span>              
                </div>
                <div class="col-sm-3 col-xs-12 filter-result-info" id="cargando_cats" align="right">
                    <i class="fa fa-spinner fa-spin"></i> <strong>Cargando Listado de Categorias...</strong>
                </div>
            </div>
        </div>                
        <table class="table table-bordered table-striped table-responsive">
            <thead>
                <tr>
                    <th><div class="th">
                        Código
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('cid') "
                              data-ng-class="{active: row == 'cid'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-cid') "
                              data-ng-class="{active: row == '-cid'}"></span>
                    </div></th>
                    <th><div class="th">
                        Categoria Socio
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('nombre') "
                              data-ng-class="{active: row == 'nombre'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-nombre') "
                              data-ng-class="{active: row == '-nombre'}"></span>
                    </div></th>                    
                    <th><div class="th">
                        Precio
                        <span class="glyphicon glyphicon-chevron-up"
                              data-ng-click=" order('precio') "
                              data-ng-class="{active: row == 'precio'}"></span>
                        <span class="glyphicon glyphicon-chevron-down"
                              data-ng-click=" order('-precio') "
                              data-ng-class="{active: row == '-precio'}"></span>
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
                <?
                foreach ($categorias as $categoria) {              
			switch ( $categoria->estado ) {
				case '0': $xcateg="BAJA"; break;
				case '1': $xcateg="ACTIVA"; break;
				case '2': $xcateg="SUSPENDIDA"; break;
				default: $xcateg="REVISAR"; break;
			}
                ?>
		  <tr>
                    <td><?=$categoria->cid?></td>
                    <td><?=$categoria->nombre?></td>
                    
                    <td align='right'><?=$categoria->precio?></td>
                    <td><?=$xcateg?></td>
                    <td>
			<a href="<?=base_url()?>admin/socios/categorias/editar/<?=$categoria->id?>">Editar</a> | 
                        <a id="btn-eliminar-categoria" href="<?=base_url()?>admin/socios/categorias/eliminar/<?=$categoria->id?>">Eliminar</a>
		    </td>
                <?
                }
                ?>                              
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
