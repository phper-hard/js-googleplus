<?php
	session_start();
	$_SESSION["file_info"] = array();
	$str='';
?>
<!DOCTYPE html>
<html lang="zh" dir="ltr">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<title>分享你的新鲜事</title>
<!--[if IE]>
<script>
(function(){if(!/*@cc_on!@*/0)return;var e = "abbr,article,aside,audio,canvas,datalist,details,dialog,eventsource,figure,footer,header,hgroup,mark,menu,meter,nav,output,progress,section,time,video".split(','),i=e.length;while(i--){document.createElement(e[i])}})()
</script>
<![endif]-->
<link href="css/layout.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-position.js"></script>
<script type="text/javascript" src="js/index.js"></script>
<script type="text/javascript" src="js/autoHeight.js"></script>


<!--抓取优酷视频图片-->
<script>
	$(function(){
		
		var $result = $('#video_result');//视频图片显示区域
		
		$('#submit').click(function(){
			
			var url = $('#url').val();
			if(!url){
				$result.html("<font color=red>请输入视频地址</font>");
				return false;
			}
			
			$result.html("<font color=green>视频加载中...</font>");
			
			$('.open_vedio').live('click', function(){
				$(this).hide();
				$(this).next('.vedio').show();
			});
			
			$('.close_vedio').live('click', function(){
				$(this).parent().parent().hide();
				$(this).parent().parent().prev('.open_vedio').show();
			});
			
			/*
			//$.ajax 不好测试 可以用post来测试 弹出data来看 无论请求失败与否,同时server.php不能有任何输出,只能有头部和结果
			//所以必须设置error_reporting(0);//服务端程序server.php被ajax调用时候必须设置为0 否则弹出的警告warning 也会导致ajax接收数据部成功
			$.post("ajax.php?op=addVideo", { Action: "post", url: url },
			function (data, textStatus){
				//alert(textStatus); //成功是success 失败fail
				alert(data);//这样就会服务端输出的任何数据格式

			});//如果这里加上,json那么只会弹出object 即结果的类型 不会把内容弹出
			*/
			$.ajax({
				url: 'ajax.php?op=addVideo',
				data: {url: url},
				dataType: 'json',
				type: 'POST',
				success: function(json){
					if(!json.status){
						$result.html("<font color=red>暂不支持该视频地址</font>");
						return false;
					}else{
						//float:left;会造成div的高度无法自动撑高
						var html = "<div class='open_vedio_p' style=' padding:3px; '>"
							+ "		<a class='open_vedio' href='javascript:;' alt='"+json.data.title+"'>"
							+ "		<img width='136' height='104' src='"+json.data.img+"' /></a>"
							+ "		<div class='vedio' style='display:none;'>"
							+ "			<div>"
							+ "				<a style='float:right' target='_new' href='"+json.data.url+"'>"+json.data.title+"</a>"
							+ "				<a class='close_vedio' href='javascript:;'>关闭</a>"
							+ "			</div>"
							+ json.data.object
							+ "		</div>"
							+ "</div>";
						$result.html(html);
						$('#video_url').val($("a[class='open_vedio'] img").attr('src'));//设置隐藏域的地址
						return false;
					}
				}
			});
		});
	});
</script>


<!--头像拍照-->					
<script type="text/javascript">
	//允许上传的图片类型
	var extensions = 'jpg,jpeg,gif,png';//视频头像会有3份 上传只会有一份
	//保存缩略图的地址.
	var saveUrl = 'save_avatar.php';//这2个文件 必须设置error_reporting(0); 否则一些提示也会造成上传失败
	//保存摄象头白摄图片的地址.
	var cameraPostUrl = 'camera.php';
	//头像编辑器flash的地址.
	var editorFlaPath = 'js/AvatarEditor.swf';

	function useCamera()
	{
		var content = '<embed height="464" width="488" ';
		content +='flashvars="type=camera';
		content +='&postUrl='+cameraPostUrl+'?&radom=1';
		content += '&saveUrl='+saveUrl+'?radom=1" ';
		content +='pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" ';
		content +='allowscriptaccess="always" quality="high" ';
		content +='src="'+editorFlaPath+'"/>';
		document.getElementById('avatar_editor').innerHTML = content;
		//$('#resultdisdiv').html($('#avatar_editor').html());
		$('#avatar_editor').show();
		hiddenIdDiv('avatar_editor'); //隐藏其他同辈div
	}
	function buildAvatarEditor(pic_id,pic_path,post_type)
	{
		var content = '<embed height="464" width="488"'; 
		content+='flashvars="type='+post_type;
		content+='&photoUrl='+pic_path;
		content+='&photoId='+pic_id;
		content+='&postUrl='+cameraPostUrl+'?&radom=1';
		content+='&saveUrl='+saveUrl+'?radom=1"';
		content+=' pluginspage="http://www.macromedia.com/go/getflashplayer"';
		content+=' type="application/x-shockwave-flash"';
		content+=' allowscriptaccess="always" quality="high" src="'+editorFlaPath+'"/>';
		document.getElementById('avatar_editor').innerHTML = content;
		
		$('#avatar_editor').show();
		hiddenIdDiv('avatar_editor');//隐藏结果div
	}
	/**
	  * 提供给FLASH的接口 ： 没有摄像头时的回调方法
	  */
	function noCamera(){
		alert("没有camare ：）");
	}
	/**
	 * 提供给FLASH的接口：编辑头像保存成功后的回调方法
	 */
	function avatarSaved(){
		var avatar_url=getCookie('avatar_url');
		imgurl='<img src="'+avatar_url+'" width="136" height="104"/>';
		$('#avatarResult-d').html(imgurl);
		$('#avatarResult-d').show();
		hiddenIdDiv('avatarResult-d'); //隐藏其他同辈div
		//$('#resultdisdiv').html(imgurl);//显示图片
		//alert(avatar_url);
		//window.location.href = '/profile.do';
	}
	/**
	* 提供给FLASH的接口：编辑头像保存失败的回调方法, msg 是失败信息，可以不返回给用户, 仅作调试使用.
	*/
	function avatarError(msg){
		alert("上传失败了");
	}

	function checkFile(){
		var path = document.getElementById('Filedata').value;
		var ext = getExt(path);
		var re = new RegExp("(^|\\s|,)" + ext + "($|\\s|,)", "ig");
		if(extensions != '' && (re.exec(extensions) == null || ext == '')) {
		 alert('对不起，只能上传jpg, gif, png类型的图片');
		 return false;
		}
		showLoading();
		return true;
	}

	function getExt(path) {
		return path.lastIndexOf('.') == -1 ? '' : path.substr(path.lastIndexOf('.') + 1, path.length).toLowerCase();
	}
	function showLoading(){
		document.getElementById('loading_gif').style.visibility = 'visible';
	}
	function hideLoading(){
		document.getElementById('loading_gif').style.visibility = 'hidden';
	}
</script>


<!--上传图片new-->
<script type="text/javascript" src="js/ajaxupload.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var oBtn = document.getElementById("unloadPic");
	var oShow = document.getElementById("uploadedName");
	var oRemind = document.getElementById("errorRemind");	
	new AjaxUpload(oBtn,{
		action:"ajax.php?op=upload",
		name:"upload",
		onSubmit:function(file,ext){      //上传时
			if(ext && /^(jpg|jpeg|png|gif)$/.test(ext)){
				//ext是后缀名
				//oBtn.value = "正在上传…";//可以放进度条
				//oBtn.disabled = "disabled";
				$('#loadingDiv').show();
			}else{	
				oRemind.style.color = "#ff3300";
				oRemind.innerHTML = "图片格式不正确！支持jpg|jpeg|png|gif";
				return false;
			}
		},
		onComplete:function(file,response){ //上传完成
			
			//oBtn.disabled = "";
			//oBtn.value = "再上传一张图片";
			oRemind.innerHTML = "";
			$('#loadingDiv').hide();
			var newChild =  document.createElement("li");
			//newChild.innerHTML = file;
			newChild.innerHTML = '<img src="'+response+'" width="136" height="104" />';
			oShow.appendChild(newChild);
			$('#img_url').val(response);//设置图片隐藏域值
			$('#upNameDiv').show();	//图片显示
			hiddenIdDiv('upNameDiv'); //除这个div隐藏全部结果集
			$('.toolbar-ul').hide(); //隐藏菜单条
		}
	});
	
	
	<!--上传视频new-->
	var oBtn2 = document.getElementById("unloadPic2");
	var oShow2 = document.getElementById("uploadedName2");
	var oRemind2 = document.getElementById("errorRemind2");	
	new AjaxUpload(oBtn2,{
		action:"ajax.php?op=upload",
		name:"upload", //文件域的名称 php文件根据这个上传
		onSubmit:function(file,ext){
			if(ext && /^(flv|mp4|rmvb|avi|wmv)$/.test(ext)){
				//ext是后缀名
				//oBtn2.value = "正在上传…";
				//oBtn2.disabled = "disabled";
				$('#loadingDiv2').show();
			}else{	
				oRemind2.style.color = "#ff3300";
				oRemind2.innerHTML = "视频格式不正确！支持flv|mp4|rmvb|avi|wmv";
				return false;
			}
		},
		onComplete:function(file,response){
			oBtn2.disabled = "";
			//oBtn2.value = "再上传1个视频";
			oRemind2.innerHTML = "";
			$('#loadingDiv2').hide();
			var newChild =  document.createElement("li");
			newChild.innerHTML = file;
			oShow2.appendChild(newChild);
			$('#video_url').val(file);
			$('#upNameDiv2').show();//图片显示
			hiddenIdDiv('upNameDiv2'); //除这个div隐藏全部结果集
			$('.toolbar-ul').hide(); 
		}
	});
	
	<!--上传附件new-->
	var oBtn3 = document.getElementById("unloadPic3");
	var oShow3 = document.getElementById("uploadedName3");
	var oRemind3 = document.getElementById("errorRemind3");	
	new AjaxUpload(oBtn3,{
		action:"ajax.php?op=upload",
		name:"upload",
		onSubmit:function(file,ext){
			if(ext && /^(doc|zip|rar|xls|txt)$/.test(ext)){
				//ext是后缀名
				//oBtn3.value = "正在上传…";
				//oBtn3.disabled = "disabled";
				$('#loadingDiv3').show();
			}else{	
				oRemind3.style.color = "#ff3300";
				oRemind3.innerHTML = "附件格式不正确！支持doc|zip|rar|xls|txt";
				return false;
			}
		},
		onComplete:function(file,response){ //response即服务端返回数据
			//alert(response) //成功为success
			oBtn3.disabled = "";
			//oBtn3.value = "再上传1个视频";
			oRemind3.innerHTML = "";
			$('#loadingDiv3').hide();
			var newChild =  document.createElement("li");
			newChild.innerHTML = file;
			oShow3.appendChild(newChild);
			$('#attach_url').val(file);
		}
	});
	
	
});
</script>


<!--私信弹窗-->
<link rel="stylesheet" href="css/tipswindown.css" type="text/css" media="all" />
<script type="text/javascript" src="js/tipswindown.js"></script>
<script>
function messageDialog(uid,uname){
	
	var conHtml='';
	conHtml+='<div id="pmessage" ><table border="0" width="100%"><tbody><tr height="30px"><td width="50px">收信人</td><td>';
	conHtml+='<input type="text" class="input_text" id="senduser" style="width:195px" value="'+uname+'">';
	conHtml+='<span class="tip1 dn">请输入你的听众的微博昵称</span></td></tr><tr><td valign="top">内&nbsp;&nbsp;&nbsp;容</td>';
	conHtml+='<td><textarea onkeyup="spnums()" id="pmcontentbox" class="input_text" style="width:350px;height:70px;"></textarea>';
	conHtml+='</td></tr><tr><td colspan="2"><p><span class="tip2 dn" id="sendmsgbox">还能输入<em>140</em>字</span>';
	conHtml+='<input type="button" class="button4" onclick="sendprimsg()" value="发送"></p></td></tr></tbody></table></div>';
	//alert(conHtml);
	$('#pmessagediv').html(conHtml);
	//$('#senduser').val(uname);//这样设置了 弹窗获取不到值 所以要在输出的时候就把值写进去
	tipsWindown("发送私信","id:pmessagediv","450","150","true","","true","extclassName")
	
	  
}
function selectFriend(uid,uname){
	
	$('#addtext').text('@'+uname+' ')
	$('#frienddiv').html('')
	//banBackspace(e)
}

function banBackspace(e) {
	// 获取event对象
	var _e = e || window.event;
	//if (_e.keyCode == 8) {e.returnvalue=false;return false;}
	$('#Fi').hide();
	
}
function forbidPaste(){
	return $.browser.msie ? false : true;
}

//获取剪贴板内容
function doGetClipboard() {
	try {
		var temp = window;
		if (window.clipboardData) { //IE直接返回内容
			return window.clipboardData.getData("Text");
		} else if (window.netscape) {
			try {
				netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
			} catch (ex) { }

			var clip = Components.classes["@mozilla.org/widget/clipboard;1"].createInstance(Components.interfaces.nsIClipboard);
			if (!clip) return;

			var trans = Components.classes["@mozilla.org/widget/transferable;1"].createInstance(Components.interfaces.nsITransferable);
			if (!trans) return;

			trans.addDataFlavor("text/unicode");
			clip.getData(trans, clip.kGlobalClipboard);
			var str = new Object();
			var len = new Object();
			try {
				trans.getTransferData("text/unicode", str, len);
			}
			catch (error) {
				return null;
			}
		}

		if (str) {
			if (Components.interfaces.nsISupportsWString) {
				str = str.value.QueryInterface(Components.interfaces.nsISupportsWString);
			} else if (Components.interfaces.nsISupportsString) {
				str = str.value.QueryInterface(Components.interfaces.nsISupportsString);
			} else {
				str = null;
			}
			if (str) {
				return str.data.substring(0, len.value / 2);
			}
		}

		return null;
	} catch (ex) {
		alert(ex);
	}
}


function clearHtml(e) {
	var _e = e || window.event;
	if (_e.ctrlKey && _e.keyCode == 86) { 
		$('#addtext').focus()
		$('#addtext').append('<span id="spanId"> </span>');
		$("#spanId").focus();
	}
	/*
	if($.browser.msie) { 
		// 获取event对象
		var _e = e || window.event;
		if($('#addtext').text()==''){
			$('#Fi').show();
		}
		if (_e.ctrlKey && _e.keyCode == 86) { 	//ctrl+v 粘贴操作
			var jHtml=doGetClipboard(); 		//获取剪贴板内容
			var oHtml=$('#addtext').append(jHtml);//追加到分享框
			//var nHtml=removeHTMLTag($('#addtext').html());//过滤所有html标签
			
			//alert(nHtml)
			// alert(oHtml)
			// alert(nHtml)
		}
		
		
	}
	*/
}
function closeTool(e) {
	//var _e = e || window.event;
	$('#lockul').hide()
	$('.toolbar-ul').hide()
}
//document.onmousedown = closeTool;//单击隐藏其他菜单
//document.onkeydown = banBackspace;// 调用：for IE、Firefox、Chrome、Safari 
//document.onkeyup = clearHtml; //jquery 可能不能获取 次方法准确
//document.onkeypress = banBackspace; //// for Opera、Firefox 

function cellkeydown(event) {  
	$('#Fi').hide();
	if (event.ctrlKey && event.keyCode == 86) {  
		var ss = document.getElementById("textArea");  
		ss.focus();		 //焦点定位到textarea 
		ss.select();  	
		setTimeout("dealwithData()",50);  // 等50毫秒，keyPress事件发生了再去处理数据  
	}     
} 

function dealwithData(event) {  
	var ss = document.getElementById("textArea");  
	$('#addtext').append(ss.value);//将内容再追加到分享框
	$('#textArea').val('');	
	ss.blur();
	
	//clearHtml(event);
} 

</script>


</head>

<body >
<div id="main" style="width:960px;height:300px;margin:20px auto;">

		<div class="sharediv" >
			<!--头像-->
			<div class="avator">
				<a href="" ><img src="images/photo.png" width="48px" height="48px"  alt="查看个人资料"  ></a>
			</div>

			<!--分享框-->
			<div class="sharebox" decorated="true">
				<form id="sharebox" name="sharebox" action="ajax.php?op=doShare"  method="post" enctype="multipart/form-data" onsubmit="doShare();return false;"><!--表单里面不能嵌套表单 否则无法提交-->
					<input type="hidden" name="forbid" id="forbid" value="0">
					<input type="hidden" name="lock" id="lock" value="0">
					<input type="hidden" name="img_url" id="img_url" value="">
					<input type="hidden" name="video_url" id="video_url" value="">
					<input type="hidden" name="attach_url" id="attach_url" value="">
					<input type="hidden" name="snap_url" id="snap_url" value="">
					<!--小箭头-->
					<div class="jiantou"></div>
					<div id="inputbox" class="inputbox">
						<span  id="closeOp" class="dn">0</span>
						<div id="xiala-lock" class="xialap">
							<img src="images/xiala2.png" width="20" height="15"  id="xiala" class="xiala r" onmouseover="xialaOp()" onmouseout="resetcloseOp()"/>
							<ul id="lockul" class="lockul dn" onmouseover="changeDis()">
								<li onclick="forbidOp('0',1)" onmouseover="this.className='liColor'" onmouseout="this.className=''">禁止评论</li>
								<li onclick="forbidOp('1',1)" onmouseover="this.className='liColor'" onmouseout="this.className=''">锁定信息</li>
							</ul>
							<!--显示图标-->
							<ul id="lockul-d" class="lockul-d">
								<li class="dn" onclick="forbidOp('0')"><a href="javascript:void(0);" class="forbid" title="禁止评论" alt="禁止评论"></a></li>
								<li class="dn" onclick="forbidOp('1')"><a href="javascript:void(0);" class="lock" title="锁定信息" alt="锁定信息"></a></li>
							</ul>
						</div>
						
						<div  id="addtext-p" class="addtext-p bord" >
							<div class="fi" id="fi" style="" ondblclick="fiOp()" >分享新鲜事...</div>
							<div  id="addtext" class="addtext" onclick="getPos();"   onkeyup="getPos();" name="addtext" g_editable="true" contenteditable="true" ></div><!--IE 不支持contenteditable="plaintext-only" -->
						</div>
						
						<div id="Bw1" class="Bw" role="button" style="display:none;" onclick="closeAll()"></div>
						
						
					</div>
					
					
					
					<!--发送私信-->
					<div id="pmessagediv" class="dn">
						
					</div>
				
					<!--结果显示-->
					<div id="resultdisdiv" class="clearfix db">
						
						<div id="upNameDiv" class="upNameDiv" style="display:none;">
							<p id="errorRemind"></p>
							<div id="loadingDiv" style="display:none;"><img src="images/loading-old.gif" /></div>
							<ol id="uploadedName"></ol><!--上传图片结果显示-->
						</div>
						
						<div id="upNameDiv2" class="upNameDiv" style="display:none;">
							<p id="errorRemind2"></p>
							<div id="loadingDiv2" style="display:none;"><img src="images/loading-old.gif" /></div>
							<ol id="uploadedName2"></ol><!--上传视频结果显示-->
						</div>
						
						<!--摄像头-->
						<iframe src="about:blank" name="upload_target" style="display:none;"></iframe>
						<div id="avatar_editor"></div>
						
						<div id="avatarResult-d" ></div><!--摄像头照片结果显示-->
						
								
						<!--截取视频图片-->
						<div id="links-d" class="clearfix dn"><!--清除浮动-->
							<ul  >
								<li>请粘贴视频播放页地址<br>支持优酷、土豆、酷六、56、乐视、搜狐</li>
								<li><input type="text" id="url" name="url" fs=""  value="" /> <button id="submit" type="button" onsubmit="return false;">确定</button></li>
								<li id="video_result"></li>
							</ul>
						</div>
						
						<!--好友显示-->
						<div id="frienddiv">
						
						<!--<ul id="ful" style="overflow-y:scroll;">
						<li id="1" onclick="selectFriend(this.id,'wuxiaohong')" onmouseover="javascript:this.className='ful_li'" onmouseout="javascript:this.className=''" class="">
						<a href="javascript:void(0)"><img src="avatar_small/113652.jpg" width="48" height="48" align="top">
						<span class="headname">wuxiaohong</span></a>
						
						</li>
						<li id="1" onclick="selectFriend(this.id,'wuxiaohong')" onmouseover="javascript:this.className='ful_li'" onmouseout="javascript:this.className=''" class="">
						<a href="javascript:void(0)"><img src="avatar_small/113652.jpg" width="48" height="48" align="top">
						<span class="headname">wuxiaohong</span></a>
						</li>
						</ul>-->
						</div>
						
						<!--上传视频-->
						<div id="video-d" class="attachdiv dn" style="display:;">
							
							<h2 >上传视频：</h2>
						
							
						</div>
						
						<!--上传附件-->
						<div id="attachment-d" class="attachdiv" style="display:none;">
							<p id="errorRemind3"></p>
							<span class="tipst" >上传附件：</span>
							<input id="unloadPic3" type="button" class="atb"  value="选择附件" />
							<div id="loadingDiv3" style="display:none;"><img src="images/loading-old.gif" /></div>
							<ol id="uploadedName3"></ol>
						</div>
						
						<!--截取链接指向网页的快照-->
						<div id="addlinks-d" style="display:none;">
							<ul  >
								<li>输入网址后，将保存网页的快照</li>
								<li><input type="text" id="url2" name="url2" fs=""  value="" /> <button id="snapshot" type="button" onclick="snapShot()">确定</button></li>
								<li id="snapshot_result">
									<!--<a href="http://www.baidu.com" target="_blank"><img src="uploads/snap/134875391754342.jpg" width="180" height="120"></a>			
									<div id="subClose" class="Bw sub1 dn" role="button" style="" onclick="subClose()"></div>-->
								</li>
							</ul>
						</div>
						
					</div>
				
					<div id="toolbar-div" class="dn">
						<!--工具条-->
						<div id="toolbar" class="toolbar">
							<a href="javascript:void(0);" class="photo" id="photo"></a>
							<a href="javascript:void(0);" class="video"  id="video"></a>
							<a href="javascript:void(0);" class="attachment" id="attachment" title="添加附件" alt="添加附件"></a>
							<a href="javascript:void(0);" class="links"  id="addlinks" title="添加链接" alt="添加链接"></a>
							
							<!--照片下拉菜单-->
							<table class="toolbar-ul" id="photo-t" style="display:none;">
								<tbody>
									<tr>
										<td><span class="uicon" ></span></td>
										<td >
											<span id="unloadPic">上传图片</span>
											
											
										</td>
									</tr>
									<tr id="makephoto2">
										<td><span class="picon" ></span></td>
										<td  onclick="useCamera()"><span id="spanButtonPlaceholdersdfs" ></span><span class="spanf">拍照</span></td>
									</tr>
								</tbody>
							</table>
							<!--视频下拉菜单-->
							<table class="toolbar-ul vul " id="video-t" style="display:none;">
								<tbody>
									<tr>
										<td id="addvideo"><span class="uicon" ></span></td>
										<td align="left"><span id="links">添加视频</td>
									</tr>
									<tr id="upvideo">
										<td><span class="picon" ></span></td>
										<td align="left">
											<span id="unloadPic2">上传视频</span>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						
						<!--分享按钮-->
						<div class="share_button">
							<input type="image" name="submit" class="bt_sub" src="images/sub.jpg"  width="72" height="29"  value="发布">
						</div>
						
						<div id="xiala-lock2">
						
						</div>
						
					</div>
				</form>
			</div>
			
	
			
			
			<div class="clear"></div>
			<div class="toolbar_down">
				<div class="toolbar dn" style="float:right;">
					<span class="photo"></span>
					<span class="video"></span>
					<span class="activity"></span>
					<span class="links"></span>
					
				</div>
			</div>
			
		</div>
		
		<!--微博列表-->
		<ul id="sharelist">
			<!--<li>	
				<div class="avator">
					<a href=""><img src="images/photo.png" width="48px" height="48px" alt="查看个人资料"></a>	
				</div>	
				<div class="sharebox list_s">
					<div id="inputbox" class="inputbox list_content">
						<div class="inputC">			
							<div class="sC">asdfasdf</div>		
						</div>		
						
					</div>
					<div class="jiantou"></div> 						
				</div>
			</li>-->
		</ul>
	
</div>
<textarea id="textArea" style="">  </textarea><!--不能设置未display:none;否则focus定位不到-->
</body>
</html>