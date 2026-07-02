<?php
/**
 * Este script esta diseñado para ser incluído desde BaseViewAction o alguno de sus descendientes.
 *
 * Los parámetros disponibles para trabajar con la plantilla son:
 * 
 *  $this->vista->data 				Array asociativo que contiene todos los parámetros de la vista para ser
 *  								utilizados en la plantilla.
 *
 * Además:
 * 
 *  $this->vista->dataTitulo		Titulo de la vista
 *  $this->vista->dataSubtitulo		Subtitulo de la vista
 *  $this->vista->dataTexto			Texto introductorio de la vista
 *  $this->vista->dataUsuario		Instancia del usuario actual.
 *  $this->vista->dataMensajeOk		Mensaje de confirmación que debe mostrarse en la vista.
 *  $this->vista->dataMensajeError	Mensaje de error que debe mostrarse en la vista.
 */
?>
	<div class="row">
		<div id="login_contenedor" class="col-xs-10 col-xs-offset-1 col-md-4 col-md-offset-4">
			
			<img src="../../resources/images/logo_hcd_celeste.jpg" alt="SGLv2">
			<h2 class="text-center">Sistema de Gesti&oacute;n Legislativa</h2>

			<?php
			$config_hostname = strtolower(gethostname());
			// Si es el entorno de Producción (Servidor: hcd02) o el de Test (Servidor: hcd06)
			if ($config_hostname == strtolower('hcd02') || $config_hostname == strtolower('hcd06')) {
				echo '<h3 class="text-center">(Versi&oacute;n Local Completa)</h3>';
			} ?>

			<form method="POST" action="index.php?c=login&a=login">
				<div class="form-group">
					<label for="f_usuario">Usuario</label>
					<div class="input-group">
						<span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
						<input type="text" class="form-control" id="f_usuario" name="f_usuario" placeholder="Nombre de Usuario" />
                    </div>
				</div>
				<div class="form-group">
					<label for="f_password">Contrase&ntilde;a</label>
					<div class="input-group">
						<span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
						<input type="password" class="form-control" id="f_password" name="f_password" placeholder="Contrase&ntilde;a" />
					</div>
				</div>
				<button type="submit" class="btn btn-info" id="f_btn_login" name="f_btn_login" value="login">
					<span class="glyphicon glyphicon-off"></span>&nbsp;Ingresar
				</button>
			</form>
		</div>
	</div>
	<style type="text/css">
		body {
			background-image: url(../../resources/images/fondo_hcd_gris.jpg);
		 	background-repeat: no-repeat;
			background-size: cover
		}
		
		nav {
			display: none;
		}
		footer {
			display:none;
		}
		.page-header {
		    border-bottom: 0
		}

		#login_contenedor {
			margin-top: 5%;
		 	padding: 20px 30px;
			background-color: #2da4c6;
			opacity: .8;
			color: #fff;
			border-radius: 10px;
			text-align: center;
		}

		#login_contenedor .form-group {
			text-align: left;
		}

		@media (max-width: 740px) {
			#login_contenedor img {
				width: 240px;
			}
		}
	</style>
	