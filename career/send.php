<?php
  // セッションのセキュリティ設定
  ini_set('session.cookie_httponly', 1); // JavaScriptからのアクセスを禁止
  ini_set('session.cookie_secure', 1);  // HTTPSのみでセッションを送信
  // ini_set('session.use_strict_mode', 1); // セッションIDの固定化を防止
  session_name("careerSession");
  session_start();
  session_regenerate_id(true); // セッションIDの再生成

  // CSRF対策
  if(!isset($_SESSION['token'])) {
      error_log('CSRFトークンが一致しません（セッション無）: ' . $_SERVER['REMOTE_ADDR']);
      exit('不正なアクセスです');
  }
  if (empty($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      error_log('CSRFトークンが一致しません: ' . $_SERVER['REMOTE_ADDR']);
      exit('不正なアクセスです');
  }

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

  if (!empty($errors)) {
      // エラー処理
      // $_SESSION['errors'] = $errors;
      // header('Location: form.php');
      error_log('送信内容エラー' . print_r($errors, true)); // エラーログに出力
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メールフォーム (エラー)</title>
    <link rel="stylesheet" href="./css/sanitize.css">
    <link rel="stylesheet" href="./css/form.css">
</head>
<body>
  <div class="ly_container">
    <h1>メールフォーム (エラー)</h1>
    <p>送信内容にエラーがあります。お手数ですが最初からやり直してください。</p>
    <form action="01.php" method="post">
        <?php foreach ($errors as $key => $error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endforeach; ?>
        <button type="submit">戻る</button>
    </form>
  </div>
</body>
</html>
<?php
    exit; // メール送信処理を中断
}

// PHPMailerのインスタンス生成
require '../contact/phpmailer/src/PHPMailer.php';
require '../contact/phpmailer/src/SMTP.php';
require '../contact/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

// print_r($mail); // PHPMailerのインスタンスを確認
// echo '<hr>TEST4!!!!<br>';
try {
    // サーバーの設定
    // $mail->SMTPDebug = 2;                      // デバッグ用 (0: オフ, 1: クライアント, 2: クライアントとサーバー)
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->isSMTP();                                            // SMTPを使用
    $mail->Host       = 'mail1003.conoha.ne.jp';                     // SMTPサーバー (例: smtp.example.com)
    $mail->SMTPAuth   = true;                                   // SMTP認証を有効化
    $mail->Username   = 'demo@ksinet-web.com';                     // SMTPユーザー名
    $mail->Password   = 'ot/88879%ConoMail';                 // SMTPパスワード
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // TLS暗号化を有効化
    // $mail->Port       = 587;                                    // TCPポート (TLSの場合は587)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;         // SSL暗号化を有効化
    $mail->Port       = 465;                                    // TCPポート (SSLの場合は465)

    // 受信者設定
    $mail->setFrom('demo@ksinet-web.com', '東洋摩擦圧接工業株式会社'); // 送信元メールアドレスと名前

    // 受信者メールアドレスと名前 (管理者)
    $adminEmails  = [
        'sakakibara-t@ksinet.co.jp',
        'asada-k@ksinet.co.jp',
        'watanabe-t+admin@ksinet.co.jp',
    ];
    foreach ($adminEmails as $adminemail) {
        $mail->addAddress($adminemail);
    }

    // $mail->addReplyTo('info@example.com', 'Information');    // 返信先メールアドレスと名前
    // $mail->addCC('cc@example.com');                           // CCメールアドレス
    // $mail->addBCC('bcc@example.com');                          // BCCメールアドレス

    // 添付ファイル
    if (!empty($file_path)) {
        $mail->addAttachment($file_path);         // 添付ファイルを追加
    }

    // 送信者情報
    $Browser = $_SERVER["HTTP_USER_AGENT"];
    $Ip = $_SERVER["REMOTE_ADDR"];
    $Host = gethostbyaddr($Ip);
    $org_timezone = date_default_timezone_get();
    date_default_timezone_set('Asia/Tokyo'); //タイムゾーンを日本に
    $Datetime = date("Y年n月j日 H:i:s");
    date_default_timezone_set($org_timezone); //タイムゾーン戻す

    $companyInfo = ""
            . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
            . "東洋摩擦圧接工業株式会社\n"
            . "〒559-0011 大阪市住之江区北加賀屋4-6-29（本社・工場）\n"
            . "TEL：06-6686-3886\n"
            . "FAX：06-6686-1131\n"
            . "━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            
    $mailInfo = ""
              . "送信日時: " . $Datetime . "\n"
              . "送信者IP: " . $Ip . "\n"
              . "送信者ホスト: " . $Host . "\n"
              . "送信者ブラウザ: " . $Browser . "\n";

    // コンテンツ
    $mailBody = htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "様より、採用の応募がありました。\n"
              . "--------------------------------------------------\n"
              . "【氏名】\n" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "\n\n"
              . "【氏名（ふりがな）】\n" . htmlspecialchars($kana, ENT_QUOTES, 'UTF-8') . "\n\n"
              . "【電話番号】\n" . htmlspecialchars($tel, ENT_QUOTES, 'UTF-8') . "\n\n"
              . "【メールアドレス】\n" . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "\n\n"
              . "【郵便番号】\n" . htmlspecialchars($zip, ENT_QUOTES, 'UTF-8') . "\n\n"
              . "【住所】\n" . htmlspecialchars($prefecture, ENT_QUOTES, 'UTF-8')
                            . htmlspecialchars($city, ENT_QUOTES, 'UTF-8')
                            . htmlspecialchars($street, ENT_QUOTES, 'UTF-8')
                            . htmlspecialchars($building, ENT_QUOTES, 'UTF-8') . "\n\n"
              . "【FAX番号】\n" . htmlspecialchars($fax, ENT_QUOTES, 'UTF-8') . "\n\n"
              . "【予備連絡先（携帯等）】\n" . htmlspecialchars($telsecondary, ENT_QUOTES, 'UTF-8') . "\n\n"
              . "【学校名】\n" . htmlspecialchars($school, ENT_QUOTES, 'UTF-8') . "\n\n"
              . "【専攻学部】\n" . htmlspecialchars($faculty, ENT_QUOTES, 'UTF-8') . "\n\n"
              . "【卒業（卒業予定）】\n" . htmlspecialchars($graduate, ENT_QUOTES, 'UTF-8') . "\n\n"
              . "【自己PR・質問等】\n" . htmlspecialchars($inquiry, ENT_QUOTES, 'UTF-8');
    $mailBody .= "\n\n" . $companyInfo;
    $mailBody .= "\n" . $mailInfo;

    $mail->isHTML(false); // テキスト形式で送信
    $mail->Subject = '【採用フォーム】応募';
    $mail->Body = $mailBody;
    
    $mail->send();
    // echo 'Message has been sent';

    // 自動返信メール設定
    $auto_reply_subject = '採用へのご応募ありがとうございます【東洋摩擦圧接工業株式会社】';
    $auto_reply_body = "ご応募いただき、ありがとうございます。";
    $auto_reply_body .= "\n\n内容を確認後、担当者よりご連絡いたします。";
    $auto_reply_body .= "\n\n連絡が無い場合やお急ぎの場合は";
    $auto_reply_body .= "\n下記連絡先までご連絡お願いいたします。";
    $auto_reply_body .= "\n\n" . $companyInfo;
    $auto_reply_body .= "\n" . $mailInfo;

    $mail->clearAddresses(); // 受信者設定をクリア
    $mail->clearAttachments(); // 添付ファイル設定をクリア
    $mail->addAddress(htmlspecialchars($email, ENT_QUOTES, 'UTF-8')); // フォーム入力者のメールアドレス

    $mail->Subject = $auto_reply_subject;
    $mail->Body = $auto_reply_body;

    $mail->send();
    // echo 'Auto reply message has been sent';
    
    // セッションを破棄
    $_SESSION = array();
    session_destroy();

    // リダイレクト
    header('Location: thanks.html');
    exit;

} catch (Exception $e) {
    error_log('メール送信エラー: ' . $mail->ErrorInfo);
    echo '<p>メール送信中にエラーが発生しました。後ほど再度お試しください。</p>';
    print_r($e->getMessage()); // エラーメッセージを表示 (デバッグ用)
    echo 'メール送信エラー: ' . $mail->ErrorInfo; // エラーメッセージを表示 (デバッグ用)
    exit;
}
