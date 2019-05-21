<?php

include("eztransaccion/user/include/tcpipnexions.php");
include("eztransaccion/user/include/xmlparser.inc");
//include( "eztransaccion/user/include/PHPLiveX-2.2.php");
include( "eztransaccion/user/include/PHPLiveX.php");

function borrar_pagoporautorizar($user, $prov)
{
        //Mensaje - Nexions HM
        $trans = "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='pgp'/><accion value='DelProv'/><tipomsj value='rqs'/><CustID value='$user'/><Token value='tk_code'/>";
        $trans .= "<Proveedor Rowno='" . $prov["rowno"]."'/>";
        $trans .= "</mensaje>";
        //Mensaje

        //Conexion - Nexions HM
        $conn = new TCPIPNexions();

        $conn->connect();
        $conn->send($trans);
        $ans = $conn->receive();

        $xml_struct = parse_xml(trim($ans));

        if ( ! $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] == "0000" )
                echo "No se pudo completar la transaccion\n\n";

        $conn->close();
        //Conexion
}



function consulta_pagosporautorizar_ajax($usr, $rowno, $cant_rows,$direction,$page)
{
	global $row_number;

        //Mensaje - Nexions HM
        $trans = $trans . "<?xml version='1.0'?>";
        $trans .= "<mensaje><trxn value='pmp'/><accion value='ConsPagXLib'/><tipomsj value='rqs'/><CustID value='" . 
		$usr . "'/><ordered 
		value='Origen'/><Rowno value='" . $rowno . "'/><CRegPag value='". $cant_rows ."'/><direction value='" . $direction . "'/>								</mensaje>";	

		echo $trans;
        //Conexion - Nexions HM
        $conn = new TCPIPNexions();

        $conn->connect();
        $conn->send($trans);
        $ans = $conn->receive();

        $conn->close();
        //Conexion
        
	$xml_struct = parse_xml(trim($ans));

        if ( ! $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] == "0000" )
        {
                echo "No se pudo completar la transaccion\n\n";
                return;
        }

        //Impresion de los proveedores

	$text_table = "<TABLE ALIGN=CENTER BORDER=0 CELLSPACING=2 CELLPADDING=2 WIDTH=100% id=pagos_autorizar><TR><TH BGCOLOR=#CCCCFF ><input type=checkbox
		 id=checkall onClick=\"CheckAll('pagos_autorizar','checkall')\"></TH><TH BGCOLOR=#CCCCFF><a href=\"javascript:\" style=\"text-decoration:
		 none\"> # </a> </TH><TH BGCOLOR=#CCCCFF>Proveedor</TH><TH BGCOLOR=#CCCCFF>Cuenta</TH><TH BGCOLOR=#CCCCFF>Banco</TH><TH
		BGCOLOR=#CCCCFF>Tipo de cuenta</TH><TH BGCOLOR=#CCCCFF>Tipo</TH><TH BGCOLOR=#CCCCFF>Descripcion</TH><TH
		BGCOLOR=#CCCCFF>Importe</TH><TH BGCOLOR=#CCCCFF>IVA</TH><TH BGCOLOR=#CCCCFF>Fec.Pago</TH><TH
		BGCOLOR=#CCCCFF>Capturo</TH><TH BGCOLOR=#CCCCFF>Firmas</TH><TH
		BGCOLOR=#CCCCFF>Firma 1</TH><TH BGCOLOR=#CCCCFF>Firma 2</TH></TR>";

	$i = 1;
	$first_row_number = 0;

	$text = "";

        foreach ($xml_struct["index"]["PAGO"] as $key=>$val)
        {
		
		if($direction == "forward")
		{
        	$text .= "<TR onMouseOver=\"PointerOver('over', this,'check" . $i . "')\" onMouseOut=\"PointerOver('out', this,'check" . $i . "')\"
		 onClick=\"PointerClick(this,'check" . $i . "')\"><TD BGCOLOR=#DDDDDD><input onClick=\"PointerClick(this,'check" . $i . "')\"
		 id='check" . $i . "' name='check" . $i . "' type=checkbox>
		</TD><TD BGCOLOR=#DDDDDD ><a href=\"
		 style=\"text-decoration: none\"> " .
		$i. " </TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["PROVEEDOR"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["NUMCTA"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["BANCO"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["TIPOCTA"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["TIPOPAG"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["DESCRIPCION"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["IMPORTE"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["IVA"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["FAPLICA"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["FAPLICA"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["FIRMAS"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["FIRMA1"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["FIRMA2"]. "</TD></TR> \n";
		
		$last_row_number = $xml_struct["vals"][$val]["attributes"]["ROWNO"];

		if ($i == 1)
			$first_row_number = $xml_struct["vals"][$val]["attributes"]["ROWNO"];
		}
		else
		{
		   $text = "<TR onMouseOver=\"PointerOver('over', this,'check" . $i . "')\" onMouseOut=\"PointerOver('out', this,'check" . $i . "')\"
                 onClick=\"PointerClick(this,'check" . $i . "')\"><TD BGCOLOR=#DDDDDD><input onClick=\"PointerClick(this,'check" . $i . "')\"
                 id='check" . $i . "' name='check" . $i . "'  type=checkbox></TD><TD BGCOLOR=#DDDDDD ><a href=\"javascript:\"
                 style=\"text-decoration: none\"> " .
                		$i. " </TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["PROVEEDOR"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["NUMCTA"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["BANCO"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["TIPOCTA"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["TIPOPAG"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["DESCRIPCION"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["IMPORTE"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["IVA"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["FAPLICA"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["FAPLICA"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["FIRMAS"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["FIRMA1"]. "</TD><TD BGCOLOR=#DDDDDD>" . 
		$xml_struct["vals"][$val]["attributes"]["FIRMA2"]. "</TD></TR>" . $text;
		
		if($i==1)
			$last_row_number = $xml_struct["vals"][$val]["attributes"]["ROWNO"];

		$first_row_number = $xml_struct["vals"][$val]["attributes"]["ROWNO"];

		}
		
		
	
		$i++;
        }

		$rowno += 10 ;	
	
	$text = $text_table . $text;

	$text .= "</TABLE><TABLE border=0 width=100%><TR><TD BGCOLOR=#FFFFFF align=left width=100%><TABLE border=0 width=100% 
	BGCOLOR=#FFFFFF><TR><TD width=60%><TD width=20% align=center>";
	
	if($direction == "forward")
                $page++;
        else
                $page--;

	if($page == 1)
	{
		$text .= "Anterior </TD><TD width=%20 align=center>";
	}
	else
	{
		$text .= "<a style=\"text-decoration: none\"  href=\"javascript:consulta_pagosporautorizar_ajax('" . $usr . "', '" . $first_row_number . "','" .
		 $cant_rows . "','backward','". $page . "','target=content,preload=listing');\">Anterior</a></TD><TD width=%20 align=center>";
	}

	if( trim($xml_struct["vals"][$xml_struct["index"]["LASTPAGE"][0]]["attributes"]["VALUE"]) == "false")
	{
		$text .= "<a style=\"text-decoration: none\" href=\"javascript:consulta_pagosporautorizar_ajax('" . $usr . "', '" . $last_row_number . "','". 
		$cant_rows ."','forward','" . $page . "','target=content,preload=listing');\">Siguiente</a></TD></TR></TABLE></TD></TR></TABLE>";
	}
	else
	{
		$text .= "Siguiente</TD></TR></TABLE></TD></TR></TABLE>";
	}

	return $text;
}




//////////////////////////////////////////
//Switch pricipal - Nexion Solutions - HM
//////////////////////////////////////////
switch($_POST['btn_pagaut'])
{
	case 'Borrar':
	 

		echo "Check1: " . $_POST['checkprueba'];
	
		for( $i=0 ; $i < 10 ; $i++)
		{
			echo "check$i: " . $_POST["check" . $i] . "\n";
		}	

		break;
		
	case 'Autorizar':
	 

		echo "Check1: " . $_POST['checkprueba'];
	
		for( $i=0 ; $i < 10 ; $i++)
		{
			echo "check$i: " . $_POST["check" . $i] . "\n";
		}	

		break;	

	default:
		$plx = new PHPLiveX();
		$plx->Export("consulta_pagosporautorizar_ajax");
//		$plx = new PHPLiveX(array("consulta_pagosporautorizar_ajax"));
		echo "<script language=\"javascript\">\n";
 		$plx->Run(); 
		echo "</script>\n";

		//echo consulta_proveedores_ajax($usr,"0","10","forward","0");
		
		include("eztransaccion/user/include/pagosprogporautorizar_main_form.inc");
		break;
}

?>
