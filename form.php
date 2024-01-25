<?php
require ('config.php');
$no 	= $_GET['no'];
$no2	= $_GET['no2'];
$mode   = $_GET['mode'];

		if(isset($no)){
	if($no !== ""){
	if(!preg_match("/^[0-9]+$/",$no)) error("スレッドナンバーが不正です");
	$sql = "SELECT * FROM {$res_table} WHERE no = {$no} AND no2 = 1";
	$result = mysql_query($sql,$db);
	if (!$result) {
	  error('SELECTクエリーが失敗しました。'.mysql_error());
	}
	$row=mysql_fetch_assoc($result);
	if($no !== $row[no]) error("指定したスレッドがありません");
	$sql = "SELECT * FROM {$thread_table} WHERE no = {$no}";
	$result = mysql_query($sql,$db);
	$row = mysql_fetch_assoc($result);
	$title = $row[title];






	if($mode == "res"){
if($no == "") error("レスナンバーを入力してください");
$sql = "SELECT * FROM {$res_table} WHERE no = {$no} AND no2 = {$no2}";
	$result = mysql_query($sql,$db);
	if (!$result) {
	  error('SELECTクエリーが失敗しました。'.mysql_error());
	}
	$row=mysql_fetch_assoc($result);
	if($no != $row[no]){
	error("レス記事が存在しません(1)");
	}
	$name	 = $row['name'];
	$name    = _my_name($name);
	$mail	 = $row['mail'];
	$comment = $row['comment'];
	
	$comment = str_replace("<br>","\n> ",$comment);
	$data    = ">>$no2\n> $name";
	if($_GET['i'] == "on"){
	$inyo = "引用｜<a href=\"?no={$no}&no2={$no2}&mode=res\">引用なし</a>";
	$data   .= "\n>".$comment;
	}else{
	$inyo = "<a href=\"?no={$no}&no2={$no2}&mode=res&i=on\">引用</a>|引用なし";
	}



echo $html_header;
	echo <<< FORM
<form action="bbs.php" method="POST">
<a href="./">スレッド一覧</a> -><a href="r.php?no=$no">$title</a> -><a href="r.php?no={$no}&viewno={$no2}">レスNo.{$no2}</a>
<HR color="#000000" size="1" width="100%">
■名前：<br><input type="text" name="name"><br>
■E-Mail：<br><input type="text" name="mail"><br>
■コメント： $inyo<br>
<textarea name="comment" rows="5">
{$data}
</textarea>
<br />
■編集PASS：<br><input type="text" name="pass"><br>

<input type="hidden" name="mode" value="res">
<input type="hidden" name="no" value="$no">
<input type="hidden" name="no2" value="$no2">
<input type="submit" value="書き込む">
</form>
<HR color="#000000" size="1" width="100%">
<center><a href="./">back</a></center>
<HR color="#000000" size="1" width="100%">
FORM;

echo $html_footer;


	}else if($mode == "edit"){
if($no == "") error("レスナンバーを入力してください");
$sql = "SELECT * FROM {$res_table} WHERE no = {$no} AND no2 = {$no2}";
	$result = mysql_query($sql,$db);
	if (!$result) {
	  error('SELECTクエリーが失敗しました。'.mysql_error());
	}
	$row=mysql_fetch_assoc($result);
	if($no != $row[no]){
	error("レス記事が存在しません(1)");
	}
	if($row['pass'] == "") error("編集パスワードが設定されていません。");
	$name	 = $row['name'];
	$name    = _my_name($name);
	$mail	 = $row['mail'];
	$comment = $row['comment'];
	$comment = str_replace("<br>","\n",$comment);
	

echo $html_header;	
	echo <<< FORM
<center><h1>スレッド式掲示板</h1></center>

<form action="bbs.php" method="POST">
<a href="./">スレッド一覧</a> -><a href="r.php?no=$no">$title</a> -><a href="r.php?no={$no}&viewno={$no2}">編集No.{$no2}</a>
<HR color="#000000" size="1" width="100%">
■名前：<br><input type="text" name="name" value="$name"><br>
■E-Mail：<br><input type="text" name="mail" value="$mail"><br>
■コメント：<br>
<textarea name="comment" rows="5">
{$comment}
</textarea>
<br />
■編集PASS：<br><input type="text" name="pass"><br>

<input type="hidden" name="mode" value="edit">
<input type="hidden" name="no" value="$no">
<input type="hidden" name="no2" value="$no2">
<input type="submit" value="編集">
<input type="submit" name="del" value="削除">
</form>
<HR color="#000000" size="1" width="100%">
<center><a href="./">back</a></center>
<HR color="#000000" size="1" width="100%">
FORM;
echo $html_footer;

	}else{
echo $html_header;
	echo <<< FORM
<center><h1>スレッド式掲示板</h1></center>

<form action="bbs.php" method="POST">
<a href="./">スレッド一覧</a> -><a href="r.php?no=$no">$title</a>
<HR color="#000000" size="1" width="100%">
■名前：<br><input type="text" name="name"><br>
■E-Mail：<br><input type="text" name="mail"><br>
■コメント：<br><textarea name="comment" rows="5"></textarea>
<br />
■編集PASS：<br><input type="text" name="pass"><br>

<input type="hidden" name="mode" value="res">
<input type="hidden" name="no" value="$no">
<input type="submit" value="書き込む">
</form>
<HR color="#000000" size="1" width="100%">
<center><a href="./">back</a></center>
<HR color="#000000" size="1" width="100%">
FORM;
echo $html_footer;

	}
	}
	}else{	
echo $html_header;
echo <<< FORM
<center><h1>スレッド式掲示板</h1></center>
<HR color="#000000" size="1" width="100%">
<form action="bbs.php" method="POST">
■スレタイ：<br><input type="text" name="title"><br>
■名前：<br><input type="text" name="name"><br>
■E-Mail：<br><input type="text" name="mail"><br>
■コメント：<br><textarea name="comment" rows="5"></textarea>
<br />
■編集PASS：<br><input type="text" name="pass"><br>
<input type="hidden" name="mode" value="thread">
<input type="submit" value="書き込む">
</form>
<HR color="#000000" size="1" width="100%">
<center><a href="./">back</a></center>
<HR color="#000000" size="1" width="100%">
FORM;
echo $html_footer;

}

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