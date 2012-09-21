MyAuthenticates-for-CakePHP
===========================
CakePHP 2.x.x 向けに作成したカスタム認証オブジェクト

* * *

MultiUsernameAuthenticate
-------------------------
FormAuthenticateクラスを拡張して、usernameとして参照するカラムを
複数指定可能にしたAuthenticateクラス

### インストール
1.	[ZIPファイル](https://github.com/okomeworld/MyAuthenticates-for-CakePHP/zipball/master)をダウンロード
2.	ダウンロードしたファイルを解凍
3.	app/Controller/Component/AuthにMultiUsernameAuthenticate.phpをコピー  
	（または任意のプラグインディレクトリにControllerディレクトリをコピー）

### 使用例
```php
<?php
class AppController extends Controller {
	public $components = array(
		'Session',
		'Auth' => array(
			'authenticate' => array(
				'MultiUsername' => array(
					'username_columns' => array('username','mail')
				),
			),
		),
	);
}
?>
```

