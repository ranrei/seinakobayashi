<!DOCTYPE html> 
<html lang="ja"> 
<head> 
    <meta charset="UTF-8"> 
    <title>mission_3-05</title> 
</head> 
<body> 
    <!--編集コマンド-->
    <form action=""  method="post" > 
        <input type= "hidden" name="edit"> 
    </form> 
    
      <?php 
       // 【サンプル】 
    // ・データベース名：tb240278db 
    // ・ユーザー名：tb-240278 
    // ・パスワード：r4ht5CdUkZm 
     //データベース接続設定
    $dsn='mysql:dbname=tb240278db;host=localhost';//データベース名、ホスト名（接続先を定義）
    $user = 'tb-240278';//MySQLのユーザー名
    $password = '4ht5CdUkZm';//MySQLのパスワード
    $pdo =new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));//PDOクラスは、PHPとデータベースサーバーの間の接続を表す。PDO 基底クラスのインスタンスを作成することにより、接続が確立されます。 
    //「ATTR_ERRMODE」SQL実行でエラーが起こった際にどう処理するかを指定します
     //テーブルの作成 
    $sql = "CREATE TABLE IF NOT EXISTS table1"
    ." (" 
    . "id INT AUTO_INCREMENT PRIMARY KEY," 
    . "name char(32)," 
    . "comment TEXT," 
    . "date DATETIME," 
    . "pass TEXT" 
    .");"; 
    $stmt = $pdo->query($sql); 
    
    //編集対象番号が送信されたときの処理
    
    if(isset($_POST["edit"])){ 
            $id = $_POST["edit"]; 
            $sql = 'SELECT * FROM table1 WHERE id=:id'; //送信された番号(id)のデータを取り出す 
            $stmt = $pdo->prepare($sql);                 
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
            $stmt->execute(); //SQLを実行する
            $results = $stmt->fetchAll(); 
            foreach($results as $row){ 
                if($row['id'] == $id){  
                    if(isset($_POST["editpass"]) &&$row['pass']== $_POST["editpass"]){  //パスワードが一致した時
                        $ed_name = $row['name']; 
                        $ed_comment = $row['comment']; 
                        $ed_pass = $row['pass']; 
                    
                        $mode=true;
                        echo "編集モード";
                        
                    }else{ //パスワードが間違っていた場合 
                         $mode=false;
                        echo "パスワードの入力にミスがあります"; 
                    } 
                } 
            } 
        } 
    
   
    
    
    //編集内容の書き込み
    if(isset($_POST["edit_n"]) && $_POST["edit_n"] > 0){ //編集番号が０より大きい.編集番号存在する
        if(isset($_POST["name"]) && isset($_POST["comment"])&& !empty($_POST["pass"])){ //コメントと名前とパスワードがある
            //送信フォーム内容の書き込み 
            $id = $_POST["edit_n"];    //編集する投稿番号 
            $name = $_POST["name"];//編集する名前
            $comment = $_POST["comment"]; //編集するコメント
            $date = date("YmdHis"); //日付
            $pass=$_POST["pass"];//パスワード
             //UPDATE文による編集 (上書きw)
                $sql = 'UPDATE table1 SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id'; 
                $stmt = $pdo->prepare($sql); 
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);                 
                $stmt->bindParam(':name', $name, PDO::PARAM_STR); 
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR); 
                $stmt->bindParam(':date', $date, PDO::PARAM_INT); 
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR); 
                $stmt->execute(); 
            } 
    }else{//新規投稿
        //編集番号が０より大きくなかったら。編集番号存在しない
     if(isset($_POST["name"]) && isset($_POST["comment"])&& !empty($_POST["pass"])){ 
    //送信フォーム内容の書き込み
     //INSERT文による書き込み （追記a）
                $sql = $pdo-> prepare("INSERT INTO table1 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)"); 
                $sql->bindParam(':name', $name, PDO::PARAM_STR); 
                $sql->bindParam(':comment', $comment, PDO::PARAM_STR); 
                $sql->bindParam(':date', $date, PDO::PARAM_INT); 
                $sql->bindParam(':pass', $pass, PDO::PARAM_STR);  
                $name = $_POST["name"]; 
                $comment = $_POST["comment"]; 
                $date = date("YmdHis");
                $pass = $_POST["pass"];  
                $sql->execute();
         
     }
         //削除フォームの書き込み
        elseif(isset($_POST["delete"])&&isset($_POST["delpass"])){//削除番号とパスワードが一致したら
             //削除対象番号が送信された時の処理  
   $id = $_POST["delete"]; //削除番号取得
                $sql = 'SELECT * FROM table1 WHERE id=:id'; 
                $stmt = $pdo->prepare($sql); 
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);  // ←その差し替えるパラメータの値を指定する
                $stmt->execute(); 
                $results = $stmt->fetchAll(); 
                foreach($results as $row){ 
                    if($row['id'] == $id){ 
                        if($row['pass'] == $_POST["delpass"]){ 
     //idもpassも合致した時、DELETE文で削除する 
                            $sql = 'delete from table1 where id=:id'; 
                            $stmt = $pdo->prepare($sql); 
                            $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
                            $stmt->execute(); 
                        }if($row['pass'] !==$_POST["delpass"]){ 
                            echo "パスワードの入力にミスがあります"; 
                        } 
                    } 
                } 
            } 
        } 

  ?>
     <!--投稿フォーム-->
    <form action=""  method="post" > 
        <input type= "hidden" name="edit_n" value=<?php if(isset($id) && isset($mode) && $mode == true){echo $id;}?>>
        <input type= "text" name="name" value=<?php if(isset($ed_name)){echo $ed_name;}else{echo "名前";} ?>> 
        <input type= "text" name="comment" value=<?php if(isset($ed_comment)){echo $ed_comment;}else{echo "コメント";}  ?>> 
        <input type="text" name="pass" value=<?php if(isset($ed_pass)){echo $ed_pass;}else{echo "パスワード";}?>><br>
        <input type="submit" name="submit"> 
        </form> 
    <!--削除フォーム--> 
    <form action=""  method="post" > 
        <input type= "number" name="delete" placeholder="削除対象番号"> 
        <input type="text" name="delpass" placeholder="パスワード"><br>
        <input type="submit" name="submit" value="削除"> 
    </form> 
    <form action=""  method="post" > 
        <input type= "number" name="edit" placeholder="編集対象番号"> 
        <input type="text" name="editpass" placeholder="パスワード"><br>
        <input type="submit" name="submit" value="編集"><br> 
    </form> 
    <?php  //ファイルの書き換えを行なってから表示機能
     //SELECT文を用いてテーブル内の値を表示する 
    $sql = 'SELECT * FROM table1'; 
    $stmt = $pdo->query($sql); 
    $results = $stmt->fetchAll(); 
    foreach($results as $row){ 
        echo $row['id'].','; 
        echo $row['name'].','; 
        echo $row['comment'].',';
        echo $row['date'].'<br>'; 
        //echo $row['pass'].',';    
    echo "<hr>"; 
    } 
    //ここに}を入れないようにする。名前とコメントがあるかの閉じる}だから、これを入れると追記と上書きが同時に行われる。
    ?> 
</body> 
</html> 