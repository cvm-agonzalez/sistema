<html>
	<head>
		<meta charset="utf-8">
		<title>Imprimir</title>
		<style type="text/css">
		body{
			font-family: 'Arial';
			font-size: 12px;            
			background: #ffffff;            
		}
        strong{
            font-size: 10px;
            font-weight: normal; 
        }
        .nap{
            height:15px;overflow: hidden;
        }
        .carnet{
            width: 300px;
            height: 194px;
            float: left;
        }
	<?

        $ent_directorio = $this->session->userdata('ent_directorio');
        $frente=base_url()."entidades/".$ent_directorio."/carnet-frente.jpg";
        $dorso=base_url()."entidades/".$ent_directorio."/carnet-dorso.jpg";

	?>
        .frente{
            background-image:url(<?=$frente?>); 
	    background-size: 100% 100%;
        }
        .dorso{
            background-image:url(<?=$dorso?>); 
	    background-size: 100% 100%;
        }
        .imagen{
            margin-top:44px;
            margin-left: 30px;
            width: 80px;
            float: left;
        }
        .datos{
            float:right;
            width: 175px;
            color: #000;
            margin-top:50px;
            line-height: 15px;
        }
        .clear{
            clear: both;
        }
        .barcode{
            margin-top: 125px;
            margin-left: 15px;
        }
		</style>
	</head>

	<!-- <body onload="window.print(); window.close();"> -->

    <div class="carnet frente"></div>
    <div class="carnet dorso">
        <div class="imagen">
            <?
            if(file_exists( BASEPATH."../entidades/".$ent_directorio."/socios/".$socio->id.".jpg" )){
		$imagen = BASEPATH."../entidades/".$ent_directorio."/socios/".$socio->id.".jpg";
            ?>
                <img src="<?=base_url()?>entidades/image_carnet.php?img=<?=$imagen?>" width="80">
            <?
            }else{
            ?>
                <img src="<?=base_url()?>entidades/<?=$ent_directorio?>/noPic.jpg" width="80">
            <?
            }
            ?>
        </div>
        <? 
        if($socio->nro_socio){
            $num = $socio->nro_socio;
        }else{
            $num = $socio->id;
        }        
        $fecha = explode(' ', $socio->alta);
        $fecha = explode('-', $fecha[0]);
        $fecha = $fecha[2].'/'.$fecha[1].'/'.$fecha[0];
        ?>

        <div class="datos">
            <div class="nap" style="font-weight:bold"><?=ucfirst($socio->apellido)?> <?=ucfirst($socio->nombre)?></div>
            <div class="nap" style="font-weight:bold">DNI <?=$socio->dni?></div>
            <div class="nap" style="font-weight:bold">Socio No. <?=$num?></div>
            <div class="nap" style="font-weight:bold">Ingreso <?=$fecha?></div>
        </div>
        <div align="right" class="barcode">
            <?
            if( file_exists(BASEPATH."../entidades/".$ent_directorio."/cupones/".$cupon->id.".png") ){
		$barra = base_url()."../entidades/".$ent_directorio."/cupones/".$cupon->id.".png";
            ?>
            <br>
            <img src="<?=$barra?>" >  
            <?
            }
            ?>
        </div>
    </div>

</html>
