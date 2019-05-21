<?php
($parametros['Access']!=""?$Access=$parametros['Access']:0);
($parametros['noGrupo']!=""?$noGrupo=$parametros['noGrupo']:0);



include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");
include("estilo.inc");
include_once("eztransaccion/user/include/nominaexpressFunc.inc");

function ControlAcceso($menu,$usr)
{
        global $errors;

        //Mensaje - Nexions HM
        $trans = "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='nom'/><accion value='ValidaOpMenu'/><tipomsj value='rqs'/><CustID value='$usr'/>";
        $trans .= "<Opmenu value='" . $menu . "'/>";
        $trans .= "</mensaje>";
        //Mensaje

        //Conexion - Nexions HM
        $conn = new TCPIPNexions();

        if(!$conn->connect())
              echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

        if(!$conn->send($trans))
              echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

        $ans = $conn->receive();

        if(trim($ans)=="")
              echo "<script> alert('  4501 - " .  $errors[4501]  . " ') </script>";

        $xml_struct = parse_xml(trim($ans));

        if (  trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000" )
                return  $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] ." - " .$xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"];

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

if ($Access!="CtaEmp" && $Access!="Localidad" && $Access!="Process")
{
	$t = new eZTemplate( "eztransaccion/user/" . $ini->read_var( "eZTransaccionMain", "TemplateDir" ),
						 "eztransaccion/user/intl/", $Language, "nomaltexp.php" );

	$t->setAllStrings();
	 
	$t->set_file( array(
		"nomaltexp_tpl" => "Nomaltexp.tpl"
		) );
		
	$t->set_var( "transaccion_buffer", $transaccion_buffer.$Pagina );

	$t->pparse( "output", "nomaltexp_tpl" );
}
$pagina="";

$_SESSION['user'] = $usr;

if($parametros['Access']=="")
{	
	$result = ControlAcceso("nomaex",$usr);
	if ( $result!="0000")
	{
		$pagina=$result;
	}
	else
	{
		include_once("eztransaccion/user/include/nominaexpress_main_form.inc");
	}
}
else
{

		switch ($Access)
		{

			case "Localidad":
			
				include_once("eztransaccion/user/include/ciudad.inc");
				
				$var_localidad=$parametros["claveEdo"];
				
				$pagina="<select class=\"inputbox_nom\" id=\"localidad\">";
				
				$sel=$localidad[$parametros["claveEdo"]];
				sort($sel);
				foreach ( $sel as $key=>$val)
				{
					$paginaMedia.="<option value='".$sel[$key]."' title='".$val."'>".$val."</option>";
				}
					
				$pagina.=$paginaMedia."</select>";
				
				break;
			case "CtaEmp"://Consulta empleados
				$pagina=formularioListado( $parametros["usr"],$parametros["rowno"],$parametros["noGrupo"], $parametros["direction"],$parametros["page"], $parametros["tfiltro"],$parametros["vfiltro"]);
				break;
			case "FrAc":
				include_once("eztransaccion/user/include/nominaexpress_main_form.inc");
				break;
			case "Confirm":
				include_once("eztransaccion/user/include/nominaexpress_confirm.inc");
				break;
			case "Process":
				include_once("eztransaccion/user/include/nominaexpress_process.inc");
				break;
			default:
				include_once("eztransaccion/user/include/nominaexpress_main_form.inc");
				break;
				
		}
}




$transaccion_buffer .=$pagina;



?>