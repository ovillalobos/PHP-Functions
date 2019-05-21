<?php

function pwdencr( $NewPass )
{
	$NewPassMaskI = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	$NewPassMaskO = "2CFaJA0DEn97NOsbT5MdBLzQRS8Ui4hWkXGfYZHceomjlwgpVqr3txIP1yuKv6";
                   //01234567890123456789012345678901234567890123456789012345678901
                   //         10        20        30        40        50        60
	$NewPassTmp = "";
	//$NewPassTmpX = "";
	$NewPWDFlag = 0;
	for($i=0;$i<10;$i++)
	{
		$NewPWDFlag = 0;
		for($j=0;$j<62;$j++)
		{
			if ( substr( $NewPass, $i, 1 ) != "" && substr( $NewPass, $i, 1 ) == substr( $NewPassMaskI, $j, 1 ) ) 
			{
					 $NewPWDFlag = 1;
					 $NewPassTmp = $NewPassTmp . substr( $NewPassMaskO, $j, 1 );
				 	 //$NewPassTmpX = $NewPassTmpX . "[i=". $i . ",j=" . $j. "<" . $NewPassTmp . ">";
			}
		}
		if ( $NewPWDFlag == 0 )
		{
			 $NewPassTmp = $NewPassTmp . substr( $NewPass, $i, 1 );
		 	 //$NewPassTmpX = $NewPassTmpX . "[i=". $i . ",EOF<" . $NewPassTmp . ">";
		}
	}
	$NewPass = $NewPassTmp;
	return $NewPass;
}

?>
