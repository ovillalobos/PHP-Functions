<?php
/////////////////////////
//Creado por Nexions - HM
/////////////////////////

if (!headers_sent())
{
        header("ETag: PUB" . time());
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header('Cache-Control: no-store, no-cache, must-revalidate' );
        header('Cache-Control: post-check=0, pre-check=0', false );
        header('Pragma: no-cache' );
}

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );

include_once("eztransaccion/user/include/reportemultipago.inc");
$session =& eZSession::globalSession();

if( !$session->fetch() )
    $session->store();

$ini =& $GLOBALS["GlobalSiteIni"];

$Language = $ini->read_var( "eZTransaccionMain", "Language" );
// $backend = $ini->read_var( "eZTransaccionMain", "Backend" );
$ServerNumber = $ini->read_var( "site", "ServerNumber" );
$DomainPostfix = $ini->read_var( "site", "DomainPostfix" );

$backend = "gw" . $ServerNumber. $DomainPostfix;

include_once( "ezuserbb/classes/ezuser.php" );

$user =& eZUserBB::currentUser();


// DebugBreak();

if ( $user )
{

    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "comprobantesmultipago.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "comprobantesmultipago_tpl" => "comprobantesmultipago.tpl"
        ) );


    $session =& eZSession::globalSession();

    if ( !$session->fetch() )
    {
        $session->store();
    }

    $tr = new eZTransaccion( );
    $usr = $session->variable( "r_usr" );
    $qki = $session->variable( "r_qki" );
    $priv = $session->variable( "r_priv" );

	if( empty( $Access ) )
	{
		$Access = "FrAc";
	}

//	echo "<script>alert('top ".$_POST['top']."');</script>";
	if($_POST['top']=='99999'|| ($_POST['top']=='' && $Access =="FrAc"))
	{
		$particularFields = "";
			//$tr->blog( $qki,"Multipago", $FrAccount, $Day, $Month, $Year, $Accion );




			//ACS-02Ene2008 Ini
			// $ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=mup&Access=FrAc&CustID=".urlencode( $usr )."&Cadpriv=".urlencode( $priv )."&Action=JustLog" .$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer ); // multipago
		  $ret_code = $tr->PostToHost( $backend, "/IBnkIIS.dll", "Trxn=mul&Access=FrAc&CustID=".urlencode( $usr )."&Cadpriv=".urlencode( $priv )."&Action=JustLog" .$particularFields, $qki, $usr, $a, $b, $c, $d );
			//ACS-02Ene2008 Fin

		  $transaccion_buffer = "";

		  $t->set_var( "transaccion_buffer", $transaccion_buffer );

		    //Escritura del template
		  $t->pparse( "output", "comprobantesmultipago_tpl" );


	}



    $usr = strtolower($usr);

    include("eztransaccion/user/include/comprobantesmultipago.inc");

}//Fin if
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/multipago/" );

    $t->pparse( "output", "user_login_tpl" );
}//Fin else
?>

