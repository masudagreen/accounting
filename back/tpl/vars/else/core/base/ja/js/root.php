<?php
$vars = array(
	'strTitle' => '統制モジュール',
	'varsSystem' => array(
		'status' => array(),
		'num' => array(
			'zIndex' => 0
		),
		'token' => '',
		'id' => array(
			'root' => 'Root',
			'choice' => 'Choice',
			'global' => 'Global',
			'window' => 'Window',
			'popup' => 'Popup',
			'temp' => 'Temp',
			'output' => 'Output',
		),
		'path' => array(
			'post' => 'index.php',
			'file' => 'output.php',
		),
		'flag' => array(
			'ssl' => 0,
			'zIndexCookieUse' => 1,
			'onstageCookieUse' => 1,
			'browser' => 1
		),
		'str' => array(
			'comma' => '，',
			'space' => '　',
			'fail' => 'システムエラー',
			'popUp' => 'ポップアップがブロックされているためエラーが生じたようです。ポップアップを有効にしてページを更新してください。',
			'selectRequire' => '選択が必要なようです。',
			'oldData' => '前提となるデータが陳腐化しているようです。ページ等を更新してください。',
			'8' => '前提となるデータが陳腐化しているようです。ページ等を更新してください。',
			'40' => '前提となるデータが陳腐化しているようです。ページ等を更新してください。',
			'errorRequest' => 'リクエストエラー',
			'maintenance' => '当該モジュールは、メンテナンス中のようです。しばらくしてからアクセスしてください。',
			'errorMail' => 'メール配信が正常に動作しなかったようです。メール認証手続などシステム上必要なメールである場合、システム管理者へ至急連絡してください。',
			'errorConnect' => 'リクエストエラー',
			'errorDataMax' => 'データ保存領域を超過してしまうためリクエストが見送られたようです。',
			'errorSession' => 'ログインセッションが切れているようです。お手数ですが、ページを更新して再度入場し直してください。',
			'errorUnexpected' => '予期しないエラーが発生しました。',
			'strAuthorityNone' => 'アクセス権限がないため閲覧できないようです。',
			'strConfirm' => '【確認】本当によろしいでしょうか？',
		)
	),
	'varsGlobal' => array(
		'varsStatus' => array(
			'numZIndex' => 0,
		),
		'tmplContext' => array(
			'varsStatus' => array(
				'numTop' => 0,
				'numLeft' => 0,
				'flagNow' => '',
			),
			'varsDetail' => array(

			),
			'tmplDetail' => array(
				'id' => '', 'flagCheckUse' => 1, 'flagCheckNow' => 1,
				'strTitle' => '', 'strClass' => 'codeLibBaseImgSheet',
				'vars' => array( 'idTarget' => '',),
				'child' => array(),
			),
		),
		'varsDetail' => array(
			array(
				'flagCheckNow'=> 0,
				'numZIndex'=> 0,
				'numLeft'=> 34,
				'numTop'=> 38,
				'numSort'=> 0,
				'id'=> 'Logout',
				'strTitle'=> 'ログアウト',
				'strClass'=> 'codeCoreLogoutImgIcon',
				'strClassOver'=> 'codeCoreLogoutImgIconOver',
				'strClassSmall'=> 'codeCoreLogoutImgIconSmall',
			),
			array(
				'flagCheckNow'=> 0,
				'numZIndex'=> 0,
				'numLeft'=> 23,
				'numTop'=> 88,
				'numSort'=> 1,
				'id'=> 'Base',
				'strTitle'=> '統制モジュール',
				'strClass'=> 'codeCoreBaseImgIcon',
				'strClassOver'=> 'codeCoreBaseImgIconOver',
				'strClassSmall'=> 'codeCoreBaseImgIconSmall',
			),
		),
	),
	'varsChoice' => array(
		'varsDetail' => array(
			array(
				'id' => 'Account',
				'idModule'  => 'Base',
				'strTitle' => 'アカウント',
				'flagCheckUse' => 0,
				'varsRequest' => array(
					'strClass'  => 'Core',
					'idModule'  => 'Base',
					'strExt'    => 'Account',
					'strChild'  => 'Choice',
					'strFunc'   => 'Js',
				),
			),
			array(
				'id' => 'AccountAll',
				'idModule'  => 'Base',
				'strTitle' => 'アカウント',
				'flagCheckUse' => 0,
				'varsRequest' => array(
					'strClass'  => 'Core',
					'idModule'  => 'Base',
					'strExt'    => 'Account',
					'strChild'  => 'AllChoice',
					'strFunc'   => 'Js',
				),
			),
			array(
				'id' => 'Term',
				'idModule'  => 'Base',
				'strTitle' => '有効期間パターン',
				'flagCheckUse' => 0,
				'varsRequest' => array(
					'strClass'  => 'Core',
					'idModule'  => 'Base',
					'strExt'    => 'Term',
					'strChild'  => 'Choice',
					'strFunc'   => 'Js',
				),
			),
			array(
				'id' => 'Module',
				'idModule'  => 'Base',
				'strTitle' => 'モジュールパターン',
				'flagCheckUse' => 0,
				'varsRequest' => array(
					'strClass'  => 'Core',
					'idModule'  => 'Base',
					'strExt'    => 'Module',
					'strChild'  => 'Choice',
					'strFunc'   => 'Js',
				),
			),
		),
	),
	'varsWindow' => array(
		array(
			'id' => 'Base',
			'strTitle' => '統制モジュール',
			'strClass' => 'codeCoreBaseImgIcon',
			'flagLockUse' => 0,
			'flagLockNow' => '',
			'flagCakeUse' => 1,
			'flagRemoveUse' => 0,
			'flagCoverUse' => 1,
			'flagHideUse' => 1,
			'flagHideNow' => 1,
			'flagFoldUse' => 1,
			'flagFoldNow' => 0,
			'flagMoveUse' => 1,
			'flagZIndexUse' => 1,
			'flagResizeUse' => 1,
			'flagResizeIni' => 'all',
			'flagResizeNow' => 'all',
			'flagSkeletonUse' => 0,
			'flagBootUse' => 1,
			'flagSwitchUse' => 1,
			'flagMenuUse' => 1,
			'flagMenuShowUse' => 1,
			'numWidthTitle' => 0,
			'numLeft' => 50,
			'numTop' => 50,
			'numWidth' => 800,
			'numHeight' => 600,
			'numWidthMin' => 800,
			'numHeightMin' => 600,
			'numZIndex' => 0
		),
	),
	'varsPopup' => array(
		'varsLayout' => array(
			'varsStatus' => array(
				'flagUse' => 1,
				'flagLockUse' => 0,
				'numZIndex' => 0,
			),
			'varsMenu' => array(
				'numWidth' => 200,
				'numHeight' => 100,
			),
			'varsFormat' => array(
				'id' => '',
				'flagType' => 'normalFormat',
				'numHeight' => 0,
				'numWidth' => 0,
				'flagHeaderLeftUse' => 1,
				'strTitleHeaderLeft' => '新着情報',
				'pathImgHeaderLeft' => 'front/else/lib/img/popup/load.png',
				'flagHeaderRightUse' => 1,
				'strTitleHeaderRight' => '',
				'pathImgHeaderRight' => 'front/else/lib/img/popup/remove.png',
				'flagBodyAutoUse' => 1,
				'strBody' => '',
				'pathImgBody' => '',
				'flagFooderUse' => 0,
				'flagFooderLeftUse' => 1,
				'strTitleFooderLeft' => '',
				'pathImgFooderLeft' => '',
				'flagFooderRightUse' => 1,
				'strTitleFooderRight' => '',
				'pathImgFooderRight' => '',
				'flagHeaderLeftWidth' => 1,
				'numWidthHeaderLeft' => 0,
				'flagHeaderRightWidth' => 0,
				'numWidthHeaderRight' => 0,
				'flagFooderLeftWidth' => 0,
				'numWidthFooderLeft' => 0,
				'flagFooderRightWidth' => 0,
				'numWidthFooderRight' => 0,
			),
		),
		'varsDetail' => array(
			'numAll' => 0,
			'strTitle' => 0,
			'stampRegister' => 0
		),
	),
);
