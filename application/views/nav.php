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
        <input id="dni-search" placeholder="Búsqueda Rápida"> <button class="btn-success" onclick="document.location.href='<?=$_GET['baseurl']?>admin/morosos'" style="padding:6px;">Ir</button>
    </div>
    <ul id="nav"
        data-ng-controller="NavCtrl"
        data-collapse-nav
        data-slim-scroll
        data-highlight-active>        
        <li>
            <a href="<?=$_GET['baseurl']?>admin/socios"><i class="fa fa-user"><span class="icon-bg bg-orange"></span></i><span data-i18n="Socios"></span></a>
            <ul <? is_active($_GET['section'],'socios') ?>>
                <li><a href="<?=$_GET['baseurl']?>admin/socios"><i class="fa fa-caret-right"></i><span data-i18n="Listado"></span></a></li>
                <li><a href="<?=$_GET['baseurl']?>admin/socios/agregar"><i class="fa fa-caret-right"></i><span data-i18n="Nuevo"></span></a></li>
                <li><a href="<?=$_GET['baseurl']?>admin/socios"><i class="fa fa-caret-right"></i><span data-i18n="Buscar"></span></a></li>
                <li><a href="<?=$_GET['baseurl']?>admin/socios/categorias"><i class="fa fa-caret-right"></i><span data-i18n="Categorias"></span></a></li>
            </ul>
        </li>         
        <li>
            <a href="<?=$_GET['baseurl']?>admin/actividades"><i class="fa fa-table"><span class="icon-bg bg-warning"></span></i><span data-i18n="Actividades"></span></a>
            <ul <? is_active($_GET['section'],'actividades') ?>>
                <li><a href="<?=$_GET['baseurl']?>admin/actividades"><i class="fa fa-caret-right"></i><span data-i18n="Listado"></span></a></li>
                <li><a href="<?=$_GET['baseurl']?>admin/actividades/agregar"><i class="fa fa-caret-right"></i><span data-i18n="Nuevo"></span></a></li>
                <li><a href="<?=$_GET['baseurl']?>admin/actividades"><i class="fa fa-caret-right"></i><span data-i18n="Buscar"></span></a></li>
            </ul>
        </li>
        <li>
            <a href="<?=$_GET['baseurl']?>admin/pagos"><i class="fa fa-dollar"><span class="icon-bg bg-success"></span></i><span data-i18n="Pagos"></span></a>
            <ul class="sub-nav" <? is_active($_GET['section'],'pagos') ?>>
                <li><a href="<?=$_GET['baseurl']?>admin/pagos"><i class="fa fa-caret-right"></i><span data-i18n="Listado de Morosos"></span></a></li>
                <li><a href="<?=$_GET['baseurl']?>admin/pagos/registrar"><i class="fa fa-caret-right"></i><span data-i18n="Registrar Pago"></span></a></li>
                <li><a href="<?=$_GET['baseurl']?>admin/pagos/facturacion"><i class="fa fa-caret-right"></i><span data-i18n="Facturación Mensual"></span></a></li>
                <li><a href="<?=$_GET['baseurl']?>admin/pagos/deuda"><i class="fa fa-caret-right"></i><span data-i18n="Financiar Deuda"></span></a></li>
                <li><a href="<?=$_GET['baseurl']?>admin/pagos/cupon"><i class="fa fa-caret-right"></i><span data-i18n="Cupón Manual"></span></a></li>
            </ul>
        </li>        
        <li>
            <a href="<?=$_GET['baseurl']?>admin/estadisticas/facturacion"><i class="fa fa-bar-chart-o"><span class="icon-bg bg-primary-light"></span></i><span data-i18n="Estadisticas"></span></a>
            <ul <? is_active($_GET['section'],'estadisticas') ?>>
                <li><a href="<?=$_GET['baseurl']?>admin/estadisticas/facturacion"><i class="fa fa-caret-right"></i><span data-i18n="Facturación"></span></a></li>
                <li><a href="<?=$_GET['baseurl']?>admin/estadisticas/actividades"><i class="fa fa-caret-right"></i><span data-i18n="Actividades"></span></a></li>            
            </ul>
        </li>        
    </ul>
</div>