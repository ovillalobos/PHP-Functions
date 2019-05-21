<?php
/////////////////////////
//Creado por Nexions - HM
/////////////////////////

function ControlAcceso($menu,$usr)
{
        global $errors;

        //Mensaje - Nexions HM
        $trans = "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='pse'/><accion value='ValidaOpMenu'/><tipomsj value='rqs'/><CustID value='$usr'/>";
        $trans .= "<Opmenu value='" . $menu . "'/>";
		//ACDP Mayo 2014 Se agrega IP remota
		$trans .= "<IP value='" . $_SERVER['REMOTE_ADDR'] . "'/>";
        $trans .= "</mensaje>";
        //Mensaje

        //Conexion - Nexions HM
        $conn = new TCPIPNexions();

        if(!$conn->connect())
        {
              echo "  4501 - " .  $errors[4501];
              return "4501";
        }

        if(!$conn->send($trans))
        {
              echo "  4501 - " .  $errors[4501];
              return "4501";
        }

        $ans = $conn->receive();

        if(trim($ans)=="")
        {
              echo "  4501 - " .  $errors[4501];
              return "4501";
        }

        $xml_struct = parse_xml(trim($ans));

        if (  trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000" )
                //return  $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] ." - " .$xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"];
				
				return $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"];

        $conn->close();
        //Conexion

        return "0000";

}


if (!headers_sent())
{
        header("ETag: PUB" . time());
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
}


include_once( "classes/INIFile.php" );
include_once( "classes/eztemplate.php" );
include_once( "classes/ezhttptool.php" );

include_once( "eztransaccion/classes/eztransaccion.php" );
include_once( "ezsession/classes/ezsession.php" );


include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");
include("eztransaccion/user/include/general_functions.inc");


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
                         "eztransaccion/user/intl/", $Language, "PagoDeServicios.php" );

    $t->setAllStrings();

    $t->set_file( array(
        "PagoDeServicios_tpl" => "PagoDeServicios.tpl"
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
    
    $t->set_var( "transaccion_buffer", $transaccion_buffer );

    //Escritura del template
    $t->pparse( "output", "PagoDeServicios_tpl" );

    $usr = strtolower($usr);

//FAF - 04112008 - Inicio	
    $result = ControlAcceso("pagoserv",$usr);
//FAF - 04112008 - Fin

    if($result=="0000")
	{
    	include("eztransaccion/user/include/pagodeservicios.inc");
	}	
    else
	{
//        echo $result;
		if ($result == "4140")
		{
		 echo " Estimado Usuario, <A HREF=". "/" . "transaccion" . "/". "clave/". "> <font color=". "#FF0000". "> Su Clave de Acceso </font> </A> requiere ser cambiada para poder realizar cualquier transacci&oacute;n.";
		}
		elseif ($result == "4141")
		{
		 echo " Su Llave ASB requiere ser activada para poder realizar cualquier transaccion.Favor de intentar más tarde."; 
		}
		elseif ($result == "4142")
		{
		 echo " No puede operar porque su ASB no se encuentra en inventarios. "; 
		}
		elseif ($result == "4143")
		{
		 echo " No puede operar hasta que cuente con su ASB activo. "; 
		}
		
		else
		{
			echo " Error en el sistema c&oacute;digo de error :" . $result;
		}
		


		//else
		//{
		//	echo $result;
		//}
		 


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

    $t->set_var( "redirect_url", "/transaccion/pagodeserviciosnuevo/" );

    $t->pparse( "output", "user_login_tpl" );
}//Fin else
?>

