<?php
//
// YEHO 08Mar2010 Consulta Reportes PEMEX Subgaseras
//

include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );

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
                         "eztransaccion/user/intl/", $Language, "ConArchPmx.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "ArchServ_tpl" => "ArchServ.tpl"
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
    $particularFields = "";
    $ret_code = 0;
    $sna_ret_code = 0;
//HB
	if(!empty($_POST['DiasPzo']))
		$DiasPzo = $_POST['DiasPzo'];
//HB
    if(empty($_POST['Access'])) {
        $_POST['Access'] = "FrAc";
    }
    $Accion  = "ConPmx";
    switch($_POST['Access']) {
    case "FrAc":
        if (isset($_POST['Access'])) {
            setcookie ( "QueAccess", $_POST['Access'], time() + ( 3600 * 24 * 365 ), "/",  "bb.com.mx", 0 ) or print( "Error: could not set cookie." );
        }
		$Parent3 = "LIGA";
		$DiasPzo = "747444089-gobierno-xmexicodf-pemex";
        $particularFields = "&Accion=".urlencode($Accion)."&DiasPzo=".urlencode($DiasPzo)."&Parent1=".urlencode($_POST['Parent1'])."&Parent3=".urlencode($Parent3);
	//	echo $particularFields;
        $tr->blog($qki,"ConPmx", $_POST['FrAccount'], $_POST['ToAccount'], $DiasPzo, $_POST['Parent1'], $Accion);
        break;
    case "Confirm":
    case "Process":
        $tr->blog($qki,"ConPmx", $_POST['FrAccount'], $_POST['ToAccount'], $DiasPzo, $_POST['Parent1'], $Accion);
        break;
    }

    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=arc&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // intercambio de archivos de nómina electrónica
   $t->set_var( "transaccion_buffer", $transaccion_buffer );
    $t->pparse( "output", "ArchServ_tpl" );

} else {
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/ArchServ/" );
    $t->pparse( "output", "user_login_tpl" );
}


?>
