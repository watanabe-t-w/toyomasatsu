<?php
  // セッションのセキュリティ設定
  ini_set('session.cookie_httponly', 1); // JavaScriptからのアクセスを禁止
  ini_set('session.cookie_secure', 1);  // HTTPSのみでセッションを送信
  // ini_set('session.use_strict_mode', 1); // セッションIDの固定化を防止　// PHP 7.0以降はデフォルトで有効
  session_name("careerSession");
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
  $sentaku = isset($_POST['sentaku']) ? $_POST['sentaku'] : '';
  $name = $_POST['name'];
  $kana = $_POST['kana'];
  $tel = $_POST['tel'];
  $email = $_POST['email'];
  $zip = $_POST['zip'];
  $prefecture = $_POST['prefecture'];
  $city = $_POST['city'];
  $street = $_POST['street'];
  $building = $_POST['building'];
  $fax = $_POST['fax'];
  $telsecondary = $_POST['telsecondary'];
  $school = $_POST['school'];
  $faculty = $_POST['faculty'];
  $graduate = $_POST['graduate'];
  $inquiry = $_POST['inquiry'];

  // エラーメッセージの初期化
  $errors = [];

  // バリデーション
  if (empty($sentaku)) {
      $errors['sentaku'] = '項目を選択してください。';
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
  if (!empty($zip)) {
    // 全角英数字 → 半角英数字に変換（mb_convert_kana の 'a' + 'n' フラグ）
    $zip_normalized = mb_convert_kana($zip, 'a'); // 'a': 全角英数字 → 半角
    // ハイフンを除去
    $zip_cleaned = str_replace('-', '', $zip_normalized);
    if (!preg_match('/^\d{7}$/', $zip_cleaned)) {
        $errors['zipcode'] = '郵便番号の形式が正しくありません（半角7桁）。';
    }
  }
  if (!empty($telsecondary)) {
    if (!preg_match('/^\d{2,4}-?\d{2,4}-?\d{3,4}$/', $telsecondary)) {
      $errors['telsecondary'] = '電話番号の形式が正しくありません。';
    }
  }
  if (empty($graduate)) {
    $errors['graduate'] = '卒業年月を選択してください。';
  } elseif (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $graduate)) {
    $errors['graduate'] = '卒業年月の形式が正しくありません（YYYY-MM）。';
  }
  if (empty($inquiry)) {
      $errors['inquiry'] = '自己PR・質問等を入力してください。';
  }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>採用フォーム - 東洋摩擦圧接工業株式会社</title>
  <link rel="stylesheet" href="../assets/css/ress.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+JP:wght@200..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.min.css">
  <!-- Add form.css -->
  <link rel="stylesheet" href="css/form.css">
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
          <span class="l-mv__heading-ja">採用情報</span><br>
          <span class="l-mv__heading-en">Recruit</span>
        </h1>
      </div>

      <div class="l-breadcrumb-wrapper">
        <div class="l-container">
          <ul class="l-breadcrumb">
            <li class="l-breadcrumb__item">
              <a href="../" class="l-breadcrumb__link">TOP</a>
            </li>
            <li class="l-breadcrumb__item">
              <a href="./" class="l-breadcrumb__link">採用情報</a>
            </li>
            <li class="l-breadcrumb__item">
              採用フォーム
            </li>
          </ul>
        </div>
      </div>

      <div class="ly_container_contact l-main-contents">
        <h2 class="heading">送信内容のご確認</h2>
        <p class="u-mb30">送信内容をご確認の上、「送信する」ボタンを押してください。</p>
          <form action="send.php" method="post">
            <div class="bl_form">
              <label for="InputName" class="bl_form_label"><span class="ttl">種別</span><span class="must">*必須</span></label>
              <div class="confirm-text"><span data-name="sentaku-radio"><?php echo htmlspecialchars($sentaku, ENT_QUOTES, 'UTF-8'); ?></span></div>
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
              <label class="bl_form_label"><span class="ttl">住所</span></label>
              <div class="confirm-text">
                <span data-name="zip"><?php echo htmlspecialchars($zip, ENT_QUOTES, 'UTF-8'); ?></span><br>
                <span data-name="prefecture"><?php echo htmlspecialchars($prefecture, ENT_QUOTES, 'UTF-8'); ?></span> 
                <span data-name="city"><?php echo htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?></span> 
                <span data-name="street"><?php echo htmlspecialchars($street, ENT_QUOTES, 'UTF-8'); ?></span> 
                <span data-name="building"><?php echo htmlspecialchars($building, ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputFax" class="bl_form_label"><span class="ttl">FAX番号</span></label>
              <div class="confirm-text"><span data-name="fax"><?php echo htmlspecialchars($fax, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputTelSecondary" class="bl_form_label"><span class="ttl">予備連絡先（携帯等）</span></label>
              <div class="confirm-text"><span data-name="TelSecondary"><?php echo htmlspecialchars($telsecondary, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputSchool" class="bl_form_label"><span class="ttl">学校名</span></label>
              <div class="confirm-text"><span data-name="school"><?php echo htmlspecialchars($school, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputFaculty" class="bl_form_label"><span class="ttl">専攻学部</span></label>
              <div class="confirm-text"><span data-name="faculty"><?php echo htmlspecialchars($faculty, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputGraduate" class="bl_form_label"><span class="ttl">卒業（卒業予定）</span><span class="must">*必須</span></label>
              <div class="confirm-text"><span data-name="graduate"><?php echo htmlspecialchars($graduate, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>
            <!-- /.bl_form -->
                    
            <div class="bl_form">
              <label for="InputTextarea" class="bl_form_label"><span class="ttl">自己PR・質問等</span><span class="must">*必須</span></label>
              <div class="confirm-text"><span data-name="inquiry"><?php echo htmlspecialchars($inquiry, ENT_QUOTES, 'UTF-8'); ?></span></div>
            </div>
            <!-- /.bl_form -->
    
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="sentaku" value="<?php echo htmlspecialchars($sentaku, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="kana" value="<?php echo htmlspecialchars($kana, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="tel" value="<?php echo htmlspecialchars($tel, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="zip" value="<?php echo htmlspecialchars($zip, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="prefecture" value="<?php echo htmlspecialchars($prefecture, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="city" value="<?php echo htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="street" value="<?php echo htmlspecialchars($street, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="building" value="<?php echo htmlspecialchars($building, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="fax" value="<?php echo htmlspecialchars($fax, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="telsecondary" value="<?php echo htmlspecialchars($telsecondary, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="school" value="<?php echo htmlspecialchars($school, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="faculty" value="<?php echo htmlspecialchars($faculty, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="graduate" value="<?php echo htmlspecialchars($graduate, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="inquiry" value="<?php echo htmlspecialchars($inquiry, ENT_QUOTES, 'UTF-8'); ?>">

            <!-- <div class="bl_form">
              <label for="InputTextarea" class="bl_form_label"><span class="ttl">プライバシーポリシーへの同意</span><span class="must">*必須</span></label>
              
              <p class="mb15">※送信にはプライバシーポリシーへの同意が必要です。</p>
              <div class="bl_privacy">
                <p>新和産業株式会社は、お客様の個人情報を適切に保護することが、弊社にとって重大な責務であると認識しており、個人情報の取扱いについて、個人情報保護に関する法令を遵守し、当サイトに於いて収集した情報は以下のポリシーに従って取り組んでおります。</p>
                <ol><li>弊社は、当サイトから収集したデータは、お問い合わせに関する連絡先として、また資料の送付先としての利用を目的として、データを留め、その範囲内でのみで利用いたします。</li><li>弊社は、その他の場合で利用をする場合には、法令でにより例外として認められている場合を除き、あらかじめご連絡をした上で利用目的を明確にし、本人確認の上利用することとします。</li><li>弊社は、法令により例外として認められている場合並びに以下で定める委託先への提供の場合を除き、利用目的をあらかじめお客様の同意を得ることなく、個人情報を第三者に提供しません。<br>＜委託先への提供＞<br>弊社は、利用目的達成に必要な範囲内において弊社との機密情報保護の契約を行った上で個人情報の取扱い、または管理を委託先業者に委託する場合があります。</li><li>弊社は、お客様の個人情報の取扱いに際して、個人情報の滅失、改ざん、漏えいなどを防止するため、社内規程等に基づき厳重な安全対策を講じます。また、個人データの取扱いを委託する場合には、委託先業者に対し、個人情報の安全管理のために必要かつ適切な監督を行います。</li><li>弊社は、お客様からのご自身の個人情報についての開示、訂正、追加又は削除、利用の停止又は消去、あるいは第三者への提供の停止のお申し出につきましては、法令に基づき、本人確認その他弊社の定める方法により応じます。</li><li>弊社は、5項のお申し出、苦情、相談、その他個人情報に関するお問合せに関しまして次の窓口を設置し、所定の手続きのご説明等をさせて頂きます。<br>＜お問合せ先＞<br>新和産業株式会社　業務部<br>電話06－6683－0701（受付時間、平日9:00～17：20まで）</li><li>弊社は、個人情報に関して適用される法令、その他の規範を遵守するとともに、法令や社会環境の変化に応じて、プライバシーポリシーの内容の一部を改訂させていただくことがございます。</li></ol>
              </div> -->
              <!-- /.bl_privacy -->
              <!-- <div class="confirm-text"><span data-name="sentaku-check"></span></div>
            </div> -->
            <!-- /.bl_form -->
    
            <div class="btn-wrapper center">
              <button class="contact-btn2 mr10" type="submit" name="back" formaction="01.php">戻る</button>
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
          <span class="l-mv__heading-ja">採用情報</span><br>
          <span class="l-mv__heading-en">Recruit</span>
        </h1>
      </div>

      <div class="l-breadcrumb-wrapper">
        <div class="l-container">
          <ul class="l-breadcrumb">
            <li class="l-breadcrumb__item">
              <a href="../" class="l-breadcrumb__link">TOP</a>
            </li>
            <li class="l-breadcrumb__item">
              <a href="./" class="l-breadcrumb__link">採用情報</a>
            </li>
            <li class="l-breadcrumb__item">
              採用フォーム
            </li>
          </ul>
        </div>
      </div>
      <div class="ly_container_contact l-main-contents">
        <h2>メールフォーム (エラー)</h2>
        <p>入力内容にエラーがあります。修正してください。</p>
        <form action="01.php" class="bl_ksiForm" method="post">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="kana" value="<?php echo htmlspecialchars($kana, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="tel" value="<?php echo htmlspecialchars($tel, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="zip" value="<?php echo htmlspecialchars($zip, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="prefecture" value="<?php echo htmlspecialchars($prefecture, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="city" value="<?php echo htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="street" value="<?php echo htmlspecialchars($street, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="building" value="<?php echo htmlspecialchars($building, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="fax" value="<?php echo htmlspecialchars($fax, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="telsecondary" value="<?php echo htmlspecialchars($telsecondary, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="school" value="<?php echo htmlspecialchars($school, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="faculty" value="<?php echo htmlspecialchars($faculty, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="graduate" value="<?php echo htmlspecialchars($graduate, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="inquiry" value="<?php echo htmlspecialchars($inquiry, ENT_QUOTES, 'UTF-8'); ?>">
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