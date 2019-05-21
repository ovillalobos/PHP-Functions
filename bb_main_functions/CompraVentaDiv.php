<?php
//
// Created on: <15-FebNov-2011>
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

// DebugBreak();

if ( $user )
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "CompraVentaDiv.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "CompraVentaDiv_tpl" => "CompraVentaDiv.tpl"
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
    $transaccion_buffer = "";
//HB
	if(!empty($_POST['Access']))
		$Access = $_POST['Access'];
		
	if(!empty($_POST['Amount']))
		$Amount = $_POST['Amount'];
		
	if(!empty($_POST['CqNo']))
		$CqNo = $_POST['CqNo'];
		
	if(!empty($_POST['Cq']))
		$Cq = $_POST['Cq'];
		
	if(!empty($_POST['FrAccount']))
		$FrAccount = $_POST['FrAccount'];
		
	if(!empty($_POST['Pos']))
		$Pos = $_POST['Pos'];
		
	if(!empty($_POST['Accion']))
		$Accion = $_POST['Accion'];
//HB
	if(empty($_POST['Access'])) {
		$_POST['Access'] = "FrAc";
	}

	if( !isset( $_POST['Amount'] ) ) {
		$_POST['Amount'] = "";
	}
	if( !isset( $CqNo ) ) {
		$CqNo = "";
	}
	if( !isset( $Cq ) ) {
		$Cq = "";
	}
	if( !isset( $FrAccount ) ) {
		$FrAccount = "";
	}
	if( !isset( $Pos ) ) {
		$Pos = "";
	}
	switch( $_POST['Access'] )
	{
		case "FrAc":
			$particularFields = "";
			$tr->blog($qki,"CompraVentaDiv", $FrAccount, $Pos, $CqNo, $_POST['Amount'], $Accion);
			break;
		case "Confirm":
		case "Process":
			$particularFields = "&Passwd=".encrypt( $_POST['code'], strtolower( $usr ) )."&Amount=".urlencode($_POST['Amount']);
			$tr->blog($qki,"CompraVentaDiv", $FrAccount, $Pos, $CqNo, $_POST['Amount'], $Accion);
			break;
	}
	$transaccion_buffer = "";

	//echo "<b>particularFields</b> ". var_dump ( $particularFields ). "<br>\n";

	$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=cvd&Access=".urlencode($_POST['Access'])."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // busqueda de cheques

	$t->set_var( "transaccion_buffer", $transaccion_buffer );
	$t->pparse( "output", "CompraVentaDiv_tpl" );

}
else
{
    $t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
                         "eztransaccion/user/intl/", $Language, "userlogin.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "user_login_tpl" => "userlogin.tpl"
        ) );

    $t->set_var( "redirect_url", "/transaccion/CompraVentaDiv/" );

    $t->pparse( "output", "user_login_tpl" );
}
?>