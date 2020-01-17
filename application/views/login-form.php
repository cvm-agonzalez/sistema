
<!doctype html>
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Ingreso Gestion de Socios</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic" rel="stylesheet" type="text/css">
        <!-- needs images, font... therefore can not be part of ui.css -->
        <link rel="stylesheet" href="<?=$baseurl?>bower_components/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?=$baseurl?>bower_components/weather-icons/css/weather-icons.min.css">
        <!-- end needs images -->

            <link rel="stylesheet" href="<?=$baseurl?>styles/ui.css"/>
            <link rel="stylesheet" href="<?=$baseurl?>styles/main.css">
                    <link rel="icon" href="<?=$baseurl?>images/favicon.png" type="image/x-icon" />
    </head>
    <body data-ng-app="app" id="app" data-custom-background="" data-off-canvas-nav="">
        <!--[if lt IE 9]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div data-ng-controller="AppCtrl">
            <div class="page-signin">

    <div class="signin-header">
        <div class="container text-center">
            <section class="logo">
                <a href="#/">Ingreso Gestion de Socios</a>
            </section>
        </div>
    </div>

    <div class="signin-body">
        <div class="container">
            <div class="form-container">

                <section class="row signin-social text-center">
                    
                </section>

                <?php
                $entidad = array('entidad' => 'entidad', 'placeholder' => 'Entidad');
                $username = array('name' => 'username', 'placeholder' => 'Usuario');
                $password = array('name' => 'password',    'placeholder' => 'Contraseña');
                $submit = array('name' => 'submit', 'value' => 'Iniciar sesión', 'title' => 'Iniciar sesión');
                ?>

                <form class="form-horizontal" name="loginUserForm" action="<?=$baseurl?>admin/login" method="post">
                    <fieldset>
                        <div ng-show="flash">
                            <div data-alert class="alert-box alert round"></div>
                        </div>                        
                        <div class="form-group">
                            <div class="input-group input-group-lg">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-home"></span>
                                </span>
<?
        if ( $id_entidad ) {
?>
                                <input type="text"
                                       class="form-control"
                                       name ="entidad"                                       
                                       value= "<?=$id_entidad.'-'.$ent_nombre?>"
                                       disabled >
<?
        } else {
?>
                                <input type="text"
                                       class="form-control"
                                       placeholder="Entidad"
                                       name ="entidad"                                       
                                       >
<?
        } 
?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group input-group-lg">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-user"></span>
                                </span>
                                <input type="text"
                                       class="form-control"
                                       placeholder="Usuario"
                                       name ="username"                                       
                                       >
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group input-group-lg">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-lock"></span>
                                </span>
                                <input type="password"
                                       class="form-control"
                                       placeholder="Contraseña"
                                       name="password"
                                       >
                                <input type="hidden" name="token" value="<?=$token?>">                                
                            </div>
                        </div>
                        <div class="form-group">
                        </div>
                        <div class="form-group">
                            <!--<a href="#/" class="btn btn-primary btn-lg btn-block"></a>-->
                            <?                        
                            if($this->session->flashdata('usuario_incorrecto'))
                                {

                                    echo $this->session->flashdata('usuario_incorrecto');
                                }
                            ?>
                            <button type="submit" ng-click="login(user)" class="btn btn-primary btn-lg btn-block">Iniciar Sesión</button>
                        </div>
                    </fieldset>
                </form>                                       
            </div>
        </div>
    </div>

</div>
        </div>


        <script src="<?=$baseurl?>scripts/vendor.js"></script>

        <script src="<?=$baseurl?>scripts/ui.js"></script>

        <script src="<?=$baseurl?>scripts/app.js"></script>
      
    </body>
</html>
