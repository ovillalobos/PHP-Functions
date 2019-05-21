
<?php
include_once("eztransaccion/user/include/tcpipnexions.php");
include_once("eztransaccion/user/include/xmlparser.inc");
include_once( "eztransaccion/user/include/PHPLiveX.php");
include_once("eztransaccion/user/include/general_functions.inc");
include_once("httplib.php");

function mes($mes_aux)
{
        switch($mes_aux)
        {
                case 'Jan': $mes = "01"; break;
                case 'Feb': $mes = "02"; break;
                case 'Mar': $mes = "03"; break;
                case 'Apr': $mes = "04"; break;
                case 'May': $mes = "05"; break;
                case 'Jun': $mes = "06"; break;
                case 'Jul': $mes = "07"; break;
                case 'Aug': $mes = "08"; break;
                case 'Sep': $mes = "09"; break;
                case 'Oct': $mes = "10"; break;
                case 'Nov': $mes = "11"; break;
                case 'Dec': $mes = "12"; break;
        }
        return $mes;
}

function mesN($mes_aux)
{
        switch($mes_aux)
        {
                case '01': $mes = "Ene"; break;
                case '02': $mes = "Feb"; break;
                case '03': $mes = "Mar"; break;
                case '04': $mes = "Abr"; break;
                case '05': $mes = "May"; break;
                case '06': $mes = "Jun"; break;
                case '07': $mes = "Jul"; break;
                case '08': $mes = "Ago"; break;
                case '09': $mes = "Sep"; break;
                case '10': $mes = "Oct"; break;
                case '11': $mes = "Nov"; break;
                case '12': $mes = "Dic"; break;
        }
        return $mes;
}

function  dif_fec($fechai, $fechaf)
{
	$di = (int)substr( $fechai,6,2 );
	$df = (int)substr( $fechaf,6,2 );
	$mi = (int)substr( $fechai,4,2 );
	$mf = (int)substr( $fechaf,4,2 );
	$yi = (int)substr( $fechai,0,4 );
	$yf = (int)substr( $fechaf,0,4 );
	$il_fm = 0 ;
	$il_fd = 0 ;
	$il_d = 0 ;

	if ( $yf > $yi)
	{
		$il_m = $mf + 12 ;
	}
	else
	{
		$il_m = $mf ;
	}
	
	$il_fm = $il_m - $mi ;
	
	if ( $il_fm < 0 )
	{
		return (31+$df) ;
	}
	
	if ( $il_fm > 1 )
	{
		return (31+$df) ;
	}
	
	if ( $il_fm == 1 )
	{
		if ( $mi == 1 || $mi == 3 || $mi == 5 || $mi == 7 || $mi == 8 || $mi == 10 || $mi == 12 )
		{
			$il_d = 31 ;
		}
		
		if ( $mi == 4 || $mi == 6 || $mi == 9 || $mi == 11 )
		{
			$il_d = 30 ;
		}

		if ( $mi == 2 )
		{
			if ( $yi % 4 == 0 && ( $yi % 100 != 0 || $yi % 400 == 0 ))
			{
				$il_d = 29 ;
			}
			else
			{
				$il_d = 28 ;			
			}			
		}
	}

	$il_fd = ($df+$il_d) - $di;
	
	return $il_fd;	
}

function consulta_depositos_efectivo($usr, $rowno, $cant_rows,$direction,$page, $lastDate,$opcion,$fechai, $fechaf, $Pos/*, $selectedCuentaE*/)
{
	global $row_number;
	global $errors;

	    $timeout = timeout_check();

        if($timeout != "")
                return $timeout;

        list( $day, $aux, $month, $aux, $year ) = split( '[/ ]', $fechai );

        $fechai = $day . substr($month,0,3) . $year;

        list( $day, $aux, $month, $aux, $year ) = split( '[/ ]', $lastDate );

        $lastDate = $day . substr($month,0,3) . $year; 


		list( $day, $aux, $month, $aux, $year ) = split( '[/ ]', $fechaf );

        $fechaf = $day . substr($month,0,3) . $year;


	$_SESSION['fechai'] = $fechai;
	$_SESSION['fechaf'] = $fechaf;
  	$_SESSION['usr_id'] = $usr;
 	$_SESSION['rowno'] = $rowno;
	$_SESSION['cant_rows'] = $cant_rows;
	$_SESSION['direction'] = $directions;

		
	$diff = 0;
	$diff = dif_fec( $fechai, $fechaf );
	//echo "DIFERENCIA ENTRE FECHAS:::::::::".$diff;

	if($fechai == "" || $fechaf == "" || $diff > 31 )
	{
		$text = "<br><b>Para el criterio seleccionado es necesario introducir un rango de fechas entre 1 y 31 días.</b>";		
        return $text;
	}
	
	//Mensaje - Nexions HM
        $trans = $trans . "<?xml version='1.0'?>";
		$trans .= "<mensaje><trxn value='mov'/><accion value='ConsDepEfectivo'/><tipomsj value='rqs'/><CustID value='" . 
		$usr . "'/><ordered value='CtaOrigen'/>	<Rowno value='" . 
		$rowno . "'/><CRegPag value='". 
		$cant_rows ."'/><direction value='" . 
		$direction . "'/><From value='". 
		$fechai  ."'/>	<To value='". 
		$fechaf ."'/><LastDate value='". 
		$lastDate."'/><format value='xml'/></mensaje>";
    //Conexion - Nexions HM
        

// REEMPLAZO OVATION POR JBOSS
    	$JBossEnabled = trim(readParam("PMPJboss","site.ini"));
    	if($JBossEnabled != "enabled")
        {
		//Conexion - Nexions HM
        $conn = new TCPIPNexions();

		if(!$conn->connect())
			return "<script> alert(' 4501 - " .  $errors[4501]  . " ') </script>";

        	if(!$conn->send($trans))
			return "<script> alert(' 4501 - " .  $errors[4501]  . " ') </script>";

        	$ans = $conn->receive();
        	
        	$conn->close();
		}    
        else
        {
			$JBoss = trim(readParam("JBossHost","site.ini"));
			$JBossPath = trim(readParam("JBossPath","site.ini"));

			$ans = requestHTTPtoJBoss($JBoss, $JBossPath, "xml", $trans);
    	}
				
        if(trim($ans)=="")
              return "<script> alert(' 4501 - " .  $errors[4501]  . " ') </script>";
		
        $xml_struct = parse_xml(trim($ans));
		
        if (  trim($xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"]) != "0000" )
		{
			$text = "<br><b>El cliente no tiene movimientos de depósitos en efectivo para pago de crédito</b>";
			//."<script> alert('" .  $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["VALUE"] . "-" .  $xml_struct["vals"][$xml_struct["index"]["ERRORNO"][0]]["attributes"]["DESC"]  . " ')</script>"
            return $text;
		}

	$text_table = "<br><TABLE ALIGN=CENTER BORDER=0 CELLSPACING=2 CELLPADDING=2 WIDTH=100% id=pagos_autorizar><TR height='15px'>";
	
	$text_table .= "<TH BGCOLOR=#5A419C class='form_grid_arc'><FONT COLOR=WHITE>Fecha</FONT></TH></TH><TH BGCOLOR=#5A419C class='form_grid_arc'><FONT COLOR=WHITE>Descripci&oacute;n</FONT></TH><TH
		BGCOLOR=#5A419C><FONT COLOR=WHITE>Sucursal</FONT></TH><TH
		BGCOLOR=#5A419C><FONT COLOR=WHITE>Monto</FONT></TH><TH
		BGCOLOR=#5A419C><FONT COLOR=WHITE>Capturo</FONT></TH>";

	$text_table .= "</TH></TR>";

	$i = 1;
	$j = 0;		
	$first_row_number = 0;

	$text = "";
	$rownos = "";
	$aux = array();
	
	foreach ($xml_struct["index"]["MOVIMIENTO"] as $key=>$val)
	{	
		//echo " valor $i".$i."valor top::".$cant_rows;
		if( $i <= ((int)$cant_rows))
		{

		
			if($i%2)
					//$color = "#EFEFF0";
					$color = "#DDDDDD";
				else
						$color = "#FFFFFF";
			
			if($direction == "forward")
			{
				//Reemplazo de caracteres especiales
				$nomprov = str_replace("*", "&", $xml_struct["vals"][$val]["attributes"]["DESC"]);
				$nomprov = str_replace("$",chr(241), $nomprov);
				$nomprov = str_replace("%",chr(209), $nomprov);
				// Reemplazo de caracteres especiales
								
				$last_row_number = $xml_struct["vals"][$val]["attributes"]["ROWNO"];
				$last_date_aux = $xml_struct["vals"][$val]["attributes"]["FECHA"];			
				
				if ($i == 1)
				{
					$no_cliente = $xml_struct["vals"][$val]["attributes"]["NUMCTA"];
					$first_row_number = $xml_struct["vals"][$val]["attributes"]["ROWNO"];
				}	
								
				$text .= 
				"<TD BGCOLOR=$color >" . 
				substr($last_date_aux,8,2) . mesN(substr($last_date_aux,5,2)) . substr($last_date_aux,0,4) . "</TD><TD BGCOLOR=$color>" .
				$nomprov. "</TD><TD BGCOLOR=$color >" . 
				$xml_struct["vals"][$val]["attributes"]["SUCURSAL"]. "</TD><TD BGCOLOR=$color ALIGN=RIGHT>" . 
				$xml_struct["vals"][$val]["attributes"]["IMPORTE"]. "</TD><TD BGCOLOR=$color ALIGN=CENTER>" . 
				$xml_struct["vals"][$val]["attributes"]["EMPLEADO"]. "</TD></TR>";
				$cuenta = $xml_struct["vals"][$val]["attributes"]["NUMCTA"];
				$last_date = substr($last_date_aux,0,4) . substr($last_date_aux,5,2) .substr($last_date_aux,8,2)  ;	
			}
		}else
		{
			$last_row_number = $xml_struct["vals"][$val]["attributes"]["ROWNO"];
			$last_date_aux = $xml_struct["vals"][$val]["attributes"]["FECHA"];
			$last_date = substr($last_date_aux,0,4) . substr($last_date_aux,5,2) .substr($last_date_aux,8,2)  ;
			$j++;
		}
		$i++;
		

	}
	$m_s = "<h4 align=right>Número de Cliente: ".$cuenta."</h4>";
	
	$text = $m_s . $text_table . $text;
		
    $text .= "\r\n </TABLE>" .
	"<br>" .
	"<p align=CENTER></p>" .
	"<table align=CENTER border=0 cellspacing=2 width=100%>" .
	"<tbody>" .
	"<tr bgcolor=#5a419c><th><font color=WHITE>*No Aplica</font></th></tr>" .
	"</tbody>\r\n ".
	"</table>" .
	"<input name=Button2 value=Regresar language=JavaScript onclick=conMovimientosBack() type=BUTTON>";
	if( $i > ((int)$cant_rows) )
	{
	$Pos++;
	$text .= "<input name=Pos id=Pos value=" . $last_row_number . " type=HIDDEN>" .
	"<input name=Dias value=" . $last_date . " type=HIDDEN>" .
	"<input name=top value=" . $cant_rows . " type='HIDDEN'>" .	
	"<input name=redirecPagInter value=SI type=HIDDEN>" .	
	"<input name=Button id=Button value='Ver más' language=JavaScript onclick=conMovimientos(false) type=BUTTON>";
	}
	$text .="<input name=FrAmount id=FrAmount value=0.0 type=HIDDEN>";

	return $text;
}

?>