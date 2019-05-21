<?php
//ACS 09Ene2008 - MenuContenidoAjax
($parametros['FrAccount']!=""?$FrAccount=$parametros['FrAccount']:0);
($parametros['Access']!=""?$Access=$parametros['Access']:0);
($parametros['AgruPagIni']!=""?$AgruPagIni=$parametros['AgruPagIni']:0);
($parametros['CustID']!=""?$usr=$parametros['CustID']:0);
($parametros['ctasArreglo']!=""?$ctasArreglo=$parametros['ctasArreglo']:0);
//ACS 09Ene2008 - MenuContenidoAjax

//print_r($parametros);

include("eztransaccion/user/include/xmlparser.inc");

function MD5_Ctas($buffer,$galletita, $usr)
{

	$posini = strpos($buffer,"<SELECT NAME=\"FrAccount\">");
	$posfin = strpos($buffer,"</SELECT>");

	$ctas 		 = substr ($buffer,$posini,($posfin-($posini+25)));
	$md5hide 	 = "";
	$cuenta 	 = "";
	$posn 	 	 = 0;
	$a 		 	 = 1;
	$ctasArreglo = "";

	while ($a)
	{
		$a = strpos($ctas,"<OPTION VALUE=\"",$posn); //extrae cuenta por cuenta
		if ($a)
		{
			$cuenta = "";
			for ($i=$a+strlen("<OPTION VALUE=\""); $i<strlen($ctas); $i++)
			{
				$caracter = $ctas[$i];
				if ($caracter >= "0" and $caracter <= "9")  //solo toma los caracteres numericos del <option>
				{
					$cuenta .= $caracter;
				}
				else
				{
					$posn = $i;
					$i = strlen($ctas);
				}
			}

			$cuenta 		= md5($galletita.trim($cuenta).trim($usr)); //se calcula su md5
			$ctasArreglo   .= $cuenta."|";
		}
	}

	// finalmente se agrega un campo hidden con todos los md5 calculados separados por pipe
	return     substr($buffer, 0, $posfin + 9) .
			   "<INPUT TYPE=HIDDEN NAME=\"ctasArreglo\" VALUE=$ctasArreglo>" .
			   substr($buffer, $posfin + 9);

}

function MD5_Valida($ctasPermitidas,$cuentaConsultando,$galletita, $usr)
{

	$ctaArreglo = explode("|", $ctasPermitidas); //extrae el campo md5 (pintado desde el fracc) en un arreglo

	foreach($ctaArreglo as $ctaPermitida)
	{
		$tmp =md5($galletita.trim($cuentaConsultando).trim($usr));	// calcula el md5 de la cuenta que quiere consultar

		if ($ctaPermitida == $tmp) // si es igual entonces es una cuenta permitida
		{
			return 0;
		}
	}
	return -1;  // es una cuenta que no es permitida (indica que el usuario modifico de alguna manera la cuenta destino y no es permitida para consulta)
}

if( !isset( $FrAccount ) ) {
    	$FrAccount = "0";
    }

$result = 0;	// DBA-DBM 04Abr2008 observaciones seguridad informatica


	// ***********************************************************
	// DBA-DGM I 04Abr2008 observaciones seguridad informatica
	if($FrAccount != "0")
    {
    	// Aqui solo entra a validar la cuenta que selcciono contra las cuentas md5 calculadas previamente
		$result = MD5_Valida($ctasArreglo,$FrAccount,$session->variable( "r_qki" ),$usr);

		if ($result != 0)
		{
			$transaccion_buffer = "<p>No existe una cuenta que pueda procesar esta transacci&oacute;n.</p><br>";
		}

	}
	// DBA-DGM F 04Abr2008 observaciones seguridad informatica
	// ***********************************************************

	if( !isset( $transaccion_buffer ) ) {
	    	$transaccion_buffer = "";
    }

	if ($result == 0) // DBA-DBM F 04Abr2008 observaciones seguridad informatica
	{
		//REF WMA-15001, Saldos Consolidados
		if ($FrAccount == "999999999")
		{
			$xml = "<mensaje><trxn value=\"pro\" /><accion value=\"consolidado\" /><access value=\"Process\" /><tipomsj value=\"rqs\" /><format value =\"xml\" /><CustID value=\"".urlencode(trim($usr))."\" /><cadpriv value=\"".urlencode($priv)."\" /></mensaje>";
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=pro&Access=FrAc&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv)."&FrAccount=".urlencode($FrAccount)."&AgruPagIni=".urlencode($AgruPagIni)."&xml=".urlencode($xml), $qki, $usr, $qki, $priv, $transaccion_buffer); // Saldos
		}
		else
		{
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=pro&Access=FrAc&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv)."&FrAccount=".urlencode($FrAccount)."&AgruPagIni=".urlencode($AgruPagIni), $qki, $usr, $qki, $priv, $transaccion_buffer); // Saldos
		}
		//REF WMA-15001, Saldos Consolidados
		$tr->blog($qki,"Saldos", $FrAccount, $Day, $Month, $Year, $Accion);


		// DBA-DGM F 04Abr2008 observaciones seguridad informatica
		if ($FrAccount == "0" and strpos($transaccion_buffer,"Seleccione el Cliente:")>0 ) // aqui solo entra para el 1st access de los saldos de clientes agrupados
		{
			$transaccion_buffer = MD5_Ctas($transaccion_buffer,$session->variable( "r_qki" ),$usr); // aqui agrega en la pagina los md5 que seran validos, para cada cliente seleccionado
		}
	}
	//ACS 09Ene2008 - MenuContenidoAjax

	//REF WMA-15001, Saldo Consolidados
	if ($FrAccount == "999999999")
	{
		echo "including what?";
		include("eztransaccion/user/include/saldosconsolidados_main_form.inc");
		$transaccion_buffer="<h1>Saldo Consolidado</h1><hr size='4' noshade='noshade'>".$transaccion_buffer;
	}
	else
	{
		echo "FrAccount es distinto de 9999999";
		$transaccion_buffer="<h1>Cancelar Domiciliaci&oacute;n</h1><hr size='4' noshade='noshade'>".$transaccion_buffer;
	}
	//REF WMA-15001, Saldo Consolidado



	$transaccion_buffer="<SCRIPT>
							function consultaSaldos ()
							{


								var parametros=	'FrAccount:'+document.getElementsByName('FrAccount')[0].options[document.getElementsByName('FrAccount')[0].selectedIndex].value+
												',Access:'+document.getElementsByName('Access')[0].value+
												',AgruPagIni:'+document.getElementsByName('AgruPagIni')[0].value+
												',CustID:'+document.getElementsByName('CustID')[0].value+
												',ctasArreglo:'+document.getElementsByName('ctasArreglo')[0].value;

								iniProcesarMenu('cancelaDomi', parametros);
							}

						</SCRIPT>".$transaccion_buffer;

	//ACS 09Ene2008 - MenuContenidoAjax

?>
