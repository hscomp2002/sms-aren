<?php
	class security_class
	{
		public $can_view = FALSE;
		public $allDetails = array();
		public function __construct($grp_id=-1)
		{
			//load
		}
		public function auth($user_id)
		{
			$user = new user_class((int)$user_id);
			
			if(isset($user->group_id))
			{
				$grp_id = $user->group_id;
				$pages1 = access_class::loadByGroup($grp_id);
				$can_view = FALSE;
				$allDetails = array();
				if(($acc_id1 = security_class::isInArray($pages1,security_class::thisPage()))!==FALSE)
					$can_view = TRUE;
				$pages2 = access_class::loadByGroup($user_id,0);
				$can_view_user = (($acc_id2 = security_class::isInArray($pages2,security_class::thisPage()))!==FALSE);
				$can_view = $can_view || $can_view_user;
				if($can_view)
				{
					$allDetails = access_det_class::loadByAcc($acc_id1);
					if(isset($acc_id2))
					{
						$allDetails2 = access_det_class::loadByAcc($acc_id2);
						$allDetails = array_merge($allDetails,$allDetails2);
					}
				}
				$se = new security_class;
				$se->can_view = $can_view;
				$se->allDetails = $allDetails;
			}
			else
				$se = null;
			return($se);
		}
		public function isInArray($arr,$val)
		{
			$out = FALSE;
			foreach($arr as $key => $value)
				if($value == $val)
					$out = $key;
			return($out);
		}
		public function detailAuth($frase)
		{
			$out = FALSE;
			if(security_class::isInArray($this->allDetails,$frase)!==FALSE)
				$out = TRUE;
			return($out);
		}
		public function thisPage()
		{
			$out = '';
			$tmp = $_SERVER["REQUEST_URI"];
			$tmp1 = explode('?',$tmp);
			$tmp2 = explode('/',$tmp1[0]);
			$tmp3 = $tmp2[count($tmp2)-1];
			//$tmp = $tmp[0];
			$out = trim($tmp3);
			return($out);
		}
	}
?>
