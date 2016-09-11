<?php
function jietu($url,$savepath){//网页截图
	if(empty($savepath)){
		exit('截图保存目录为空或者不是目录！');
	}else{
		//如果截图目录不存在则新建
		if(!file_exists($savepath)){
			mkdir($savepath);
		}
	}
	if(!url_exists($url)){
		exit('URL地址不存在！');
	}
	$newImgName=time().rand(0,99999).'.jpg';
	$filename=rtrim($savepath,'/').'/'.$newImgName;//拼装生成截图的文件名
	$rutycapt=dirname($_SERVER['SCRIPT_FILENAME']).'/js/IECapt.exe';//$_SERVER['SCRIPT_FILENAME'] 该文件包包含在index.php 那就是index.php的目录 本机IECapt的路径
	$cmd="$rutycapt --url=$url --out=$filename";//拼装截图命令
	system($cmd);//执行命令
	//echo $cmd;
	return $newImgName;
}
function url_exists($url){ //判断URL地址是否存在
	$head=@get_headers($url);
	if(is_array($head)){
		return true;
	}else{
		return false;
	}
}

?>