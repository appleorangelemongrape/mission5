<?php

error_reporting(E_ALL & ~E_NOTICE);
//使用変数用意
$id = null;
$name = $_POST["name"];
$contents = $_POST["comment"];
date_default_timezone_set('Asia/Tokyo');
$created_at = date("Y:m::d H:i:s");
$pass=$_POST['password'];//投稿パスワード
$pass2=$_POST['pass1'];//削除パスワード
$pass3=$_POST['pass2'];//編修パスワード
$editName="";
$editComment="";
$editNumber="";
	// DB接続設定
//データベースへの接続
$dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    


//テーブル作成
$sql = "CREATE TABLE IF NOT EXISTS test4"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
    . "comment TEXT,"
    . "datedata DATETIME,"
    . "password TEXT"
	.");";
    $stmt = $pdo->query($sql);
    

	//書き込み
	if(!empty($_POST['name']) && !empty($_POST["comment"])&& !empty($_POST['password'])){
		if(!empty($_POST["edit_post"])){
			$editkey=$_POST["edit_post"]; //変更する投稿番号
			
			$sql = 'UPDATE test4 SET name=:name,comment=:comment,password=:password WHERE id=:id';
			$edit1 = $pdo->prepare($sql);
			$edit1->bindParam(':name', $name, PDO::PARAM_STR);
			$edit1->bindParam(':comment', $contents, PDO::PARAM_STR);
      $edit1->bindParam(':password', $pass, PDO::PARAM_STR);
      $edit1->bindParam(':id', $editkey, PDO::PARAM_INT);
			$edit1->execute();
		}else{
			$regist = $pdo->prepare("INSERT INTO test4(id, name, comment, datedata,password) VALUES (:id,:name,:comment,:datedata,:password)");
			$regist->bindParam(":id", $id,PDO::PARAM_INT);
			$regist->bindParam(":name", $name,PDO::PARAM_STR);
			$regist->bindParam(":comment", $contents,PDO::PARAM_STR);
      $regist->bindParam(":datedata", $created_at,PDO::PARAM_STR);
      $regist->bindParam(":password", $pass,PDO::PARAM_STR);
			$regist->execute();

		}
	 
	}

  if(!empty($_POST['deleteNum'])&& !empty($_POST['pass1'])){
    // 削除対象番号が入力されたら、削除
   
    $deleteNum = $_POST['deleteNum'];
    $sql = 'SELECT * FROM test4 WHERE id=:id ';
    $delete = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $delete->bindParam(':id', $deleteNum, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
    $delete->execute();                             // ←SQLを実行する。
    $lines = $delete->fetchAll(); 
    foreach ($lines as $line){
      if($pass2==$line['password']){
       $sql = 'delete from test4 where id=:id';
       $del = $pdo->prepare($sql);
       $del->bindParam(':id', $deleteNum, PDO::PARAM_INT);
       $del->execute();
      }
    }
  } 
  
  

//編集番号
  if(!empty($_POST['editNum'])&&!empty($_POST['pass2'])){//edit番号が入っていたら
		
		$editNum=$_POST['editNum'];//変数に代入
		
    $sql = 'SELECT * FROM test4 WHERE id=:id ';
    $edit = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
    $edit->bindParam(':id', $editNum, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
    $edit->execute();                             // ←SQLを実行する。
    $lines = $edit->fetchAll(); 
		
		foreach ($lines as $line){
		//$rowの中にはテーブルのカラム名が入る
    if($line['password']==$_POST['pass2']){
      $editName=$line['name'];
			$editComment=$line['comment'];
	    $editNumber=$line['id'];
    }
			
	
	}
  }

	$sql = 'SELECT * FROM test4';
	$st = $pdo->query($sql);
	$results = $st->fetchAll();
foreach($results as $loop){
		
		// 投稿番号が削除対象番号でない場合は、投稿詳細を表示
		if ($loop['id'] != '') {
				echo  "【投稿番号】".$loop['id']."<br>" ;
				echo "【名前】".$loop['name']."<br>";
				echo "【コメント】".$loop['comment']."<br>";
				echo "【投稿日時】" . date("Y:m:d H:i:s", strtotime($loop['datedata'])) . "<br>";
				echo "<hr>";
		}
}	

?>

<section>
    <h2>掲示板</h2>
    <form action="" method="post">
    <input type="text" name="name" placeholder="名前"  value="<?php echo $editName;?>">
        <input type="text" name="comment" placeholder="コメント"   value="<?php echo $editComment;?>">
        <input type="text" name="password" placeholder="パスワード" >
        <input type="hidden" name="edit_post" value="<?php echo $editNumber;?>">
		<input type="submit" name="submit" >		
    </form>
</section>

<form action="" method="post">
        <input type="number" name="deleteNum" placeholder="削除対象番号">
        <input type="text" name="pass1" placeholder="パスワード" >
        <input type="submit" name="submit2" value="削除">
</form>

<form action="" method="post">
        <input type="number" name="editNum" placeholder="編集対象番号">
        <input type="text" name="pass2" placeholder="パスワード" >
        <input type="submit" name="submit3" value="編集">
</form>
</body>
</html>
<!DOCTYPE html>
<meta charset="UTF-8">
<title>簡易掲示板</title>