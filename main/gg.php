<?php
	include_once("../kernel.php");
	$out = '';
	if(isset($_REQUEST['table']))
	{
		$tableArray = explode(',',$_REQUEST['table']);
		foreach($tableArray as $table)
		{
			if(!file_exists('../class/'.$table.'_class.php'))
			{
		                $cg = new class_generator($table);
        		        $f = fopen('../class/'.$table.'_class.php','w+');
                		fwrite($f,$cg->output);
	                	fclose($f);
			}
			$grops = (isset($_REQUEST['grops']))?$_REQUEST['grops']:'';
			$grop_array = ($grops != '') ? explode(',',$grops) : array();
			if(file_exists('demo.php') && !file_exists($table.'.php'))
			{
				$grid_sample = file('demo.php');
				$fields = mysql_class::loadField($table);
				$field_start = FALSE;
				$phpFile = '';
				foreach($grid_sample as $line)
				{
					if(strpos($line,"@field@") === FALSE && !$field_start)
						$phpFile .= str_replace("@table@",$table,$line);
					else if(strpos($line,"@field@") !== FALSE && !$field_start)
						$field_start = TRUE;
					else if(strpos($line,"@field@") === FALSE && $field_start)
        	        	        {
						$field_line = $line;
						foreach($fields as $index=>$field)
							$phpFile .= str_replace("@index@",$index,str_replace("@name@",$field,$field_line));
					}
					else if(strpos($line,"@field@") !== FALSE && $field_start)
						$field_start = FALSE;
				}
				$f = fopen($table.'.php',"w+");
				fwrite($f,$phpFile);
				fclose($f);
				foreach($grop_array as $grop_id)
					mysql_class::accessToGroup($table.'.php',$grop_id);
				$out .= $table.'.php is created<br/>';
			}
			else
				$out .= "امکان ایجاد $table.'.php نمی باشد.<br/>";
		}
	}
?>
<html>
	<head>
		<title>
			Grid Generator
		</title>
	</head>
	<body>
		<?php
			echo $out;
		?>
		<form method="get">
			<br/>
			Table Name : <input type="text" id="table" name="table" value="" />
			Group IDs (camma seprated) : <input type="text" id="grops" name="grops" value="1" />
			<br/>
			<button>
				Submit
			</button>
		</form>
	</body>
</html>
