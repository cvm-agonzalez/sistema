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
        .frente{
            background-image:url(<?=base_url()?>images/carnet-frente-new.png); 
	    background-size: 100% 100%;
        }
        .dorso{
            background-image:url(<?=base_url()?>images/carnet-dorso-new.png); 
	    background-size: 100% 100%;
        }
        .imagen{
            margin-top:50px;
            margin-left: 15px;
            width: 80px;
            float: left;
        }
        .datos{
            float:right;
            width: 165px;
            color: #FFF;
            margin-top:50px;
            line-height: 15px;
        }
        .clear{
            clear: both;
        }
        .barcode{
            margin-top: 0px;
        }
		</style>
	</head>

	<!-- <body onload="window.print(); window.close();"> -->

    <div class="carnet frente"></div>
    <div class="carnet dorso">
        <div class="imagen">
            <?
            if(file_exists('images/socios/'.$socio->Id.'.jpg')){
                
            ?>
                <img src="<?=base_url()?>images/image_carnet.php?img=socios/<?=$socio->Id?>.jpg" width="80">
            <?
            }else{
            ?>
                <img src="<?=base_url()?>images/noPic.jpg" width="80">
            <?
            }
            ?>
        </div>
        <? 
        if($socio->socio_n){
            $num = $socio->socio_n;
        }else{
            $num = $socio->Id;
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
            if( file_exists("images/cupones/".$cupon->Id.".png") ){
            ?>
            <br>
            <img src="<?=base_url()?>images/cupones/<?=$cupon->Id?>.png" >  
            <?
            }
            ?>
        </div>
    </div>

    <!--
		<div style="float:left; width:48%">
            <div style="float:left; width:100px; border:2px solid #000; height:100px;">
            <?
            if(file_exists('images/socios/'.$socio->Id.'.jpg')){
                
            ?>
                <img src="<?=base_url()?>images/image_carnet.php?img=socios/<?=$socio->Id?>.jpg" width="100">
            <?
            }else{
            ?>
                <img src="<?=base_url()?>images/g1.jpg" width="100">
            <?
            }
            ?>                
            </div>
            <div align="left" style="float:left; width:60%; padding-left:4%; line-height:20px;">
                &nbsp;<strong>Apellido:</strong> <?=$socio->apellido?><br>
                &nbsp;<strong>Nombres:</strong> <?=$socio->nombre?><br>
                &nbsp;<strong>D.N.I.:</strong> <?=$socio->dni?><br>

                <? 
                if($socio->socio_n){
                    $num = $socio->socio_n;
                }else{
                    $num = $socio->Id;
                }
                ?>

                &nbsp;<strong>Socio N°:</strong> <?=$num?><br>

                <?
                $fecha = explode(' ', $socio->alta);
                $fecha = explode('-', $fecha[0]);
                $fecha = $fecha[2].'/'.$fecha[1].'/'.$fecha[0];
                ?>

                <strong>Fecha de Ingreso:</strong> <?=$fecha?>

            </div>
            <div class="clear:both;"></div>
        </div>
        <div style="float:left; width:44%; border-left:1px dotted #000; padding-left:1%" align="center">
            <div style="float:left; width:29%;">
                <img src="<?=base_url()?>images/carnet.png" width="90">
            </div>
            <div style="float:left; width:65%; padding-top:0px;">
                <?
                if( file_exists("images/cupones/".$cupon->Id.".png") ){
                ?>
                    <img src="<?=base_url()?>images/cupones/<?=$cupon->Id?>.png">                   
                <div align="center" style="padding-top:10px; padding-left: 35px">
                    Valor de la Cuota: $ <?=$monto?>
                </div>
                <?
                }
                ?>
            </div>
            <div style="clear:both;"></div>
            <span style="font-size:10px;">Este carnet carece de validez si no se acompaña con el recibo actualizado de la cuota social.</span>
      
        </div>
	</body>-->
</html>
