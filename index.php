<?php
require ('config.php');

$mode = $_GET['mode'];
if($mode != ""){
if (!preg_match("/^[a-zA-Z0-9\-]+$/", $mode)){
error("モードが不正です。");
}
}
$search = _no_tag($_GET['search']);





if($mode == "function"){
echo <<< FORM
<html>
<head>
<title>スレッド式掲示板 test</title>
</head>
<body bgcolor=black text=white>
<center><h1>スレッド式掲示板 test</h1></center>


</body></html>
FORM;
exit;
}else if($mode == "search"){


echo $html_header;
echo <<< FORM
<center><h1>スレッド式掲示板</h1></center>

<HR color="#000000" size="1" width="100%">
■検索<br />
<form action="./" method="GET">
<input type="text" name="search" value="">
<br />
<input type="submit" value="検索">
</form>
<HR color="#000000" size="1" width="100%">

<HR color="#000000" size="1" width="100%">
<center><a href="./">back</a></center>
<HR color="#000000" size="1" width="100%">
FORM;


echo $html_footer;


exit;
}



echo $html_header;
echo <<< FORM
<center><h1>スレッド式掲示板</h1></center>

<HR color="#000000" size="1" width="100%">
<a href="form.php">スレ立て</a>|<a href="?mode=search">検索</a>|<a href="./">更新</a>|
<HR color="#000000" size="1" width="100%">
FORM;


	$sql = "SELECT * FROM $thread_table ORDER by time3 DESC";
	$result = mysql_query($sql,$db);
		if (!$result) {
 		  error('SELECTクエリーが失敗しました。'.mysql_error());
		}

/*--------------------------------------------------------------


		表示部分


----------------------------------------------------------------*/


	$view 	= 15;//1ページに表示する件数
	$p 	= $_GET['p'];
	if($p == ""){
	$p 	= 0;
	}else{
	if(!preg_match("/^[0-9]+$/",$p)) error("ページナンバーが不正です。");
	}
	$start 	= $p * $view;
	$end 	= $start + $view;


//SQLテーブル内アルゴリズム
/*
1
2
3
4
5
6
7
.....

これをソートするので
7
6
5
4
3
2
1

P=1のとき、1～$view、すなわち最新$view件の表示

*/

	for($i=$start;$i<$end;$i++){

if($search == ""){
	$sql = "SELECT * FROM $thread_table ORDER by time3 DESC LIMIT {$i},{$view};";
}else{
$search_title = '%'.$search.'%';
	$sql = "SELECT * FROM $thread_table WHERE title LIKE '{$search_title}' ORDER by time3 DESC LIMIT {$i},{$view};";
}

	$result = mysql_query($sql,$db);
		if (!$result) {
 		  error('SELECTクエリーが失敗しました。'.mysql_error());
		}
	$row 	= mysql_fetch_assoc($result);
	$no 	= $row['no'];
	$title  = $row['title'];
	$res    = $row['res'];
	$time2	= $row['time2'];
	$access = $row['access'];
	$new_time = time() - $time2;
	if($new_time < 600){
//	$new = "<font color=red>New!!</font>";
	$new = "<img src=./images/new.gif>";
	}else{
	$new = "";
	}

if($no != ""){
echo <<< FORM
$no:<a href="r.php?no={$no}">{$title}</a> ({$res}/{$access}) $new <br />

FORM;
//<HR color="#000000" size="1" width="100%">
}
}
/*--------------------------------------------------------------


		前のページ・次のページ


----------------------------------------------------------------*/
echo "<HR color=\"#000000\" size=\"1\" width=\"100%\">";


if($search != ""){
	$result = mysql_query("select count(*) from {$thread_table} WHERE title LIKE '{$search_title}'", $db);
}else{
$result = mysql_query("select count(*) from {$thread_table}", $db);
}

	$row 	= mysql_fetch_array($result, MYSQL_ASSOC);
	$count	 = $row['count(*)'];
	$count--;

	if($p < 1){

echo <<< FORM
前の{$view}件
FORM;
	}else{
	$p2 = $p - 1;


if($search != ""){
echo <<< FORM
<a href="?p=$p2&search=$search">前の{$view}件</a>
FORM;
}else{

echo <<< FORM
<a href="?p=$p2">前の{$view}件</a>
FORM;
}
	}


	
	if($end > $count){
echo <<< FORM
|次の{$view}件
FORM;
	}else{
	$p2 = $p + 1;

if($search != ""){
echo <<< FORM
|<a href="?p=$p2&search=$search">次の{$view}件</a>
FORM;
}else{
echo <<< FORM
|<a href="?p=$p2">次の{$view}件</a>
FORM;
}

	}

echo $html_footer;


function _no_tag($str){
if(get_magic_quotes_gpc()){
$str = stripslashes($str);
}


$str = str_replace("\x0D\x0A", "\n", $str);
$str = str_replace("\x0D", "\n", $str);
$str = str_replace("\x0A", "\n", $str);
//SQLインジェクション対策
$str = str_replace("&", "&amp;", $str);
$str = str_replace("\"","&quot;", $str);
$str = str_replace("'","&#039;", $str);
$str = str_replace("<", "&lt;", $str);
$str = str_replace(">", "&gt;", $str);
$str = str_replace("\n", "<br>", $str);
return $str;

}

function _ok_tag($str){
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

