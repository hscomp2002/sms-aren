<?php
date_default_timezone_set('Asia/Tehran');
include_once("../class/conf.php");
include_once("../class/mysql_class.php");
/** Include PHPExcel_IOFactory */
require_once '../class/excel/PHPExcel/IOFactory.php';
if(isset($_REQUEST['uploaded']))
{
    $mod = trim($_REQUEST['mod']);
    $file_path = '../xls/files/'.trim($_REQUEST['uploaded']);
    if (!file_exists($file_path)) {
            exit("no file");
    }

    $inputFileType = 'Excel5';
    //	$inputFileType = 'Excel2007';
    //	$inputFileType = 'Excel2003XML';
    //	.$inputFileType = 'OOCalc';
    //	$inputFileType = 'Gnumeric';
    //$inputFileName = '../xls/files/new_inbox.xls';
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $worksheetData = $objReader->load($file_path);
    $all = $worksheetData->getActiveSheet()->toArray(null,true,true,true);
    $my = new mysql_class;
    foreach($all as $i=>$val)
    {
        switch ($mod) {
            case 'pak':
                $my->ex_sqlx("delete from group_numbers where numbers_id in (select id from numbers where mobiles='".$val['B']."')");
                $my->ex_sqlx("delete from numbers where mobiles='".$val['B']."'");
                break;
            case 'kharej':
                $my->ex_sqlx("update numbers set is_siah=0 where mobiles='".$val['B']."'");
                break;
            case 'vared':
                $my->ex_sqlx("update numbers set is_siah=1 where mobiles='".$val['B']."'");
                break;
        }
    }
    die('done');
}

?>
<script src="../js/vendor/jquery.ui.widget.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="../js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="../js/jquery.fileupload.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="../js/bootstrap.min.js"></script>
<script>
    $(function () {
        'use strict';
        // Change this to the location of your server-side upload handler:
        var url = '../xls/';
        $('#fileupload').fileupload({
            url: url,
            dataType: 'json',
            done: function (e, data) {
                $("#khoon").html('<img src="../img/status_fb.gif">');
                $.each(data.result.files, function (index, file) {
                    $('<p/>').text(file.name).appendTo('#files');
                    var mod='kharej';
                    $.each($('input[type=radio]'),function(id,feild){
                        if($(feild).prop('checked'))
                            mod = $(feild).prop('id');
                    });
                    var obj={'uploaded':file.name,'mod':mod};
                    $.get("kala.php",obj,function(result){
                        $("#khoon").html('عملیات با موفقیت انجام گرفت');
                    });
                });
            },
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');
    });
</script>
<div class="container">
    <!-- The fileinput-button span is used to style the file input field as button -->
    <div style="padding:10px;" >
        <input type="radio" name="action_ra" id="kharej" checked="checked" >
        خارج کردن از لیست سیاه
        <input type="radio" name="action_ra" id="vared" >
       افزودن به لیست سیاه
        <input type="radio" name="action_ra" id="pak" >
        پاک کردن از لیست شماره ها
    </div>
    <span class="btn btn-success fileinput-button">
        <i class="glyphicon glyphicon-plus"></i>
        <span>انتخاب فایل ...</span>
        <!-- The file input field used as target for the file upload widget -->
        <input id="fileupload" type="file" name="files[]" multiple>
    </span>
    <br>
    <br>
    <!-- The global progress bar -->
    <div id="progress" class="progress">
        <div class="progress-bar progress-bar-success"></div>
    </div>
    <!-- The container for the uploaded files -->
    <div id="files" class="files"></div>
    <div id="khoon" ></div>
</div>
