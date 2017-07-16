<section class="page page-profile">
    <div class="panel panel-default">
        <div class="panel-heading"><strong><span class="fa fa-plus"></span> Agregar Usuario</strong></div>
        <div class="panel-body">
            <form class="form-horizontal ng-pristine ng-valid" action="<?=$baseurl?>admin/admins/agregar" method="post">

                <div class="form-group">
                    <label for="" class="col-sm-2">Nombre de usuario</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2">E-Mail</label>
                    <div class="col-sm-10">
                        <input type="mail" class="form-control">
                    </div>
                </div>    
                <div class="form-group">
                    <label for="" class="col-sm-2">Dirección</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control">
                    </div>
                </div>                 
                <button type="submit" class="btn btn-success">Agregar</button>
            </form>
        </div>
    </div>
</section>                    