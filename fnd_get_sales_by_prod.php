<?php
date_default_timezone_set('America/Los_Angeles');

require_once('../../set/cnf_conect_dbpdo.php'); 
require_once('../../set/in_db_query_functions.php');
require_once('../../set/in_general_functions.php');
require_once('../../set/in_getData_fnd.php');

$LBSCompare		= gda_LBSValue_compare();
$call_method  	= $_POST["method"];
$jsondata		= array();

function getDataChart( $LBSProd, $LBSTotal ){
	$setDataChart = ( $LBSProd * 100 ) / $LBSTotal;
	return $setDataChart;
}

switch( $call_method ) 
{
	case "consult":
		$inProdID   	= $_POST['inProdID'];
		$inDateRange	= $_POST['inDateRange'];
		$inStartDate   	= $_POST['inStartDate'];
		$inEndDate   	= $_POST['inEndDate'];
		
		if( $inDateRange == "auto" ){
			$DATE_QUERY 		= new DateTime();
			$DATE_MONTH			= strtoupper( $DATE_QUERY -> format('F') );
			$DATE_YEAR			= $DATE_QUERY -> format('Y');
			$DATE_DAY			= $DATE_QUERY -> format('d');
			
			$DATE_FIRST_DAY 	= new DateTime();
			$DATE_FIRST_DAY 	-> modify('first day of this month');
			$VALUE_FIRST_DAY 	= $DATE_FIRST_DAY -> format('Y-m-d');
			$FIRST_DAY_FORM_USA	= $DATE_FIRST_DAY -> format('m-d-Y');
			
			$DATE_LAST_DAY 		= new DateTime();
			$DATE_LAST_DAY 		-> modify('last day of this month');
			$VALUE_LAST_DAY 	= $DATE_LAST_DAY -> format('Y-m-d');
			$LAST_DAY_FORM_USA 	= $DATE_LAST_DAY -> format('m-d-Y');
		} else {
			$DATE_QUERY 		= new DateTime($inStartDate);
			$DATE_MONTH			= strtoupper( $DATE_QUERY -> format('F') );
			$DATE_YEAR			= $DATE_QUERY -> format('Y');
			$DATE_DAY			= $DATE_QUERY -> format('d');
			
			$DATE_FIRST_DAY 	= new DateTime($inStartDate);
			$VALUE_FIRST_DAY 	= $DATE_FIRST_DAY -> format('Y-m-d');
			$FIRST_DAY_FORM_USA	= $DATE_FIRST_DAY -> format('m-d-Y');
			
			$DATE_LAST_DAY 		= new DateTime($inEndDate);
			$VALUE_LAST_DAY 	= $DATE_LAST_DAY -> format('Y-m-d');
			$LAST_DAY_FORM_USA 	= $DATE_LAST_DAY -> format('m-d-Y');
		}
		
		try{
			$totalBags 			= 0;
			$totalBox 			= 0;
			$totalBuckets 		= 0;
			$totalDrums 		= 0;
			$totalGallons 		= 0;
			$totalLbs 			= 0;
			$totalMetalGallon 	= 0;
			$totalMiniDrums 	= 0;
			$totalPails 		= 0;
			$totalPallets 		= 0;
			$totalSample 		= 0;
			$totalTote		 	= 0;
			$totalLbsSold		= 0;
			$donutData_Axis		= array();
			$donutData_Legend	= "";
			
			$qse_prodSales = 'SELECT 
								SUM(it.quantity) AS total,
								it.type AS type
							FROM 
								db_items AS it 
							WHERE 
								it.desc = :inProdDesc AND 
								it.effective between :inDateFrom AND :inDateTo
							GROUP BY 
								it.type;';
								
			unset( $stmt );
			$stmt = $db->prepare( $qse_prodSales );
			if( $stmt->execute( array( 
					":inProdDesc" 	=> $inProdID,
					":inDateFrom" 	=> $VALUE_FIRST_DAY,
					":inDateTo" 	=> $VALUE_LAST_DAY
			))){
				while( $row_prodSales = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
					$totalSale = $row_prodSales['total'];
					
					switch( $row_prodSales['type'] ){
						case "Bags": 			$totalBags 			= $totalSale;	$totalLbsSold = $totalLbsSold+($row_prodSales['total'] * $LBSCompare["BAGSisLBS"]); break;
						case "Boxs": 			$totalBox 			= $totalSale; 	$totalLbsSold = $totalLbsSold+($row_prodSales['total'] * $LBSCompare["BOXisLBS"]);	break;
						case "Buckets": 		$totalBuckets 		= $totalSale; 	$totalLbsSold = $totalLbsSold+($row_prodSales['total'] * $LBSCompare["BUCKisLBS"]);	break;
						case "Drums": 			$totalDrums 		= $totalSale; 	$totalLbsSold = $totalLbsSold+($row_prodSales['total'] * $LBSCompare["DRUMisLBS"]);	break;
						case "Gallons": 		$totalGallons 		= $totalSale; 	$totalLbsSold = $totalLbsSold+($row_prodSales['total'] * $LBSCompare["GALLisLBS"]);	break;
						case "Lbs": 			$totalLbs 			= $totalSale; 	$totalLbsSold = $totalLbsSold+($row_prodSales['total'] * $LBSCompare["LBSisLBS"]);	break;
						case "Metal Gallon": 	$totalMetalGallon 	= $totalSale; 	$totalLbsSold = $totalLbsSold+($row_prodSales['total'] * $LBSCompare["MEGAisLBS"]);	break;
						case "Mini Drums": 		$totalMiniDrums 	= $totalSale; 	$totalLbsSold = $totalLbsSold+($row_prodSales['total'] * $LBSCompare["MIDRisLBS"]);	break;
						case "Pails": 			$totalPails 		= $totalSale; 	$totalLbsSold = $totalLbsSold+($row_prodSales['total'] * $LBSCompare["PAILisLBS"]);	break;
						case "Pallets": 		$totalPallets 		= $totalSale; 	$totalLbsSold = $totalLbsSold+($row_prodSales['total'] * $LBSCompare["PALLisLBS"]);	break;
						case "Sample": 			$totalSample 		= $totalSale; 	$totalLbsSold = $totalLbsSold+($row_prodSales['total'] * $LBSCompare["SAMPisLBS"]);	break;
						case "Tote":			$totalTote 			= $totalSale; 	$totalLbsSold = $totalLbsSold+($row_prodSales['total'] * $LBSCompare["TOTEisLBS"]);	break;
					}
				}
				
				$LBSTotal = $totalLbsSold;
				
				if( $totalBags != 0 ){
					$LBSProd 	= $totalBags * $LBSCompare["BAGSisLBS"];
					$donutData_Axis[]  = array("label" => "Bags", "data" => getDataChart( $LBSProd, $LBSTotal ), "color" => "#F34336");
					$donutData_Legend .= '<li id="Bags" class="boGetHistory" onclick="getHistory(this)" ><i class="fa fa-circle" style="color: #F34336;" ></i> Bags</li>';
				}
				if( $totalBox != 0 ){
					$LBSProd 	= $totalBox * $LBSCompare["BOXisLBS"];		
					$donutData_Axis[] = array("label" => "Boxs", "data" => getDataChart( $LBSProd, $LBSTotal ), "color" => "#E81F62");
					$donutData_Legend .= '<li id="Boxs" class="boGetHistory" onclick="getHistory(this)" ><i class="fa fa-circle" style="color: #E81F62;" ></i> Box</li>';
				}
				if( $totalBuckets != 0 ){
					$LBSProd 	= $totalBuckets * $LBSCompare["BUCKisLBS"];		
					$donutData_Axis[] = array("label" => "Buckets", "data" => getDataChart( $LBSProd, $LBSTotal ), "color" => "#9C29AE");
					$donutData_Legend .= '<li id="Buckets" class="boGetHistory" onclick="getHistory(this)" ><i class="fa fa-circle" style="color: #9C29AE;" ></i> Buckets</li>';
				}
				if( $totalDrums != 0 ){
					$LBSProd 	= $totalDrums * $LBSCompare["DRUMisLBS"];		
					$donutData_Axis[] = array("label" => "Drums", "data" => getDataChart( $LBSProd, $LBSTotal ), "color" => "#673BB5");
					$donutData_Legend .= '<li id="Drums" class="boGetHistory" onclick="getHistory(this)" ><i class="fa fa-circle" style="color: #673BB5;" ></i> Drums</li>';
				}
				if( $totalGallons != 0 ){
					$LBSProd 	= $totalGallons * $LBSCompare["GALLisLBS"];		
					$donutData_Axis[] = array("label" => "Gallons", "data" => getDataChart( $LBSProd, $LBSTotal ), "color" => "#4051B3");
					$donutData_Legend .= '<li id="Gallons" class="boGetHistory" onclick="getHistory(this)" ><i class="fa fa-circle" style="color: #4051B3;" ></i> Gallons</li>';
				}
				if( $totalLbs != 0 ){
					$LBSProd 	= $totalLbs * $LBSCompare["LBSisLBS"];		
					$donutData_Axis[] = array("label" => "Lbs", "data" => getDataChart( $LBSProd, $LBSTotal ), "color" => "#2596F1");
					$donutData_Legend .= '<li id="Lbs" class="boGetHistory" onclick="getHistory(this)" ><i class="fa fa-circle" style="color: #2596F1;" ></i> Lbs</li>';
				}
				if( $totalMetalGallon != 0 ){
					$LBSProd 	= $totalMetalGallon * $LBSCompare["MEGAisLBS"];		
					$donutData_Axis[] = array("label" => "Metal Gallon", "data" => getDataChart( $LBSProd, $LBSTotal ), "color" => "#89C24D");
					$donutData_Legend .= '<li id="Metal Gallon" class="boGetHistory" onclick="getHistory(this)" ><i class="fa fa-circle" style="color: #89C24D;" ></i> Metal Gallon</li>';
				}
				if( $totalMiniDrums != 0 ){
					$LBSProd 	= $totalMiniDrums * $LBSCompare["MIDRisLBS"];		
					$donutData_Axis[] = array("label" => "Mini Drums", "data" => getDataChart( $LBSProd, $LBSTotal ), "color" => "#CCDB3F");
					$donutData_Legend .= '<li id="Mini Drums" class="boGetHistory" onclick="getHistory(this)" ><i class="fa fa-circle" style="color: #CCDB3F;" ></i> Mini Drums</li>';
				}
				if( $totalPails != 0 ){
					$LBSProd 	= $totalPails * $LBSCompare["PAILisLBS"];		
					$donutData_Axis[] = array("label" => "Pails", "data" => getDataChart( $LBSProd, $LBSTotal ), "color" => "#FEC019");
					$donutData_Legend .= '<li id="Pails" class="boGetHistory" onclick="getHistory(this)" ><i class="fa fa-circle" style="color: #FEC019;" ></i> Pails</li>';
				}
				if( $totalPallets != 0 ){
					$LBSProd 	= $totalPallets * $LBSCompare["PALLisLBS"];		
					$donutData_Axis[] = array("label" => "Pallets", "data" => getDataChart( $LBSProd, $LBSTotal ), "color" => "#02BBD3");
					$donutData_Legend .= '<li id="Pallets" class="boGetHistory" onclick="getHistory(this)" ><i class="fa fa-circle" style="color: #02BBD3;" ></i> Pallets</li>';
				}
				if( $totalSample != 0 ){
					$LBSProd 	= $totalSample * $LBSCompare["SAMPisLBS"];		
					$donutData_Axis[] = array("label" => "Sample", "data" => getDataChart( $LBSProd, $LBSTotal ), "color" => "#785447");
					$donutData_Legend .= '<li id="Sample" class="boGetHistory" onclick="getHistory(this)" ><i class="fa fa-circle" style="color: #785447;" ></i> Sample</li>';
				}
				if( $totalTote != 0 ){
					$LBSProd 	= $totalTote * $LBSCompare["TOTEisLBS"];		
					$donutData_Axis[] = array("label" => "Tote", "data" => getDataChart( $LBSProd, $LBSTotal ), "color" => "#5F7C8A");
					$donutData_Legend .= '<li id="Tote" class="boGetHistory" onclick="getHistory(this)" ><i class="fa fa-circle" style="color: #5F7C8A;" ></i> Tote</li>';
				}

				$jsondata['Bags']			= $totalBags;
				$jsondata['Boxs']			= $totalBox;
				$jsondata['Buckets']		= $totalBuckets;
				$jsondata['Drums']			= $totalDrums;
				$jsondata['Gallons']		= $totalGallons;
				$jsondata['Lbs']			= $totalLbs;
				$jsondata['Metal_Gallon']	= $totalMetalGallon;
				$jsondata['Mini_Drums']		= $totalMiniDrums;
				$jsondata['Pails']			= $totalPails;
				$jsondata['Pallets']		= $totalPallets;
				$jsondata['Sample']			= $totalSample;
				$jsondata['Tote']			= $totalTote;
				$jsondata['TotalLBS']		= $totalLbsSold;
				
				$jsondata['donutData_Axis']		= json_encode( $donutData_Axis );
				$jsondata['donutData_Legend']	= $donutData_Legend;
				
				$jsondata['success'] = true;
				$jsondata['message'] = "The registry was selected correctly in the system.";
				$jsondata['code'] 	 = "SMK_CODE(OKCOP1001).";
			} else {
				$jsondata['success'] = false;
				$jsondata['message'] = "An error occurred while trying to read the registry in the system.".get_msg_try_again();
				$jsondata['code'] 	 = "SMK_CODE(ECOP001).";
			}
		}catch(PDOException $ex) {
			$jsondata['success'] = false;
			$jsondata['message'] = "An error occurred while trying to read the registry in the system.".get_msg_try_again()."(".$ex.")";
			$jsondata['code'] 	 = "SMK_CODE(ECOTRP001).";
		}	
	break;
	case "getHistory":
		try{
			$inProdID   = $_POST['inProdID'];
			$inProdType	= $_POST['inProdType'];
			
			$get_month_array = gda_monthDate_2018();
			$get_date_array	 = array();
			
			for( $i=0 ; $i<12 ; $i++ ){
				$get_date_array = explode( "|", $get_month_array[$i] );
				$qse_prodSales = '	SELECT 
										SUM(it.quantity) AS total
									FROM 
										db_items AS it 
									WHERE 
										it.desc = :inProdID AND 
										it.type = :inProdType AND 
										it.effective between :inDateFrom AND :inDateTo';
				unset( $stmt );
				$stmt = $db->prepare( $qse_prodSales );
				$stmt->execute( array( ":inProdID" => $inProdID, ":inProdType" => $inProdType, ":inDateFrom" => $get_date_array[1], ":inDateTo" => $get_date_array[2] ) );
				if( $stmt->fetchColumn() > 0 ){
					$totalLbsSold = 0;
					
					unset( $stmt );
					$stmt = $db->prepare( $qse_prodSales );
					$stmt->execute( array( ":inProdID" => $inProdID, ":inProdType" => $inProdType, ":inDateFrom" => $get_date_array[1], ":inDateTo" => $get_date_array[2] ) );
					while( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
						$totalLbsSold = $row['total'];
					}
					
					$val_xaxis[] = array( $get_date_array[0], $totalLbsSold );
				} else {
					$val_xaxis[] = array( $get_date_array[0], 0 );
				}
			}
			
			$jsondata['valXaxis']		= json_encode( $val_xaxis );
			$jsondata['tagBarLine']		= "&nbsp;<strong> Total of ".$inProdType." sold in 2018</strong> - ". $inProdID;
			
			$jsondata['success'] = true;
			$jsondata['message'] = "The registry was selected correctly in the system.";
			$jsondata['code'] 	 = "SMK_CODE(OKCOP1001).";
		}catch(PDOException $ex) {
			$jsondata['success'] = false;
			$jsondata['message'] = "An error occurred while trying to read the registry in the system.".get_msg_try_again()."(".$ex.")";
			$jsondata['code'] 	 = "SMK_CODE(ECOTRP001).";
		}	
	break;
}

echo json_encode( $jsondata, JSON_FORCE_OBJECT );
?>