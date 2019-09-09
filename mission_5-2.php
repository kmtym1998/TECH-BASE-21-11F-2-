<?php
    $dsn = 'database name';
	$user = 'user name';
	$password = 'password';
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);
    
    //$dropTable = "DROP TABLE users";
    //$pdo->query($dropTable);

    $createTable = <<<EOS
        CREATE TABLE IF NOT EXISTS users(
        id INT AUTO_INCREMENT PRIMARY KEY,
        postNumber INT,
        userName char(32), 
        comment TEXT, 
        postDate TIMESTAMP,
        passwords TEXT
    )
EOS;

    $pdo->query($createTable);

    function recordNum($tableName) {
        $sql = "SELECT count(id) as cnt from" . $tableName;
        $res = $pdo->query($sql);
        $row = $pdo->fetch_assoc($res);
        return $row['cnt'];
    }

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>ミッション５</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

    <h1>あなたの行きたい国はどこ？</h1>
    <?php //編集モードか否か
        if(empty($_POST['editNum_0'])): //否 新規登録  ?>

            <form method="POST" action="mission_5-1.php">
                名前   
                <input type="text" name='name'><br>
                コメントの入力<br>
                <textarea name="comment" cols="100" rows="5" ></textarea><br>
                <br>
                <input type="submit"><br>
                <br>
                削除指定番号
                <input type="tel" name="deleteNum"> <br>
                編集番号指定
                <input type="tel" name="editNum_0"> <br>
                パスワード
                <input type="password" name="password"> <br>
                <input type="submit">
            </form>
    <?php else: //編集モード ?>
        <?php
            $editNum = intval($_POST['editNum_0']);
            //編集番号として入力された投稿を探す
            $selectOrder = "SELECT * FROM users WHERE id = :id";
            $stmt = $pdo->prepare($selectOrder);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $id = $_POST['editNum_0'];
            $stmt->execute();
            $results = $stmt->fetchAll();
        ?>

        <?php if(!empty($results)): ?>
            <form method="POST" action="mission_5-1.php">
                名前
                <input type="text" name='editName' value="<?= $results[0]['userName'] ?>"><br>
                コメントの入力<br>
                <textarea name="editComment" cols="100" rows="5"><?= $results[0]['comment'] ?></textarea><br>
                <br>
                パスワード
                <input type="password" name="password" value="<?= $_POST['password'] ?>">
                <input type="submit" value="編集完了"><br>
                <input type="hidden" name="editNum" value="<?= $editNum ?>">
            </form>
        <?php else: ?>
            <?php header("Location: mission_5-1.php") ?>
        <?php endif ?>
    <?php endif ?>

    <hr>

    <?php

        //削除番号が入力されたかどうか
        if(!empty($_POST['deleteNum'])){ //された
            if(!empty($_POST['password'])){ //パスワードが入力された
                
                //パスワードが一致するかどうか確認
                $selectOrder = "SELECT passwords FROM users WHERE id = :id";
                $stmt = $pdo->prepare($selectOrder);
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                    $id = $_POST['deleteNum'];
                $stmt->execute();
                $results = $stmt->fetchAll();


                if(!empty($results[0])){ //パスワードがデータベースに入っている
                    if($results[0]['passwords'] == $_POST['password']){ //パスワードが一致する
                        //削除の処理
                        $deleteOrder = "DELETE FROM users WHERE id = :id";
                        $stmt = $pdo->prepare($deleteOrder);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                            $id = $_POST['deleteNum'];                
                        $stmt->execute();
                    }else{
                        echo '削除できません';
                        echo "<br>";
                        echo '投稿時にパスワードが入力されていないか、パスワードが一致しないか、投稿番号が存在しません';
                        echo "<br>";
                        echo "<hr>";
                    }
                }else{ //削除できなかった
                    echo '削除できません';
                    echo "<br>";
                    echo '投稿時にパスワードが入力されていないか、パスワードが一致しないか、投稿番号が存在しません';
                    echo "<br>";
                    echo "<hr>";
                }
            }else{
                echo '削除できません';
                echo "<br>";
                echo 'パスワードが入力されていません';
                echo "<br>";
                echo "<hr>";
            }
        }

        if(!empty($_POST['name']) && !empty($_POST['comment'])){ //コメント新規投稿の時の処理            
            $insertOrder = $pdo->prepare("INSERT INTO users(
                userName, comment, postDate, passwords
                )VALUES(:userName, :comment, :postDate, :passwords)
            ");
            $insertOrder->bindParam(':userName', $_POST['name'], PDO::PARAM_STR);
            $insertOrder->bindParam(':comment', $_POST['comment'], PDO::PARAM_STR);
            $insertOrder->bindParam(':postDate', $postDate, PDO::PARAM_STR);
                $postDate = date('Y/m/d H:i:s');
            $insertOrder->bindParam(':passwords', $passwords, PDO::PARAM_STR);
                if(!empty($_POST['password'])){
                    $passwords = $_POST['password'];
                }else{
                    $passwords = '';
                }

            $insertOrder->execute();

        }elseif(!empty($_POST['editName']) && !empty($_POST['editComment'])){ //編集モードだったとき

            //パスワードが一致するかどうか確認
            $selectOrder = "SELECT passwords FROM users WHERE id = :id";
            $stmt = $pdo->prepare($selectOrder);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $id = $_POST['editNum'];
            $stmt->execute();
            $results = $stmt->fetchAll();

            if(!empty($results[0]['passwords'])){ //パスワードがデータベースに入っている(新規の時に入力してる)
                if($results[0]['passwords'] == $_POST['password']){ //パスワードが一致する
                    $updateOrder = 'UPDATE users SET userName=:userName, comment=:comment WHERE id=:id';
                    $stmt = $pdo->prepare($updateOrder);
                    $stmt->bindParam(':userName', $editName, PDO::PARAM_STR);
                        $editName = $_POST['editName'];
                    $stmt->bindParam(':comment', $editComment, PDO::PARAM_STR);
                        $editComment = $_POST['editComment'];
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $id = intval($_POST['editNum']);
                    $stmt->execute();
                }else{
                    echo '編集できません';
                    echo "<br>";
                    echo 'パスワードが一致しないか、投稿時にパスワードが設定されていません';
                    echo "<hr>";
                }
            }else{
                echo '編集できません';
                echo "<br>";
                echo 'パスワードが一致しないか、投稿時にパスワードが設定されていません';
                echo "<hr>";
            }
        }

        //コメントを表示する
        
        $selectAll = 'SELECT * FROM users';
        $stmt = $pdo->query($selectAll);
        $results = $stmt->fetchAll();
        $results = array_reverse($results);
        foreach ($results as $row){
            echo $row['id'].'：';
            echo $row['userName'].'    ';
            echo $row['postDate'] . "<br>";
            echo $row['comment'] . "<br>";
            echo "<hr>";
        }
        ?>

</body>
</html>