var uid=0;
// \s?\/\/.*?$
////用火狐浏览器调试js IE只会提示对象没这个方法 没这个属性等
//设置cookie
function setCookie(name,value) 
{ 
	var s='http://abc.com';
	//var Days = 30;
	var exp = new Date(); 
	//exp.setTime(exp.getTime() + Days*24*60*60*1000);
	exp.setTime(exp.getTime() + 86400000); 
	document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString(); //注意过期时间需要乘以1000 因为getTime()单位是毫秒
}

//读取cookies 
function getCookie(name) 
{ 
	var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");
	if(arr=document.cookie.match(reg)){
	    return unescape(arr[2]);
	}else{ 
	    return null; 
	}
}

//删除cookie
function delCookie(name) 
{ 
	var exp = new Date(); 
	exp.setTime(exp.getTime() - 1); 
	var cval=getCookie(name); 
	if(cval!=null){ 
	    document.cookie= name + "="+cval+";expires="+exp.toGMTString();
	}
}
 


//查找好友
function searchFrind(uid){
	
}
//发送私信 这个也行 不能拖动 
function messageDialog2(uid,uname){
	$('#senduser').val(uname);
	$("#ful li").manhuaDialog({					       
		Event : "click",								//触发响应事件
		title : "发送私信",							//弹出层的标题
		type : "id",									//弹出层类型(text、容器ID、URL、Iframe)
		content : "pmessagediv",						//弹出层的内容获取(text文本、容器ID名称、URL地址、Iframe的地址)
		width : 500,									//弹出层的宽度
		height : 300,									//弹出层的高度	
		scrollTop : 200,								//层滑动的高度也就是弹出层时离顶部滑动的距离
		isAuto : false,									//是否自动弹出
		time : 2000,									//设置弹出层时间，前提是isAuto=true
		isClose : false,  								//是否自动关闭		
		timeOut : 5000									//设置自动关闭时间，前提是isClose=true	
	});

}

//禁止评论，锁定信息
function forbidOp(indexN,closeDo){

	if(indexN==0){ //评论操作
		if($('#lockul li').eq(0).html()=='允许评论'){
			$('#forbid').val(0);
			$('#lockul-d li').eq(0).hide();//隐藏图标
			$('#lockul li').eq(0).html('禁止评论');//文字转换
		}else{
			$('#forbid').val(1);
			$('#lockul-d li').eq(0).show();//显示图标
			$('#lockul li').eq(0).html('允许评论');//文字转换
		}

	}else if(indexN==1){ //信息
		if($('#lockul li').eq(1).html()=='公开信息'){
			$('#lock').val(0);
			$('#lockul-d li').eq(1).hide();//隐藏图标
			$('#lockul li').eq(1).html('锁定信息');//文字转换
		}else{
			$('#lock').val(1);
			$('#lockul-d li').eq(1).show();//显示图标
			$('#lockul li').eq(1).html('公开信息');//文字转换
		}
		
	}
	
	$('#lockul').hide();   //隐藏下拉
	hiddenNODiv();
	if($("#toolbar-div").is(":hidden")==false){  //菜单已经显示
		$('#addtext').css('width',460);return false;
	}
	if($('#forbid').val()==1 && $('#lock').val()==1){ //有2个图标
		$('#addtext').css('width',420); 
	}else if($('#forbid').val()==0 && $('#lock').val()==0){ //设置到原来的
		
		$('#addtext').css('width',460); 
	}else{
		$('#addtext').css('width',440); 
	}
	
	/*
	var closeV = arguments[1] ? arguments[1] : 0;//没传值 默认为0
	//if(closeDo=='') alert(11) 这种不行
	//alert(closeV)
	if(closeV==1){ 
		if($('#forbid').val()==1 && $('#lock').val()==1){ //有2个图标
			$('#addtext').css('width',420); 
		}else {
			$('#addtext').css('width',440); 
		}
	}else{
		if($('#forbid').val()==0 && $('#lock').val()==0){ //有2个图标
			$('#addtext').css('width',460); 
		}else {
			$('#addtext').css('width',440); 
		}
	}
	*/
	
	//$('#addtext').removeClass('bord');
	//$('#addtext-p').addClass('bord');//外面增加边框
}


//生成快照
function snapShot(){
	var snapUrl=$('#url2').val();
	$('#snapshot_result').html('<img src="images/loading-old.gif" />');//显示进度条 这个gif图片 要在浏览器中看才会动 之间windows看 不会动
	// $.post("ajax.php?op=snapshot", { "snapUrl": snapUrl},
	// function(data){
		// if(data){ //插入成功 出错也会data也会为真 所以得判断
			//$('#snap_url').val(snapUrl);//设置隐藏域值
			// alert(data);
		// }else{
			// alert(data);
		// }
	 
	// });
	$.ajax({
		url: 'ajax.php?op=snapshot',
		data: {snapUrl: snapUrl},
		dataType: 'json',
		type: 'POST',
		success: function(json){
			if(!json.status){ //失败为0 {"data":{"newImgName":"12234.jpg"},"status":1} 因为返回的是json格式 所以可直接操作json.status 
				alert('操作失败')
				return false;
			}else{
				$('#snap_url').val(json.data.newImgName);
				var snapHtml='<a href="'+snapUrl+'" target="_blank"><img src="uploads/snap/'+json.data.newImgName+'" width="180" height="320" /></a>';
				$('#snapshot_result').html(snapHtml);
				return false;
			}
		}
	});
}
//隐藏下拉
function hiddenId(ids){
	var hiddenArr = new Array('photo-t','video-t');
	for ( var i=0 ; i < hiddenArr.length ; ++i ){ 
		if(hiddenArr[i]==ids) continue; 
		if($("#"+hiddenArr[i]).is(":hidden")==false){ 
			$("#"+hiddenArr[i]).hide();
		}
	}
}
//隐藏结果
function hiddenIdDiv(ids){
	//resultdisdiv下所有显示的div id
	var hiddenArr = new Array('upNameDiv','upNameDiv2','avatar_editor','avatarResult-d','links-d','frienddiv','video-d','attachment-d','addlinks-d');
	for ( var i=0 ; i < hiddenArr.length ; ++i ){ 
		if(hiddenArr[i]==ids) continue; 
		if($("#"+hiddenArr[i]).is(":hidden")==false){ 
			$("#"+hiddenArr[i]).hide();
		}
	}
}
//隐藏不必要显示的div
function hiddenNODiv(){ 
	//resultdisdiv下所有显示的div id
	var hiddenArr = new Array('lockul','photo-t','video-t');
	for ( var i=0 ; i < hiddenArr.length ; ++i ){ 
		if($("#"+hiddenArr[i]).is(":hidden")==false){ 
			$("#"+hiddenArr[i]).hide();
		}
	}
}

//关闭具体div
function closeDiv(ids){
	$('#'+ids).hide();
}
//发布微博
function doShare(){
	var addtextc=$('#addtext').html();//内容 注意用text 因为换做div
	var reg = /^\s+$/;
	if(reg.test(addtextc) || addtextc==''){ //不能为空和空格
		alert('不能为空');
		return false;
	}
	var forbidc=$('#forbid').val();
	var lockc=$('#lock').val();
	var img_urlc=$('#img_url').val();
	var video_urlc=$('#video_url').val();
	var attach_urlc=$('#attach_url').val();
	var snap_urlc=$('#snap_url').val();
	var snapUrl=$('#url2').val(); //快照链接地址 变量不存在会提示错误
	// $addtext=$('#addtext').val();
	
	$.post("ajax.php?op=doShare", 
	{ "addtext": addtextc,"is_forbid":forbidc,"is_lock":lockc,"img_url":img_urlc,"video_url":video_urlc,"attach_url":attach_urlc,"snap_url":snap_urlc},
	function(data){
		if(data==1){ //插入成功 出错也会data也会为真 所以得判断
			//alert('发布成功');
			//div{float:left;}会造成所在的父div高度无法自动撑开 需要注意
			var sHtml='<li>';
			sHtml+='	<div class="avator">';
			sHtml+='		<a href="" ><img src="images/photo.png" width="48px" height="48px"  alt="查看个人资料"  ></a>';
			sHtml+='	</div>';
			sHtml+='	<div class="sharebox list_s" >';
			sHtml+='		<div id="inputbox" class="inputbox list_content">';
			sHtml+='			<div class="inputC">';
			sHtml+='			<div class="sC">'+addtextc+'</div>';
			if(img_urlc!=''){
				sHtml+='			<div class="sP">';
				sHtml+='				<dl>';
				sHtml+='					<dd><img src="'+img_urlc+'" width="136" height="104" /></dd>';
				sHtml+='				</dl>';
				sHtml+='			</div>';
				
			}
			if(video_urlc!=''){
				sHtml+='			<h3>视频图片：</h3>';
				sHtml+='			<div class="sV" title="视频">';
					
				sHtml+='			</div>';
			}	
			if(attach_urlc!=''){
				sHtml+='			<h3>附件：</h3>';
				sHtml+='			<div class="sA">';
				sHtml+='				<dl>';
				sHtml+='					<dd><a href="uploads/'+attach_urlc+'">'+attach_urlc+'</a></dd>';//附件直接下载
				sHtml+='				</dl>';
				sHtml+='			</div>';
				
			}
			
			if(snap_urlc!=''){
				sHtml+='			<h3>网页快照：</h3>';
				sHtml+='			<div class="sA">';
				sHtml+='				<dl>';
				sHtml+='					<dd><a href="'+snapUrl+'" target="_blank"><img src="uploads/snap/'+snap_urlc+'" width="180" height="320" /></a></dd>';
				sHtml+='				</dl>';
				sHtml+='			</div>';
				sHtml+='		</div>';
			}
			
			sHtml+='		</div>'; //自动适应div  list_content关闭
			sHtml+='		</div>';
			sHtml+=' 		<div class="jiantou"></div>';
			sHtml+='	</div>';
			sHtml+='</li>';
			
			$('#sharelist').prepend(sHtml)
			if(video_urlc!=''){
				$(".sV").first().html($("#video_result").html());
				
			}
			
		}else{
			alert('发布失败')
		}
	 
	});
}
function changeDis(){
	$("#lockul").hover(
        function(){$(this).css('display', 'block');},
        function(){$(this).css('display', 'none');}
     );
}
function changeColor(){
	$("#lockul li").hover( 
        function(){$(this).addClass('liColor');},
        function(){$(this).removeClass('liColor');}
     );
}
function fiOp(){ 
	if($("#toolbar-div").is(":hidden")==true){  //菜单如果是隐藏的
			
		hiddenNODiv();//隐藏不需显示的div
		var con=getCookie('beforeConent'); //获取上一次未保存的数据
		if(con != null){
			$('#addtext').val(con);//设置未保存的数据
		}
		if($('#xiala-lock').html()!=''){ //如果这个不为空 说明已经双击了一次 就无需执行
			$('#addtext').css('width',470);
			$("#addtext").addClass('inputbox-b');
			$('#xiala-lock2').html($('#xiala-lock').html());
			$('#xiala-lock').html('');
			$('#addtext_label').hide();
			$('#lockul').addClass('lockulbig');
			$('#toolbar-div').show();//显示菜单条
			$('#Bw1').show();		 //显示关闭按钮
			
		}
	}
}
function xialaOp(){
	if($('#closeOp').html()==1) return;
	$('#lockul').show();

}
function resetcloseOp(){
	if($("#toolbar-div").is(":hidden")==true){  //菜单已经显示 从新置为0
		$('#closeOp').html('0')
	}

}
function closeAll(){
	
	$('.toolbar-ul').hide();
	$('#lockul').hide();
	$('#addtext').css('width',414); 
	$("#inputbox").removeClass('inputbox-b');
	$('#lockul').removeClass('lockulbig');
	$('#xiala-lock').html($('#xiala-lock2').html());
	$('#xiala-lock2').html('');
	$('#toolbar-div').hide(); 
	$('#Bw1').hide();
	hiddenIdDiv('rrr');
	//hiddenNoDiv();
	$('#closeOp').html('1')
	//$('#xiala').removeAttr('onmouseover');//移除鼠标悬停事件
	
}

//if (event.ctrlKey && event.keyCode == 86) {
function clearHtml22(){
	if($.browser.msie){ //IE设置：浏览器菜单：“工具“-”Internet选项“-”安全“选项卡-选择Internet区域，点击“自定义级别”，在列表中找到“允许对剪贴板进行编程访问”，选择“启用”，确定。
		var clipBoardContent=doGetClipboard(); //获取剪贴板内容
		//alert(clipBoardContent)
		//alert($('#addtext').html())
		$('#addtext').append(clipBoardContent)
	}
}

function closeB(){

	$('.toolbar-ul').hide()
	
}
//获取选中内容
function getSelect() { 
    return  (document.all) ? document.selection.createRange().text : document.getSelection();//获取当前选中的文本
    
}
function setFocus(){
	//alert(0)
}
//光标移到text 中指定位置    
function setCaret(id,pos)   
{   
    var textbox = document.all(id);   
    var r = textbox.createTextRange();   
    r.collapse(true);   
    r.moveStart('character',pos);   
    r.select();   
}
function insertContent(value) {
	//可以用$.browser.msie这是Jquery用来检测浏览器是否为IE
	$('#addtext').focus();
	if ($.browser.msie) {
		if (document.selection) {
			$('#addtext').focus();
			var C = document.selection.createRange();
			if (C) {
				C.pasteHTML(value);//DIV支持pasteHTML，这个是我选用DIV的重要原因
			}
		}
	} else {
		document.execCommand('InsertHtml', false, value);//chrome暂不支持execCommand，firefox没问题
	}
}
//获取鼠标位置
function getPos()
{
	//pos = document.selection.createRange();
}
function removeHTMLTag(str) {
	//str = str.replace(/<\/?[^>]*>/ig,'</br>'); //去除HTML tag
	str = str.replace(/<\/?([^>]*)?>/ig,'\n'); //去除HTML tag
	str = str.replace(/[ | ]*\n/ig,'\n'); //去除行尾空白
	str = str.replace(/\n[\s| | ]*\r/g,'\n'); //去除多余空行
	str=str.replace(/&nbsp;/ig,'');//去掉&nbsp; g代表全局，i表示不区分大小写
	return str;
} 
function doPaste(){
	$('#fi').hide();
	//<img src="file:///C:/Users/111/AppData/Local/Temp/%7B[W]SJ%601FUGóJ0GE%7DI0JW.gif" sysface="27"/>
	if($.browser.mozilla || $.browser.msie){
		
		// oldText=$('#addtext').html();
		// newText=oldText.replace(/<img src=.*\/>/ig,'\n');
		// alert(newText)
		// $('#addtext').html(newText)
		
		// if (event.ctrlKey && event.keyCode == 86) {	
			// oldText=$('#addtext').html();
			// newText=removeHTMLTag(oldText); //去除html js css 标签
			// $('#addtext').html(newText)
		// }
	}
}	
//doc加载完后执行
$(document).ready(function(){
	// $('#xiala').bind('mouseover',function(){
		// $('#lockul').show();
	// })

	
	$('#main').click(function(){
		$('#lockul').hide()
		
		//setTimeout("closeB()",3000); //通过延迟设置事件执行顺
		
	})
	var isChrome = navigator.userAgent.toLowerCase().match(/chrome/) != null;
	if (isChrome) { //谷歌浏览器
		$('#addtext').attr('contenteditable','plaintext-only'); //谷歌设置div内容只能是文本内容 自动过滤html css js
	}
	// if($.browser.msie) { 
		// alert("这是IE"+$.browser.version); 
	// }else if($.browser.opera) {
		// alert("这是opera"+$.browser.version); 
	// }else if($.browser.mozilla){
		// alert("这是mozilla"+$.browser.version); 
	// }else if($.browser.safa){
		// alert("这是safa"+$.browser.version); 
	// }else{
		// alert("这是谷歌或者其他浏览器")//谷歌没有专门的判断  可用js
	// };
	
	//分享新鲜事效果
	$("#addtext").focus(function(){
		$("#fi").css('color',"#ccc")
	})
	$("#addtext").blur(function(){ 
		var values=$("#fi").text();
		if(values=="" || values==null ){ //失去焦点 值为空才显示
			$("#fi").text("分享新鲜事...");
			$("#fi").css('color',"#ccc")
			$("#fi").show();
		}
	})
		
	
   var v=$('#addtext').val();
   //输入框 键盘按下
   $("#addtext").keydown(function(event){
		
		
		$('#fi').hide();
		var oldVal=$('#addtext').html();//获取输入框内容
		// if(oldVal==v){
			// $('#addtext').val('');
		// }
		// if(oldVal==''){
			// $('.Fi').show();
		// }
		
		// if(event.keyCode == 8){
			// var reg = /^@\w{1,20}\s{1}$/;//@xiaohong 形式 屏蔽退格键
			// if(reg.test(oldVal)){ 
				// return false;
			// }
		// }
		// if (event.ctrlKey && event.keyCode == 67){
			// var selectText=getSelect(); //选中内容
			
		// }
		/*
		if($.browser.msie) {  //IE
			if (event.ctrlKey && event.keyCode == 86) { //ctrl+v 按下
	
				var oldText=$('#addtext').html(); //原先内容
				
				var selectText=getSelect(); //选中内容
				var clipBoardTxt=doGetClipboard();//剪贴板内容
				if(selectText != ''){
					newText=oldText.replace(selectText,clipBoardTxt);//剪贴板内容替换选中内容 replace有返回值
					//alert(newText)
					insertContent(newText)
					//$('#addtext').html(newText)
				}else{
					insertContent(clipBoardTxt)
					//$('#addtext').append(clipBoardTxt)
				}
				
				return false;
				
				
			}
			//else if(isChrome){//谷歌
			
			// }else{ //火狐及其他
				// var ss = document.getElementById("textArea");  
				// ss.focus();//焦点定位到textarea 
				// ss.select();  	
				// setTimeout("dealwithData()",50);  // 等50毫秒，keyPress事件发生了再去处理数据  
				// oldText=$('#addtext').html();
				// newText=removeHTMLTag(oldText);//去除html js css 标签
				
				// $('#addtext').html(newText)
			// }
			
		}
		*/
		if($.browser.msie){
			if (event.keyCode == 13) { //enter 回车 替换
				insertContent('<br>')
				return false;
			}
		}		
		if (event.ctrlKey && event.keyCode == 13) { //ctrl+enter 发布
			doShare(); //执行 ajax发表
			//document.sharebox.submit();//提交名称为sharebox的表单 只对php有效
			delCookie('beforeConent'); //提交后就删除上一次未提交内容
		}
		if (event.keyCode == 0X1B) {   //esc键取消发表
			var bConent=$('#addtext').html();
			setCookie('beforeConent',bConent);//存入cookie
			return false;
		}
		
    });
	//输入框 键盘释放
	$("#addtext").keyup(function(event){
		var newVal=$('#addtext').html();
		var keyCode = event.which;
		$('#fi').hide();
		// if (keyCode == 46 || keyCode == 8) { //删除键
			// if(newVal==''){
				// $('#addtext').val(v);
			// }    
		// }
		
		//alert(newVal.ToString().LTrim('@'));
		//alert(newVal.substr(0,1));// 第1个字符
		//alert(newVal.charAt(0));// 第1个字符  一样
		if(newVal.length<10){
			
			if(newVal=='@'){ //为@就查询数据库
				$('#frienddiv').show();	 //显示好友div
				hiddenIdDiv('frienddiv');//除好友div隐藏全部
				hiddenId('a');			 //隐藏全部
				$('#toolbar-div').hide();//隐藏工具条?
				$.post("ajax.php?op=searchFriend", {uid:3},
				function (data, textStatus){
					//alert(textStatus); //成功是success 失败fail
					//alert(data);//这样就会服务端输出的任何数据格式
				
					$("#frienddiv").html(eval(data));//插入到div中eval转化下代码
				}); //ctrl+shift+q 是添加 /**/
				

			}else if(newVal.charAt(0)=='@'){
				//alert(newVal.substr(1,newVal.length));
				var like=newVal.substr(1,newVal.length);//获取除去@后的字符
				$.post("ajax.php?op=searchFriend", {uid:3,uname:like},
				function (data, textStatus){
					$("#frienddiv").html(eval(data));//插入到div中eval转化下代码
					
				}); 
			}
			
		}
		var bConent=$('#addtext').html();
		setCookie('beforeConent',bConent);
		
		
		
    });
	//鼠标粘帖+ctrl+v事件
	$("#addtext").bind('paste', function(e) {
		$('#fi').hide();
		if($.browser.mozilla || $.browser.msie){
			
			var el = $(this);
			setTimeout(function() {
				var oldText = $(el).html();
				var patrn=/\d+:\d+:\d+/;  
				if (patrn.exec(oldText)){ //exec也行 qq
					//oldText=removeHTMLTag(oldText);
					//oldText=oldText.replace(/<\s?img[^>]*>/gi,function(m,i){return "<div>"+m+"</div>";}); //将图片用div包裹
					oldText=oldText.replace(/<\s?img[^>]*>/gi,"");
					$('#addtext').html(oldText)
				}else{		
					$.post("remove.php", {removeHtml:oldText},
					function (data, textStatus){
						//alert(data)
						$('#addtext').html(data)
					}); 
				}
			}, 100);	
		}
		
	});
	//输入框 双击
	$("#addtext").dblclick(function(){
		$("#fi").hide() ;//隐藏提示
		if($("#toolbar-div").is(":hidden")==true){  //菜单如果是隐藏的
			
			hiddenNODiv();//隐藏不需显示的div
			var con=getCookie('beforeConent'); //获取上一次未保存的数据
			if(con != null){
				$('#addtext').html(con);//设置未保存的数据
			}
			if($('#xiala-lock').html()!=''){ //如果这个不为空 说明已经双击了一次 就无需执行
				$('#addtext').css('width',470);
				$("#addtext").addClass('inputbox-b');
				$('#xiala-lock2').html($('#xiala-lock').html());
				$('#xiala-lock').html('');
				$('#addtext_label').hide();
				$('#lockul').addClass('lockulbig');
				$('#toolbar-div').show();//显示菜单条
				$('#Bw1').show();		 //显示关闭按钮
				
			}
		}
    });
	
	//分享新鲜事单击
	$('#fi').click(function(){ 
		$('#addtext').focus()
		//document.all.addtext.focus();//IE会提示错误 对象无这个属性
		
	})

	//下拉列表背景变色
	$('.toolbar-ul tr').hover(
		function () {
			$(this).addClass("trhover");
		},
		function () {
			$(this).removeClass("trhover");
		}
	).click(function(){
		$('.toolbar-ul').hide();
	});
	
	$('#unloadPic3').hover(
		function () {
			$(this).toggleClass("uphover");
		}
	)
	
	//禁止评论失去焦点
	$('#lockul').mouseleave(function(){
		$('#lockul').hide();
	})

	//照片-下拉菜单
	$('#photo').click(function(){
		hiddenNODiv();
		$('#photo-t').show();
		hiddenId('photo-t');
	})
	//视频-下拉菜单
	$('#video').click(function(){
		hiddenNODiv();
		$('#video-t').show();
		hiddenId('video-t');
		
	})
	
	$('#up_video').click(function(){
		$('#attache_tips').html('添加视频：');
		//fu.ExtIn=["flv","avi","wmv","mp3"]; //动态设置上传类型
		//getId("idExt").innerHTML = fu.ExtIn.join("，");
		hiddenId('s');
		$('#attachment-d').show();
		hiddenIdDiv('attachment-d');
		
		
		
	})
	
	
	//添加附件
	$('#attachment').click(function(){
		hiddenNODiv();
		$('#attache_tips').html('添加附件：');
		//fu.ExtIn=["doc","xls","rar","zip"];//如果上传附件表单被嵌套在其他表单中 会出错 获取不到表单
		//getId("idExt").innerHTML = fu.ExtIn.join("，");
		
		//$('#attachment-d').toggleClass('db');
		hiddenId('s');
		$('#attachment-d').show();
		
		hiddenIdDiv('attachment-d');
	})
	
	//添加视频链接
	$('#links').click(function(){
		
		$('#links-d').show();
		hiddenId('links-d');
		hiddenIdDiv('links-d');
		
	})
	//添加网址-快照
	$('#addlinks').click(function(){
		hiddenNODiv();
		$('#addlinks-d').show();
		hiddenId('addlinks-d');
		hiddenIdDiv('addlinks-d');
		
	})
	//发送私信
	$('#ful22 li').mouseover(function(){
		
	})
	
	$('.bt_sub').hover(
		function(){
			$(this).addClass('subhover');
		},
		function(){
			$(this).removeClass('subhover');
		}
	)
	//关闭好友列表 只会对已经是显示的div起作用 动态添加的div 无效
	// $('#close-frienddiv').click(function(){
		// $('#frienddiv').hide();
	// })

	
	
});



	

