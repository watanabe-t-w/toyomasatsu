<?php
  // セッションのセキュリティ設定
  ini_set('session.cookie_httponly', 1); // JavaScriptからのアクセスを禁止
  ini_set('session.cookie_secure', 1);  // HTTPSのみでセッションを送信
  // ini_set('session.use_strict_mode', 1); // セッションIDの固定化を防止  // PHP 7.0以降はデフォルトで有効
  session_name("contactSession");
  session_start();
  session_regenerate_id(true); // セッションIDの再生成

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $_SESSION['is_post_request'] = true;
  } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['is_post_request'])) {
      // POST直後の最初のGETでファイル情報とフラグを消す
      unset($_SESSION['file_name']);
      unset($_SESSION['file_path']);
      unset($_SESSION['is_post_request']);
  }

  // CSRF対策用トークン生成
  if (empty($_SESSION['token'])) {
      // $_SESSION['token'] = bin2hex(random_bytes(32)); // PHP 7.0以降はrandom_bytes()を使用
      $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); // PHP 5.3.7以降はopenssl_random_pseudo_bytes()を使用
  }
  $token = $_SESSION['token'];

  // 添付ファイルの情報がある場合、入力欄に表示
  if (isset($_SESSION['file_name'])) {
      $file_name = $_SESSION['file_name'];
      $file_path = $_SESSION['file_path'];
  } else {
      $file_name = '';
      $file_path = '';
  }
?>
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
  <link rel="stylesheet" href="css/form.css">
  <script src="js/confirm.js" defer></script>
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
        <h2 class="heading">相談・見積もりフォーム</h2>
        <p class="u-mb30">
          下記項目にご記入頂き送信して下さい。<br>
          必須項目は必ずご記入ください。
        </p>
          <form action="confirm.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <input type="hidden" name="token" value="<?php echo $token; ?>">

            <div class="bl_form">
              <label for="InputCompany" class="bl_form_label"><span class="ttl">会社名</span><span class="must">*必須</span></label>
              <input type="text" class="form-control" id="InputCompany" name="company" placeholder="例）東洋摩擦圧接工業株式会社" data-errmsg="会社名が未入力です。" value="<?php echo isset($_POST['company']) ? htmlspecialchars($_POST['company'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
              <div id="company_error" class="form-check form-cautoin text-danger bg-text-danger"></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputName" class="bl_form_label"><span class="ttl">氏名</span><span class="must">*必須</span></label>
              <input type="text" class="form-control" id="InputName" name="name" placeholder="例）東洋　摩擦" data-errmsg="氏名が未入力です。" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
              <div id="name_error" class="form-check form-cautoin text-danger bg-text-danger"></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputKana" class="bl_form_label"><span class="ttl">氏名（ふりがな）：ひらがなで入力してください（例：とうよう まさつ）</span></label>
              <input type="text" class="form-control" id="InputKana" name="kana" placeholder="例）とうよう　まさつ" value="<?php echo isset($_POST['kana']) ? htmlspecialchars($_POST['kana'], ENT_QUOTES, 'UTF-8') : ''; ?>">
              <div id="kana_error" class="form-check form-cautoin text-danger bg-text-danger"></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputTel" class="bl_form_label"><span class="ttl">電話番号：8～12桁の半角数字で入力してください（例：0666863886）</span><span class="must">*必須</span></label>
              <input type="text" class="form-control" id="InputTel" name="tel"  placeholder="例）0666863886" data-errmsg="電話番号が未入力です。" value="<?php echo isset($_POST['tel']) ? htmlspecialchars($_POST['tel'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
              <div id="Tel_error" class="form-check"></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputEmail" class="bl_form_label"><span class="ttl">メールアドレス：半角英数字で入力してください（例：name@company.com）</span><span class="must">*必須</span></label>
              <input type="email" class="form-control sizefull" id="InputEmail" name="email" placeholder="英数半角・例）name@company.com" data-errmsg="メールアドレスが未入力です。" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>" required>
              <div id="E-mail_error" class="form-check form-cautoin text-danger bg-text-danger"></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <label for="InputTelSecondary" class="bl_form_label"><span class="ttl">予備連絡先（携帯等）：8～12桁の半角数字で入力してください（例：0666863886）</span></label>
              <input type="text" class="form-control sizefull" id="InputTelSecondary" name="telsecondary" placeholder="例）0666863886" value="<?php echo isset($_POST['telsecondary']) ? htmlspecialchars($_POST['telsecondary'], ENT_QUOTES, 'UTF-8') : ''; ?>">
              <div id="TelSecondary_error" class="form-check form-cautoin text-danger bg-text-danger"></div>
            </div>
            <!-- /.bl_form -->          
            <div class="bl_form">
              <label for="Inputinquiry" class="bl_form_label"><span class="ttl">ご質問内容等</span><span class="must">*必須</span></label>
              <textarea class="form-control" id="Inputinquiry" name="inquiry" rows="10" placeholder="" data-errmsg="ご質問内容等が未入力です。" required><?php echo isset($_POST['inquiry']) ? htmlspecialchars($_POST['inquiry'], ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
              <div id="inquiry_error" class="form-check"></div>
            </div>
            <!-- /.bl_form -->
            <div class="bl_form">
              <?php if (!empty($file_name)): ?>
                <div id="bl_Inputfil" class="bl_file">
                    <p>添付ファイル： <?php echo htmlspecialchars($file_name, ENT_QUOTES, 'UTF-8'); ?></p>
                    <input type="hidden" name="file_path" value="<?php echo $file_path; ?>">
                    <input type="hidden" name="file_name" value="<?php echo $file_name; ?>">
                    <button id="button-del" type="button">削除</button>
                </div>
                <div id="bl_Inputfil2" class="is-hidden">
                    <label for="Inputfil">添付ファイル (PDF, 最大5MB)：ファイル名は半角英数字にしてください</label>
                    <input type="file" id="Inputfile" name="file" accept=".pdf">
                    <span id="Inputfil-error" class="bl_msgInput"></span>
                </div>
              <?php  else: ?>
                <label for="Inputfile" class="bl_form_label"><span class="ttl">添付ファイル (PDF, 最大5MB)：ファイル名は半角英数字にしてください</span></label>
                <input type="file" class="form-control sizefull" id="Inputfile" name="file"  accept=".pdf" placeholder="">
                <div id="file_error" class="form-check form-cautoin text-danger bg-text-danger"></div>
              <?php endif; ?>
            </div>
            <!-- /.bl_form -->       

            <!-- error msg -->
            <div class="bl_form txt_all_error">
                <div id="txt_all_error" class="form-check txt_all_error"></div>
            </div>
            <!-- /.bl_form -->
            
            <div class="btn-wrapper center">
              <button class="contact-btn" type="submit" id="confirm" name="confirm">お問い合わせ内容を確認</button>
            </div>
    
          </form>
      </div>
      <!-- /.ly_container_contact -->
    </main>

  <!-- <section class="l-contact">
        <div class="l-container">
          <a href="../contact/" class="l-contact__box">
            <div class="l-contact__text">
              <h2 class="l-contact__heading">
                <img src="../assets/img/common/icon_mail.svg" alt="" width="48" height="34">
                相談・見積もり
              </h2>
              <p class="l-contact__desc">
                3営業日以内に<span class="u-nowrap">担当よりご連絡させていただきます。</span>
              </p>
            </div>
            <div class="l-contact__btn-wrapper">
              <span class="c-btn-arrow c-btn-arrow--text-l c-btn-arrow--white l-contact__btn">
                お問い合わせ
                <svg  xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12">
                  <path class="cls-1" d="M10.8,7.1997939H1.2c-.6625,0-1.2-.5375231-1.2-1.2000515s.5375-1.2000515,1.2-1.2000515H10.8c.6625,0,1.2,.5375231,1.2,1.2000515s-.5375,1.2000515-1.2,1.2000515Z"/>
                  <path class="cls-1" d="M5.8898437,12c-.3117187,0-.6226562-.1203177-.8578125-.360953-.4632813-.4742391-.4546875-1.2336467,.01875-1.6969479l4.0328125-3.9423568L5.0507812,2.0573856c-.4734375-.4633011-.4820313-1.2227088-.01875-1.6969479,.4640625-.4734578,1.2226562-.4812707,1.696875-.0187508l4.9101562,4.8002061c.2304687,.2257909,.3609375,.5351792,.3609375,.8578493s-.1304688,.6320584-.3609375,.8578493l-4.9101562,4.8002061c-.2335938,.2281348-.5367187,.3422022-.8390625,.3422022Z"/>
                </svg>
              </span>
            </div>
          </a>
        </div>
      </section> -->

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
