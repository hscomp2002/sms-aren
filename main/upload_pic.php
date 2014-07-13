<?php
	include_once("../kernel.php");
	$id = (isset($_REQUEST['id']))?(int)$_REQUEST['id']:-1;
	$tb = (isset($_REQUEST['table']))?$_REQUEST['table']:'kala';
	$field = (isset($_REQUEST['field']))?$_REQUEST['field']:'';
	if(isset($_FILES['imageFile']))
	{
		$tmp_target_path = "../img".(($tb=='kala')?'/kala':'');
		$ext = explode('.',basename( $_FILES['imageFile']['name']));
                $ext = $ext[count($ext)-1];
                if(strtolower($ext)=='jpg' || strtolower($ext)=='png')
                {
	                $target_path =$tmp_target_path."/".basename( $_FILES['imageFile']['name']).'.'.$ext;
                        if(move_uploaded_file($_FILES['imageFile']['tmp_name'], $target_path))
                        {
				$mysql = new mysql_class;
				$pic = 'pic';
				switch($field)
				{
					case 'thumb':
						$pic = 'thumb';
						break;
				}
				$mysql->ex_sqlx("update `$tb` SET `$pic` = '$target_path' WHERE `id` ='$id';");
				$out = 'ok';
                        }
                }
		$out = "<script>window.parent.RPage();</script>";
		die($out);
	}
?>
<html>
	<head>
		<script>
			function send1()
			{
				document.getElementById("frm25").submit();
			}
		</script>
	</head>
	<body>
		<img src="../img/status_fb.gif" title="Loading . . ." style="display:none;" id="kh"/>
		<div id="frm">
			<form method="post" enctype="multipart/form-data" name='frm25' id='frm25'>
				<input type="hidden" id="table" name="table" value="<?php echo $tb; ?>" />
				<input type="file" id="imageFile" name="imageFile"/>
				<button onclick='send1();'>
					بروزرسانی
				</button>
			</form>
		</div>
	</body>
</html>
