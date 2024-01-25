<?php
require ('config.php');


/*--------------------------------------------------------------


		変数のチェック＆スレチェック


----------------------------------------------------------------*/



	$no = $_GET['no'];
	if(empty($no)) error("レスナンバーを入力してください。");
	if(!preg_match("/^[0-9]+$/",$no)) error("スレッドナンバーが不正です");
	$sql = "SELECT * FROM {$res_table} WHERE no = '{$no}' AND no2 = 1";
	$result = mysql_query($sql,$db);
	if (!$result) {
	  error('SELECTクエリーが失敗しました。'.mysql_error());
	}
	$row=mysql_fetch_assoc($result);
	if($no != $row[no]){
	error("スレッドが存在しません row[no]=".$row[no]);
	}
	$sql = "SELECT * FROM {$thread_table} WHERE no = {$no}";
	$result = mysql_query($sql,$db);
	$row = mysql_fetch_assoc($result);
	$title = $row[title];




	$query = "LOCK TABLES {$thread_table} WRITE";
	$result 	= mysql_query($query, $db);
 	if (!$result) {
	  error('SELECTクエリーが失敗しました。'.mysql_error());
	}


	#アクセスカーンたー
   // テーブルロック


	$result 	= mysql_query("select max(access) from {$thread_table} WHERE no='$no' FOR UPDATE", $db);
	$row		= mysql_fetch_array($result, MYSQL_ASSOC);
	$access		= $row['max(access)'] + 1;
	$sql1 		= "UPDATE {$thread_table} SET access='$access' WHERE no='$no'";



	$result1 	= mysql_query($sql1, $db);
	if(!$result1){
	error("挿入エラー{$thread_table}".mysql_error());
	}


	$query = "UNLOCK TABLES";
	$result 	= mysql_query($query, $db);
 	if (!$result) {
	  error('SELECTクエリーが失敗しました。'.mysql_error());
	}





	//>>1や>>2を対応させる
	$viewno = $_GET['viewno'];
	if(isset($viewno)){
	if($viewno == "") error("記事表示ナンバーを入力してください");
	if(!preg_match("/^[0-9\-]+$/",$viewno)) error("記事表示ナンバーが不正です");
	}



$echo = $html_header;

$echo .= <<< FORM
<center><h1>スレッド式掲示板</h1></center>
<a href="./">スレッド一覧</a> -> $title
<HR color="#000000" size="1" width="100%">
{$access}アクセス
<HR color="#000000" size="1" width="100%">
<a href="form.php?no={$no}">書き込む</a>|
<HR color="#000000" size="1" width="100%">
FORM;


$sql = "SELECT * FROM {$res_table} WHERE no = {$no}";
$res_list = array();
$result = mysql_query($sql,$db);
$row=mysql_fetch_assoc($result);
	$no	= $row['no'];
	$no2 	= $row['no2'];
	$name	= $row['name'];
	$mail	= $row['mail'];
	$pass	= $row['pass'];
	$time1	= $row['time1'];
	$time2	= $row['time2'];
	$comment= $row['comment'];
	$ip	= $row['ip'];
	$host	= $row['host'];
	$ua	= $row['ua'];
	$dev1	= $row['dev1'];
	$kisyu	= $row['kisyu'];
		$name    = _my_name($name);
		$comment = _my_tag($no,$comment);
	if($mail) $name = "<a href=\"mailto:$mail\">{$name}</a>";
	if($pass){
	$edit = "[<a href=\"form.php?no={$no}&no2={$no2}&mode=edit\">Edit</a>]";
	}else{
	$edit = "[Edit]";
	}
$echo .= <<< FORM
[{$no2}] $name
<br />
<br />
$comment
<br />
<br />
({$kisyu}/{$dev1})
<br />
[<a href="form.php?no={$no}&no2={$no2}&mode=res">Res</a>] $edit
<br />
$time1
<HR color="#000000" size="1" width="100%">
FORM;




$echo .= "<center>レス記事表示</center><HR color=\"#000000\" size=\"1\" width=\"100%\">";



	$result = mysql_query("select count(*) from {$res_table} WHERE no='$no'", $db);
	$row 	= mysql_fetch_array($result, MYSQL_ASSOC);
	$count	 = $row['count(*)'];
	$count--;
	$count2 = $count;
/*--------------------------------------------------------------


		表示部分


----------------------------------------------------------------*/

	$view 	= 5;//1ページに表示する件数
	$p 	= $_GET['p'];
	if($p == ""){
	$p 	= 0;
	}
	$start 	= $p * $view;
	$end 	= $start + $view;
	

	if (preg_match("/\-/",$viewno)){
	list($start1, $end1) = explode('-',$viewno);
	
	if($end1 > $count + 1) $noviewflag = 1;
	if($start1 < 0) $noviewflag = 1;

	$sql = "SELECT * FROM {$res_table} WHERE no = {$no} AND no2 = {$start1}";
	$result = mysql_query($sql,$db);
	if (!$result) {
	  error('SELECTクエリーが失敗しました。'.mysql_error());
	}
	$row=mysql_fetch_assoc($result);
	if($no != $row[no]){
	error("レス記事が存在しません(1)");
	}
	$sql = "SELECT * FROM {$res_table} WHERE no = {$no} AND no2 = {$end1}";
	$result = mysql_query($sql,$db);
	if (!$result) {
	  error('SELECTクエリーが失敗しました。'.mysql_error());
	}
	$row=mysql_fetch_assoc($result);
	if($no != $row[no]){
	error("レス記事が存在しません(2)");
	}
/*--------------------------------------------------------------


		>>1-2や>>2などに対応させるために、アルゴリズムが複雑！？


----------------------------------------------------------------*/

	$ed1 = $end1;
	$st1 = $start1;
	$start1 = $count2 - $end1;
	$end1   = $count2   - $st1 + 1;
	$start = $start1;
	$end = $end1;
	}else if(preg_match("/^[0-9]+$/",$viewno)){
	$sql = "SELECT * FROM {$res_table} WHERE no = {$no} AND no2 = {$viewno}";
	$result = mysql_query($sql,$db);
	if (!$result) {
	  error('SELECTクエリーが失敗しました。'.mysql_error());
	}
	$row=mysql_fetch_assoc($result);
	if($no != $row[no]){
	error("レス記事が存在しません(3)");
	}
	$ed1 = $viewno;
	$st1 = $viewno;
	$start1 = $count2 - $viewno;
	$end1   = $count2   - $st1 + 1;
	$start = $start1;
	$end = $end1;

	}else{
	$end--;
	$start--;
	}


	if($sqltype){
	$views = $end1 - $start1;
	}else{
				
	$views = $view;
	}






	for($i=$end;$i>$start;$i--){
	$sql = "SELECT * FROM {$res_table} WHERE no = $no AND no2 != '1' ORDER by no2 DESC LIMIT $i,$views;";
	$result = mysql_query($sql,$db);
	if (!$result) {
 	  error('SELECTクエリーが失敗しました。'.mysql_error());
	}
	$row 	= mysql_fetch_assoc($result);
	


	$no2 	= $row['no2'];
	$name	= $row['name'];
	$mail	= $row['mail'];
	$pass	= $row['pass'];
	$time1	= $row['time1'];
	$time2	= $row['time2'];
	$comment= $row['comment'];
	$ip	= $row['ip'];
	$host	= $row['host'];
	$ua	= $row['ua'];
	$dev1	= $row['dev1'];
	$kisyu	= $row['kisyu'];
	$name    = _my_name($name);
	if($mail) $name = "<a href=\"mailto:$mail\">{$name}</a>";
	$comment = _my_tag($no,$comment);
	if($pass){
	$edit = "[<a href=\"form.php?no={$no}&no2={$no2}&mode=edit\">Edit</a>]";
	}else{
	$edit = "[Edit]";
	}
if($no2 != ""){
$echo .= <<< FORM
[{$no2}] $name
<br />
<br />
$comment
<br />
<br />
({$kisyu}/{$dev1})
<br />
[<a href="form.php?no={$no}&no2={$no2}&mode=res">Res</a>] $edit
<br />
$time1
<HR color="#000000" size="1" width="100%">
FORM;
}

}
/*--------------------------------------------------------------


		前のページ・次のページ


----------------------------------------------------------------*/
$echo .= "<HR color=\"#000000\" size=\"1\" width=\"100%\">";
	$end++;
	if($p < 1 || $viewno != ""){

$echo .= <<< FORM
前の{$view}件
FORM;

	}else{
	$p2 = $p - 1;
$echo .= <<< FORM
<a href="?no=$no&p=$p2">前の{$view}件</a>
FORM;

	}

	if($end >= $count || $viewno != ""){
$echo .= <<< FORM
|次の{$view}件
FORM;

	}else{
	$p2 = $p + 1;
$echo .= <<< FORM
|<a href="?no=$no&p=$p2">次の{$view}件</a>
FORM;
	}


$echo .= "<HR color=\"#000000\" size=\"1\" width=\"100%\">";

$echo .= $html_footer;

echo $echo;


function _my_name($log1_name){


$log1_name = strtr($log1_name, Array('◆' => '◇','★' => '☆')); 
$sharp = strpos($log1_name, '#'); 
if(FALSE !== $sharp){ 
$tripkey = substr($log1_name, ($sharp + 1)); 
$salt = substr($tripkey . 'H.', 1, 2); 
$salt = strtr($salt, ':;<=>?@[\\]^_`', 'ABCDEFGabcdef'); 
$salt = preg_replace('/[^\.\/0-9A-Za-z]/', '.', $salt); 
$trip = substr(crypt($tripkey,$salt), -10); 
$names = substr($log1_name, 0, $sharp);
$log1_name = $names . '◆' . $trip;

}
return $log1_name;


}


function _my_tag($no,$data){


$data = preg_replace("/&gt;&gt;([0-9]+)(?![-\d])/", "<a href=\"./r.php?no={$no}&viewno=$1&guid=ON\">&gt;&gt;$1</a>", $data);
$data = preg_replace("/&gt;&gt;([0-9]+)\-([0-9]+)/", "<a href=\"./r.php?no={$no}&viewno=$1-$2&guid=ON\">&gt;&gt;$1-$2</a>", $data);


$color1 = "BlueViolet";
$color2 = "Navy";
$color3 = "Salmon";
$color4 = "Fuchsia";
$color5 = "DarksLateGray";
$color6 = "OrangeRed";

$data = str_replace("<br>","\n",$data);

$data = preg_replace("/^&gt;(\s.+)/m","<font color=\"$color1\">$0</font>",$data);
$data = preg_replace("/^&gt;&gt;&gt;&gt;(.+)/m","<font color=\"$color2\">\\0</font>",$data);
$data = preg_replace("/^&gt;&gt;&gt;(.+)/m","<font color=\"$color3\">\\0</font>",$data);
$data = preg_replace("/^&gt;&gt;(.+)/m","<font color=\"$color4\">\\0</font>",$data);
$data = preg_replace('/^&gt;(.+)/m','<font color='.$color5.'>\\0</font>',$data);
$data = preg_replace("/^#(.+)/m","<font color=\"$color6\">\\0</font>",$data);
$data = str_replace("\n","<br>",$data);
return $data;
}





function _no_tag($str){
if(get_magic_quotes_gpc()){
$str = stripslashes($str);
}
$str = str_replace("\x0D\x0A", "\n", $str);
$str = str_replace("\x0D", "\n", $str);
$str = str_replace("\x0A", "\n", $str);
$str = str_replace("<", "&lt;", $str);
$str = str_replace(">", "&gt;", $str);
$str = str_replace("\n", "<br>", $str);
//SQLインジェクション対策
$str = str_replace("&", "&amp;", $str);
$str = str_replace("\"","&quot;", $str);
$str = str_replace("'","&#039;", $str);

return $str;

}

function _ok_tag($str){
$str = str_replace("\x0D\x0A", "\n", $str);
$str = str_replace("\x0D", "\n", $str);
$str = str_replace("\x0A", "\n", $str);
$str = str_replace("<br>", "\n", $str);
$str = str_replace("&lt;", "<", $str);
$str = str_replace("&gt;", ">", $str);
return $str;
}


function error($mes){

global $html_header,$html_footer;

echo $html_header;

echo <<< FORM
<h1>ERROR!!</h1>
$mes
FORM;

echo $html_footer;


exit;

}


?>

