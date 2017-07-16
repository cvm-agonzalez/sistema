<div class="page-signin">

    <div class="signin-header">
        <div class="container text-center">
            <section class="logo">
                <a href="#/">{{main.brand}}</a>
            </section>
        </div>
    </div>

    <div class="signin-body">
        <div class="container">
            <div class="form-container">

                <section class="row signin-social text-center">
                    
                </section>

                <?php
                $username = array('name' => 'username', 'placeholder' => 'Usuario');
                $password = array('name' => 'password',    'placeholder' => 'Contraseña');
                $submit = array('name' => 'submit', 'value' => 'Iniciar sesión', 'title' => 'Iniciar sesión');
                ?>

                <form class="form-horizontal" name="loginUserForm" action="admin/login" method="post">
                    <fieldset>
                        <div ng-show="flash">
                            <div data-alert class="alert-box alert round"></div>
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
                                <? echo "hola".$error.$token; print_r($data); ?>
                            </div>
                        </div>
                        <div class="form-group">
                        </div>
                        <div class="form-group">
                            <!--<a href="#/" class="btn btn-primary btn-lg btn-block"></a>-->
                            <button type="submit" ng-click="login(user)" class="btn btn-primary btn-lg btn-block">Iniciar Sesión</button>
                        </div>
                    </fieldset>
                </form>                                       
            </div>
        </div>
    </div>

</div>