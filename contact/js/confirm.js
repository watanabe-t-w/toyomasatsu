// Description: フォームの入力確認

// フォームの送信ボタンを取得
const submitButton = document.getElementById('confirm');

// フォームの入力要素を取得
const InputCompany = document.getElementById('InputCompany');
const InputName = document.getElementById('InputName');
const InputKana = document.getElementById('InputKana');
const InputTel = document.getElementById('InputTel');
const InputEmail = document.getElementById('InputEmail');
const InputTelSecondary = document.getElementById('InputTelSecondary');
const Inputinquiry = document.getElementById('Inputinquiry');
const Inputfile = document.getElementById('Inputfile');

// エラーメッセージを表示する要素を取得
const companyError = document.getElementById('company_error');
const nameError = document.getElementById('name_error');
const kanaError = document.getElementById('kana_error');
const telError = document.getElementById('Tel_error');
const emailError = document.getElementById('E-mail_error');
const telSecondaryError = document.getElementById('TelSecondary_error');
const inquiryError = document.getElementById('inquiry_error');
const inputError = document.getElementById('file_error');
const txtallError = document.getElementById('txt_all_error');

let error_msg = '';
txtallError.textContent = '';
txtallError.classList.remove('is-error', 'is-success');
document.querySelector('.bl_form.txt_all_error').style.display = 'none';

// 会社名の入力内容が変更されたときにエラーメッセージを表示
InputCompany.addEventListener('input', () => {
  if (InputCompany.value === '') {
    companyError.textContent = '会社名を入力してください';
    companyError.classList.add('is-error');
    companyError.classList.remove('is-success');
  } else {
    companyError.textContent = 'OK';
    companyError.classList.add('is-success');
    companyError.classList.remove('is-error');
  }
});

// 氏名の入力内容が変更されたときにエラーメッセージを表示
InputName.addEventListener('input', () => {
  if (InputName.value === '') {
    nameError.textContent = '氏名を入力してください';
    nameError.classList.add('is-error');
    nameError.classList.remove('is-success');
  } else {
    nameError.textContent = 'OK';
    nameError.classList.add('is-success');
    nameError.classList.remove('is-error');
  }
});

// 氏名（ふりがな）の入力内容が変更されたときにエラーメッセージを表示
InputKana.addEventListener('input', () => {
  if (InputKana.value === '') {
    kanaError.textContent = '';
    kanaError.classList.remove('is-error', 'is-success');
  } else if (!isValidKana(InputKana.value)) {
    kanaError.textContent = 'ふりがなは平仮名で入力してください';
    kanaError.classList.add('is-error');
    kanaError.classList.remove('is-success');
  } else {
    kanaError.textContent = 'OK';
    kanaError.classList.add('is-success');
    kanaError.classList.remove('is-error');
  }
});

// 電話番号の入力内容が変更されたときにエラーメッセージを表示
InputTel.addEventListener('input', () => {
  if (InputTel.value === '') {
    telError.textContent = '電話番号を入力してください';
    telError.classList.add('is-error');
    telError.classList.remove('is-success');
  } else if (!isValidTel(InputTel.value)) {
    telError.textContent = '電話番号の形式が正しくありません';
    telError.classList.add('is-error');
    telError.classList.remove('is-success');
  } else {
    telError.textContent = 'OK';
    telError.classList.add('is-success');
    telError.classList.remove('is-error');
  }
});

// メールアドレスの入力内容が変更されたとき
InputEmail.addEventListener('input', () => {
  if (InputEmail.value.trim() === '') {
    emailError.textContent = 'メールアドレスを入力してください';
    emailError.classList.add('is-error');
    emailError.classList.remove('is-success');
  } else if (!isValidEmail(InputEmail.value)) {
      emailError.textContent = 'メールアドレスの形式が正しくありません';
      emailError.classList.add('is-error');
      emailError.classList.remove('is-success');
  } else {
      emailError.textContent = 'OK';
      emailError.classList.add('is-success');
      emailError.classList.remove('is-error');
  }
});

// 予備連絡先の入力内容が変更されたときにエラーメッセージを表示
InputTelSecondary.addEventListener('input', () => {
  if (InputTelSecondary.value === '') {
    telSecondaryError.textContent = '';
    telSecondaryError.classList.remove('is-error', 'is-success');
  } else if (!isValidTel(InputTelSecondary.value)) {
    telSecondaryError.textContent = '予備連絡先の形式が正しくありません';
    telSecondaryError.classList.add('is-error');
    telSecondaryError.classList.remove('is-success');
  } else {
    telSecondaryError.textContent = 'OK';
    telSecondaryError.classList.add('is-success');
    telSecondaryError.classList.remove('is-error');
  }
});

// お問い合わせ内容の入力内容が変更されたとき
Inputinquiry.addEventListener('input', () => {
  if (Inputinquiry.value.trim() === '') {
    inquiryError.textContent = 'お問い合わせ内容を入力してください。';
    inquiryError.classList.add('is-error');
    inquiryError.classList.remove('is-success');
  } else {
    inquiryError.textContent = 'OK';
    inquiryError.classList.add('is-success');
    inquiryError.classList.remove('is-error');
  }
});

// 添付ファイルの入力内容が変更されたとき
Inputfile.addEventListener('change', () => {
  if (Inputfile.files.length > 0) {
      const file = Inputfile.files[0];
      if (file.type !== 'application/pdf') {
          inputError.textContent = 'PDFファイルのみアップロード可能です。';
          inputError.classList.add('is-error');
          inputError.classList.remove('is-success');
      } else if (file.size > 5 * 1024 * 1024) {
          inputError.textContent = 'ファイルサイズは5MB以下にしてください。';
          inputError.classList.add('is-error');
          inputError.classList.remove('is-success');
      } else if (isValidFileName(file.name)) {
          inputError.textContent = '添付ファイル名は半角英数字にしてください';
          inputError.classList.add('is-error');
          inputError.classList.remove('is-success');
      } else {
          inputError.textContent = 'OK';
          inputError.classList.add('is-success');
          inputError.classList.remove('is-error');
      }
  } else {
      inputError.textContent = '';
      inputError.classList.remove('is-error', 'is-success');
  }
});

function validateForm() {
  let isValid = true;
  let errmsg = "";
  error_msg = '';
  txtallError.textContent = '';
  txtallError.classList.remove('is-error', 'is-success');
  document.querySelector('.bl_form.txt_all_error').style.display = 'none';

  // 会社名の入力内容が空の場合はエラーメッセージを表示
  if (InputCompany.value === '') {
    errmsg = '会社名を入力してください';
    companyError.textContent = errmsg;
    companyError.classList.add('is-error');
    companyError.classList.remove('is-success');
    isValid = false;
    addErrorMessage(errmsg);
  } else {
    companyError.textContent = 'OK';
    companyError.classList.add('is-success');
    companyError.classList.remove('is-error');
  }

  // 氏名の入力内容が空の場合はエラーメッセージを表示
  if (InputName.value === '') {
    errmsg = '氏名を入力してください';
    nameError.textContent = errmsg;
    nameError.classList.add('is-error');
    nameError.classList.remove('is-success');
    isValid = false;
    addErrorMessage(errmsg);
  } else {
    nameError.textContent = 'OK';
    nameError.classList.add('is-success');
    nameError.classList.remove('is-error');
  }

  // 氏名（ふりがな）の入力内容が空の場合はエラーメッセージを表示
  if (InputKana.value === '') {
    kanaError.classList.remove('is-error', 'is-success');
  } else if (!isValidKana(InputKana.value)) {
    errmsg = 'ふりがなは平仮名で入力してください';
    kanaError.textContent = errmsg;
    kanaError.classList.add('is-error');
    kanaError.classList.remove('is-success');
    isValid = false;
    addErrorMessage(errmsg);
  } else {
    kanaError.textContent = 'OK';
    kanaError.classList.add('is-success');
    kanaError.classList.remove('is-error');
  }

  // 電話番号の入力内容が空の場合はエラーメッセージを表示
  if (InputTel.value === '') {
    errmsg = '電話番号を入力してください';
    telError.textContent = errmsg;
    telError.classList.add('is-error');
    telError.classList.remove('is-success');
    isValid = false;
    addErrorMessage(errmsg);
  } else if (!isValidTel(InputTel.value)) {
    errmsg = '電話番号の形式が正しくありません';
    telError.textContent = errmsg;
    telError.classList.add('is-error');
    telError.classList.remove('is-success');
    isValid = false;
    addErrorMessage(errmsg);
  } else {
    telError.textContent = 'OK';
    telError.classList.add('is-success');
    telError.classList.remove('is-error');
  }

  // メールアドレスの入力内容が空の場合はエラーメッセージを表示
  if (InputEmail.value.trim() === '') {
    errmsg = 'メールアドレスを入力してください';
    emailError.textContent = errmsg;
    emailError.classList.add('is-error');
    emailError.classList.remove('is-success');
    isValid = false;
    addErrorMessage(errmsg);
  } else if (!isValidEmail(InputEmail.value)) {
      errmsg = 'メールアドレスの形式が正しくありません';
      emailError.textContent = errmsg;
      emailError.classList.add('is-error');
      emailError.classList.remove('is-success');
      isValid = false;
      addErrorMessage(errmsg);
  } else {
      emailError.textContent = 'OK';
      emailError.classList.add('is-success');
      emailError.classList.remove('is-error');
  }

  // 予備連絡先の入力内容が空の場合はエラーメッセージを表示
  if (InputTelSecondary.value === '') {
    telSecondaryError.classList.remove('is-error', 'is-success');
  } else if (!isValidTel(InputTelSecondary.value)) {
    errmsg = '予備連絡先の形式が正しくありません';
    telSecondaryError.textContent = errmsg;
    telSecondaryError.classList.add('is-error');
    telSecondaryError.classList.remove('is-success');
    isValid = false;
    addErrorMessage(errmsg);
  } else {
    telSecondaryError.textContent = 'OK';
    telSecondaryError.classList.add('is-success');
    telSecondaryError.classList.remove('is-error');
  }

  // お問い合わせ内容の入力内容が空の場合はエラーメッセージを表示
  if (Inputinquiry.value.trim() === '') {
    errmsg = 'お問い合わせ内容を入力してください。';
    inquiryError.textContent = errmsg;
    inquiryError.classList.add('is-error');
    inquiryError.classList.remove('is-success');
    isValid = false;
    addErrorMessage(errmsg);
  } else {
    inquiryError.textContent = 'OK';
    inquiryError.classList.add('is-success');
    inquiryError.classList.remove('is-error');
  }

  // 添付ファイルの入力内容が正しくない場合はエラーメッセージを表示
  if (Inputfile.files.length > 0) {
    const file = Inputfile.files[0];
    if (file.type !== 'application/pdf') {
        errmsg = 'PDFファイルのみアップロード可能です。';
        inputError.textContent = errmsg;
        inputError.classList.add('is-error');
        inputError.classList.remove('is-success');
        isValid = false;
        addErrorMessage(errmsg);
    } else if (file.size > 5 * 1024 * 1024) {
        errmsg = 'ファイルサイズは5MB以下にしてください。';
        inputError.textContent = errmsg;
        inputError.classList.add('is-error');
        inputError.classList.remove('is-success');
        isValid = false;
        addErrorMessage(errmsg);
    } else if (isValidFileName(file.name)) {
        errmsg = '添付ファイル名は半角英数字にしてください';
        inputError.textContent = errmsg;
        inputError.classList.add('is-error');
        inputError.classList.remove('is-success');
        isValid = false;
        addErrorMessage(errmsg);
    } else {
        inputError.textContent = 'OK';
        inputError.classList.add('is-success');
        inputError.classList.remove('is-error');
    }
  } else {
      inputError.textContent = '';
  }

  if (!isValid) {
    txtallError.textContent = error_msg;
    txtallError.classList.add('is-error');
    document.querySelector('.bl_form.txt_all_error').style.display = 'block';
  }

  return isValid;
}

function addErrorMessage(errmsg) {
  if (error_msg !== "") error_msg += "\n";
  error_msg += errmsg; 
}

// フォームの送信ボタンをクリックしたときにフォームの入力内容を確認
submitButton.addEventListener('click', (event) => {
  // フォームの入力内容を確認
  if (!validateForm()) {
    // フォームの入力内容が正しくない場合はフォームの送信をキャンセル
    event.preventDefault();
    submitButton.blur();
    return;
  }

  sessionStorage.setItem('isBack', 'true');
});

// メールアドレスの形式をチェックする関数
function isValidEmail(email) {
  const regex = /^[a-zA-Z0-9_+-]+(.[a-zA-Z0-9_+-]+)*@[a-zA-Z0-9-]+(.[a-zA-Z0-9-]+)*\.[a-zA-Z]{2,6}$/;
  return regex.test(email);
}

// 電話番号の形式をチェックする関数
function isValidTel(tel) {
  const regex = /^\d{2,4}-?\d{2,4}-?\d{4}$/;
  return regex.test(tel);
}

// ふりがなの形式をチェックする関数
function isValidKana(kana) {
  const regex = /^[ぁ-んー　 ]+$/;
  return regex.test(kana);
}

// ファイルの名前をチェックする関数
function isValidFileName(name) {
  const regex = /[^a-zA-Z0-9_\.-]/;
  return regex.test(name);
}

// 添付ファイル削除ボタン
// 確認画面から戻ってきた場合の処理
const buttonDel = document.getElementById('button-del');
const blInputfil = document.getElementById('bl_Inputfil');
const blInputfil2 = document.getElementById('bl_Inputfil2');

if ( buttonDel !== null) {
  buttonDel.addEventListener('click', () => {
    blInputfil.remove();
    blInputfil2.classList.add('is-visible');
  });
}

// 初期設定
window.addEventListener('DOMContentLoaded', () => {
  const isBack = sessionStorage.getItem('isBack');
  if (!isBack) return;

  setTimeout(() => {
    const inputFields = [
      InputCompany,
      InputName,
      InputKana,
      InputTel,
      InputEmail,
      InputTelSecondary,
      Inputinquiry,
      Inputfile
    ];

    inputFields.forEach(field => {
      if (field) {
        field.dispatchEvent(new Event('input'));
      }
    });

    sessionStorage.removeItem('isBack');
  }, 0);
});
