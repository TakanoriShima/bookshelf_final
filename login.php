<?php
    // 安全対策のための関数
    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'utf-8');
    }
    
    // フラッシュメッセージ表示用変数を準備
    $flash_message = '';
    
    // MySQLサーバ接続に必要な値を変数に代入
    $host = 'localhost';
    $username = 'dbuser';
    $password = 'dbpass';
    $db_name = 'bookshelf_final';
    
    // 変数を設定して、MySQLサーバに接続
    $database = mysqli_connect($host, $username, $password, $db_name);
    
    // 接続を確認し、接続できてない場合にはエラーを出力して終了する
    if ($database == false) {
        die('Connect Error (' . mysqli_connect_errno() . ') ' . mysqli_connect_error());
    }
    
    // MySQLにutf8で接続するための設定をする
    $charset = 'utf8';
    mysqli_set_charset($database, $charset);

    // 新規会員登録
    if (array_key_exists('login', $_POST)) {

        $sql = 'SELECT * FROM users WHERE email=? AND password=?';
        // SQL文実行の準備
        $statement = mysqli_prepare($database, $sql);
        // バインド処理
        mysqli_stmt_bind_param($statement, 'ss', $_POST['email'], $_POST['password']);
        // 本番実行
        mysqli_stmt_execute($statement);
        // 結果の取得
        $result = mysqli_stmt_get_result($statement);
        // 注目する会員情報を連想配列として抜き出す
        $user = mysqli_fetch_assoc($result);
        
        // もし、データベースに該当ユーザーが存在すれば
        if($user !== null){
            // セッション開始
            session_start();
            // セッションにログインユーザー情報を保存
            $_SESSION['login_user'] = $user;
            
            // リダイレクト
            header('Location: bookshelf_index.php');
            exit;
        }else{
            // フラッシュメッセージのセット
            $flash_message = 'ユーザーは見つかりません';
        }
    }
    
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
                <nav>
                    <a href="./signup.php">会員登録</a>
                </nav>
            </div>
        </header>
        <?php if($flash_message !== ''){ ?>
        <div id="flash_message">
            <p><?php print h($flash_message); ?></p>
        </div>
        <?php } ?>
        <div id="wrapper">
            <div id="main">
                <h1 class="title">ログイン</h1>
                <form action="login.php" method="post" class="form_book">
                    <div class="book_image">
                        email: <input type="email" name="email" placeholder="メールアドレスを入力" required>
                    </div>
                    <div class="book_title">
                        パスワード: <input type="password" name="password" required>
                    </div>
                    <div class="book_submit">
                        <input type="submit" name="login" value="ログイン">
                    </div>
                </form>
            </div>
        </div>
        <footer>
            <small>© 2019 Bookshelf.</small>
        </footer>
    </body>
</html>