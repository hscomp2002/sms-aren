<?php
	class factor_class
	{
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from `factor` where `id` = $id",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id=(int)$r['id'];
					$this->user_id=(int)$r['user_id'];
					$this->tarikh=$r['tarikh'];
					$this->en=(int)$r['en'];
					$this->zamanTahvil=$r['zamanTahvil'];
					$this->nahveTahvil=$r['nahveTahvil'];
					$this->makaneTahvil=$r['makaneTahvil'];
					$this->jamKol = (int)$r['jamKol'];
					$this->hazineErsal=(int)$r['hazineErsal'];
					$this->toz=$r['toz'];
					$this->isTasfie=(int)$r['isTasfie'];
					$this->typ=(int)$r['typ'];
					$this->status=(int)$r['status'];
					$this->mantaghe_id = (int)$r['mantaghe_id'];
					$this->transporter_id = (int)$r['transporter_id'];
					$this->pardakht = (int)$r['pardakht'];
				}
			}
		}
		public function noTakhfifKol()
		{
			$out = 0;
			$my = new mysql_class;
			$my->ex_sql("select `kala_id`,`tedad` from `factor_det` where `factor_id` = ".$this->id,$q);
			foreach($q as $r)
			{
				$k = new kala_class((int)$r['kala_id']);
				if(isset($k->id))
					$out += $k->ghimat_user*((int)$r['tedad']);
			}
			return($out);
		}
		public function updateFactorDet($kala_id,$tedad,$user_id,$factor_id = -1)
		{
			$conf = new conf;
			$out = FALSE;
			$factor_id = (int)$factor_id;
			$user_id = (int)$user_id;
			if($factor_id < 0)
				$factor_id = (int)$this->id;
			if($factor_id > 0 && $user_id > 0)
			{
				$kala_id = (int)$kala_id;
				$tedad = (int)$tedad;
				$my = new mysql_class;
				$my->ex_sql("select `id`,`tedad`,`ghimat` from `factor_det` where `kala_id` = $kala_id and `factor_id` = $factor_id",$q);
				$out = isset($q[0]);
				if($out)
				{
					$tedad_old = (int)$q[0]['tedad'];
					$ghimat123 = 0;
					$takh = takhfif_class::get($kala_id,$user_id,$tedad);
					$takhfif = $takh['takhfif'];
					if($tedad_old>0)
						$ghimat123 = (int)$q[0]['ghimat']*$tedad/$tedad_old;
					else
					{
						$k = new kala_class($kala_id);
						$ghimat123 = $tedad*$k->ghimat*(100-$takhfif)/100;
					}
					$my->ex_sqlx("update  `factor_det` set `ghimat` = $ghimat123 , `tedad` = $tedad, `takhfifBaste` = ".$takh['basteTakhfif']['value']." , `takhfifModir` = ".$takh['modirTakhfif']['value']." , `takhfifTedad` = ".$takh['tedadTakhfif']['value']." where `id` = ".$q[0]['id']);
					$out = array('kala_id'=>$kala_id,'tedad'=>$tedad,'ghimat'=>$ghimat123);
				}
			}
			return($out);
		}
		public function removeFactorDet($kala_id,$factor_id = -1)
                {
                        $out = FALSE;
                        $factor_id = (int)$factor_id;
                        if($factor_id < 0)
                                $factor_id = (int)$this->id;
                        if($factor_id > 0)
                        {
				$kala_id = (int)$kala_id;
				$my = new mysql_class;
                                $my->ex_sql("select `id` from `factor_det` where `kala_id` = $kala_id and `factor_id` = $factor_id",$q);
                                $out = isset($q[0]);
				if($out)
	                                $my->ex_sqlx("delete from `factor_det` where `id` = ".$q[0]['id']);
			}
			return($out);
                }
		public function loadByUser($user_id,$str=FALSE)
		{
			$out = array();
			$user_id = (int)$user_id;
			$my = new mysql_class;
			$my->ex_sql("select `id` from `factor` where `user_id` = $user_id",$q);
			foreach($q as $r)
				$out[] = (int)$r['id'];
			return(($str?((count($out)>0)?implode(',',$out):''):($out)));
		}
		public function jam()
		{
			$conf = new conf;
			$ersal = ($conf->ersal=='')?25000:(int)$conf->ersal;
			$ersalUpLimit = ($conf->ersalUpLimit=='')?750000:(int)$conf->ersalUpLimit;
			$my = new mysql_class;
			$ghimatKol = 0;
			//echo "select sum(`ghimat`) `kol` from `factor_det` where `factor_id` = ".$this->id;
                        $my->ex_sql("select sum(`ghimat`) `kol` from `factor_det` where `factor_id` = ".$this->id,$q);
			if(isset($q[0]))
				$ghimatKol = (int)$q[0]['kol'];
			$ersal = $conf->ersal==''?25000:$conf->ersal;
			$mablaghKol = $ghimatKol;
			if($mablaghKol > 300000 && $mablaghKol < 600000)
				$ersal = 15000;
			else if($mablaghKol >=600000 && $mablaghKol< $ersalUpLimit )
				$ersal = 7500;
			else if ($mablaghKol >= $ersalUpLimit )
				$ersal=0;
/*
			if($ghimatKol < $ersalUpLimit && $ghimatKol > 0)
			{
				if($ghimatKol > 300000 && $ghimatKol < 600000)
					$ersal = 15000;
				else if($ghimatKol >=600000 && $ghimatKol <750000)
					$ersal = 7500;
				else if ($ghimatKol <750000)
					$ersal = 0;
			}
*/
			//echo "update `factor` set `jamKol` = $ghimatKol , `hazineErsal` = $ersal where `id` =".$this->id;
			$my->ex_sqlx("update `factor` set `jamKol` = $ghimatKol , `hazineErsal` = $ersal where `id` =".$this->id);
			$this->jamKol = $ghimatKol;
			$this->hazineErsal = $ersal;
			$out = array('kol'=>$ghimatKol,'ersal'=>$ersal);
			return($out);
		}
		public function daramad($azt,$tat='')
		{
			$out = 0;
			$my = new mysql_class;
			$az = date("Y-m-d",strtotime($azt));
			$ta = date("Y-m-d",strtotime($tat));
			$sh = ($tat != '')?" and date(`tarikh`) <= '$ta'":'';
			//echo "select sum(`jamKol`) `kol` from `factor` where date(`tarikh`) >= '$az' $sh";
			$my->ex_sql("select sum(`jamKol`) `kol` from `factor` where date(`tarikh`) >= '$az' $sh",$q);
			if(isset($q[0]))
				$out = (int)$q[0]['kol'];
			return($out);
		}
		public function decMojoodi()
		{
			$my = new mysql_class;
			$my->ex_sql("select `kala_id`,`tedad` from `factor_det` where `factor_id` = ".$this->id,$q);
			foreach($q as $r)
			{
				$k = new kala_class((int)$r['kala_id']);
				$k->decMojoodi((int)$r['tedad']);
			}
		}
		public function refreshMablagh($factor_id=-1)
		{
			$conf = new conf;
			$out = array('before'=>-1,'after'=>-1);
			$ersal = $conf->ersal==''?25000:$conf->ersal;
			$ersalUpLimit = ($conf->ersalUpLimit=='')?750000:(int)$conf->ersalUpLimit;
			$factor_id = (int)$factor_id;
			if($factor_id <= 0 && isset($this->id))
                                $factor_id = (int)$this->id;
                        if($factor_id>0)
                        {
				$my = new mysql_class;
        	                $my->ex_sql("select sum(`ghimat`) as `sg` from `factor_det` where `factor_id` = ".$factor_id,$q);
				if(isset($q[0]))
					$out['after'] = (int)$q[0]['sg'];
				$q = null;
				$my->ex_sql("select `jamKol` from `factor` where `id` = $factor_id",$q);
				if(isset($q[0]))
					$out['before'] = (int)$q[0]['jamKol'];
				$ghimatKol = $out['after'];
				
				if($ghimatKol > 300000 && $ghimatKol < 600000)
					$ersal = 15000;
				else if($ghimatKol >=600000 && $ghimatKol<$ersalUpLimit )
					$ersal = 7500;
				else if ($ghimatKol >=$ersalUpLimit)
					$ersal =0;
				
				$this->hazineErsal = $ersal;
				$this->jamKol = $ghimatKol;
				if($out['after'] >= 0)
					$my->ex_sqlx("update `factor` set `jamKol` = ".$out['after'].",`hazineErsal`=$ersal where `id` = $factor_id");
			}
			return($out);
		}
		public function restoreFactorBack()
		{
			$out = FALSE;
			if(isset($this->id))
				$factor_id = (int)$this->id;
			if($factor_id>0)
			{
				$my = new mysql_class;
				factor_det_class::insert($factor_id);
				$mabs = $this->refreshMablagh();
				if((int)$this->isTasfie == 1 && $mabs['after'] != $mabs['before'])
				{
					$def = $mabs['after'] - $mabs['before'];
					$defStr = (($def > 0)?' - ':' + ').abs($def);
					$defPrStr = ($def > 0)?'کاهش':'افزایش';
					$my->ex_sqlx("update `profile` set `etebar` = `etebar` $defStr where `user_id` = ".$this->user_id);
					user_etebar_class::add($this->user_id,-1*$def/abs($def),-1*$def,$defPrStr.' اعتبار به میزان '.abs($def).' بابت برگشتی از فاکتور شماره '.$factor_id);
				}
				$out = TRUE;
			}
			return($out);
		}
		public function loadPishFactor($factor_id,$user_id,$page,$pdf,$extra = FALSE)
		{
			function loadSaat()
			{
				$out =<<<SEL
					<select name="saat" id="saat" class="inp" style="font-family:tahoma;" >
					<!--
							<option value="-1">در اسرع وقت</option>
							<option value="-2">قبل از ظهر</option>
							<option value="-3">قبل از نیمه شب</option>
							<option value="8:00:00">بین ۸ تا ۱۱ قبل از ظهر</option>
					-->
							<option value="11:00:00">بین ١۱ تا ١۴</option>
							<option value="14:00:00">بین ١۴ تا ١۷</option>
							<option value="17:00:00">بین ١۷ تا ۲۰</option>
					</select>
SEL;
				return($out);
			}
			function loadZaman()
			{
				$conf = new conf;
				$out = '';
				$dayCount = ($conf->dayCount!='')?(int)$conf->dayCount:6;
				$i = 1;
				$days = array();
				while($dayCount > count($days))
				{
					$st = strtotime(date("Y-m-d ")." + $i day");
					$d = date("D",$st);
					if($d != "Fri")
						$days[] = array("st"=>$st,"d"=>(($i==0)?'امروز':jdate("l d F",$st)),"gd"=>$d);
					$i++;
				}
				foreach($days as $day)
					$out .= '<option value="'.$day['st'].'">'.$day['d'].'</option>';
				return($out);
			}
			$conf = new conf;
			$prof = new profile_class($user_id);
			$viewTakhCols = array();
			$takhCols = 0;
			$my = new mysql_class;
			$my->ex_sql("select * from `factor_det` where `factor_id`=$factor_id",$q);
			if(count($q)>0)
			{
				if($pdf)
				{
					foreach($q as $r)
					{
						$k = new kala_class($r['kala_id']);
						$takh = takhfif_class::get((int)$r['kala_id'],$user_id,$r['tedad']);
						$takhfif = $takh['takhfif'];
						$takhBaste = $takh['basteTakhfif']['name'].'('.$takh['basteTakhfif']['value'].')';
						$takhModir = $takh['modirTakhfif']['name'].'('.$takh['modirTakhfif']['value'].')';
						$takhTedad = $takh['tedadTakhfif']['name'].'('.$takh['tedadTakhfif']['value'].')';
						$takhSaghf = $takh['saghfTakhfif'];
						if(!in_array('takh_baste_col',$viewTakhCols) && $takhBaste>0)
						{
							$viewTakhCols[] = "takh_baste_col";
							$takhCols++;
						}
						if(!in_array('takh_modir_col',$viewTakhCols) && $takhModir>0)
						{
							$viewTakhCols[] = "takh_modir_col";
							$takhCols++;
						}
						/*
						if(!in_array('takh_saghf_col',$viewTakhCols) && ($takhBaste>0 || $takhModir>0 || $takhfif>0))
						{
							$viewTakhCols[] = "takh_saghf_col";
							$takhCols++;
						}
						*/
						if(!in_array('takh_kol_col',$viewTakhCols) && $takhfif>0)
						{
							$viewTakhCols[] = "takh_kol_col";
							$takhCols++;
						}
					}
				}
				$out = '<table class="pishfactorTable" ><tr>';
				$out .= '<th>ردیف</th>';
				$out .= '<th>کالا</th>';
				$out .= '<th>قیمت واحد(ریال)</th>';
				$out .= '<th>تعداد</th>';
				if(($pdf && in_array('takh_baste_col',$viewTakhCols)) || !$pdf)
					$out .= '<th class="takh_baste_col takh">تخفیف بسته</th>';
				if(($pdf && in_array('takh_modir_col',$viewTakhCols)) || !$pdf)
					$out .= '<th colspan="2" class="takh_modir_col takh">تخفیف مدیر</th>';
				//if(($pdf && in_array('takh_saghf_col',$viewTakhCols)) || !$pdf)
					//$out .= '<th class="takh_saghf_col takh">سقف تخفیف</th>';
				if(($pdf && in_array('takh_kol_col',$viewTakhCols)) || !$pdf)
					$out .= '<th class="takh_kol_col takh">تخفیف</th>';
				$out .= '<th>قیمت کل(ریال)</th>';
				$out .= '</tr>';
				$tedadKol = 0;
				$ghimatKol = 0;
				$ghimatKolKham = 0;
				$i = 0;
				$ersal = $conf->ersal==''?25000:$conf->ersal;
				$ersalUpLimit = ($conf->ersalUpLimit=='')?750000:(int)$conf->ersalUpLimit;
				$takhfifCols = 0;
				foreach($q as $r)
				{
					$k = new kala_class($r['kala_id']);
					$takh = takhfif_class::get((int)$r['kala_id'],$user_id,$r['tedad']);
					$takhfif = $takh['takhfif'];
					$takhBaste = $takh['basteTakhfif']['name'].'('.$takh['basteTakhfif']['value'].')';
					$takhModir = $takh['modirTakhfif']['name'].'('.$takh['modirTakhfif']['value'].')';
					$takhTedad = $takh['tedadTakhfif']['name'].'('.$takh['tedadTakhfif']['value'].')';
					$takhSaghf = $takh['saghfTakhfif'];
					//if($sab['tedad'] > $k->tedad_baste)
					//	$takhfif = $k->takhfif;
					$ghimatKolKala = $r['tedad']*$k->ghimat*(100-$takhfif)/100;
					$ghimatKolKalaKham = $r['tedad']*(($k->ghimat_user>0)?$k->ghimat_user:$k->ghimat);
					$out .= '<tr id="kala_'.$r['kala_id'].'">';
					$out .= '<td>'.($i+1).((!$pdf)?'<img src="../img/cancel.png" style="width:10px;" class="pointer" onclick="removeKala('.$r['kala_id'].');" />':'').'</td>';
					$out .= '<td>'.$k->name.'</td>';
					$out .= '<td>'.$k->ghimat.' <span class="line" >'.$k->ghimat_user.'</span></td>';
					$out .= '<td>'.((!$pdf)?'<input class="fact_tedad" id="tedad_'.$k->id.'" name="tedad_'.$k->id.'" value="'.$r['tedad'].'" style="width:30px;" />':$r['tedad']).'</td>';
					if(($pdf && in_array('takh_baste_col',$viewTakhCols)) || !$pdf)
						$out .= '<td id="takh_baste_'.$r['kala_id'].'" class="takh_baste_col takh">'.$takhBaste.'</td>';
					if(!in_array('takh_baste_col',$viewTakhCols) && $takhBaste>0)
					{
						$viewTakhCols[] = "takh_baste_col";
						$takhCols++;
					}
					if(($pdf && in_array('takh_modir_col',$viewTakhCols)) || !$pdf)
						$out .= '<td id="takh_modir_'.$r['kala_id'].'" colspan="2" class="takh_modir_col takh">'.$takhModir.'</td>';
					if(!in_array('takh_modir_col',$viewTakhCols) && $takhModir>0)
					{
						$viewTakhCols[] = "takh_modir_col";
						$takhCols+=2;
					}
					/*
					if(($pdf && in_array('takh_saghf_col',$viewTakhCols)) || !$pdf)
						$out .= '<td class="takh_saghf_col takh">'.$takhSaghf.'</td>';
					if(!in_array('takh_saghf_col',$viewTakhCols) && ($takhBaste>0 || $takhModir>0 || $takhfif>0))
					{
						$viewTakhCols[] = "takh_saghf_col";
						$takhCols++;
					}
					*/
					if(($pdf && in_array('takh_kol_col',$viewTakhCols)) || !$pdf)
						$out .= '<td id="takh_kol_'.$r['kala_id'].'" class="takh_kol_col takh">'.$takhfif.'</td>';
					if(!in_array('takh_kol_col',$viewTakhCols) && $takhfif>0)
					{
						$viewTakhCols[] = "takh_kol_col";
						$takhCols++;
					}
					$out .= '<td id="ghimat_'.$k->id.'">'.$ghimatKolKala.'</td>';
					$out .= '</tr>';
					$tedadKol += $r['tedad'];
					$ghimatKol += $ghimatKolKala;
					$ghimatKolKham += $ghimatKolKalaKham;
					$i++;
				}
				
				$out .= '<tr>';
				$out .= '<th id="th_noTakhfif" colspan="'.($takhCols+4).'" align="left">جمع بدون تخفیف</th>';
				$out .= '<th id="noTakhfifKol" >'.$ghimatKolKham.'</th>';
				$out .= '</tr>';
				//$out .= '<tr>';
				$out .= '<tr>';
				$out .= '<th id="th_sood" style="color:green;" colspan="'.($takhCols+4).'" align="left">سود حاصل از خرید از سامانه فروش دارما</th>';
				$out .= '<th id="sood" >'.($ghimatKolKham-$ghimatKol).'</th>';
				$out .= '</tr>';
				$out .= '<tr>';
				$out .= '<th id="th_ghimat_col" colspan="'.($takhCols+4).'" align="left">جمع اقلام</th>';
				$out .= '<th id="ghimat_kol" >'.$ghimatKol.'<input type="hidden" id="ersal" name="ersal" value="'.$ersal.'" /></th>';
				$out .= '</tr>';
				
				if($ghimatKol > 300000 && $ghimatKol < 600000)
					$ersal = 15000;
				else if($ghimatKol >=600000 && $ghimatKol<$ersalUpLimit)
					$ersal = 7500;
				else if ($ghimatKol>$ersalUpLimit)
					$ersal=0;
				$out .= '<tr>';
				$out .= '<th id="th_ersal" colspan="'.($takhCols+4).'" align="left">هزینه ارسال</th>';
				$out .= '<th id="hazineErsal" >'.$ersal.'</th>';
				$out .= '</tr>';

				$out .= '<tr>';
				$out .= '<th id="maliat_afzoode" colspan="'.($takhCols+4).'" align="left">ماليات برارزش افزوده</th>';
				$out .= '<th id="maliat_afzoode_val" >0</th>';
				$out .= '</tr>';
				
				$out .= '<tr>';
				$out .= '<th id="th_all" colspan="'.($takhCols+4).'" align="left">جمع کل</th>';
				$out .= '<th id="all" >'.($ghimatKol+$ersal).'</th>';
				$out .= '</tr></table>';
				if($extra)
				{
					$out .='
					<div is="div_extra" >
					<table>
						<tr>
							<td colspan="10">
								مکان دریافت کالا :
								<div id="khodamDiv">
									خودم : <input type="radio" name="girande" id="selfAddr" checked="checked" /><span id="makan_def">'.$prof->addr.'</span><br/>
									دیگری : <input type="radio" name="girande" id="otherAddr" />
								</div>
								<div id="digariDiv" style="display:none;">
									نام گیرنده : <input id="girande_name" name="girande_name" />
									تلفن همراه : <input id="girande_mob" name = "girande_mob" /><br/>
									آدرس : <textarea id="makan" name="makan" rows="3" style="width:90%;"  >'.$prof->addr.'</textarea><br/>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="10">
								زمان دریافت کالا : <select id="zaman" name="zaman" class="inp" >'.loadZaman().'</select>
								'.loadSaat().'
							</td>
						</tr>
						<tr>
							<td colspan="10">
								اگر در محل کسی نبود چه کنیم ؟
								<textarea id="nahve" name="nahve" cols="40" ></textarea>
							</td>
						</tr></table></div>';
				}
				$jViewTakhCols = toJSON($viewTakhCols);
				$scrt = <<<SCR
					<script>
						var viewTakhCols = $jViewTakhCols;
						function removeKala(kalaId)
						{
							if(confirm('آیا حذف انجام شود؟'))
							{
								$("#sss_div").html("درحال بروز رساني قيمت ها<img src='../img/status_fb.gif' >");
								$.getJSON("$page?factor_id=$factor_id&user_id=$user_id&kala_id="+kalaId+"&",function(result){
									$("#sss_div").html("");
									if(typeof result.status != 'undefined')
									{
										if(result.status === true)
										{
											refreshData();
										}
										else
											alert('خطا در حذف');
									}
									else
										alert('خطا در حذف');
		
								});
							}
						}
						$(document).ready(function(){
								$(".fact_tedad").keyup(function(e){ 
								var code = e.which;
								if(code==13)
								{
									e.preventDefault();
									tedadChanged(e.currentTarget);
								}
							});
							$(".fact_tedad").spinner({
								change: function( event, ui ) 
								{
									tedadChanged(event.currentTarget);
								}
							});
							$(".fact_tedad").spinner({
								min:1
							});
							$("#selfAddr").click(function(){
								$("#girande_name").val('');
								$("#girande_mob").val('');
								$("#makan").val($("#makan_def").text());
								$("#digariDiv").hide();
							});
							$("#otherAddr").click(function(){
								$("#girande_name").val('');
								$("#girande_mob").val('');
								$("#makan").val('');
								$("#digariDiv").show();
									});
							$(".takh").hide();
							for(i in viewTakhCols)
								$('.'+viewTakhCols[i]).show();
						});
					</script>
SCR;
			$out.="\n".$scrt;
		}
		else
			$out='empty';
		return($out);
		}
	}
?>
