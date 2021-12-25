<?php
    // 不正アクセス対策
    require_once 'login_filter.php';
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
                    <a href="./bookshelf_form.php"><img src="./images/icon_plus.png" alt="">書籍登録</a>
                </nav>
            </div>
        </header>
        <div id="wrapper">
            <div id="main">
                <form action="bookshelf_index.php" method="post" class="form_book" enctype="multipart/form-data">
                    <div class="book_title">
                        書籍名: <input type="text" name="add_book_title" placeholder="書籍タイトルを入力" required>
                    </div>
                    <div class="book_image">
                        画像: <input type="file" name="add_book_image" required>
                    </div>
                    <div class="book_title">
                        更新用パスワード: <input type="password" name="add_book_password" required>
                    </div>
                    <div class="book_submit">
                        <input type="submit" name="submit_add_book" value="登録">
                    </div>
                </form>
            </div>
        </div>
        <footer>
            <small>© 2019 Bookshelf.</small>
        </footer>
    </body>
</html>