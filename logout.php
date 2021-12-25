<?php
    // セッション開始
    session_start();
    
    // ログインしているユーザー情報を破棄
    $_SESSION['login_user'] = null;
    
    // ログイン画面にリダイレクト
    header('Location: login.php');
    exit;