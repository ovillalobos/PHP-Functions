<?php
include("eztransaccion/user/include/xmlparser.inc");
include('eztransaccion/user/include/estilo.inc'); 
	$particularFields = "";
	if ( isset( $Pos ) )
	{
		$particularFields = "&Pos=".urlencode($Pos);
			
	}
	$particularFields .= "&direccion=".urlencode($direccion);
	if ( !isset( $cantRegPag ) ) 
	{
		//ITD [231353] 23Abr2013 Mostrar Bitacora de movimientos [Inicio]
		$particularFields .= "&cantRegPag=10";
		//ITD [231353] 23Abr2013 Mostrar Bitacora de movimientos [Fin]
	}
	else 
	{
		$particularFields .= "&cantRegPag=".urlencode($cantRegPag);
	}

	$particularFields .= "&rownoFirstRec=".urlencode($rownoFirstRec);
	$particularFields .= "&rownoLastRec=".urlencode($rownoLastRec);
	// FECHAS PARA LAS CONSULTAS Y PERMITIR LA NAVEGABILIDAD.
//	$particularFields .= "&fechaFirstRec=".urlencode($fechaFirstRec);	// Fecha del primer registro del recordset.
	$particularFields .= "&fechaPrevRec=".urlencode($fechaPrevRec);		// Fecha del primer registro de la pagina actual.
	$particularFields .= "&fechaNextRec=".urlencode($fechaNextRec);		// Fecha del ultimo registro de la pagina actual.
//	$particularFields .= "&fechaLastRec=".urlencode($fechaLastRec);		// Fecha del ultimo registro del recordset.
	
	// HORAS PARA LAS CONSULTAS Y PERMITIR LA NAVEGABILIDAD.
//	$particularFields .= "&horaFirstRec=".urlencode($horaFirstRec);	// hora del primer registro del recordset.
	$particularFields .= "&horaPrevRec=".urlencode($horaPrevRec);		// Hora del primer registro de la pagina actual.
	$particularFields .= "&horaNextRec=".urlencode($horaNextRec);		// Hora del ultimo registro de la pagina actual.
//	$particularFields .= "&horaLastRec=".urlencode($horaLastRec);		// Hora del ultimo registro del recordset.
	
    $transaccion_buffer = "";

	// SCRIPT SECTION.
	$transaccion_buffer .= "<script language='JavaScript'>";
	$transaccion_buffer .= "	jQuery( document ).ready( function () { ";
	$transaccion_buffer .= "		$( '#btn_next' ).click( function () { ";
	$transaccion_buffer .= " 			var rownoFirst = $( '#rownoFirst' ).val();";
	$transaccion_buffer .= "			var rownoLast  = $( '#rownoLast'  ).val();";
	$transaccion_buffer .= "			var fechaNext  = $( '#fechaNext'  ).val();";
	$transaccion_buffer .= "			var horaNext   = $( '#horaNext'   ).val();";
	$transaccion_buffer .= "			var direccion  = 'forward';";
							//ITD [231353] 23Abr2013 Mostrar Bitacora de movimientos [Inicio]
	$transaccion_buffer .= "			var cantRegPag = '10';";	// PARAMETRIZABLE
							//ITD [231353] 23Abr2013 Mostrar Bitacora de movimientos [Fin]
	$transaccion_buffer .= "			var params;";
	$transaccion_buffer .= "	";	// LINE BREAK.
	$transaccion_buffer .= "			params  = 'direccion:'   + direccion; ";
	$transaccion_buffer .= "			params += ',cantRegPag:' + cantRegPag;";
	$transaccion_buffer .= "			params += ',rownoFirst:' + rownoFirst;";
	$transaccion_buffer .= "			params += ',rownoLast:'  + rownoLast; ";
	$transaccion_buffer .= "			params += ',fechaNext:'  + fechaNext; ";
	$transaccion_buffer .= "			params += ',horaNext:'   + horaNext;  ";
	$transaccion_buffer .= "	";	// LINE BREAK.
	$transaccion_buffer .= "			iniProcesarMenu('bitacoraIBNK', params);";
	$transaccion_buffer .= "		});";
	$transaccion_buffer .= "	";	// LINE BREAK.
	$transaccion_buffer .= "		$( '#btn_next' ).mouseover( function () { ";
	$transaccion_buffer .= "			$( this ).removeClass( 'button' );";
	$transaccion_buffer .= "			$( this ).addClass( 'buttonON' );";
	$transaccion_buffer .= "		});";
	$transaccion_buffer .= "	";	// LINE BREAK.
	$transaccion_buffer .= "		$( '#btn_next' ).mouseout( function () { ";
	$transaccion_buffer .= "			$( this ).removeClass( 'buttonON' );";
	$transaccion_buffer .= " 			$( this ).addClass( 'button' );";
	$transaccion_buffer .= "		});";
	$transaccion_buffer .= "	";	// LINE BREAK.
	$transaccion_buffer .= " 		$( '#btn_prev' ).click( function () { ";
	$transaccion_buffer .= "			var rownoFirst = $( '#rownoFirst' ).val();";
	$transaccion_buffer .= "			var rownoLast  = $( '#rownoLast'  ).val();";
	$transaccion_buffer .= "			var fechaPrev  = $( '#fechaPrev'  ).val();";
	$transaccion_buffer .= "			var horaPrev   = $( '#horaPrev'   ).val();";
	$transaccion_buffer .= "			var direccion  = 'backward';";
							//ITD [231353] 23Abr2013 Mostrar Bitacora de movimientos [Inicio]
	$transaccion_buffer .= "			var cantRegPag = '10';";	// PARAMETRIZABLE.
							//ITD [231353] 23Abr2013 Mostrar Bitacora de movimientos [Fin]
	$transaccion_buffer .= " 			var params;";
	$transaccion_buffer .= "	";	// LINE BREAK.
	$transaccion_buffer .= "			params  = 'direccion:'   + direccion; ";
	$transaccion_buffer .= "			params += ',cantRegPag:' + cantRegPag;";
	$transaccion_buffer .= "			params += ',rownoFirst:' + rownoFirst;";
	$transaccion_buffer .= "			params += ',rownoLast:'  + rownoLast; ";
	$transaccion_buffer .= "			params += ',fechaPrev:'  + fechaPrev; ";
	$transaccion_buffer .= "			params += ',horaPrev:'   + horaPrev;  ";
	$transaccion_buffer .= "	";	// LINE BREAK.
	$transaccion_buffer .= "			iniProcesarMenu('bitacoraIBNK', params);";
	$transaccion_buffer .= "		});";
	$transaccion_buffer .= "	";	// LINE BREAK.
	$transaccion_buffer .= "		$( '#btn_prev' ).mouseover( function () { ";
	$transaccion_buffer .= "			$( this ).removeClass( 'button' );";
	$transaccion_buffer .= "			$( this ).addClass( 'buttonON' );";
	$transaccion_buffer .= " 		});";
	$transaccion_buffer .= "	";	// LINE BREAK.
	$transaccion_buffer .= "		$( '#btn_prev' ).mouseout( function () { ";
	$transaccion_buffer .= "			$( this ).removeClass( 'buttonON' );";
	$transaccion_buffer .= " 			$( this ).addClass( 'button' );";
	$transaccion_buffer .= "		});";
	$transaccion_buffer .= "	";	// LINE BREAK.
	$transaccion_buffer .= "	});";
	$transaccion_buffer .= "</script>";
	
    $ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=mib&Access=FrAc&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer); // Saldos Tarjetas de Credito
	$transaccion_buffer="<h1>Bitácora de Movimientos en Internet</h1><hr noshade='noshade' size='4' />".$transaccion_buffer;
    
?>