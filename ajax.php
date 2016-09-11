<?php
error_reporting(0);
require('./config.inc.php');	//包含数据库操作类
require('functions/common.php');//函数库

$op=$_GET['op'];

if($op=='searchFriend'){//搜索好友
	$uid=intval($_POST['uid']);
	$c='';
	if(!empty($_POST['uname'])){
		$c=" and u.user_name like '%$_POST[uname]%'";
	}
	$sql="select * from et_friend f left join et_users u on f.fid_jieshou=u.user_id where f.fid_fasong='$uid' $c";
    $res=$db->getall($sql);//onclick="sendMsg(this.id,\''.$v[user_name].'\')"
	if(count($res)>5){
		$str='<div id="close-frienddiv" class="Bw Bw2" role="button" onclick="closeDiv('."'frienddiv'".')"></div><ul id="ful" style="overflow-y:scroll;">';//超过5个 显示滚动条
	}else{
		$str='<div id="close-frienddiv" class="Bw Bw2" role="button" onclick="closeDiv('."'frienddiv'".')"></div><ul id="ful" >';
	}
	foreach($res as $k=>$v){
		//if($k<6)
		//弹窗版
		//$str.='<li id="'.$v['user_id'].'"  onclick="messageDialog(this.id,\''.$v[user_name].'\')" onmouseover="javascript:this.className=\'ful_li\'" onmouseout="javascript:this.className=\'\'"><a href="javascript:void(0)"><img src="avatar_small/'.$v['user_head'].'" width="48" height="48" align="top" /><span class="headname">'.$v['user_name'].'</span></a></li>';
		if($k==0){ //第1条选中
			$str.='<li id="'.$v['user_id'].'" style="background:#fff;" onclick="selectFriend(this.id,\''.$v[user_name].'\')" onmouseover="javascript:this.className=\'ful_li\'" onmouseout="javascript:this.className=\'\'"><a href="javascript:void(0)"><img src="avatar_small/'.$v['user_head'].'" width="48" height="48" align="top" /><span class="headname">'.$v['user_name'].'</span></a></li>';
		}else{
			$str.='<li id="'.$v['user_id'].'"  onclick="selectFriend(this.id,\''.$v[user_name].'\')" onmouseover="javascript:this.className=\'ful_li\'" onmouseout="javascript:this.className=\'\'"><a href="javascript:void(0)"><img src="avatar_small/'.$v['user_head'].'" width="48" height="48" align="top" /><span class="headname">'.$v['user_name'].'</span></a></li>';
		}
		
	}
	$str.='</ul>';
	echo json_encode($str);
}else if($op=='doShare'){//发布
	$uid=3;
	$content=$_POST['addtext'];
	
	$sql="insert into et_content(user_id,content_body,posttime,type,img_url,video_url,attach_url,snap_url,is_forbid,is_lock)
	value('$uid','$content',".time().",'网页','".$_POST['img_url']."','".$_POST['video_url']."','".$_POST['attach_url']."','".$_POST['snap_url']."','".$_POST['is_forbid']."','".$_POST['is_lock']."')";
	$f=$db->query($sql);
	
	if($f){
		echo 1;
	}else{
		echo 0;
	}
}else if($op=='snapshot'){//网页快照
	$snapUrl=$_POST['snapUrl'];
	
	$newImgName=jietu($snapUrl,'./uploads/snap');//url地址不能省略www，省略之后截图为空白
	
	require_once './classes/thumb/ThumbLib.inc.php';
	$thumb = PhpThumbFactory::create('./uploads/snap/'.$newImgName);//缩略
	$thumb->resize(180, 320);
	$thumb->save('./uploads/snap/'.$newImgName, 'jpg');
	$result['data'] = array('newImgName'=>$newImgName);
	if(!$result['data'])
		$result['status'] = 0;
	else
		$result['status'] = 1;
	echo json_encode($result);
	//$thumb->show();
}else if($op=='addVideo'){//视频截图
	require_once "classes/VideoUrlParser.class.php";
	$result['data'] = VideoUrlParser::parse($_POST['url']);
	if(!$result['data'])
		$result['status'] = 0;
	else
		$result['status'] = 1;

	echo json_encode($result);
	
}else if($op=='upload'){ //上传
	$file_path = 'uploads/';
	$f_name=$_FILES['upload']['name'];//upload 即上传域名称
	$file_up = $file_path.strtolower(basename($f_name));
	$tmp_type=strtolower(substr(strrchr($f_name,"."),1));//获取文件扩展名
	if (file_exists($file_up)){ 
		echo "<script>alert('对不起,文件 ".$f_name." 已经存在,上传失败!')</script>"; 
		exit;
	} 
	$string = 'abcdefghijklmnopgrstuvwxyz0123456789';
	$rand = '';
	for ($x=0;$x<12;$x++){
		$rand .= substr($string,mt_rand(0,strlen($string)-1),1);//随机
	}
	$t=date("ymdHis").substr($gettime[0],2,6).$rand;
	$uploadfile=$file_path.$t.".".$tmp_type; 
	//echo $uploadfile;exit;
	if(move_uploaded_file($_FILES['upload']['tmp_name'],$uploadfile)){
		echo $uploadfile;	
	}else{
		echo 'fail';	
	}
}
exit;