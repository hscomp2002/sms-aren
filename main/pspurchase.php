<?php 
	include_once("../kernel.php");
/*
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die('error');
*/
	include_once('../class/parser.php');
	
  	//var_dump($array);
//echo("<br /><br /><h1>");
//echo $array["resultObj"]["result"];
//echo("</h1>")
	//var_dump($_REQUEST);
	if(isset($_GET['tref']) && isset($_GET['iN']) && isset($_GET['iD']))
	{
		$iN = (int)$_GET['iN'];
		$iD = trim($_GET['iD']);
		$result = post2https($_GET['tref'],'https://epayment.bankpasargad.com/CheckTransactionResult.aspx');
		//var_dump($result);
		$bank_out = makeXMLTree($result);
		//var_dump($bank_out);
		if($bank_out["resultObj"]["result"]=="True" && $iN==(int)$bank_out["resultObj"]['invoiceNumber'] && $iD==trim($bank_out['resultObj']['invoiceDate']) )
		{
			$pardakht = new pardakht_class((int)$bank_out['resultObj']['invoiceNumber']);
			if(isset($pardakht->id))
			{
				$pardakht->bank_out = serialize($bank_out);
				$my = new mysql_class;
				$my->ex_sqlx("update `pardakht` set `bank_out` = '".$pardakht->bank_out."' where `id` = ".$pardakht->id);
				$toz = 'افزایش اعتبار با کد رهگیری '.$pardakht->getBarcode().' مورخ '.jdate("H:i d / m / Y",strtotime(trim($bank_out['resultObj']['invoiceDate'])));
				$user_sabt = isset($_SESSION[$conf->app.'_user_id'])?(int)$_SESSION[$conf->app.'_user_id']:$pardakht->user_id;
				user_etebar_class::add($pardakht->user_id,1,$pardakht->mablagh,$toz,$user_sabt);
				$prf = new profile_class($pardakht->user_id);
				if(!isset($prf->id))
					$my->ex_sqlx("insert into `profile` (`user_id`,`etebar`) values (".$pardakht->user_id.",".$pardakht->mablagh.")");
				else
					$my->ex_sqlx("update `profile` set `etebar` = `etebar` + ".$pardakht->mablagh." where `user_id` = ".$pardakht->user_id);
				$prf = new profile_class($pardakht->user_id);
				if($pardakht->factor_id > 0)
				{
					$fac = new factor_class($pardakht->factor_id);
					if($prf->etebar >= ($fac->jamKol+$fac->hazineErsal))
					{
						$fac->decMojoodi();
						$toz = 'کاهش اعتبار بابت فاکتور شماره '.$pardakht->factor_id;
						$my->ex_sqlx("update `profile` set `etebar` = `etebar` - ".($fac->jamKol+$fac->hazineErsal)." , `hajm_rialy_takhfif` = `hajm_rialy_takhfif` + ".($fac->jamKol+$fac->hazineErsal)."  where `user_id` = ".$pardakht->user_id);
						user_etebar_class::add($pardakht->user_id,-1,$pardakht->mablagh,$toz,$pardakht->user_id);
						$my->ex_sqlx("update `factor` set `isTasfie` = 1 where `id` = ".$pardakht->factor_id);
						$out = 'اعتبار شما با موفقیت افزایش یافت و  فاکتور شماره '.$pardakht->factor_id.'  پرداخت شد.';
					}
					else
					{
						//$my->ex_sqlx("update 
						$out = 'اعتبار شما با موفقیت افزایش یافت ، اما به علت کمتر بودن آن از فاکتور شماره '.$pardakht->factor_id.' فاکتور مربوطه پرداخت نشد.';
					}
				}
				else
				{
					$out = 'اعتبار شما با موفقیت افزایش یافت.';
				}
	
			}
			else
				$out = 'متاسفانه کد خرید شما نامعتبر استو جهت پیگیری خرید خود با در دست داشتم کد رهگیری بانک خود با ما تماس بگیرید. با عرض پوزش از مشکل پیش آمده('.((int)$bank_out['resultObj']['invoiceNumber']).').
';
		}
		else
			$out = 'در تراکنش مالی مشکلی پیش آمده است پرداخت انجام نشد مجدد سعی نمایید درصورت پرداخت وجه ، حداکثر تا سه روز کاری وجه به حساب شما بازگشت داده می شود
';
	}
	else
		$out = 'در تراکنش مالی مشکلی پیش آمده است پرداخت انجام نشد مجدد سعی نمایید درصورت پرداخت وجه ، حداکثر تا سه روز کاری وجه به حساب شما بازگشت داده می شود.';
			
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />	
		<title>
		</title>
	</head>
	<body>
		<div align="center" >
			<table style="border:solid 1px #666666;margin-top:20px;padding:10px;" class="round" >
				<tr>
					<th>
						<div><img src="../img/darma.png" ></div>
						سامانه فروش دارما
					</th>
					<td>
						<div align="center" id="content" >
							<?php
								echo $out; 
							?>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" >
						<input class="inp" type="button" value="بازگشت" onclick="window.location='index.php';" />
					</td>
				</tr>
			</table>
		</div>
		
	</body>
</html>
