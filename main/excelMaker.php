<?php
	date_default_timezone_set('Asia/Tehran');
	include('../simplejson.php');
	require_once '../class/excel/PHPExcel.php';
	$inp = $_REQUEST['inp'];
	if(file_exists("../xls/$inp"))
	{
		$fi = file("../xls/$inp");
		$tmp = explode('.',$inp);
		$xlsFile = $tmp[0];
		//echo $fi[0];
		$str = str_replace("\\\"",'"',$fi[0]);
		$arr = str_replace("\\/","/",$str);
		$arr =trim($arr);
		$sheet1 =(array)json_decode($arr);
		$sheet=array();
		foreach($sheet1 as $as)
			$sheet[] = (array)$as;

		$columns =0;
		if(isset($sheet[0]))
			$columns = count($sheet[0]);
		
		$callStartTime = microtime(true);

		$objPHPExcel= new PHPExcel();

		$styleArray = array(
			'font'  => array(
				'bold'  => false,
				'color' => array('rgb' => '000000'),
				'size'  => '10',
				'name'  => 'tahoma'
			)
			);


		$NewWorkSheet = new PHPExcel_Worksheet($objPHPExcel, "sheet1");
		$NewWorkSheet->fromArray($sheet,'empty');





		$objPHPExcel->removeSheetByIndex();
		$objPHPExcel->addSheet($NewWorkSheet,0);

		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->getDefaultStyle()->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->setRightToLeft(true);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		//$objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		for($i=0;$i<$columns;$i++)
			$objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($i)->setAutoSize(true);


		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save("../xls/$xlsFile.xlsx");
		unlink('../xls/'.$inp);
	}
	else
		die("File Does Not Exist");
?>
<script>window.location.href = '../xls/<?php echo $xlsFile;?>.xlsx';</script>
