<?php
/////////////////////////
//Creado por Nexions - HM
/////////////////////////

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );

include( "eztransaccion/user/include/cred_func.inc" );

session_start();

$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();

if ( $user )
{

    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "res_linea.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "res_linea_tpl" => "res_linea.tpl"
        ) );


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
    $transaccion_buffer = "";

    $t->set_var( "transaccion_buffer", "" );

    //Escritura del template
	$t->pparse( "output", "res_linea_tpl" );

    $usr = strtolower($usr);

    $result = "0000"; // ControlAcceso("cappag",$usr);
	echo cleanSession( "res_linea" );

	echo cleanSession( "saldoscre" );


    if($result=="0000")
    {
	   	include("eztransaccion/user/include/res_linea_main_form.inc");
    }
    else
        echo $result;



}//Fin if
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/res_linea/" );

    $t->pparse( "output", "user_login_tpl" );
}//Fin else
?>

