<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Agregar Categoria Socio</strong></div>
        <div class="panel-body">
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/socios/categorias/agregar-do" method="post">
                <div class="form-group">
                    <label for="" class="col-sm-2">Nombre</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Precio</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="precio">
                    </div>
                </div>                               
                <div class="form-group">
                    <label for="" class="col-sm-2">Precio Unitario Adicional</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="precio_unit">
                    </div>
                </div>                               
                
                <button type="submit" class="btn btn-success">Agregar</button>
            </form>
        </div>
    </div>
</section>                    
