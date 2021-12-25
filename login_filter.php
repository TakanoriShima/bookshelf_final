<?php
    // セッション開始
    session_start();
    
    // ログインしているユーザー情報をセッションから取得
    $login_user = $_SESSION['login_user'];
    
    // ログインしているユーザーがいなければ
    if($login_user === null){
        // ログイン画面にリダイレクト
        header('Location: login.php');
        exit;
    }