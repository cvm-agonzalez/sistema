<?
function is_active($uri,$actual){
    $ur = explode("-",$uri);
    if($ur[0] == $actual){
        echo 'style="display:block"';
    }
}
?>
<div id="nav-wrapper">
    <div class="dni-search">
        <form id="socio_search" action="<?=$_GET['baseurl']?>admin/socios/buscar" method="get">
            <input id="socio_search_input" name="dni" value="" placeholder="Búsqueda Rápida [DNI]"> 
            <button class="btn-success" style="padding:6px;">Ir</button>
        </form>
    </div>
    <ul id="nav"
    data-ng-controller="NavCtrl"
    data-collapse-nav
    data-slim-scroll
    data-highlight-active>        

<!--Opciones de Menu Socios-->
	<? switch($_GET['rango']) {
		case '0': 
		case '1': ?>
    			<li>
        			<a href="<?=$_GET['baseurl']?>admin/socios"><i class="fa fa-user"><span class="icon-bg bg-orange"></span></i><span data-i18n="Socios"></span></a>
        			<ul <? is_active($_GET['section'],'socios') ?>>
            			<li><a href="<?=$_GET['baseurl']?>admin/socios"><i class="fa fa-caret-right"></i><span data-i18n="Listado"></span></a></li>
            			<li><a href="<?=$_GET['baseurl']?>admin/socios/categorias"><i class="fa fa-caret-right"></i><span data-i18n="Categorias de Socios"></span></a></li>                
            			<li><a href="<?=$_GET['baseurl']?>admin/socios/agregar"><i class="fa fa-caret-right"></i><span data-i18n="Nuevo"></span></a></li>                
        			</ul>
    			</li>         
	<?		break;
		case '2': ?>
    			<li>
                                <a href="<?=$_GET['baseurl']?>admin/socios"><i class="fa fa-user"><span class="icon-bg bg-orange"></span></i><span data-i18n="Socios"></span></a>
                                <ul <? is_active($_GET['section'],'socios') ?>>
                                <li><a href="<?=$_GET['baseurl']?>admin/socios"><i class="fa fa-caret-right"></i><span data-i18n="Listado"></span></a></li>
        			</ul>

    			</li>         

	<? 		break;
		} ?>

<!--Opciones de Menu Actividades-->
        <? switch($_GET['rango']) {
                case '0': 
                case '1': ?>

    			<li>
        			<a href="<?=$_GET['baseurl']?>admin/actividades"><i class="fa fa-table"><span class="icon-bg bg-warning"></span></i><span data-i18n="Actividades"></span></a>
        			<ul <? is_active($_GET['section'],'actividades') ?>>
            			<li><a href="<?=$_GET['baseurl']?>admin/actividades"><i class="fa fa-caret-right"></i><span data-i18n="Listado"></span></a></li>
            			<li><a href="<?=$_GET['baseurl']?>admin/actividades/agregar"><i class="fa fa-caret-right"></i><span data-i18n="Nuevo"></span></a></li>                
            			<li><a href="<?=$_GET['baseurl']?>admin/actividades/comisiones"><i class="fa fa-caret-right"></i><span data-i18n="Comisiones"></span></a></li>
            			<li><a href="<?=$_GET['baseurl']?>admin/actividades/asociar"><i class="fa fa-caret-right"></i><span data-i18n="Asociar"></span></a></li>
            			<li><a href="<?=$_GET['baseurl']?>admin/actividades/load-asoc-activ"><i class="fa fa-caret-right"></i><span data-i18n="Relacion Masiva"></span></a></li>
        			</ul>
    			</li>
	<?		break;
		case '2': ?>
    			<li>
        			<a href="<?=$_GET['baseurl']?>admin/actividades"><i class="fa fa-table"><span class="icon-bg bg-warning"></span></i><span data-i18n="Actividades"></span></a>
        			<ul <? is_active($_GET['section'],'actividades') ?>>
            			<li><a href="<?=$_GET['baseurl']?>admin/actividades"><i class="fa fa-caret-right"></i><span data-i18n="Listado"></span></a></li>
        			</ul>
    			</li>         

	<? 		break;
		} ?>

<!--Opciones de Menu Pagos-->
        <? switch($_GET['rango']) {
                case '0': 
                case '1': ?>
    			<li>
        			<a href="<?=$_GET['baseurl']?>admin/pagos"><i class="fa fa-dollar"><span class="icon-bg bg-success"></span></i><span data-i18n="Pagos"></span></a>
        			<ul class="sub-nav" <? is_active($_GET['section'],'pagos') ?>>
            			<!--                <li><a href="<?=$_GET['baseurl']?>admin/pagos"><i class="fa fa-caret-right"></i><span data-i18n="Impresión de Listados"></span></a></li>-->
            			<li><a href="<?=$_GET['baseurl']?>admin/pagos/registrar"><i class="fa fa-caret-right"></i><span data-i18n="Registrar Pago"></span></a></li>            
            			<li><a href="<?=$_GET['baseurl']?>admin/pagos/cupon"><i class="fa fa-caret-right"></i><span data-i18n="Generar Cupón"></span></a></li>
            			<li><a href="<?=$_GET['baseurl']?>admin/pagos/editar"><i class="fa fa-caret-right"></i><span data-i18n="Editar Pagos"></span></a></li>
        			</ul>
    			</li>        
	<?		break;
		case '2': ?>
    			<li>
        			<a href="<?=$_GET['baseurl']?>admin/pagos"><i class="fa fa-dollar"><span class="icon-bg bg-success"></span></i><span data-i18n="Pagos"></span></a>
    			</li>
	<? 		break;
		} ?>

<!--Opciones de Menu Estadisticas-->
    <li>
        <a href="<?=$_GET['baseurl']?>admin/estadisticas/facturacion"><i class="fa fa-bar-chart-o"><span class="icon-bg bg-primary-light"></span></i><span data-i18n="Estadisticas"></span></a>
        <ul <? is_active($_GET['section'],'estadisticas') ?>>
            <li><a href="<?=$_GET['baseurl']?>admin/estadisticas/facturacion"><i class="fa fa-caret-right"></i><span data-i18n="Facturación"></span></a></li>
            <li><a href="<?=$_GET['baseurl']?>admin/estadisticas/cobranza"><i class="fa fa-caret-right"></i><span data-i18n="Cobranza"></span></a></li>            
        </ul>
    </li>

<!--Opciones de Menu Reportes-->
    <li>
        <a href="<?=$_GET['baseurl']?>admin/reportes"><i class="fa fa-tasks"><span class="icon-bg bg-warning"></span></i> <span data-i18n="Listados"></span></a>
        <ul class="sub-nav" <? is_active($_GET['section'],'reportes') ?>>
            <li><a href="<?=$_GET['baseurl']?>admin/reportes/socios"><i class="fa fa-caret-right"></i><span data-i18n="Listados de Socios"></span></a></li>
            <li><a href="<?=$_GET['baseurl']?>admin/reportes/cobros"><i class="fa fa-caret-right"></i><span data-i18n="Listados de Cobros"></span></a></li>
            <li><a href="<?=$_GET['baseurl']?>admin/reportes/exportar"><i class="fa fa-caret-right"></i><span data-i18n="Exportación Datos"></span></a></li>
        </ul>

    </li> 
   <? if ( $_GET['rango'] < 2 ) { ?>
    	<li>
        	<a href="<?=$_GET['baseurl']?>admin/envios"><i class="fa fa-envelope"><span class="icon-bg bg-success"></span></i> <span data-i18n="Envíos Masivos"></span></a>
    	</li> 
   <? } ?>
</ul>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $("input#socio_search_input").focus();
})
a= $('input#socio_search_input').autocomplete({ serviceUrl:'<?=$_GET['baseurl']?>autocomplete/get/socios-dni|nombre|apellido',
    onSelect: function (suggestion) {            
        $('input#socio_search_input').val(suggestion.data);
    } 
});
</script>
