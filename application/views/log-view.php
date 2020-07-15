<div class="page page-table" >
	<div class="panel panel-default table-dynamic">
      	<div class="panel-heading"><strong><span class="fa fa-dollar"></span>VER LOG</strong></div>
	<div class="panel-body">
		<form id="log_view_form" method="post" action="<?=$baseurl?>admin/admins/ult_movs">

			<div class="col-sm-12">
				<div class="col-sm-3">
                     			<label for="" class="col-sm-3">login </label>
                       			<select name="login" id="login" >
						<? 
							if ( $rango == 0 ) {
							?>
              							<option value="todos" <? if($login == "todos"){echo 'selected';} ?> >Todos</option>
							<?
							}
						  foreach ( $logines as $login ) { 
						?>
              						<option value="<?=$login->login?>" <? if($login == $login->login){echo 'selected';} ?> ><?=$login->login?></option>
						<? } ?>
                       			</select>
				</div>
				<div class="col-sm-4">
                     			<label for="" class="col-sm-4">Dias Historia</label>
                       			<select name="dias" id="dias" >
              					<option value="1" <? if($dias == 1){echo 'selected';} ?> >Ultimas 24 Hs</option>
              					<option value="7" <? if($dias == 7){echo 'selected';} ?> >Ultima semana</option>
              					<option value="30" <? if($dias == 30){echo 'selected';} ?> >Ultimo mes</option>
              					<option value="60" <? if($dias == 60){echo 'selected';} ?> >Ultimos dos meses</option>
              					<option value="120" <? if($dias == 120){echo 'selected';} ?> >Ultimas 4 meses</option>
                       			</select>
                		</div>
				<div class="col-sm-3">
                        		<label for="" class="col-sm-12" > &nbsp; </label>
	                     		<button class="btn btn-success"  >Procesa<i id="gen_nvo" class="fa fa-spin fa-spinner hidden"></i></button>
				</div>
                	</div>
	                <div class="col-sm-12">
                        	<label for="" class="col-sm-12" > &nbsp; </label>
                	</div>
					
		</form>

		<div class="clearfix"></div>

		<div class="panel panel-default table-dynamic">
                <div class="form-group col-lg-18">

			 <table class="table table-bordered table-striped table-responsive table-resumen">
                                <thead>
                                        <tr>
                                                <th><div class="th-resumen">Login</div></th>
                                                <th><div class="th-resumen">TimeStamp</div></th>
                                                <th><div class="th-resumen">Operacion</div></th>
                                                <th><div class="th-resumen">Tabla</div></th>
                                                <th><div class="th-resumen">Observacion</div></th>
                                        </tr>
                                </thead>
                                <tbody>
                                        <?
                                        foreach ($logs as $log) {
                                        ?>
                                        <tr>
                                                <td><?=$log->login?></td>
                                                <td><?=$log->log_ts?></td>
                                                <td><?=$log->operacion?></td>
                                                <td><?=$log->tabla?></td>
                                                <td><?=$log->observacion.substr(0,30)?></td>
                                        </tr>
					<? } ?>
                                                                    
				</tbody>
		</div>
		</div>

        </div>
      </div>
    </div>
</div>
