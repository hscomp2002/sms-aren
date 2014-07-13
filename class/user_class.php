<?php
	class user_class
	{
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from `user` where `id` = $id",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->fname=$r['fname'];
					$this->lname=$r['lname'];
					$this->id=(int)$r['id'];
					$this->user=$r['user'];
					$this->pass=$r['pass'];
					$this->group_id=(int)$r['group_id'];
					$this->en = (int)$r['en'];
					$this->isEnable = (int)$r['isEnable'];
					$this->eshterak = (int)$r['eshterak'];
				}
			}
		}
		public function resetPassword($email)
		{
			$out = FALSE;
			$my = new mysql_class;
			$my->ex_sql("select `user_id` as `id` from `profile` where `email` = '$email'",$q);
			if(isset($q[0]))
			{
				$newPass = rand(1,9999).'A';
				$npass = md5($newPass);
				$my->ex_sqlx("update `user` set `pass` = '$npass' where `id` = ".$q[0]['id']);
				$message = '<html><body dir="rtl">رمز عبور جدید شما عبار است از <br/>'.$newPass.'</body></html>';
				$e = new email_class($email,'دارما ، رمز عبور جدید',$message);
				$out = TRUE;
			}
			return($out);
		}
	}
?>