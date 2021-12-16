<?php

    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'utf-8');
    }

    // MySQLサーバ接続に必要な値を変数に代入
    $host = 'localhost';
    $username = 'dbuser';
    $password = 'dbpass';
    $db_name = 'bookshelf_final';
    
    // クエリパラメータで飛んできた書籍番号を取得
    $id = $_GET['id'];
    
    // 変数を設定して、MySQLサーバに接続
    $database = mysqli_connect($host, $username, $password, $db_name);
    
    // 接続を確認し、接続できてない場合にはエラーを出力して終了する
    if ($database == false) {
        die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }
    
    // MySQLにutf8で接続するための設定をする
    $charset = 'utf8';
    mysqli_set_charset($database, $charset);
    
    // SQL文の組み立て
	$sql = "SELECT * FROM books WHERE id=?";
	// SQL文実行の準備
    $statement = mysqli_prepare($database, $sql);
    // バインド処理
    mysqli_stmt_bind_param($statement, 'i', $id);
    // 本番実行
    mysqli_stmt_execute($statement);
    // 結果の取得
    $result = mysqli_stmt_get_result($statement);
    // 注目する書籍情報を連想配列として抜き出す
    $book = mysqli_fetch_assoc($result);
    // $book = mysqli_fetch_array($result, MYSQLI_ASSOC);
?>    
<!doctype html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>Bookshelf | カンタン！あなたのオンライン本棚</title>
        <link rel="stylesheet" href="bookshelf.css">
    </head>
    <body>
        <header>
            <div id="header">
                <div id="logo">
                    <a href="./bookshelf_index.php"><img src="./images/logo.png" alt="Bookshelf"></a>
            </div>
        </header>
        <div id="wrapper">
            <div id="main">
                <form action="bookshelf_index.php" method="post" class="form_book" enctype="multipart/form-data">
                    <input type="hidden" name="book_id" value="<?php print h($id); ?>">
                    <div class="book_title">
                        書籍名: <input type="text" name="edit_book_title" value="<?php print h($book['title']); ?>" placeholder="書籍タイトルを入力" value="" required>
                    </div>
                    <div class="book_image">
                        現在の画像: <img src="<?php print h($book['image_url']); ?>" alt="">
                    </div>
                    <div class="book_image">
                        画像: <input type="file" name="edit_book_image">
                    </div>
                    <div class="book_title">
                        更新用パスワード: <input type="password" name="edit_book_password" required>
                    </div>
                    <div class="book_submit">
                        <input type="submit" name="submit_edit_book" value="更新">
                    </div>
                </form>
            </div>
        </div>
        <footer>
            <small>© 2019 Bookshelf.</small>
        </footer>
        <?php 
            // データベースからの取得結果を削除
            mysqli_free_result($result);
            // データベースとの接続を切る
            mysqli_stmt_close($statement);
        ?>
    </body>
</html>    
