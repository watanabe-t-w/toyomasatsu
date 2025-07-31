<?php
  // セッションのセキュリティ設定
  ini_set('session.cookie_httponly', 1); // JavaScriptからのアクセスを禁止
  ini_set('session.cookie_secure', 1);  // HTTPSのみでセッションを送信
  // ini_set('session.use_strict_mode', 1); // セッションIDの固定化を防止　// PHP 7.0以降はデフォルトで有効
  session_name("contactSession");
  session_start();
  session_regenerate_id(true); // セッションIDの再生成

  // CSRF対策
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!isset($_SESSION['token'])) {
      error_log('CSRFトークンが一致しません（セッション無）: ' . $_SERVER['REMOTE_ADDR']);
      exit('不正なアクセスです');
    }
    if (empty($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      error_log('CSRFトークンが一致しません: ' . $_SERVER['REMOTE_ADDR']);
      exit('不正なアクセスです');
    }
  }
  // トークンを変数に代入
  $token = $_POST['token'];

  // 入力値の取得
  $company = $_POST['company'];
  $name = $_POST['name'];
  $kana = $_POST['kana'];
  $tel = $_POST['tel'];
  $email = $_POST['email'];
  $telsecondary = $_POST['telsecondary'];
  $inquiry = $_POST['inquiry'];
  //$sentaku = isset($_POST['sentaku']) ? $_POST['sentaku'] : [];

  // エラーメッセージの初期化
  $errors = [];

  // バリデーション
  if (empty($company)) {
      $errors['company'] = '会社名を入力してください。';
  }
  if (empty($name)) {
      $errors['name'] = '氏名を入力してください。';
  }
  if (!empty($kana)) {
    if (!preg_match('/^[ぁ-ゖー\s]+$/u', $kana)) {
      $errors['kana'] = 'ふりがなはひらがなで入力してください。';
    }
  }
  if (empty($tel)) {
      $errors['tel'] = '電話番号を入力してください。';
  }
  if (!preg_match('/^\d{2,4}-?\d{2,4}-?\d{3,4}$/', $tel)) {
    $errors['tel'] = '電話番号の形式が正しくありません。';
  }
  if (empty($email)) {
      $errors['email'] = 'メールアドレスを入力してください。';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = 'メールアドレスの形式が正しくありません。';
  }
  if (!empty($telsecondary)) {
    if (!preg_match('/^\d{2,4}-?\d{2,4}-?\d{3,4}$/', $telsecondary)) {
      $errors['telsecondary'] = '電話番号の形式が正しくありません。';
    }
  }
  //if (empty($sentaku)) {
  //    $errors['sentaku'] = '複数選択項目を選択してください。';
  //}
  if (empty($inquiry)) {
      $errors['inquiry'] = 'お問い合わせ内容を入力してください。';
  }

  // 添付ファイルの処理
  if (!empty($_FILES['file']['name'])) {
      $file_name = $_FILES['file']['name'];
      $file_size = $_FILES['file']['size'];
      $file_tmp  = $_FILES['file']['tmp_name'];
      $file_type = $_FILES['file']['type'];
      $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

      // ファイル名のサニタイズ
      $file_name = basename($_FILES['file']['name']); // パス情報を除去
      $file_name = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $file_name); // 不正な文字を置換

      // ファイル形式のチェック
      if ($file_ext !== 'pdf') {
          $errors['file'] = 'PDFファイルのみアップロード可能です。';
      }
      // ファイル形式のチェック（MIMEタイプの検証）
      $allowed_mime_types = ['application/pdf'];
      // if (!in_array(mime_content_type($file_tmp), $allowed_mime_types)) {
      //     $errors['file'] = 'PDFファイルのみアップロード可能です。';
      // }
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime_type = finfo_file($finfo, $file_tmp);
      finfo_close($finfo);
      if (!in_array($mime_type, $allowed_mime_types)) {
          $errors['file'] = 'PDFファイルのみアップロード可能です。';
      }

      // ファイルサイズのチェック
      if ($file_size > 5 * 1024 * 1024) {
          $errors['file'] = 'ファイルサイズは5MB以下にしてください。';
      }

      // エラーがなければ一時ディレクトリに保存
      if (empty($errors['file'])) {
          $upload_dir = 'tmp/'; // 一時ディレクトリ
          if (!is_dir($upload_dir)) {
              mkdir($upload_dir, 0777, true); // ディレクトリが存在しない場合は作成
          }
          $file_path = $upload_dir . uniqid() . '_' . $file_name;
          if (!move_uploaded_file($file_tmp, $file_path)) {
              $errors['file'] = 'ファイルのアップロードに失敗しました。';
          }
          // ファイルパスをセッションに保存
          $_SESSION['file_path'] = $file_path;

          // ファイルパスとファイル名をセッションに保存
          $_SESSION['file_path'] = $file_path;
          $_SESSION['file_name'] = $file_name;
      }
  }

  // 新規添付が空でセッションのファイルを使用する場合、セッションからファイル名とファイルパスを取得
  if(empty($_FILES['file']['name']) && !empty($_POST['file_name'])){
      $file_name =  $_SESSION['file_name'];
      $file_path =  $_SESSION['file_path'];
  }

  //  新規添付が空でアップ済みのファイルを削除した場合、セッションからファイル名とファイルパスを削除
  if(empty($_FILES['file']['name']) && empty($_POST['file_name'])){
      unset($_SESSION['file_name']);
      unset($_SESSION['file_path']);
      $file_name =  '';
      $file_path =  '';
  }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>相談・見積もり - 東洋摩擦圧接工業株式会社</title>
  <link rel="stylesheet" href="../assets/css/ress.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@200..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.min.css">
  <!-- Add form.css -->
  <link rel="stylesheet" href="./css/sanitize.css"> 
  <link rel="stylesheet" href="./css/form.css">
<?php
// エラーがある場合、エラーメッセージを表示
if (!empty($errors)) {
?>
  <style>
      /* ここにCSSを記述します */
      label {
          display: block;
          margin-bottom: 5px;
      }
      .error {
          color: red;
      }
  </style>
<?php
}
?>  
</head>
<body class="page-form">
  <div class="l-wrapper">
    <header class="l-header js-header">
      <div class="l-header__logo">
        <a href="../" class="l-header__logo-link">
          <img src="../assets/img/common/logo_header.svg" alt="東洋摩擦圧接工業株式会社" width="271" height="40">
        </a>
      </div>

      <nav class="l-header__nav js-nav">
        <ul class="l-header__menu">
          <li class="l-header__menu-item">
            <a href="../tech/" class="l-header__menu-link"><span class="l-header__menu-text">加工技術</span></a>
            <span class="l-header__menu-icon js-subMenuBtn"></span>
            <ul class="l-header__submenu js-subMenu">
              <li class="l-header__submenu-item">
                <a href="../tech/tech1.html" class="l-header__submenu-link">
                  摩擦圧接加工
                  <svg  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                    <path class="cls-1" d="M10.8,7.1997939H1.2c-.6625,0-1.2-.5375231-1.2-1.2000515s.5375-1.2000515,1.2-1.2000515H10.8c.6625,0,1.2,.5375231,1.2,1.2000515s-.5375,1.2000515-1.2,1.2000515Z"/>
                    <path class="cls-1" d="M5.8898437,12c-.3117187,0-.6226562-.1203177-.8578125-.360953-.4632813-.4742391-.4546875-1.2336467,.01875-1.6969479l4.0328125-3.9423568L5.0507812,2.0573856c-.4734375-.4633011-.4820313-1.2227088-.01875-1.6969479,.4640625-.4734578,1.2226562-.4812707,1.696875-.0187508l4.9101562,4.8002061c.2304687,.2257909,.3609375,.5351792,.3609375,.8578493s-.1304688,.6320584-.3609375,.8578493l-4.9101562,4.8002061c-.2335938,.2281348-.5367187,.3422022-.8390625,.3422022Z"/>
                  </svg>
                </a>
              </li>
              <li class="l-header__submenu-item">
                <a href="../tech/tech2.html" class="l-header__submenu-link">
                  高周波焼入加工
                  <svg  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                    <path class="cls-1" d="M10.8,7.1997939H1.2c-.6625,0-1.2-.5375231-1.2-1.2000515s.5375-1.2000515,1.2-1.2000515H10.8c.6625,0,1.2,.5375231,1.2,1.2000515s-.5375,1.2000515-1.2,1.2000515Z"/>
                    <path class="cls-1" d="M5.8898437,12c-.3117187,0-.6226562-.1203177-.8578125-.360953-.4632813-.4742391-.4546875-1.2336467,.01875-1.6969479l4.0328125-3.9423568L5.0507812,2.0573856c-.4734375-.4633011-.4820313-1.2227088-.01875-1.6969479,.4640625-.4734578,1.2226562-.4812707,1.696875-.0187508l4.9101562,4.8002061c.2304687,.2257909,.3609375,.5351792,.3609375,.8578493s-.1304688,.6320584-.3609375,.8578493l-4.9101562,4.8002061c-.2335938,.2281348-.5367187,.3422022-.8390625,.3422022Z"/>
                  </svg>
                </a>
              </li>
              <!-- <li class="l-header__submenu-item">
                <a href="../tech/tech3.html" class="l-header__submenu-link">
                  高周波焼入作業工程
                  <svg  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                    <path class="cls-1" d="M10.8,7.1997939H1.2c-.6625,0-1.2-.5375231-1.2-1.2000515s.5375-1.2000515,1.2-1.2000515H10.8c.6625,0,1.2,.5375231,1.2,1.2000515s-.5375,1.2000515-1.2,1.2000515Z"/>
                    <path class="cls-1" d="M5.8898437,12c-.3117187,0-.6226562-.1203177-.8578125-.360953-.4632813-.4742391-.4546875-1.2336467,.01875-1.6969479l4.0328125-3.9423568L5.0507812,2.0573856c-.4734375-.4633011-.4820313-1.2227088-.01875-1.6969479,.4640625-.4734578,1.2226562-.4812707,1.696875-.0187508l4.9101562,4.8002061c.2304687,.2257909,.3609375,.5351792,.3609375,.8578493s-.1304688,.6320584-.3609375,.8578493l-4.9101562,4.8002061c-.2335938,.2281348-.5367187,.3422022-.8390625,.3422022Z"/>
                  </svg>
                </a>
              </li> -->
              <li class="l-header__submenu-item">
                <a href="../tech/tech4.html" class="l-header__submenu-link">
                  機械加工
                  <svg  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                    <path class="cls-1" d="M10.8,7.1997939H1.2c-.6625,0-1.2-.5375231-1.2-1.2000515s.5375-1.2000515,1.2-1.2000515H10.8c.6625,0,1.2,.5375231,1.2,1.2000515s-.5375,1.2000515-1.2,1.2000515Z"/>
                    <path class="cls-1" d="M5.8898437,12c-.3117187,0-.6226562-.1203177-.8578125-.360953-.4632813-.4742391-.4546875-1.2336467,.01875-1.6969479l4.0328125-3.9423568L5.0507812,2.0573856c-.4734375-.4633011-.4820313-1.2227088-.01875-1.6969479,.4640625-.4734578,1.2226562-.4812707,1.696875-.0187508l4.9101562,4.8002061c.2304687,.2257909,.3609375,.5351792,.3609375,.8578493s-.1304688,.6320584-.3609375,.8578493l-4.9101562,4.8002061c-.2335938,.2281348-.5367187,.3422022-.8390625,.3422022Z"/>
                  </svg>
                </a>
              </li>
            </ul>
          </li>
          <li class="l-header__menu-item">
            <a href="../work/" class="l-header__menu-link"><span class="l-header__menu-text">実績</span></a>
          </li>
          <li class="l-header__menu-item">

            <a href="../about/" class="l-header__menu-link"><span class="l-header__menu-text">会社案内</span></a>
            <span class="l-header__menu-icon js-subMenuBtn"></span>
            <ul class="l-header__submenu js-subMenu">
              <li class="l-header__submenu-item">
                <a href="../about/" class="l-header__submenu-link">
                  ご挨拶
                  <svg  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                    <path class="cls-1" d="M10.8,7.1997939H1.2c-.6625,0-1.2-.5375231-1.2-1.2000515s.5375-1.2000515,1.2-1.2000515H10.8c.6625,0,1.2,.5375231,1.2,1.2000515s-.5375,1.2000515-1.2,1.2000515Z"/>
                    <path class="cls-1" d="M5.8898437,12c-.3117187,0-.6226562-.1203177-.8578125-.360953-.4632813-.4742391-.4546875-1.2336467,.01875-1.6969479l4.0328125-3.9423568L5.0507812,2.0573856c-.4734375-.4633011-.4820313-1.2227088-.01875-1.6969479,.4640625-.4734578,1.2226562-.4812707,1.696875-.0187508l4.9101562,4.8002061c.2304687,.2257909,.3609375,.5351792,.3609375,.8578493s-.1304688,.6320584-.3609375,.8578493l-4.9101562,4.8002061c-.2335938,.2281348-.5367187,.3422022-.8390625,.3422022Z"/>
                  </svg>
                </a>
              </li>
              <li class="l-header__submenu-item">
                <a href="../about/#companyinfo" class="l-header__submenu-link">
                  会社概要
                  <svg  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                    <path class="cls-1" d="M10.8,7.1997939H1.2c-.6625,0-1.2-.5375231-1.2-1.2000515s.5375-1.2000515,1.2-1.2000515H10.8c.6625,0,1.2,.5375231,1.2,1.2000515s-.5375,1.2000515-1.2,1.2000515Z"/>
                    <path class="cls-1" d="M5.8898437,12c-.3117187,0-.6226562-.1203177-.8578125-.360953-.4632813-.4742391-.4546875-1.2336467,.01875-1.6969479l4.0328125-3.9423568L5.0507812,2.0573856c-.4734375-.4633011-.4820313-1.2227088-.01875-1.6969479,.4640625-.4734578,1.2226562-.4812707,1.696875-.0187508l4.9101562,4.8002061c.2304687,.2257909,.3609375,.5351792,.3609375,.8578493s-.1304688,.6320584-.3609375,.8578493l-4.9101562,4.8002061c-.2335938,.2281348-.5367187,.3422022-.8390625,.3422022Z"/>
                  </svg>
                </a>
              </li>
              <li class="l-header__submenu-item">
                <a href="../about/management.html" class="l-header__submenu-link">
                  経営方針
                  <svg  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                    <path class="cls-1" d="M10.8,7.1997939H1.2c-.6625,0-1.2-.5375231-1.2-1.2000515s.5375-1.2000515,1.2-1.2000515H10.8c.6625,0,1.2,.5375231,1.2,1.2000515s-.5375,1.2000515-1.2,1.2000515Z"/>
                    <path class="cls-1" d="M5.8898437,12c-.3117187,0-.6226562-.1203177-.8578125-.360953-.4632813-.4742391-.4546875-1.2336467,.01875-1.6969479l4.0328125-3.9423568L5.0507812,2.0573856c-.4734375-.4633011-.4820313-1.2227088-.01875-1.6969479,.4640625-.4734578,1.2226562-.4812707,1.696875-.0187508l4.9101562,4.8002061c.2304687,.2257909,.3609375,.5351792,.3609375,.8578493s-.1304688,.6320584-.3609375,.8578493l-4.9101562,4.8002061c-.2335938,.2281348-.5367187,.3422022-.8390625,.3422022Z"/>
                  </svg>
                </a>
              </li>
            </ul>
          </li>
          <li class="l-header__menu-item">
            <a href="../equipment/" class="l-header__menu-link"><span class="l-header__menu-text">設備一覧</span></a>
          </li>
          <li class="l-header__menu-item">
            <a href="../career/" class="l-header__menu-link"><span class="l-header__menu-text">採用情報</span></a>
            <span class="l-header__menu-icon js-subMenuBtn"></span>
            <ul class="l-header__submenu js-subMenu">
              <li class="l-header__submenu-item">
                <a href="../career/" class="l-header__submenu-link">
                  募集要項
                  <svg  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                    <path class="cls-1" d="M10.8,7.1997939H1.2c-.6625,0-1.2-.5375231-1.2-1.2000515s.5375-1.2000515,1.2-1.2000515H10.8c.6625,0,1.2,.5375231,1.2,1.2000515s-.5375,1.2000515-1.2,1.2000515Z"/>
                    <path class="cls-1" d="M5.8898437,12c-.3117187,0-.6226562-.1203177-.8578125-.360953-.4632813-.4742391-.4546875-1.2336467,.01875-1.6969479l4.0328125-3.9423568L5.0507812,2.0573856c-.4734375-.4633011-.4820313-1.2227088-.01875-1.6969479,.4640625-.4734578,1.2226562-.4812707,1.696875-.0187508l4.9101562,4.8002061c.2304687,.2257909,.3609375,.5351792,.3609375,.8578493s-.1304688,.6320584-.3609375,.8578493l-4.9101562,4.8002061c-.2335938,.2281348-.5367187,.3422022-.8390625,.3422022Z"/>
                  </svg>
                </a>
              </li>
              <li class="l-header__submenu-item">
                <a href="../career/01.php" class="l-header__submenu-link">
                  採用フォーム
                  <svg  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                    <path class="cls-1" d="M10.8,7.1997939H1.2c-.6625,0-1.2-.5375231-1.2-1.2000515s.5375-1.2000515,1.2-1.2000515H10.8c.6625,0,1.2,.5375231,1.2,1.2000515s-.5375,1.2000515-1.2,1.2000515Z"/>
                    <path class="cls-1" d="M5.8898437,12c-.3117187,0-.6226562-.1203177-.8578125-.360953-.4632813-.4742391-.4546875-1.2336467,.01875-1.6969479l4.0328125-3.9423568L5.0507812,2.0573856c-.4734375-.4633011-.4820313-1.2227088-.01875-1.6969479,.4640625-.4734578,1.2226562-.4812707,1.696875-.0187508l4.9101562,4.8002061c.2304687,.2257909,.3609375,.5351792,.3609375,.8578493s-.1304688,.6320584-.3609375,.8578493l-4.9101562,4.8002061c-.2335938,.2281348-.5367187,.3422022-.8390625,.3422022Z"/>
                  </svg>
                </a>
              </li>
            </ul>
          </li>
          <li class="l-header__menu-item">
            <a href="../contact/" class="l-header__menu-link">
              <img src="../assets/img/common/icon_mail.svg" alt="" width="26" height="18">
              相談・見積もり
            </a>
          </li>
        </ul>
      </nav>

      <button class="l-header__nav-btn js-navBtn">
        <span class="sr-only">MENU</span>
        <span class="l-header__nav-btn-bar"></span>
      </button>
    </header>
<?php
// エラーがない場合、確認画面を表示
if (empty($errors)) {
?>
    <main>
      <div class="l-mv">
        <h1 class="l-mv__heading">
          <span class="l-mv__heading-ja">相談・見積もり</span>
        </h1>
      </div>
      <div class="l-breadcrumb-wrapper">
        <div class="l-container">
          <ul class="l-breadcrumb">
            <li class="l-breadcrumb__item">
              <a href="../" class="l-breadcrumb__link">TOP</a>
            </li>
            <li class="l-breadcrumb__item">
              相談・見積もり
            </li>
          </ul>
        </div>
      </div>
      <div class="ly_container_contact l-main-contents">
        <h2 class="heading">送信内容のご確認</h2>
        <p class="u-mb30">送信内容をご確認の上、「送信する」ボタンを押してください。</p>
          <form action="send.php" method="post">
            <div class="bl_form">
              <label for="InputCompany" class="bl_form_label"><span class="ttl">会社名</span><span class="must">*必須</span></label>
              <div class="confirm-text"><span data-name="company"><?php echo htmlspecialchars($company, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputName" class="bl_form_label"><span class="ttl">氏名</span><span class="must">*必須</span></label>
              <div class="confirm-text"><span data-name="name"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputKana" class="bl_form_label"><span class="ttl">氏名（ふりがな）</span></label>
              <div class="confirm-text"><span data-name="kana"><?php echo htmlspecialchars($kana, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputTel" class="bl_form_label"><span class="ttl">電話番号</span><span class="must">*必須</span></label>
              <div class="confirm-text"><span data-name="Tel"><?php echo htmlspecialchars($tel, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputEmail" class="bl_form_label"><span class="ttl">メールアドレス</span><span class="must">*必須</span></label>
              <div class="confirm-text"><span data-name="E-mail"><?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputTelSecondary" class="bl_form_label"><span class="ttl">予備連絡先（携帯等）</span></label>
              <div class="confirm-text"><span data-name="TelSecondary"><?php echo htmlspecialchars($telsecondary, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>
            <!-- /.bl_form -->        
            <div class="bl_form">
              <label for="InputTextarea" class="bl_form_label"><span class="ttl">ご質問内容等</span><span class="must">*必須</span></label>
              <div class="confirm-text"><span data-name="inquiry"><?php echo nl2br(htmlspecialchars($inquiry, ENT_QUOTES, 'UTF-8')); ?></span></div>
            </div>
            <!-- /.bl_form -->
            <?php if (!empty($file_name)): ?> 
              <div class="bl_form">
                <label for="InputFile" class="bl_form_label"><span class="ttl">添付ファイル</span></label>
                <div class="confirm-text"><span data-name="file"><?php echo htmlspecialchars($file_name, ENT_QUOTES, 'UTF-8'); ?></span></div>
              </div>
            <?php endif; ?>
            <!-- /.bl_form -->

            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="company" value="<?php echo htmlspecialchars($company, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="kana" value="<?php echo htmlspecialchars($kana, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="tel" value="<?php echo htmlspecialchars($tel, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="telsecondary" value="<?php echo htmlspecialchars($telsecondary, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="inquiry" value="<?php echo htmlspecialchars($inquiry, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="file_name" value="<?php echo htmlspecialchars($file_name, ENT_QUOTES, 'UTF-8'); ?>">

            <div class="btn-wrapper center">
              <button class="contact-btn2 mr10" type="submit" name="back" formaction="index.php">戻る</button>
              <button class="contact-btn2" type="submit" name="transmission" formaction="send.php">送信する</button>
            </div>
          </form>
      </div>
      <!-- /.ly_container_contact -->
    </main>
<?php
} else {
  // エラーがある場合、エラーメッセージを表示
?>
    <main>
      <div class="l-mv">
        <h1 class="l-mv__heading">
          <span class="l-mv__heading-ja">相談・見積もり</span>
        </h1>
      </div>
      <div class="l-breadcrumb-wrapper">
        <div class="l-container">
          <ul class="l-breadcrumb">
            <li class="l-breadcrumb__item">
              <a href="../" class="l-breadcrumb__link">TOP</a>
            </li>
            <li class="l-breadcrumb__item">
              相談・見積もり
            </li>
          </ul>
        </div>
      </div>
      <div class="ly_container_contact l-main-contents">
        <h2>メールフォーム (エラー)</h2>
        <p>入力内容にエラーがあります。修正してください。</p>
        <form action="index.php" class="bl_ksiForm" method="post">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
            <input type="hidden" name="company" value="<?php echo htmlspecialchars($company, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="kana" value="<?php echo htmlspecialchars($kana, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="tel" value="<?php echo htmlspecialchars($tel, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="telsecondary" value="<?php echo htmlspecialchars($telsecondary, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="inquiry" value="<?php echo htmlspecialchars($inquiry, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="file_name" value="<?php echo htmlspecialchars($file_name, ENT_QUOTES, 'UTF-8'); ?>">
            <?php foreach ($errors as $key => $error): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endforeach; ?>
            <button type="submit">戻る</button>
        </form>
      </div>
      <!-- /.ly_container_contact -->
    </main>
<?php
}
?>
    <footer class="l-footer">
      <div class="l-container">
        <div class="l-footer__top">
          <div class="l-footer__top-left">
            <div class="l-footer__logo">
              <img src="../assets/img/common/logo_footer.svg" alt="" width="324" height="54">
            </div>
            <p>
              〒559‐0011 大阪市住之江区北加賀屋4-6-29<br>
              TEL:06-6686-3886<br>
              FAX:06-6686-1131
            </p>
          </div>
          <div class="l-footer__top-right">
            <ul class="l-footer__menu">
              <li class="l-footer__menu-item">
                <a href="../tech/" class="l-footer__menu-link">
                  加工技術
                </a>
  
                <ul class="l-footer__submenu">
                  <li class="l-footer__submenu-item">
                    <a href="../tech/tech1.html" class="l-footer__submenu-link">
                      摩擦圧接加工
                    </a>
                  </li>
                  <li class="l-footer__submenu-item">
                    <a href="../tech/tech2.html" class="l-footer__submenu-link">
                      高周波焼入加工
                    </a>
                  </li>
                  <!-- <li class="l-footer__submenu-item">
                    <a href="../tech/tech3.html" class="l-footer__submenu-link">
                      高周波焼入作業工程
                    </a>
                  </li> -->
                  <li class="l-footer__submenu-item">
                    <a href="../tech/tech4.html" class="l-footer__submenu-link">
                      機械加工
                    </a>
                  </li>
                </ul>
              </li>
              <li class="l-footer__menu-item">
                <a href="../work/" class="l-footer__menu-link">
                  実績
                </a>
              </li>
              <li class="l-footer__menu-item">
                <a href="../about/" class="l-footer__menu-link">
                  会社案内
                </a>
  
                <ul class="l-footer__submenu">
                  <li class="l-footer__submenu-item">
                    <a href="../about/" class="l-footer__submenu-link">
                      ご挨拶
                    </a>
                  </li>
                  <li class="l-footer__submenu-item">
                    <a href="../about/#companyinfo" class="l-footer__submenu-link">
                      会社概要
                    </a>
                  </li>
                  <li class="l-footer__submenu-item">
                    <a href="../about/management.html" class="l-footer__submenu-link">
                      経営方針
                    </a>
                  </li>
                </ul>
              </li>
              <li class="l-footer__menu-item">
                <a href="../equipment/" class="l-footer__menu-link">
                  設備一覧
                </a>
              </li>
              <li class="l-footer__menu-item">
                <a href="../career/" class="l-footer__menu-link">
                  採用情報
                </a>
  
                <ul class="l-footer__submenu">
                  <li class="l-footer__submenu-item">
                    <a href="../career/" class="l-footer__submenu-link">
                      募集要項
                    </a>
                  </li>
                  <li class="l-footer__submenu-item">
                    <a href="../career/01.php" class="l-footer__submenu-link">
                      採用フォーム
                    </a>
                  </li>
                </ul>
              </li>
              <li class="l-footer__menu-item">
                <a href="../contact/" class="l-footer__menu-link">
                  相談・見積もり
                </a>
              </li>
            </ul>
          </div>
        </div>
        <div class="l-footer__bottom">
          <p class="l-footer__copyright">
            ©Toyomasatsu. ALL Rights Reserved.
          </p>

          <ul class="l-footer__bottom-menu">
            <li class="l-footer__bottom-menu-item">
              <a href="../privacy/" class="l-footer__bottom-menu-link">
                プライバシーポリシー
              </a>
            </li>
            <li class="l-footer__bottom-menu-item">
              <a href="../privacy/index2.html" class="l-footer__bottom-menu-link">
                サイトポリシー
              </a>
            </li>
            <li class="l-footer__bottom-menu-item">
              <a href="../sitemap/" class="l-footer__bottom-menu-link">
                サイトマップ
              </a>
            </li>
          </ul>
        </div>
      </div>
    </footer>
  </div>  

<script src='https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js' integrity='sha512-NcZdtrT77bJr4STcmsGAESr06BYGE8woZdSdEgqnpyqac7sugNO+Tr4bGwGF3MsnEkGKhU2KL2xh6Ec+BqsaHA==' crossorigin='anonymous'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/ScrollToPlugin.js' integrity='sha512-nmADveI0ZdXmASlOKcl3+yrEljg2OKbsQSH02R6QBUtDTfdSO81ngCvQVaMOMC+KvewvMtSn0mrHpd45SPTZrQ==' crossorigin='anonymous'></script>
<script src="../assets/js/script.min.js"></script>
</body>
</html>
