<?php 
	class lang_fa_class
	{
		const	title = "";
		const	logo_text = '';
		const	all_manegment = "امکانات";
		const	welcom = "خوش آمدید";
		const	grp_user ="مدیریت گروه کاربری";
		const	user = "مدیریت کاربران";
		const	time = "مدیریت زمان";
		const	bandwidth = "مدیریت پهنای باند";
		const	filter = "مدیریت فیلتر";
		const	download = "مدیریت عدم دانلود";
		const	logout = "خروج";
		const	successdel = "حذف با موفقیت انجام شد";
		const	unsuccessdel = "این مشخصه حداقل برای یک گروه اختصاص یافته است حذف ممکن نیست";
		const	successadd = "با موفقیت افزوده شد";
		const	success_done = "با موفقیت انجام شد";
		const 	unsuccess_done = "انجام عملیات نا موفق بود";
		const	unsuccessadd = "این مشخصه حداقل برای یک گروه اختصاص یافته است افزودن ممکن نیست";
		const	filter_name = "نام فیلتر";
		const	accessdeny = "نشست شما منقضی شده است دوباره وارد شوید";
		const	ask_del = "آیا حذف انجام شود؟";
		const	delete = "حذف";
		const	delete_conf = "آیا حذف انجام شود؟";
		const	edit = "ویرایش";
		const	new_item = "جدید";
		const	auth_users = "کاربران مجاز";
		const	save = "ذخیره ";
		const 	savechanges = "ذخیره‌تغییرات";
		const 	savenew = "ذخیره‌جدید";
		const	error_ip = "نشانی آی‌پی درست وارد نشده است";
		const	error_mac = "نشانی مک درست وارد نشده است";
		const	active = "وضعیت فعال";
		const	inactive = "وضعیت غیر‌فعال";
		const	grp = "گروه‌کاربری";
		const	lname = "نام‌خانوادگی";
		const	search = "جستجو";
		const	apply = "اعمال تغییرات";
		const	change_state_confirm ="آیا تغییر وضعیت پراکسی سرور انجام شود؟";
		public function filterAlert($oks,$noks)
		{
			if($oks!="" && $noks!="" )
			{
				$oks = substr($oks,0,-1);
				$noks = substr($noks,0,-1);
				$out = "فیلتر(های)۰ \\n $oks \\n با موفقیت حذف شدند و فیلتر(های)۰ \\n $noks \\n حذف نشدند";
			}
			else if($oks!="")
			{
				$oks = substr($oks,0,-1);
				$out = "فیلتر(های)۰ \\n $oks \\n با موفقیت حذف شدند";
			}
			else if($noks!="")
                        {
                                $noks = substr($noks,0,-1);
                                $out = "فیلتر(های)۰ \\n $noks \\n حذف نشدند";
                        }
			else
			{
				$out = "خطا در عمالکرد";
			}
			return $out;
		}
                
	}
?>
