<?
    function is_active($uri,$actual){
        $ur = explode("-",$uri);
        if($ur[0] == $actual){
            echo 'style="display:block"';
        }
    }
?>
<div id="nav-wrapper">
    <ul id="nav"
        data-ng-controller="NavCtrl"
        data-collapse-nav
        data-slim-scroll
        data-highlight-active>        
        <li>
            <a href="<?=$_GET['baseurl']?>comisiones/socios"><i class="fa fa-user"><span class="icon-bg bg-orange"></span></i><span data-i18n="Socios"></span></a>
            <ul <? is_active($_GET['section'],'socios') ?>>
                <li><a href="<?=$_GET['baseurl']?>facturacion/view"><i class="fa fa-caret-right"></i><span data-i18n="Facturacion y Cobranza"></span></a></li>
                <li><a href="<?=$_GET['baseurl']?>lista_socios_act/view"><i class="fa fa-caret-right"></i><span data-i18n="Listado de Socios x Actividad"></span></a></li>
                <li><a href="<?=$_GET['baseurl']?>lista_morosos/view"><i class="fa fa-caret-right"></i><span data-i18n="Listado de Morosos"></span></a></li>
            </ul>
        </li>         
    </ul>
</div>
