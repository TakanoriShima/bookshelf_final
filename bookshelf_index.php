<?php

    // var_dump($_GET);
    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'utf-8');
    }
    // MySQLサーバ接続に必要な値を変数に代入
    $host = 'localhost';
    $username = 'dbuser';
    $password = 'dbpass';
    $db_name = 'bookshelf';
    
    // 変数を設定して、MySQLサーバに接続
    $database = mysqli_connect($host, $username, $password, $db_name);
    
    // 接続を確認し、接続できてない場合にはエラーを出力して終了する
    if ($database == false) {
        die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }
    
    // MySQLにutf8で接続するための設定をする
    $charset = 'utf8';
    mysqli_set_charset($database, $charset);
    
    // ここにMySQLを使った何らかの処理を書く
    
    //bookshelf_form.phpから送られてくる書籍データの登録
    if (array_key_exists('submit_add_book', $_POST)) {
        // まずは送られてきた画像をuploadsフォルダに移動させる
        $file_name = $_FILES['add_book_image']['name'];
        $image_path = './uploads/' . $file_name;
        move_uploaded_file($_FILES['add_book_image']['tmp_name'], $image_path);
        // データベースに書籍を新規登録する
        $sql = 'INSERT INTO books (title, image_url, password, status) VALUES (?, ?, ?, "unread")';
        $statement = mysqli_prepare($database, $sql);
        mysqli_stmt_bind_param($statement, 'sss', $_POST['add_book_title'], $image_path, $_POST['add_book_password']);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    
    // ステータス変更の処理
    if (array_key_exists('submit_book_unread', $_POST)) {
        $sql = 'UPDATE books SET status = "unread" WHERE id = ?';
        $statement = mysqli_prepare($database, $sql);
        mysqli_stmt_bind_param($statement, 'i', $_POST['book_id']);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    elseif (array_key_exists('submit_book_reading', $_POST)) {
        $sql = 'UPDATE books SET status = "reading" WHERE id = ?';
        $statement = mysqli_prepare($database, $sql);
        mysqli_stmt_bind_param($statement, 'i', $_POST['book_id']);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    elseif (array_key_exists('submit_book_finished', $_POST)) {
        $sql = 'UPDATE books SET status = "finished" WHERE id = ?';
        $statement = mysqli_prepare($database, $sql);
        mysqli_stmt_bind_param($statement, 'i', $_POST['book_id']);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    elseif (array_key_exists('submit_book_pending', $_POST)) {
        $sql = 'UPDATE books SET status = "pending" WHERE id = ?';
        $statement = mysqli_prepare($database, $sql);
        mysqli_stmt_bind_param($statement, 'i', $_POST['book_id']);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    
    // 書籍の削除処理
    if (array_key_exists('submit_book_delete', $_POST)) {
        $sql = 'DELETE FROM books WHERE id = ?';
        $statement = mysqli_prepare($database, $sql);
        mysqli_stmt_bind_param($statement, 'i', $_POST['book_id_2']);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    
    //bookshelf_edit.phpから送られてくる書籍データの更新
    if (array_key_exists('submit_edit_book', $_POST)) {
        // まずは送られてきた画像をuploadsフォルダに移動させる
        $file_name = $_FILES['edit_book_image']['name'];
        $image_path = './uploads/' . $file_name;
        move_uploaded_file($_FILES['edit_book_image']['tmp_name'], $image_path);
        // データベースに書籍を新規登録する
        $sql = 'UPDATE books SET title=?, image_url=? WHERE id=?';
        $statement = mysqli_prepare($database, $sql);
        mysqli_stmt_bind_param($statement, 'ssi', $_POST['edit_book_title'], $image_path, $_POST['book_id']);
        mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);
    }
    
    // 未読数のカウント
    $sql = 'SELECT COUNT(*) as count FROM books where status = "unread"';
    $result = mysqli_query($database, $sql);
    $record = mysqli_fetch_assoc($result);
    $count_unread = $record['count'];
    // 読中数のカウント
    $sql = 'SELECT COUNT(*) as count FROM books where status = "reading"';
    $result = mysqli_query($database, $sql);
    $record = mysqli_fetch_assoc($result);
    $count_reading = $record['count'];
    // 読了数のカウント
    $sql = 'SELECT COUNT(*) as count FROM books where status = "finished"';
    $result = mysqli_query($database, $sql);
    $record = mysqli_fetch_assoc($result);
    $count_finished = $record['count'];
    // 保留数のカウント
    $sql = 'SELECT COUNT(*) as count FROM books where status = "pending"';
    $result = mysqli_query($database, $sql);
    $record = mysqli_fetch_assoc($result);
    $count_pending = $record['count'];
    
    // 検索以外のボタンが押されたならば
    if(!array_key_exists('submit_search', $_GET)){
        
        // どのボタンを押したか（どのステージで絞り込みをするか）を判断し、SELECT文を変更する
        if (array_key_exists('submit_only_unread', $_POST)) {
            $sql = 'SELECT * FROM books WHERE status = "unread" ORDER BY created_at DESC';
        }
        elseif (array_key_exists('submit_only_reading', $_POST)) {
            $sql = 'SELECT * FROM books WHERE status = "reading" ORDER BY created_at DESC';
        }
        elseif (array_key_exists('submit_only_finished', $_POST)) {
            $sql = 'SELECT * FROM books WHERE status = "finished" ORDER BY created_at DESC';
        }
        elseif (array_key_exists('submit_only_pending', $_POST)) {
            $sql = 'SELECT * FROM books WHERE status = "pending" ORDER BY created_at DESC';
        }
        else {
            $sql = 'SELECT * FROM books ORDER BY created_at DESC';
        }
        // いづれかの$sqlを実行して$resultに代入する
        $result = mysqli_query($database, $sql);
    
    }else{
        // あいまい検索キーワードの組み立て
        $keyword = '%' . $_GET['keyword'] . '%';
        // SQL文の組み立て
    	$sql = "SELECT * FROM books WHERE title LIKE ? ORDER BY created_at DESC";
    	// SQL文実行の準備
        $statement = mysqli_prepare($database, $sql);
        // バインド処理
        mysqli_stmt_bind_param($statement, 's', $keyword);
        // 本番実行
        mysqli_stmt_execute($statement);
        // 結果の取得
        $result = mysqli_stmt_get_result($statement);

    }
    // MySQLを使った処理が終わると、接続は不要なので切断する
    mysqli_close($database);
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
                <nav style="display: flex">
                    <form action="./bookshelf_index.php" style="margin-right: 20px;"><input type="search" name="keyword" placeholder="書籍名" style="margin-right: 10px;"><button type="submit" name="submit_search" value="検索">検索</button></form>
                    <a href="./bookshelf_form.php"><img src="./images/icon_plus.png" alt="">書籍登録</a>
                </nav>
            </div>
        </header>
        <div id="cover">
            <h1 id="cover_title">カンタン！あなたのオンライン本棚</h1>
            <form action="bookshelf_index.php" method="post">
                <div class="book_status unread active">
                    <input type="submit" name="submit_only_unread" value="未読"><br>
                    <div class="book_count"><?php print h($count_unread); ?></div>
                </div> 
                <div class="book_status reading active">
                    <input type="submit" name="submit_only_reading" value="読中"><br>
                    <div class="book_count"><?php print h($count_reading); ?></div>
                </div>
                <div class="book_status finished active">
                    <input type="submit" name="submit_only_finished" value="読了"><br>
                    <div class="book_count"><?php print h($count_finished); ?></div>
                </div>
                <div class="book_status pending active">
                    <input type="submit" name="submit_only_pending" value="保留"><br>
                    <div class="book_count"><?php print h($count_pending); ?></div>
                </div>
            </form>
        </div>
        <div class="wrapper">
            <div id="main">
                <div id="book_list">
<?php
                    if ($result) {
                        while ($record = mysqli_fetch_assoc($result)) {
                            // 1レコード分の値をそれぞれ変数に代入する
                            $id = $record['id'];
                            $title = $record['title'];
                            $image_url = $record['image_url'];
                            $status = $record['status'];
                            $created_at = $record['created_at'];
?>
                    <a href="bookshelf_edit.php?id=<?php print h($id); ?>">
                        <?php if($status === 'unread'){ ?>
                        <div class="book_item bg_unread">
                        <?php }else if($status === 'reading'){ ?>
                        <div class="book_item bg_reading">
                        <?php }else if($status === 'finished'){ ?>
                        <div class="book_item bg_finished">
                        <?php }else if($status === 'pending'){ ?>
                        <div class="book_item bg_pending">
                        <?php } ?>
                            <div class="book_image">
                                <img src="<?php print h($image_url); ?>" alt="">
                            </div>
                            <div class="book_detail">
                                <div class="book_title">
                                    <?php print h($title); ?>
                                </div>
                                <div class="book_title">
                                    <?php print h($created_at); ?>
                                </div>
                                <form action="bookshelf_index.php" method="post">
                                    <input type="hidden" name="book_id" value="<?php print h($id); ?>">
                                    <div class="book_status unread <?php if ($status == 'unread') print 'active'; ?>">
                                        <input type="submit" name="submit_book_unread" value="未読">
                                    </div>
                                    <div class="book_status reading <?php if ($status == 'reading') print 'active'; ?>">
                                        <input type="submit" name="submit_book_reading" value="読中">
                                    </div>
                                    <div class="book_status finished <?php if ($status == 'finished') print 'active'; ?>">
                                        <input type="submit" name="submit_book_finished" value="読了">
                                    </div>
                                    <div class="book_status pending <?php if ($status == 'pending') print 'active'; ?>">
                                        <input type="submit" name="submit_book_pending" value="保留">
                                    </div>
                                </form>
                                <form action="bookshelf_index.php" method="post">
                                    <input type="hidden" name="book_id_2" value="<?php print h($id); ?>">
                                    <div class="book_delete">
                                        <input type="submit" name="submit_book_delete" value="削除する"><img src="./images/icon_trash.png" alt="icon trash">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </a>
<?php
                }
                mysqli_free_result($result);
            }
?>
                </div>
            </div>
        </div>
        <footer>
            <small>c 2019 Bookshelf.</small>
        </footer>
    </body>
</html>