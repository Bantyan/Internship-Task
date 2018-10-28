<?php

header('Content-Type: text/html; charset=UTF-8');
// DB接続
$dsn = 'データベース名';
$user = 'ユーザー名';
$passward = 'パスワード';
$pdo = new PDO($dsn,$user,$passward);

// テーブルを作る
$sql = "CREATE TABLE formdata"
	."("
	."id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,"
	."name char(10),"
	."comment TEXT,"
	."time TIMESTAMP,"
	."passward char(10)"
	.");";
$stmt = $pdo -> query($sql);

//投稿機能
if(!empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['pass']) && empty($_POST['hide'])){
	
	// 変数に格納
	$name = $_POST['name'];
	$comment = $_POST['comment'];
	$time = date("Y-m-d/ H:i:s");
	$passward = $_POST['pass'];

	// insert
	$sql = "INSERT INTO formdata(id,name,comment,time,passward) VALUES (0,'$name','$comment','$time','$passward')"; // オートインクリメントで自動的に数字振ってる(DBが)
	$pdo -> query($sql);

}else if(!empty($_POST['name']) && !empty($_POST['comment']) && empty($_POST['pass']) && empty($_POST['hide'])){
	echo "パスワードを入力してください";

}else if(empty($_POST['name']) && !empty($_POST['comment']) && empty($_POST['pass']) && empty($_POST['hide'])){
	echo "名前を入力してください";

}else if(!empty($_POST['name']) && empty($_POST['comment']) && empty($_POST['pass']) && empty($_POST['hide'])){
	echo "コメントをを入力してください";
}

// 削除機能
if(ctype_digit($_POST['delete']) && !empty($_POST['deletepass'])){

	// selectする際、投稿された番号をと一致するカラムを指定するために変数にしておく
	$deleteId = $_POST['delete'];

	// 投稿された番号と一致するidとpasswardを取得
	$sql = "SELECT id,passward FROM formdata where id ="
	. "$deleteId"
	. ";";
	$stmt = $pdo -> query($sql);
	$arraydelete = $stmt -> fetch();

	if($_POST['deletepass'] == $arraydelete[passward]){
		$passdel = "DELETE FROM formdata where id ="
		."$deleteId"
		.";";
		$pdo -> query($passdel);
	
	}else{
		echo "パスワードが違います";

	}
}
else if(ctype_digit($_POST['delete']) && empty($_POST['deletepass'])){
	echo パスワードを入力してください;
}

// 編集用にhiddenフォームに番号を送信するための準備をする条件分岐
if(ctype_digit($_POST['edit']) && !empty($_POST['editpass'])){

	// 編集したい番号と一致するカラムを取得したい
	$postId = $_POST['edit'];
	$sql = "SELECT * FROM formdata where id ="
	."$postId"
	.";";
	$test = $pdo -> query($sql);
	$arraypass = $test -> fetch();

	// idとpasswardが一致したら内容を変数にして、value値で表示するようにする
	if($_POST['edit'] == $arraypass[id] && $_POST['editpass'] == $arraypass[passward]){
		$editowe = $arraypass[id];
		$edittwo = $arraypass[name];
		$editthree = $arraypass[comment];
	
	}else if($_POST['editpass'] != $arraypass[passward]){
		echo "パスワードが違います";
	}

}else if(!empty($_POST['edit']) && empty($_POST['editpass'])){
	echo "パスワードを入力してください";
}

// 編集機能
if(!empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['hide']) && !empty($_POST['pass'])){

	// フォームに入っている数字とidを比較したい。→変数にしておく
	$editId = $_POST['hide'];

	$sql = "SELECT * FROM formdata where id ="
	."$editId"
	.";";
	$test = $pdo -> query($sql);
	$hidepass = $test -> fetch();
	

	if(!empty($_POST['pass']) == $hidepass[passward]){
		
		// 変数に格納
		$newname = $_POST['name'];
		$newcomment = $_POST['comment'];
		$time = date("Y-m-d/ H:i:s");

		// hiddenフォームとidが一致したら…一致したカラムの内容を編集
		$sql = "update formdata set name='$newname' , comment='$newcomment' , time='$time' where id ="
		."$editId"
		.";";
		$pdo -> query($sql);
	
	}else if(!empty($_POST['pass']) != $hidepass[passward]){
		echo "パスワードが違います";
	}

}else if(!empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['hide']) && empty($_POST['pass'])){
		echo "パスワードを入力してください";
	}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>mission4</title>
</head>
<body>
	<form method="post" action="">
	<!-- toukou -->
		<input type="text" name="name" placeholder="名前"  value="<?=$edittwo?>"><br>
		<input type="text" name="comment" placeholder="コメント" value="<?=$editthree?>">
		<input type="hidden" name="hide" value="<?=$editowe?>"> <br>
		<input type="passward" name="pass" placeholder="この投稿のpassward">
		<input type="submit">
	<br><br>
	<!-- delete -->
		<input type="text" name="delete" placeholder="削除対象番号"><br>
		<input type="passward" name="deletepass" placeholder="削除したい番号のpassward">
		<input type="submit" value="削除">
	<br><br>
	<!-- edit -->
		<input type="text" name="edit" placeholder="編集対象番号"><br>
		<input type="passward" name="editpass" placeholder="編集したい番号のpassward">
		<input type="submit" value="編集">
	</form>
<body>
</html>


<?php
// web上で表示
$place = "SELECT * FROM formdata ORDER BY id ASC;"; //　データを昇順に並べている
$results = $pdo -> query($place);
foreach($results as $row){
	echo $row['id']." ".$row['name']." ".$row['comment']." " .$row['time'];
	echo "<br>";
}
?>
