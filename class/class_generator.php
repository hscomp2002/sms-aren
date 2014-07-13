<?php
	class class_generator
	{
		public $output = "";
		public function __construct($tableName)
		{
			$mysql = new mysql_class;
			$out = "<?php\n\tclass $tableName"."_class\n\t{\n";
			$mysql->ex_sql("select * from `$tableName` where 0=1",$q);
			if(isset($q[0]))
				foreach($q[0] as $field=>$value)
					$out .= "\t\tpublic $".$field."='-1';\n";
			$out .= "\t\tpublic function __construct(\$id=-1)\n\t\t{\n";
			$out .= "\t\t\tif((int)\$id > 0)\n";
			$out .= "\t\t\t{\n";
			$out .= "\t\t\t\t\$mysql = new mysql_class;\n";
			$out .= "\t\t\t\t\$mysql->ex_sql(\"select * from `$tableName` where `id` = \$id\",\$q);\n";
			$out .= "\t\t\t\tif(isset(\$q[0]))\n";
			$out .= "\t\t\t\t{\n";
			$out .= "\t\t\t\t\t\$r = \$q[0];\n";
			$q = null;
			$mysql->ex_sql("select * from `$tableName` ",$q);
			if(isset($q[0]))
				foreach($q[0] as $field=>$value)
					$out .= "\t\t\t\t\t\$this->".$field."=\$r['".$field."'];\n";
			else
				echo "$tableName is empty !";
			$out .= "\t\t\t\t}\n\t\t\t}\n\t\t}\n";
/*
			$out .= "\t\tpublic function loadField(\$id,\$field)\n\t\t{\n";
			$out .= "\t\t\t\$out = FALSE;\n";
			$out .= "\t\t\tif((int)\$id > 0 && is_array(\$field) && count(\$field) > 0)\n";
			$out .= "\t\t\t{\n";
			$out .= "\t\t\t\t\$field_txt = '';\n";
			$out .= "\t\t\t\tfor(\$i = 0;\$i < count(\$field);\$i++)\n";
			$out .= "\t\t\t\t\t\$field_txt .= '`'.\$field[\$i].'`'.((\$i < count(\$field)-1)?',':'');\n";
                        $out .= "\t\t\t\tmysql_class::ex_sql(\"select \$field_txt from `$tableName` where `id` = \$id\",\$q);\n";
                        $out .= "\t\t\t\tif(\$r = mysql_fetch_array(\$q))\n";
                        $out .= "\t\t\t\t{\n";
                        $out .= "\t\t\t\t\t\$this->id=\$id;\n";
                        $out .= "\t\t\t\t\tfor(\$i = 0;\$i < count(\$field);\$i++)\n";
			$out .= "\t\t\t\t\t{\n";
			$out .= "\t\t\t\t\t\t\$this->{\$field[\$i]} = \$r[\$field[\$i]];\n";
			$out .= "\t\t\t\t\t\t\$out[\$field[\$i]] = \$r[\$field[\$i]];\n";
			$out .= "\t\t\t\t\t}\n";
                        $out .= "\t\t\t\t}\n";
                        $out .= "\t\t\t}\n";
                        $out .= "\t\t\treturn(\$out);\n";
			$out .= "\t\t}\n";
*/
			$out .= "\t}\n?>";
			$this->output = $out;
		}
	}
?>
