<?php
require_once ("admin_conn.php");
chkLogin();

$action = be("all","action");

switch($action)
{
	case "add":
	case "edit" : headAdmin ("广告管理"); info();break;
	case "save" : save();break;
	case "del" : del();break;
	case "tj" : headAdmin ("统计代码管理"); tj();break;
	case "tjsave" : tjsave();break;
	default : headAdmin ("广告管理"); main();break;
}
dispseObj();

function save()
{
	$file = be("post","file");
	$filecontent = stripslashes(be("post","filecontent"));
	fwrite(fopen("../template/" . app_templatedir . "/ads/" . $file.".js","wb"),$filecontent);
	echo "保存完毕";
}

function del()
{
	$fpath = "../template/" . app_templatedir . "/ads/";
	$file = be("get","file");
	if(file_exists($fpath.$file)){
		unlink($fpath.$file);
	}
    redirect( getReferer() );
}

function tjsave()
{
	$tjstr = stripslashes(be("post","tjstr"));
	fwrite(fopen("../js/tj.js","wb"),$tjstr);
	echo "保存完毕";
}

function main()
{
	$fpath = "../template/" . app_templatedir . "/ads/";
	if (!is_dir( $fpath)){
		mkdir($fpath);
	}
	$fso = opendir( $fpath );
?>
<script language="javascript">
$(document).ready(function(){
	$("#btnAdd").click(function(){
		location.href= "?action=add";
	});
});
</script>
<script type="text/javascript" src="../js/function.js"></script>
<table width="96%" border="0" align="center" cellpadding="3" cellspacing="1">
	<tr>
	<td>
		<h3>当前广告文件存放路径：<?php echo $fpath?></h3>
	</td>
	</tr>
</table>
<table class="tb">
	<tr>
	<td width="15%">广告名称</td>
	<td width="10%">文件大小</td>
	<td width="15%">修改时间</td>
	<td>广告调用代码</td>
	<td width="15%">操作</td>
	</tr>
	<?php
	if (count($fso)==0){
	?>
    <tr><td align="center" colspan="5">没有任何记录!</td></tr>
	<?php
	}
	else{
		while ( $sFile = readdir( $fso ) ){
			if ( is_file( $fpath . $sFile ) ){
				$fsize= round( filesize( $fpath . $sFile ) / 1024 );
				$ftime = isToDay ( date('Y-m-d H:i:s',filemtime ($fpath . $sFile)) );
	?>
    <tr>
      <td><?php echo $sFile?></td>
      <td><?php echo $fsize?>KB</td>
      <td><?php echo $ftime?></td>
      <td>
	  <input id="text<?php echo $sFile?>" name="text<?php echo $sFile?>" type="text" value='<script src="{joyplus:templatepath}ads/<?php echo $sFile?>"></script>' size="70"> 
	  <input class="input" type="button" value="复制" name=Submit onClick='copyData(document.getElementById("text<?php echo $sFile?>").value);'>
	  </td>
      <td><A href="admin_ads.php?action=edit&file=<?php echo $sFile?>">修改</a> | <A href="admin_ads.php?action=del&file=<?php echo $sFile?>" onClick="return confirm('确定要删除吗?');">删除</a></td>
    </tr>
	<?php
  		  }
		}
	}
	?>
	<tr>
	<td colspan="5"><input type="button" id="btnAdd" value="添加"  class="input"/></td>
	</tr>
</table>
<?php
}

function info()
{
	global $action;
	if ($action=="edit"){
		$file = be("get","file");
		$fpath = "../template/" . app_templatedir . "/ads/";
		if(!file_exists($fpath . $file)){ errMsg ("找不到该广告文件","admin_ads.php") ;}
		$fc = file_get_contents($fpath .$file);
		$file = replaceStr($file,".js","");
	}
?>
<script language="javascript">
$(document).ready(function(){
	$("#form1").validate({
		rules:{
			file:{
				required:true,
				maxlength:64
			}
		}
	});
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info',function(){
	        });
	    }
	});
	$("#btnCancel").click(function(){
		location.href= "?action=main";
	});
});
</script>
<form action="?action=save" method="post" name="form1" id="form1">
<table class="tb">
	<input type="hidden" id="flag" name="flag" value="<?php echo $action?>">
	<tr>
	<td width="20%" >广告文件名：</td>
	<td><input id="file" size=50 value="<?php echo $file?>" name="file" <?php if($action=="edit"){?>readonly="readonly"<?php }?>>.js
	&nbsp;&nbsp;&nbsp;广告内容需要html转js操作，否则无法正常显示。
	</td>
	</tr>
	<tr>
	<td>广告内容：</td>
	<td><textarea id="filecontent" name="filecontent" style="width:90%;" rows="15"><?php echo $fc?></textarea></td>
	</tr>
	<tr>
	<td colspan="2" align="center"><input class="input" type="submit" value="保存" id="btnSave"> <input class="input" type="button" value="返回" id="btnCancel"></td>
	</tr>
</table>
</form>
<?php
}

function tj()
{
	$fc = file_get_contents("../js/tj.js");
?>
<script language="javascript">
$(document).ready(function(){
	$('#form1').form({
		onSubmit:function(){
			if(!$("#form1").valid()) {return false;}
		},
	    success:function(data){
	        $.messager.alert('系统提示', data, 'info',function(){
	        });
	    }
	});
});
</script>

<?php
}
?>
</body>
</html>