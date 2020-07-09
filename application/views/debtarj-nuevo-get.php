<div class="page page-table" >
    <div class="panel panel-default table-dynamic">
        <div class="panel-heading"><strong><span class="fa fa-user"></span> ADHERIR DEBITO TARJETA CREDITO </strong></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-1 bg-info" align="center" style="padding:15px; margin-bottom:10px;">
                            <i class="fa fa-users text-large stat-icon"></i>
                    </div>
                    <form id="get_socio_dt_form" action="<?=$baseurl?>admin/debtarj/nuevo-get" method="post">
                        <div class="form-group col-lg-5" style="padding-top:20px;">                                            
                            <div id="id_socio" >
                                <div class="col-sm-7">
                                    <input type="text" name="sid" id="sid" class="form-control" placeholder="Ingrese ID Socio">
                                </div>
                                <div class="col-sm-7">

				<button id="btn_sid" type="submit" class="btn btn-primary">Buscar</button> 

                                </div>
				<? if ( $mensaje != '' ) { ?>
                                	<div class="col-sm-7">
					<label id="msj" ><?=$mensaje?></label> 
                                	</div>
				
				<? } ?>
                            </div>
                        </div> 
                    </form>

                </div>            
            </div>

    </div>
</div>

