// Description: フォームの入力確認

// フォームの送信ボタンを取得
const submitButton = document.getElementById('confirm');

// 住所変換 ボタン
const zipBtn = document.querySelector('.zip-btn'); 

// フォームの入力要素を取得
const sentakuInputs = document.getElementsByName('sentaku');
const InputName = document.getElementById('InputName');
const InputKana = document.getElementById('InputKana');
const InputTel = document.getElementById('InputTel');
const InputEmail = document.getElementById('InputEmail');
const InputZip = document.getElementById('InputZip');
const InputPrefecture = document.getElementById('InputPrefecture');
const InputCity = document.getElementById('InputCity');
const InputStreet = document.getElementById('InputStreet');
const InputBuilding = document.getElementById('InputBuilding');
const InputFax = document.getElementById('InputFax');
const InputTelSecondary = document.getElementById('InputTelSecondary');
const InputSchool = document.getElementById('InputSchool');
const InputFaculty = document.getElementById('InputFaculty');
const InputGraduate = document.getElementById('InputGraduate');
const Inputinquiry = document.getElementById('Inputinquiry');

// エラーメッセージを表示する要素を取得
const sentakuError = document.getElementById('sentaku_error');
const nameError = document.getElementById('name_error');
const kanaError = document.getElementById('kana_error');
const telError = document.getElementById('Tel_error');
const emailError = document.getElementById('E-mail_error');
const zipError = document.getElementById('zip_error');
const prefectureError = document.getElementById('prefecture_error');
const cityError = document.getElementById('city_error');
const streetError = document.getElementById('street_error');
const buildingError = document.getElementById('building_error');
const faxError = document.getElementById('Fax_error');
const telSecondaryError = document.getElementById('TelSecondary_error');
const graduateError = document.getElementById('graduate_error');
const inquiryError = document.getElementById('inquiry_error');
const txtallError = document.getElementById('txt_all_error');

let error_msg = '';
txtallError.textContent = '';
txtallError.classList.remove('is-error', 'is-success');
document.querySelector('.bl_form.txt_all_error').style.display = 'none';

// 種別 選択肢の入力内容が変更されたとき
sentakuInputs.forEach(inputItem => {
  inputItem.addEventListener('change', () => {
    if (!isValidSentaku(sentakuInputs)) {
      sentakuError.textContent = '種別を選択してください';
      sentakuError.classList.add('is-error');
      sentakuError.classList.remove('is-success');
    } else {
      sentakuError.textContent = 'OK';
      sentakuError.classList.add('is-success');
      sentakuError.classList.remove('is-error');
    }
  });
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

// 郵便番号
InputZip.addEventListener('input', () => {
    if (InputZip.value === '') {
    zipError.classList.remove('is-error', 'is-success');
  } else {
    const raw = InputZip.value;
    // 全角英数字を半角に変換
    const normalized = raw.replace(/[Ａ-Ｚａ-ｚ０-９]/g, s =>
      String.fromCharCode(s.charCodeAt(0) - 0xFEE0)
    );
    // ハイフンは無視（削除）
    const cleaned = normalized.replace(/-/g, '');

    // エラー判定
    if (cleaned === '') {
      zipError.textContent = '郵便番号を入力してください';
      zipError.classList.add('is-error');
      zipError.classList.remove('is-success');
      zipBtn.disabled = true;
    } else if (!isValidZip(cleaned)) {  
      zipError.textContent = '郵便番号の形式が正しくありません（半角7桁）';
      zipError.classList.add('is-error');
      zipError.classList.remove('is-success');
      zipBtn.disabled = true;
    } else {
      zipError.textContent = 'OK';
      zipError.classList.add('is-success');
      zipError.classList.remove('is-error');
      zipBtn.disabled = false;
    }
  }
});

// FAX番号の入力内容が変更されたときにエラーメッセージを表示
InputFax.addEventListener('input', () => {
  if (InputFax.value === '') {
    faxError.textContent = '';
    faxError.classList.remove('is-error', 'is-success');
  } else if (!isValidTel(InputFax.value)) {
    faxError.textContent = 'FAX番号の形式が正しくありません';
    faxError.classList.add('is-error');
    faxError.classList.remove('is-success');
  } else {
    faxError.textContent = 'OK';
    faxError.classList.add('is-success');
    faxError.classList.remove('is-error');
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

// 卒業の入力内容が変更されたとき
InputGraduate.addEventListener('input', () => {
  if (!InputGraduate.value === '') {
    graduateError.textContent = '卒業年月を選択してください。';
    graduateError.classList.add('is-error');
    graduateError.classList.remove('is-success');
  } else if (!isValidGraduate(InputGraduate.value)) {
    graduateError.textContent = '卒業年月の形式が正しくありません（例：2025-03）。';
    graduateError.classList.add('is-error');
    graduateError.classList.remove('is-success');
  } else {
    graduateError.textContent = 'OK';
    graduateError.classList.add('is-success');
    graduateError.classList.remove('is-error');
  }
});

// 自己PR・質問等の入力内容が変更されたとき
Inputinquiry.addEventListener('input', () => {
  if (Inputinquiry.value.trim() === '') {
    inquiryError.textContent = '自己PR・質問等を入力してください。';
    inquiryError.classList.add('is-error');
    inquiryError.classList.remove('is-success');
  } else {
    inquiryError.textContent = 'OK';
    inquiryError.classList.add('is-success');
    inquiryError.classList.remove('is-error');
  }
});

function validateForm() {
  let isValid = true;
  let errmsg = "";
  error_msg = '';
  txtallError.textContent = '';
  txtallError.classList.remove('is-error', 'is-success');
  document.querySelector('.bl_form.txt_all_error').style.display = 'none';

  // 種別 選択肢の入力内容が空の場合はエラーメッセージを表示
  if (!isValidSentaku(sentakuInputs)) {
    errmsg = '種別を選択してください';
    sentakuError.textContent = errmsg;
    sentakuError.classList.add('is-error');
    sentakuError.classList.remove('is-success');
    isValid = false;
    addErrorMessage(errmsg);
  } else {
    sentakuError.textContent = 'OK';
    sentakuError.classList.add('is-success');
    sentakuError.classList.remove('is-error');
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
} else {
    emailError.textContent = 'OK';
    emailError.classList.add('is-success');
    emailError.classList.remove('is-error');
  }

  // 郵便番号
  if (InputZip.value === '') {
    zipError.classList.remove('is-error', 'is-success');
  } else {
    const raw = InputZip.value;
    // 全角英数字を半角に変換
    const normalized = raw.replace(/[Ａ-Ｚａ-ｚ０-９]/g, s =>
      String.fromCharCode(s.charCodeAt(0) - 0xFEE0)
    );
    // ハイフンは無視（削除）
    const cleaned = normalized.replace(/-/g, '');
    if (cleaned === '') {
      errmsg = '郵便番号を入力してください';
      zipError.textContent = errmsg;
      zipError.classList.add('is-error');
      zipError.classList.remove('is-success');
      isValid = false;
      addErrorMessage(errmsg);
    } else if (!isValidZip(cleaned)) {  
      errmsg = '郵便番号の形式が正しくありません（半角7桁）';
      zipError.textContent = errmsg;
      zipError.classList.add('is-error');
      zipError.classList.remove('is-success');
      isValid = false;
      addErrorMessage(errmsg);
    } else {
      zipError.textContent = 'OK';
      zipError.classList.add('is-success');
      zipError.classList.remove('is-error');
    }
  }

  // FAX番号の入力内容
  if (InputFax.value === '') {
    faxError.classList.remove('is-error', 'is-success');
  } else if (!isValidTel(InputFax.value)) {
    errmsg = 'FAX番号の形式が正しくありません';
    faxError.textContent = errmsg;
    faxError.classList.add('is-error');
    faxError.classList.remove('is-success');
    isValid = false;
    addErrorMessage(errmsg);
  } else {
    faxError.textContent = 'OK';
    faxError.classList.add('is-success');
    faxError.classList.remove('is-error');
  }

  // 予備連絡先の入力内容
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

  // 卒業年月の入力内容
  if (InputGraduate.value.trim() === '') {
    errmsg = '卒業年月を選択してください。';
    graduateError.textContent = errmsg;
    graduateError.classList.add('is-error');
    graduateError.classList.remove('is-success');
    isValid = false;
    addErrorMessage(errmsg);
  } else if (!isValidGraduate(InputGraduate.value)) {
    errmsg = '卒業年月を選択してください。';
    graduateError.textContent = errmsg;
    graduateError.classList.add('is-error');
    graduateError.classList.remove('is-success');
    isValid = false;
    addErrorMessage(errmsg);
  } else {
    graduateError.textContent = 'OK';
    graduateError.classList.add('is-success');
    graduateError.classList.remove('is-error');
  }

  // 自己PR・質問等の入力内容が空の場合はエラーメッセージを表示
  if (Inputinquiry.value.trim() === '') {
    errmsg = '自己PR・質問等を入力してください。';
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

// 選択肢の入力内容をチェックする関数
function isValidSentaku(field) {
  let isChecked = false;
  field.forEach(inputItem => {
    if (inputItem.checked) {
      isChecked = true;
    }
  });
  return isChecked;
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

// 郵便番号の形式をチェックする関数
function isValidZip(zip) {
  const regex = /^[0-9]{7}$/;
  return regex.test(zip);
}

// 創業年月の形式をチェックする関数
function isValidGraduate(graduate) {
  const regex = /^\d{4}-(0[1-9]|1[0-2])$/;
  if (!graduate) return false; 
  if (/^\d{4}$/.test(graduate)) return false;
  return regex.test(graduate);
}

// 住所変換
zipBtn.addEventListener('click', function () {
  let zipcode = InputZip.value;
  zipcode = zipcode.replace(/[０-９]/g, s => String.fromCharCode(s.charCodeAt(0) - 65248));
  zipcode = zipcode.replace(/-/g, '');
  zipcode = zipcode.replace(/[^0-9]/g, '');
  if (zipcode.length !== 7) {
    alert('郵便番号は7桁で入力してください');
    return;
  }

  InputPrefecture.value = "";
  InputCity.value =  "";
  InputStreet.value =  "";
  fetch(`https://zipcloud.ibsnet.co.jp/api/search?zipcode=${zipcode}`)
    .then(response => response.json())
    .then(data => {
      if (data.results && data.results.length > 0) {
        const result = data.results[0];
        InputPrefecture.value = result.address1; // 都道府県
        InputCity.value = result.address2;       // 市区町村
        InputStreet.value = result.address3;     // 町域
      } else {
        alert('該当する住所が見つかりませんでした');
      }
    })
    .catch(() => {
      alert('通信エラーが発生しました');
    });
});

// 初期設定
window.addEventListener('DOMContentLoaded', () => {
  const isBack = sessionStorage.getItem('isBack');
  if (!isBack) return;

  setTimeout(() => {
    if (typeof sentakuInputs !== 'undefined') {
      sentakuInputs.forEach(input => {
        input.dispatchEvent(new Event('change'));
      });
    }

    const inputFields = [
      InputName,
      InputKana,
      InputTel,
      InputEmail,
      InputZip,
      InputPrefecture,
      InputCity,
      InputStreet,
      InputBuilding,
      InputFax,
      InputSchool,
      InputFaculty,
      InputGraduate,
      Inputinquiry
    ];

    inputFields.forEach(field => {
      if (field) {
        field.dispatchEvent(new Event('input'));
      }
    });

    sessionStorage.removeItem('isBack');
  }, 0);
});
