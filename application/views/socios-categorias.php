                    <div class="page page-profile">
                        <div class="panel panel-default">
                            <div class="panel-heading"><strong><span class="fa fa-user"></span> CATEGORIAS</strong></div>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Precio</th>
                                        <th>Socios</th>
                                        <th>Opciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <tr>
                                        <td>1</td>
                                        <td><span class="color-success">Activos</td>
                                        <td>70</td>
                                        <td><span class="label label-info">200</span></td>
                                        <td>
                                            <a href=""><i class="fa fa-gear"></i> Editar</a>  | 
                                            <a href=""><i class="fa fa-times"></i> Eliminar</a>
                                        </td>
                                    </tr>                                    
                                    <tr>
                                        <td>1</td>
                                        <td><span class="color-success">Cadete</td>
                                        <td>70</td>
                                        <td><span class="label label-info">240</span></td>
                                        <td>
                                            <a href=""><i class="fa fa-gear"></i> Editar</a>  | 
                                            <a href=""><i class="fa fa-times"></i> Eliminar</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td><span class="color-success">Grupo Familiar</td>
                                        <td>70</td>
                                        <td><span class="label label-info">170</span></td>
                                        <td>
                                            <a href=""><i class="fa fa-gear"></i> Editar</a>  | 
                                            <a href=""><i class="fa fa-times"></i> Eliminar</a>
                                        </td>
                                    </tr>                                    

                                </tbody>
                            </table>
                        </div>
                    </div>
<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Agregar Categoria</strong></div>
        <div class="panel-body">
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/admins/agregar" method="post">

                <div class="form-group">
                    <label for="" class="col-sm-2">Nombre</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">Precio</label>
                    <div class="col-sm-10">
                        <input type="mail" class="form-control">
                    </div>
                </div>                    
                <button type="submit" class="btn btn-success">Agregar</button>
            </form>
        </div>
    </div>
</section>                    