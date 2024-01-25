<?php
require('config.php');


if ($pc == "pc") {
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
}
$mode = $_POST['mode'];
/*--------------------------------------------------------------


		書き込み


----------------------------------------------------------------*/
$ua = $_SERVER['HTTP_USER_AGENT'];
$host = gethostbyaddr(getenv('REMOTE_ADDR'));
$ip = $_SERVER['REMOTE_ADDR'];

if ($mode == "thread") {

	$title = $_POST['title'];
	$title = trim($title);
	$title = _no_tag($title);
	$name = $_POST['name'];
	$name = trim($name);
	$name = _no_tag($name);
	$mail = $_POST['mail'];
	$mail = trim($mail);
	$mail = _no_tag($mail);
	$comment = $_POST['comment'];
	$comment = trim($comment);
	$comment = _no_tag($comment);
	$pass = $_POST['pass'];
	$pass = trim($pass);
	$pass = _no_tag($pass);


	$time1 = date("Y/m/d H:i");
	$time2 = time();
	//$time3 = time();
	$res = "0";
	if ($title == "")
		error("スレタイは入力してください");
	if ($name == "") {
		$name = "ななしさん＠Noname";
	}
	if ($comment == "")
		error("本文は入力必須です。");
	if (strlen($title) > 60)
		error("スレタイは全角30文字以内で入力してください。");
	if (strlen($name) > 60)
		error("名前は全角30文字以内で入力してください。");

	if ($mail) {
		if (strlen($mail) > 60)
			error("mailは全角30文字以内で入力してください。");
	}

	if (strlen($comment) > 8000)
		error("本文全角4000文字以内で入力してください。");
	if ($pass) {
		if (!preg_match("/^[a-zA-Z0-9\-]+$/", $pass))
			error("編集パスワードは半角英数字20文字以内で入力してください。");
		if (strlen($pass) > 20)
			error("パスワードは半角英数字20文字以内で入力してください。");
	}

	// テーブルロック

	$query = "LOCK TABLES {$thread_table} WRITE,{$res_table} WRITE";
	$result = mysql_query($query, $db);
	if (!$result) {
		error('SELECTクエリーが失敗しました。' . mysql_error());
	}




	$result = mysql_query("select max(no) from {$thread_table} FOR UPDATE", $db);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$no = $row['max(no)'] + 1;
	$result = mysql_query("select max(time3) from {$thread_table} FOR UPDATE", $db);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$time3 = $row['max(time3)'] + 1;
	$sql = "INSERT INTO {$thread_table} (no,title,time1,time2,time3,res,access) VALUES (
	'" . $no . "',
	'" . $title . "',
	'" . $time1 . "',
	'" . $time2 . "',
	'" . $time3 . "',
	'" . $res . "',
	'" . $access . "'

	);";
	$result = mysql_query($sql, $db);
	if (!$result) {
		error("挿入エラー{$thread_table}" . mysql_error());
	}

	$result = mysql_query("select max(no) from {$res_table} FOR UPDATE", $db);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$no = $row['max(no)'] + 1;
	$result = mysql_query("select max(no2) from {$res_table} FOR UPDATE", $db);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$no2 = 1;




	$sql = "INSERT INTO {$res_table} (no,no2,name,mail,pass,time1,time2,comment,ip,host,ua,dev1,kisyu) VALUES (
	'" . $no . "',
	'" . $no2 . "',
	'" . $name . "',
	'" . $mail . "',
	'" . $pass . "',
	'" . $time1 . "',
	'" . $time2 . "',
	'" . $comment . "',
	'" . $ip . "',
	'" . $host . "',
	'" . $ua . "',
	'" . $dev1 . "',
	'" . $kisyu . "'
	);";
	$result = mysql_query($sql, $db);
	if (!$result) {
		error("挿入エラー{$res_table}" . mysql_error());
	}

	$query = "UNLOCK TABLES";
	$result = mysql_query($query, $db);
	if (!$result) {
		error('SELECTクエリーが失敗しました。' . mysql_error());
	}
	header("Location: ./");
	exit;






} else if ($mode == "res") {
	// テーブルロック

	$query = "LOCK TABLES {$thread_table} WRITE,{$res_table} WRITE";
	$result = mysql_query($query, $db);
	if (!$result) {
		error('SELECTクエリーが失敗しました。' . mysql_error());
	}
	$no = $_POST['no'];
	if (!preg_match("/^[0-9]+$/", $no))
		error("スレッドナンバーが不正です");
	$sql = "SELECT * FROM {$res_table} WHERE no = '{$no}' AND no2 = '1'";
	$result = mysql_query($sql, $db);
	if (!$result) {
		error('SELECTクエリーが失敗しました。' . mysql_error());
	}
	$row = mysql_fetch_assoc($result);
	if ($no !== $row[no])
		error("指定したスレッドがありません");

	$name = $_POST['name'];
	$name = trim($name);
	$name = _no_tag($name);
	$mail = $_POST['mail'];
	$mail = trim($mail);
	$mail = _no_tag($mail);
	$comment = $_POST['comment'];
	$comment = trim($comment);
	$comment = _no_tag($comment);
	$pass = $_POST['pass'];
	$pass = trim($pass);
	$pass = _no_tag($pass);


	if ($name == "") {
		$name = "ななしさん＠Noname";
	}
	if ($comment == "")
		error("本文は入力必須です。");
	if (strlen($title) > 60)
		error("スレタイは全角30文字以内で入力してください。");
	if (strlen($name) > 60)
		error("名前は全角30文字以内で入力してください。");

	if ($mail) {
		if (strlen($mail) > 60)
			error("mailは全角30文字以内で入力してください。");
	}

	if (strlen($comment) > 8000)
		error("本文全角4000文字以内で入力してください。");
	if ($pass) {
		if (!preg_match("/^[a-zA-Z0-9\-]+$/", $pass))
			error("編集パスワードは半角英数字20文字以内で入力してください。");
		if (strlen($pass) > 20)
			error("パスワードは半角英数字20文字以内で入力してください。");
	}


	$time1 = date("Y/m/d H:i");
	$time2 = time();
	//$time3 = time();
	$result = mysql_query("select max(res) from {$thread_table} WHERE no='$no' FOR UPDATE", $db);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$res = $row['max(res)'] + 1;
	$result = mysql_query("select max(time3) from {$thread_table} FOR UPDATE", $db);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$time3 = $row['max(time3)'] + 1;
	if ($mail == "sage") {
		//time1.time2(エポック秒)とレスと時間だけ更新
		$sql1 = "UPDATE {$thread_table} SET time1='$time1',time2='$time2',res='$res' WHERE no='$no'";
	} else {
		$sql1 = "UPDATE {$thread_table} SET time1='$time1',time2='$time2',time3='$time3',res='$res' WHERE no='$no'";
	}

	$result = mysql_query("select max(no2) from {$res_table} WHERE no='$no' FOR UPDATE", $db);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);
	$no2 = $row['max(no2)'] + 1;

	$sql2 = "INSERT INTO {$res_table} (no,no2,name,mail,pass,time1,time2,comment,ip,host,ua,dev1,kisyu) VALUES (
	'" . $no . "',
	'" . $no2 . "',
	'" . $name . "',
	'" . $mail . "',
	'" . $pass . "',
	'" . $time1 . "',
	'" . $time2 . "',
	'" . $comment . "',
	'" . $ip . "',
	'" . $host . "',
	'" . $ua . "',
	'" . $dev1 . "',
	'" . $kisyu . "'
	);";



	#Insertコマンドは一気にやっちゃう


	$result1 = mysql_query($sql1, $db);
	if (!$result1) {
		error("挿入エラー{$thread_table}" . mysql_error());
	}
	$result2 = mysql_query($sql2, $db);
	if (!$result2) {
		error("挿入エラー{$res_table}" . mysql_error());
	}

	$query = "UNLOCK TABLES";
	$result = mysql_query($query, $db);
	if (!$result) {
		error('SELECTクエリーが失敗しました。' . mysql_error());
	}
	header("Location: ./r.php?no=$no");
	exit;




} else if ($mode == "edit") {
	$no = $_POST['no'];
	$no2 = $_POST['no2'];
	if (!preg_match("/^[0-9]+$/", $no))
		error("スレッドナンバーが不正です");
	if (!preg_match("/^[0-9]+$/", $no2))
		error("レスナンバーが不正です");
	$sql = "SELECT * FROM {$res_table} WHERE no = '{$no}' AND no2 = '{$no2}'";
	$result = mysql_query($sql, $db);
	if (!$result) {
		error('SELECTクエリーが失敗しました。1' . mysql_error());
	}
	$row = mysql_fetch_assoc($result);
	if ($no !== $row[no])
		error("指定したスレッドがありません");
	$sql = "SELECT * FROM {$res_table} WHERE no = '{$no}' AND no2 = '{$no2}'";
	$result = mysql_query($sql, $db);
	if (!$result) {
		error('SELECTクエリーが失敗しました。2' . mysql_error());
	}
	$row = mysql_fetch_assoc($result);
	if ($no != $row[no]) {
		error("レス記事が存在しません(1)");
	}
	if ($row['pass'] == "")
		error("編集パスワードが設定されていません。");

	$name = $_POST['name'];
	$name = trim($name);
	$name = _no_tag($name);
	$mail = $_POST['mail'];
	$mail = trim($mail);
	$mail = _no_tag($mail);
	$comment = $_POST['comment'];
	$comment = trim($comment);
	$comment = _no_tag($comment);
	$pass = $_POST['pass'];
	$pass = trim($pass);
	$pass = _no_tag($pass);
	$time1 = date("Y/m/d H:i");
	$time2 = time();
	//$time3 = time();
	if ($pass != $row['pass'])
		error("編集パスワードが違います");

	if ($name == "") {
		$name = "ななしさん＠Noname";
	}
	if ($comment == "")
		error("本文は入力必須です。");
	if (strlen($title) > 60)
		error("スレタイは全角30文字以内で入力してください。");
	if (strlen($name) > 60)
		error("名前は全角30文字以内で入力してください。");

	if ($mail) {
		if (strlen($mail) > 60)
			error("mailは全角30文字以内で入力してください。");
	}

	if (strlen($comment) > 8000)
		error("本文全角4000文字以内で入力してください。");
	if ($pass) {
		if (!preg_match("/^[a-zA-Z0-9\-]+$/", $pass))
			error("編集パスワードは半角英数字20文字以内で入力してください。");
		if (strlen($pass) > 20)
			error("パスワードは半角英数字20文字以内で入力してください。");
	}
	// テーブルロック

	$query = "LOCK TABLES {$thread_table} WRITE,{$res_table} WRITE";
	$result = mysql_query($query, $db);
	if (!$result) {
		error('SELECTクエリーが失敗しました。3' . mysql_error());
	}

	$sql = "SELECT * FROM {$res_table} WHERE no = '{$no}' AND no2 = '{$no2}' FOR UPDATE";
	$result = mysql_query($sql, $db);
	$row = mysql_fetch_array($result, MYSQL_ASSOC);


	if ($_POST['del']) { //削除

		if ($no2 == "1") { //スレごと消す
			$sql = "DELETE FROM {$thread_table} WHERE no = '{$no}'";
			$result = mysql_query($sql, $db);
			if (!$result) {
				error("スレッド削除に失敗しました" . mysql_error());
			}
			$sql = "DELETE FROM {$res_table} WHERE no = '{$no}'";
			$result = mysql_query($sql, $db);
			if (!$result) {
				error("レステーブル削除に失敗しました" . mysql_error());
			}
			header("Location: ./");
			exit;

		} else { //スレごとは消さない
			$sql = "UPDATE {$res_table} SET name='あぼーん',mail='',pass='',time1='$time1',time2='$time2',comment='あぼーん',ip='$ip',host='$host',ua='$ua',dev1='???',kisyu='???' WHERE no='$no' AND no2='$no2'";
			$result = mysql_query($sql, $db);
			if (!$result) {
				error("編集に失敗しました" . mysql_error());
			}
			$query = "UNLOCK TABLES";
			$result = mysql_query($query, $db);
			if (!$result) {
				error('SELECTクエリーが失敗しました。4' . mysql_error());
			}
			header("Location: ./r.php?no=$no");
			exit;

		}
	} else { //編集
		$result = mysql_query("select max(time3) from {$thread_table} FOR UPDATE", $db);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$time3 = $row['max(time3)'] + 1;

		if ($mail == "sage") {
			//time1.time2(エポック秒)とレスと時間だけ更新
			$sql = "UPDATE {$thread_table} SET time1='$time1',time2='$time2' WHERE no='$no'";
		} else {
			$sql = "UPDATE {$thread_table} SET time1='$time1',time2='$time2',time3='$time3' WHERE no='$no'";
		}
		$result = mysql_query($sql, $db);
		if (!$result) {
			error("挿入エラー{$thread_table}" . mysql_error());
		}

		$sql = "UPDATE {$res_table} SET name='$name',mail='$mail',time1='$time1',time2='$time2',comment='$comment',ip='$ip',host='$host',ua='$ua',dev1='$dev1',kisyu='$kisyu' WHERE no='$no' AND no2='$no2'";
		$result = mysql_query($sql, $db);
		if (!$result) {
			error("編集に失敗しました" . mysql_error());
		}
		$query = "UNLOCK TABLES";
		$result = mysql_query($query, $db);
		if (!$result) {
			error('SELECTクエリーが失敗しました。5' . mysql_error());
		}
		header("Location: ./r.php?no=$no");
		exit;



	} //削除
} else {
	error("モードが不正です。");
}






function _no_tag($str)
{
	if (get_magic_quotes_gpc()) {
		$str = stripslashes($str);
	}
	$str = str_replace("\x0D\x0A", "\n", $str);
	$str = str_replace("\x0D", "\n", $str);
	$str = str_replace("\x0A", "\n", $str);
	//SQLインジェクション対策
	$str = str_replace("&", "&amp;", $str);
	$str = str_replace("\"", "&quot;", $str);
	$str = str_replace("'", "&#039;", $str);
	$str = str_replace("<", "&lt;", $str);
	$str = str_replace(">", "&gt;", $str);
	$str = str_replace("\n", "<br>", $str);
	return $str;

}

function _ok_tag($str)
{
	$str = str_replace("<br>", "\n", $str);
	$str = str_replace("&lt;", "<", $str);
	$str = str_replace("&gt;", ">", $str);
	return $str;
}


function error($mes)
{
	echo <<<HTML
<html>
<body>
<h1>エラー</h1>
$mes
</body>
</html>
HTML;
	exit;

}


?>