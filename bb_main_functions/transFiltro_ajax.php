<?php
include("eztransaccion/user/include/xmlparser.inc");	//REF AAS-filtro cuenta
include("eztransaccion/user/include/httplib.php");	//REF AAS-filtro cuenta

//NX I 138929 Ene2010 Filtro por Nombre - Inicio
include_once( "classes/ezlogdb.php" );			
include_once( "classes/ezlog.php" ); 		

$SPFuseJboss = trim(readParam("SPFuseJBoss","site.ini")); 
//NX I 138929 Ene2010 Filtro por Nombre - Fin

/*
VMV - Filtro AJAX para SPEI y TEF - 129451
*/
    $transaccion_buffer = "";

    if(empty($Access)) {
        $Access = "FrAc";
    }

    $Empresa = "TT";

    if(empty($Position)) { $Position = ""; }

    switch($Access) {
    case "FrAc":
        $particularFields = "&Filtro=".urlencode($Filtro);
        $tr->blog($qki,"transFiltro_ajax", $FrAccount, $Apocope, $NombreB, $Amount, $Empresa);
        break;
    }
	
    $transaccion_buffer = ""; 
    //$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=spf&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);
	//  NX I 138929 Ene2010 Filtro por Nombre
	
	if ($TFiltro == "FNombre")
	{
		//NXN  08May2012 - Filtro x Nombre SPEI - Inicio	
		//$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=spf&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);		
		if ($SPFuseJboss === "NO") {	
			$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=spf&Access=".urlencode($Access)."&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv).$particularFields, $qki, $usr, $qki, $priv, $transaccion_buffer);		
		}
		else
		{
			$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";
			$xml .="<mensaje>";
			$xml .="<trxn value=\"spf\" />";
			$xml .="<accion value=\"filtronombre\" />";
			$xml .="<access value=\"Process\" />";
			$xml .="<tipomsj value=\"rqs\" />";
			$xml .="<CustID value=\"".urlencode(trim($usr))."\" />";
			$xml .="<format value=\"xml\" />";
			$xml .="<cadpriv value=\"".urlencode($priv)."\" />";
			$xml .="<tipo value=\"".urlencode($Type)."\" />";
			$xml .="<filter value=\"".urlencode(trim($Filtro))."\" />";
			$xml .="</mensaje>";

			$JBoss = trim(readParam("JBossHost","site.ini"));
			$JBossPath = trim(readParam("JBossPath","site.ini"));
			
			$LogBB = new eZLogDB ();
			$LogBB->StoreTime();
			$TrxnLogBB = 'JbossSPF'; 
			$AccessLogBB = 'qry'; 
			
			$ret_code = requestHTTPtoJBoss($JBoss, $JBossPath, "xml", $xml);
				
			if($ret_code==="Connection refused") {
			
				$transaccion_buffer = $transaccion_buffer . "<script language='javascript'>document.getElementById('beneficiarioDIV').innerHTML = ''; ";
				$transaccion_buffer = $transaccion_buffer . "document.getElementById('cmbBeneDIV').innerHTML = '<select name=\"ToAccount\"><option value=\"998877665544332211\">Servicio no disponible por el momento</option></select>';</script>";
			}
			else {
				include("eztransaccion/user/include/transfiltro_vtm_jboss.inc");
			}
		}			
		//NXN  08May2012 - Filtro x Nombre SPEI - Fin		
	}
	else if ($TFiltro == "FTarjeta")
	{	
		$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";
		$xml .="<mensaje>";
		$xml .="<trxn value=\"ftr\" />";
		$xml .="<accion value=\"filtrobenevtm\" />";
		$xml .="<access value=\"Process\" />";
		$xml .="<tipomsj value=\"rqs\" />";
		$xml .="<CustID value=\"".urlencode(trim($usr))."\" />";
		$xml .="<format value=\"xml\" />";
		$xml .="<cadpriv value=\"".urlencode($priv)."\" />";
		$xml .="<tipo value=\"".urlencode($Type)."\" />";
		$xml .="<filter value=\"".urlencode(trim($Filtro))."\" />";
		$xml .="</mensaje>";

		//$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=ftr&Access=Process&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv)."&xml=".urlencode($xml), $qki, $usr, $qki, $priv, $transaccion_buffer);

		$JBoss = trim(readParam("JBossHost","site.ini"));
		$JBossPath = trim(readParam("JBossPath","site.ini"));

		$ret_code = requestHTTPtoJBoss($JBoss, $JBossPath, "xml", $xml);
		include("eztransaccion/user/include/transfiltro_vtm.inc");
	}
	else
	{
		$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>";
		$xml .="<mensaje>";
			$xml .="<trxn value=\"ftr\" />";
			$xml .="<accion value=\"filtrocuenta\" />";
			$xml .="<access value=\"Process\" />";
			$xml .="<tipomsj value=\"rqs\" />";
			$xml .="<CustID value=\"".urlencode(trim($usr))."\" />";
			$xml .="<format value=\"xml\" />";
			$xml .="<cadpriv value=\"".urlencode($priv)."\" />";
			$xml .="<tipo value=\"".urlencode($Type)."\" />";
			$xml .="<filter value=\"".urlencode(trim($Filtro))."\" />";
		$xml .="</mensaje>";


		$ret_code = $tr->PostToHost($backend, "/IBnkIIS.dll", "Trxn=ftr&Access=Process&CustID=".urlencode($usr)."&Cadpriv=".urlencode($priv)."&xml=".urlencode($xml), $qki, $usr, $qki, $priv, $transaccion_buffer);

		//$JBoss = trim(readParam("JBossHost","site.ini"));
		//$JBossPath = trim(readParam("JBossPath","site.ini"));

		//$ret_code = requestHTTPtoJBoss($JBoss, $JBossPath, "xml", $xml);

		include("eztransaccion/user/include/transfiltro_main_form.inc");
	}
	//  NX F 138929 Ene2010 Filtro por Nombre
	if ($Access == "Confirm" and (trim($ToAccount) == "" or trim($ToAccount) == "998877665544332211")) {
		$transaccion_buffer = "El beneficiario no puede quedar vac&iacute;o</b>";
	}
?>
