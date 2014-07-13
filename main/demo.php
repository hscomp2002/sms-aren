<?php
        include_once("../kernel.php");
        $SESSION = new session_class;
        register_shutdown_function('session_write_close');
        session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);
        $gname = "gname_@table@";
	$input =array($gname=>array('table'=>'@table@','div'=>'main_div_@table@'));
        $xgrid = new xgrid($input);
	@field@
	$xgrid->column[$gname][@index@]['name'] = '@name@';
	@field@
        $xgrid->canAdd[$gname] = TRUE;
        $xgrid->canDelete[$gname] = TRUE;
        $xgrid->canEdit[$gname] = TRUE;
        $out =$xgrid->getOut($_REQUEST);
        if($xgrid->done)
                die($out);
?>
<script type="text/javascript" >
        $(document).ready(function(){
                var args=<?php echo $xgrid->arg; ?>;
                intialGrid(args);
        });
</script>
<div id="main_div_@table@"></div>
