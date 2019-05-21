<?php
//06Jun2010  ACS CamCve I
ini_set( "include_path", "/var/www/html/bajio/" );

include_once( "classes/INIFile.php" );
include_once( "classes/ezlog.php" );

// CONFIGURACIÓN DE CAMPAÑAS SITE.INI [OVVC] => 28031986
$CampSrv=trim($ini->read_var("site", "CampSrv"));
$CampBD=trim($ini->read_var("site", "CampBD"));
$CampUsr=trim($ini->read_var("site", "CampUsr"));
$CampPwd=trim($ini->read_var("site", "CampPwd"));
$CampImag=trim($ini->read_var("site", "CampImag"));

if ( $session->variable( "r_ccv" ) == "cambioCve" )
{
	$transaccion_buffer = "";
	$transaccion_buffer = "<h1>Saldos</h1>
							<hr noshade='noshade' size='4'>
							<p></p><p><font color='#5a419c'>
							<strong>Estimado Usuario, Su <a href='/transaccion/clave/'> <font color='#ff0000'>Clave de Acceso</font> </a> requiere ser cambiada para poder realizar cualquier transacción.</strong></font>";
	return;
}
//06Jun2010  ACS CamCve F
//ACS 09Ene2008 - MenuContenidoAjax
($parametros['FrAccount']!=""?$FrAccount=$parametros['FrAccount']:0);
($parametros['Access']!=""?$Access=$parametros['Access']:0);
($parametros['AgruPagIni']!=""?$AgruPagIni=$parametros['AgruPagIni']:0);
($parametros['CustID']!=""?$usr=$parametros['CustID']:0);
($parametros['ctasArreglo']!=""?$ctasArreglo=$parametros['ctasArreglo']:0);
//ACS 09Ene2008 - MenuContenidoAjax

//print_r($parametros);

include("eztransaccion/user/include/xmlparser.inc");
include_once ( "classes/ezlog.php" );  //NXN 07sep2012 track 235490
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

if( !isset /*(HB AGL - Ajustes PHP5)*/( $FrAccount ) ) {
    	$FrAccount = "0";
    }
//JAC 138815 I Saldos Consolidados ctes sin agrupación

$perfil = $session->variable("r_perfil");
$view = $session->variable("r_view");
//JAC 138815 F Saldos Consolidados ctes sin agrupación

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

	if( !isset /*(HB AGL - Ajustes PHP5)*/( $transaccion_buffer ) ) {
	    	$transaccion_buffer = "";
    }
	//JAC 138815 I Saldos Consolidados ctes sin agrupación
	//
	 if($result == 0)
	{
		if($perfil == "permor" || $perfil == "gobierno")
		{
			if($view != "saldo")
			{
				$FrAccount = "999999999";
				$cuenta 	= md5($session->variable( "r_qki" ).trim($session->variable( "r_cno" )).trim($usr)); //se calcula su md5 //NXN 07sep2012 track 235490
				$ctasArreglo    = $cuenta."|";				 //NXN 07sep2012 track 235490
			}
		}
	}
	//JAC 138815 F Saldos Consolidados ctes sin agrupación

	if ($result == 0) // DBA-DBM F 04Abr2008 observaciones seguridad informatica
	{
		//REF WMA-15001, Saldos Consolidados
		if ($FrAccount == "999999999")
		{
			// REF JAC-26Apr2010, INICIO
			// $xml = "<mensaje><trxn value=\"pro\" /><accion value=\"consolidado\" /><access value=\"Process\" /><tipomsj value=\"rqs\" /><format value =\"xml\" /><CustID value=\"".urlencode(trim($usr))."\" /><cadpriv value=\"".urlencode($priv)."\" /></mensaje>";
			$xml = "<mensaje><trxn value=\"pro\" /><accion value=\"consolidado\" /><access value=\"Process\" /><tipomsj value=\"rqs\" /><format value =\"xml\" /><CustID value=\"".urlencode(trim(str_replace(" ", "_ESPACIO_", $usr)))."\" /><cadpriv value=\"".urlencode($priv)."\" /></mensaje>";
			// REF JAC-26Apr2010, FIN
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
	//MAT -01Mar2012- [ inicio] Validacion del usuario
	$validaCust = strpos($transaccion_buffer, $usr );

	if( $validaCust === false)
	{
		$transaccion_buffer = "<span><br><br><b> Por favor int&eacute;ntelo nuevamente.<b></span>";
	}
	//MAT -01Mar2012- [ fin]  Validacion del usuario

	//ACS 09Ene2008 - MenuContenidoAjax
	//REF WMA-15001, Saldo Consolidados
	if ($FrAccount == "999999999")
	{
		$requestJboss = $xml; //NXN 07sep2012 track 235490
		$responseJboss = $transaccion_buffer; //NXN 07sep2012 track 235490
		include("eztransaccion/user/include/saldosconsolidados_main_form.inc");
		$transaccion_buffer="<h1>Saldo Consolidado</h1><hr size='4' noshade='noshade'>".$transaccion_buffer;
	}
	else
	{
		$transaccion_buffer="<h1>Saldos</h1><hr size='4' noshade='noshade'>".$transaccion_buffer;
	}
	//REF WMA-15001, Saldo Consolidado
		
		
	/***************************************************************************************************************************************************
	[MODULO] CAMPAÑAS MODULO DE SALDOS
	 [AUTOR] OMAR VICENTE VILLALOBOS CASTRO
	****************************************************************************************************************************************************/
	
	// [INFO] => VARIABLES LOCALES
	$valida_cta 	= "Si";
	$cta_peques 	= "Pekes";
	$cta_chavos 	= "Chavos";
	$cta_inversion 	= "Plazos";
	$cta_vistas 	= "Vistas";
	$result 		= "";
	$str_condicion	= "";
	$cust 			= $session->variable( 'r_cno' );
	
	/******************************************************************************************************************************************************
	[TITULO] => VARIABLES DE BASE DE DATOS	
	 [AUTOR] => OMAR VICENTE VILLALOBOS CASTRO
	--------------- ---------------	---------------------------------------------------	-------------------------------------------------------------------
	nombre			Tipo			Enum datos											Descripción
	--------------- ---------------	---------------------------------------------------	-------------------------------------------------------------------
	str_status		varchar(10)		activo - cancelado									Estátus de la campaña
	str_modulo		varchar(20)		saldos - actualiza									Modulo donde se mostrará la campaña en BajioNet	
	str_titulo		varchar(150)														Nombre de la campaña
	str_columnaA	varchar(50)															Nombre de la columna A (Nombre Sorte)
	str_columnaB	varchar(50)															Nombre de la columna B (#Número de boletos)
	str_condicion	varchar(20)		inversion - vista - pekesychavos - vistaeinversion	Condición para mostrar la información de la campaña
	str_tipoCampa	varchar(10)		cuentas -  completa									Forma en la que se mostrará la información
	int_idCampana	int(11)																ID de la campaña configurada para relacionar con información		
	str_promoCamp	varchar(2)		si - no												Captación mostrar bases a todos los clientes
	str_msgInicial	varchar(600)														Información de la campaña antes del resultado de boletos
	str_msgFinal	varchar(600)														Información de las bases después del resultado de boletos
	str_msgSinBo	varchar(600)														Información al cliente de que no tiene boletos y muestra bases
	--------------- ---------------	---------------------------------------------------	-------------------------------------------------------------------
	*******************************************************************************************************************************************************/
	
	//[INFO] => CONEXIÓN A LA BASE DE DATOS PARA OBTENER CONFIGURACIÓN DE CAMPAÑA
	//[TEST] => echo "VALORES => ".$CampSrv."|".$CampUsr."|".$CampPwd."|".$CampBD;	
	$con_database = mysqli_connect( $CampSrv, $CampUsr, $CampPwd );
	if ( $con_database ){
		if ( mysqli_select_db( $con_database,$CampBD ) ){
			//[INFO] => CONDICIONES PARA CONSULTA DE CONFIGURACIÓN DE CAMPAÑA
			$str_status	  = "activo";
			$str_modulo	  = "saldos";				
			$str_consulta = "SELECT * FROM conf_campanias WHERE str_status = '".$str_status."' AND str_modulo = '".$str_modulo."';";
			$array_result = mysqli_query( $con_database, $str_consulta );
			//[INFO] => OBTIENE LA INFORMACIÓN DE LA BASE DE DATOS
			if( mysqli_num_rows( $array_result ) ){
				while( $row = mysqli_fetch_array( $array_result ) ){
					$str_titulo		= $row{'str_titulo'};
					$str_columnaA	= $row{'str_columnaA'};
					$str_columnaB	= $row{'str_columnaB'};
					$str_condicion	= $row{'str_condicion'};
					$str_tipoCampa	= $row{'str_tipoCampa'};
					$int_idCampana	= $row{'int_idCampana'};			
					$str_promoCamp 	= $row{'str_promoCamp'};
					$str_msgInicial	= $row{'str_msgInicial'};
					$str_msgFinal	= $row{'str_msgFinal'};
					$str_msgSinBo	= $row{'str_msgSinBo'};
				}
			}
		}
	}
	
	switch($str_condicion){ // [INFO] => VALIDA EL TIPO DE CONDICIÓN PARA FILTRAR EL MENSAJE DE CAMPAÑA A MOSTRAR
		case "inversion" :	
			if( strpos($transaccion_buffer, $cta_inversion) ){
				$result = cargaCampana( $cust, $Access, $FrAccount, $CampSrv, $CampUsr, $CampPwd, $CampBD, $CampImag, $str_promoCamp, $str_tipoCampa, $int_idCampana, $str_titulo, $str_columnaA, $str_columnaB, $str_msgInicial, $str_msgFinal, $str_msgSinBo );
			}
		break;
		case "vista" :	
			if( strpos($transaccion_buffer, $cta_vistas) ){
				$result = cargaCampana( $cust, $Access, $FrAccount, $CampSrv, $CampUsr, $CampPwd, $CampBD, $CampImag, $str_promoCamp, $str_tipoCampa, $int_idCampana, $str_titulo, $str_columnaA, $str_columnaB, $str_msgInicial, $str_msgFinal, $str_msgSinBo );
			}
		break;
		case "pekesychavos" :	
			if( strpos($transaccion_buffer, $cta_peques) || strpos($transaccion_buffer, $cta_chavos) ){
				$result = cargaCampana( $cust, $Access, $FrAccount, $CampSrv, $CampUsr, $CampPwd, $CampBD, $CampImag, $str_promoCamp, $str_tipoCampa, $int_idCampana, $str_titulo, $str_columnaA, $str_columnaB, $str_msgInicial, $str_msgFinal, $str_msgSinBo );
			}
		break;
		case "vistaeinversion" :	
			if( strpos($transaccion_buffer, $cta_vistas) || strpos($transaccion_buffer, $cta_inversion) ){
				$result = cargaCampana( $cust, $Access, $FrAccount, $CampSrv, $CampUsr, $CampPwd, $CampBD, $CampImag, $str_promoCamp, $str_tipoCampa, $int_idCampana, $str_titulo, $str_columnaA, $str_columnaB, $str_msgInicial, $str_msgFinal, $str_msgSinBo );
			}
		break;
		default:
			$result = "";
		break;
	}
	
	$transaccion_buffer.="<SCRIPT>
							function consultaSaldos ()
							{
								var parametros=	'FrAccount:'+document.getElementsByName('FrAccount')[0].options[document.getElementsByName('FrAccount')[0].selectedIndex].value+
												',Access:'+document.getElementsByName('Access')[0].value+
												',AgruPagIni:'+document.getElementsByName('AgruPagIni')[0].value+
												',CustID:'+document.getElementsByName('CustID')[0].value+
												',ctasArreglo:'+document.getElementsByName('ctasArreglo')[0].value;
												
								iniProcesarMenu('saldos', parametros);
							}
						</SCRIPT>".$result;		
			
	/***************************************************************************************************************************************************
	MODULO: FUNCIONES GENERALES DE CAMPAÑAS
	 AUTOR: OMAR VICENTE VILLALOBOS CASTRO
	****************************************************************************************************************************************************/			
	function cargaCampana( $cust, $Access, $FrAccount, $CampSrv, $CampUsr, $CampPwd, $CampBD, $CampImag, $str_promoCamp, $str_tipoCampa, $int_idCampana, $str_titulo, $str_columnaA, $str_columnaB, $str_msgInicial, $str_msgFinal, $str_msgSinBo ){
		// [INFO] => VARIABLES DEL SISTEMA
		$str_validaBoletos 	= 'no';
		$str_result 		= "";

		// [INFO] => VALIDA LA CUENTA DE SESIÓN EN DADO QUE EXISTA, EN CASO CONTRARIO TOMA LA CUENTA DEL COMBO
		if ( $Access == 'FrAc' and $FrAccount == 0 ){ 
			$str_validaBoletos = 'no'; 
		}else if( $Access == 'FrAc' and $FrAccount != 0 ){
			$str_validaBoletos = 'si'; 
		}else if( $Access == '' and $FrAccount == 0 and $cust != 0 ){
			$str_validaBoletos = 'si';
			$FrAccount = $cust;
		}	
		
		// [INFO] => BANDERA PARA NO MOSTRAR LA CAMPAÑA AL CONSULTAR TODOS Y CONSOLIDADO
		if ( $str_validaBoletos == 'si' )
		{		
			$conexion = mysqli_connect($CampSrv, $CampUsr, $CampPwd);
			if ( $conexion )
			{
				if ( mysqli_select_db($conexion,$CampBD) )
				{
					if ( $str_tipoCampa == "completa" )	// [INFO] => MUESTRA LA INFORMACIÓN DE BOLETOS DE FORMA CONSOLIDADA
					{
						//$consulta  = "SELECT nombreSorteo, numBoletos FROM campanias WHERE numCliente = ".$FrAccount." AND campanaId = ".$int_idCampana." GROUP BY nombreSorteo;";
						$consulta  = "SELECT nombreSorteo, sum(numBoletos) as totalBoletos FROM campanias WHERE numCliente = ".$FrAccount." AND campanaId = ".$int_idCampana." GROUP BY nombreSorteo;";
						$resultado = mysqli_query($conexion, $consulta);
						
						if(mysqli_num_rows($resultado))
						{
							$str_result  = "<center><div style='max-width: 850px; margin: 0px auto; border: 0px solid #000;' ><BR>";
							$str_result .= "<font color='#5A419C'>".$str_msgInicial."</font><BR><BR>";
							$str_result .= "<table width='40%' border='0' cellspacing='3'><tbody>
												<tr>
													<th bgcolor='#5A419C' align='CENTER'><font color='WHITE'>".$str_columnaA."</font></th>
													<th bgcolor='#5A419C' align='CENTER'><font color='WHITE'>".$str_columnaB."</font></th>
												</tr>";
											
							while( $row = mysqli_fetch_array($resultado) ){
							
								$str_nomSorteo = "";
								
								if($row{'nombreSorteo'}=="Cancun") { 
									$str_nomSorteo = str_replace("u","ú",$row{'nombreSorteo'}); 
								} else { 
									$str_nomSorteo = $row{'nombreSorteo'}; 
								}
								
								$str_result .= "<tr><td bgcolor='#DDDDDD'>".$str_nomSorteo."</td><td bgcolor='#DDDDDD'>".$row{'totalBoletos'}."</td></tr>";
							}
							
							$str_result .= "</tbody></table><BR>";
							$str_result .= "<font color='#5A419C'>".$str_msgFinal."</font><BR><BR>";
							$str_result .= "</div></center><BR><BR>";
							$str_result .= $consulta;
						}
					}
					else	// [INFO] => MUESTRA LA INFORMACIÓN DETALLADA POR CADA UNA DE LAS CUENTAS
					{						
						$inicioCampaña = strtolower($str_promoCamp);
						
						if ( $inicioCampaña == "si" )
						{
							$msgInicial = "<font color='#5A419C'>".$str_msgInicial."</font><BR><BR>";
							
							$str_result  = "<center><div style='max-width: 550px; margin: 0px auto; border: 0px solid #000;' ><BR>";
							$str_result .= $msgInicial;
							$str_result .= "</div></center><BR><BR>";
						}
						else
						{
							$consulta  = "SELECT codigoProducto, subProducto, numBoletos FROM campanias WHERE numCliente = ".$FrAccount." AND campanaId = ".$int_idCampana.";";
							$resultado = mysqli_query($conexion, $consulta);				
							
							/******************************************************************************************************************************************
							CREACIÓN DE MENSAJES
							*******************************************************************************************************************************************/																								
							$msgInicial = "<font color='#5A419C'>".$str_msgInicial."</font><BR><BR>";
							$msgFinal	= "<font color='#5A419C'>".$str_msgFinal."</font><BR><BR>";
							$msgSin		= "<font color='#5A419C'>".$str_msgSinBo."</font><BR><BR>";
							
							if( mysqli_num_rows($resultado) )
							{																					
								$str_result = "<center><div style='max-width: 850px; margin: 0px auto; border: 0px solid #000;' ><BR>";
								$str_result .= $msgInicial;
								$str_result .= "<table width='40%' border='0' cellspacing='3'><tbody><tr>
													<th bgcolor='#5A419C' align='CENTER'><font color='WHITE'>".$str_columnaA."</font></th>
													<th bgcolor='#5A419C' align='CENTER'><font color='WHITE'>".$str_columnaB."</font></th>
												</tr>";
											
								$colorRow = "#F2F2F2";
								$cambiaColor = 1;
								$boletosInversion = 0;
								
								while ( $row = mysqli_fetch_array($resultado) ) {																									

									// [INFO] => PROCESO PARA CAMBIAR EL COLOR EN LAS TABLAS
									if( $cambiaColor%2 == 0 ) $colorRow = "#F2F2F2"; else $colorRow = "#DDDDDD";														
									
									// [INFO] => CONVERSIÓN DE TIPOS DE CUENTAS [INICIO] {
									$tipoCuenta = strtoupper($row{'codigoProducto'});														
									if( $tipoCuenta == "CHEQSI" ){ $tipoConvertida = "CLASICA"; } else { $tipoConvertida = $tipoCuenta; }																		
									
									if( $row{'codigoProducto'} == '180' || $row{'codigoProducto'} == '190') // [INFO] => HACE LA SUMATORIA PARA INVERSIÓN CODIGO 180 Y 190
									{
										$boletosInversion = $boletosInversion + $row{'numBoletos'};
									}
									else // [INFO] => VA MOSTRANDO LAS CUENTAS CONFORME ENCUENTRA EN LA BASE DE DATOS
									{
										$str_result.= " <tr>
														<td bgcolor='".$colorRow."'>".$tipoConvertida." ".$row{'subProducto'}."</td>
														<td bgcolor='".$colorRow."'>".$row{'numBoletos'}."</td>
													</tr>";
										$cambiaColor ++;
									}																
								}
								
								// [INFO] => SI ENCUENTRA BOLETOS EN INVERSIÓN LOS MUESTRA CONFORME A LA SUMA REALIZADA
								if ( $boletosInversion > 0 )
								{
									// [INFO] => PROCESO PARA CAMBIAR EL COLOR EN LAS TABLAS
									if( $cambiaColor%2 == 0 ) $colorRow = "#F2F2F2"; else $colorRow = "#DDDDDD";
									$str_result.= " <tr>
													<td bgcolor='".$colorRow."'>INVERSIONES</td>
													<td bgcolor='".$colorRow."'>".$boletosInversion."</td>
												</tr>";
								}
								
								$str_result.= "</tbody></table><BR>";
								$str_result.= $msgFinal;
								$str_result.= "</div></center><BR><BR>";
							}
							else
							{
								$str_result  = "<center><div style='max-width: 850px; margin: 0px auto; border: 0px solid #000;' ><BR>";
								$str_result .= $msgSin;
								$str_result .= "</div></center><BR><BR>";
							}
						}
					}
				}
			}
		}
		return $str_result;
	}
	/*********************************************************************************************************************************
	[INFO] => CAMPAÑA CONSOLIDADA
	$str_titulo		= "Sorteo Aniversario";
	$str_msgInicial	= "¡Felicidades! Has acumulado los siguientes boletos electrónicos:";
	$str_msgFinal	= "<STRONG>Para consultar mayor información y bases del sorteo Conmemoración 20 años haz <a href='/images/bajiosecure/banner_secure/campanas/basesCampanaSaldos.jpg' target='_blank' onclick='window.open(this.href, this.target, 'scrollbars=no,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no'); return false;'>Clic Aquí</a>.</STRONG>";
	$str_msgSinBo	= "";
	
	[INFO] => CAMPAÑA INICIAL
	$str_titulo		= "Sorteo Aniversario";
	$str_msgInicial	= "<STRONG>Sorteo Aniversario</STRONG><BR><BR>Para consultar mayor informaci&oacute;n y bases del sorteo <a href='/images/bajiosecure/banner_secure/campanas/basesCampanaSaldos.jpg' target='_blank' onclick='window.open(this.href, this.target, 'scrollbars=no,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no'); return false;'>Clic Aquí</a>.";
	$str_msgFinal	= "";
	$str_msgSinBo	= "";
	
	[INFO] => CAMPAÑA NORMAL
	$str_titulo		= "Sorteo Aniversario";
	$str_msgInicial	= "<STRONG>¡Felicidades!</STRONG> Has acumulado los siguientes boletos electrónicos con los que tendrás la oportunidad de participar en el sorteo*";
	$str_msgFinal	= "<STRONG>Para mayor información por favor consulte las bases del sorteo</STRONG><BR><BR>* Los boletos participan cuando: 1) Los recursos para apertura o incremento en cuentas de vista e inversiones provengan de dinero nuevo y 2) las cuentas de vista deberán presentar y mantener un incremento de Captación durante la vigencia total de la campaña.";
	$str_msgSinBo	= "<STRONG>Por el momento no cuenta con boletos electrónicos generados.</STRONG><BR><BR>Consulta las bases de la promoción dando <a href='/images/bajiosecure/banner_secure/campanas/basesCampanaSaldos.jpg' target='_blank' onclick='window.open(this.href, this.target, 'scrollbars=no,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no'); return false;'>Clic Aquí</a>, para conocer mayor detalle de nuestro Sorteo Aniversario";
	
	[INFO] => CAMPAÑA CAMBIO DE LEYENDA
	$str_titulo		= "Sorteo Aniversario";
	$str_msgInicial	= "<STRONG>¡Felicidades!</STRONG> Has acumulado los siguientes boletos electrónicos con los cuales participarás en nuestro Sorteo Aniversario.";
	$str_msgFinal	= "<STRONG>¡Mucha suerte!</STRONG>";
	$str_msgSinBo	= "<STRONG>Usted no cuenta con boletos electrónicos participantes para el Sorte Aniversario</STRONG><BR><BR>Consulta las bases de la promoción dando <a href='/images/bajiosecure/banner_secure/campanas/basesCampanaSaldos.jpg' target='_blank' onclick='window.open(this.href, this.target, 'scrollbars=no,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no'); return false;'>Clic Aquí</a>, para conocer mayor detalle de nuestro Sorteo Aniversario";
	**********************************************************************************************************************************/
?>