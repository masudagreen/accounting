<?php

$vars = array(
	'varsRule'  => array(),
	'varsCheck'  => array(),
	'varsItem'  => array(
		'varsId' => array(
			'id' => '識別番号',
			'stampBook' => '取引日時',
			'flagFiscalReport' => '決算整理仕訳',
			'strTitle' => '摘要',
			'idAccountTitleDebit' => '借方勘定科目',
			'flagFSDebit' => '借方F/S',
			'numValueDebit' => '借方金額',
			'idSubAccountTitleDebit' => '借方補助科目',
			'idDepartmentDebit' => '借方部門',
			'flagConsumptionTaxDebit' => '借方消費税区分',
			'numRateConsumptionTaxDebit' => '借方消費税率',
			'flagConsumptionTaxWithoutCalcDebit' => '借方消費税入力方法',
			'numValueConsumptionTaxDebit' => '借方消費税金額',

			'idAccountTitleCredit' => '貸方勘定科目',
			'flagFSCredit' => '貸方F/S',
			'numValueCredit' => '貸方金額',
			'idSubAccountTitleCredit' => '貸方補助科目',
			'idDepartmentCredit' => '貸方部門',
			'flagConsumptionTaxCredit' => '貸方消費税区分',
			'numRateConsumptionTaxCredit' => '貸方消費税率',
			'flagConsumptionTaxWithoutCalcCredit' => '貸方消費税入力方法',
			'numValueConsumptionTaxCredit' => '貸方消費税金額',

			'idAccount' => '担当者',
			'arrSpaceStrTag' => 'タグ',
		),
		'varsComment' => array(
			'strRowMax' => '仕訳行数は、10行までが上限となっています。',
			'strSpaceMax' => '許容データ領域をオーバーしているため登録できないようです。',
			'strUploadError' => 'アップロードに失敗しました。',
			'strUploadSize' => '管理者が許容しているアップロードサイズを超過しているため登録できないようです。',
			'strConvert' => '読み込んだデータに文字化け又は未知のデータがあるようです。<br>※Linuxコマンドを使用しているためOSは推奨環境であるLinuxを使用してください。<br>※インポートデータをあらかじめUTF-8にしておくと文字化けリスクを低減できます。',
			'strSum' => '貸借合計金額が一致しないようです。',
			'strSumMax' => '貸借合計金額は、99,999,999,999が上限となっています。',
			'strStrMax' => '文字数が許容範囲を超えているようです。',

			'strDebit' => '借方に記入漏れの仕訳があるようです。',
			'strCredit' => '貸方に記入漏れの仕訳があるようです。',
			'strStatus' => '識別番号【 <%replace%> 】<br>',
			'strIdBlank' => '識別番号がない仕訳があるようです。',
			'strFormat' => ' : フォーマットが正しくないようです。',
			'strTime' => ' : 当期の会計期間に属さないようです。',
			'strTime2' => ' : 中間決算整理仕訳は当期の会計期間に属さないようです。',
			'strMust' => ' : 記入が必要なようです。',
			'strTaxFree' => ' : 消費税の事業者区分が免税設定になっているため当該勘定科目はインポートできないようです。',
			'strNone' => ' : 記入されているものが存在しないようです。',
			'strTax' => ' : 消費税額が入力金額を超過しているようです。',
			'strTaxRate' => ' : 予期しない消費税率が設定されているようです。',
			'strTaxRatePre' => ' : 施行前の消費税率が設定されているようです。',
			'strMonetaryClaim' => '「金銭債権譲渡」は2014年4月1日以後から設定できるようです。',
			'strTaxOut' => ' : 別記の場合、消費税額は0にしなければならないようです。',
			'strAccountTitle' => ' : 勘定科目がないためエラーになっているようです。',
			'strAuthority' => ' : 必要なアクセス権限が付されていないようです。',
			'strAuthorityEntity' => ' : 当該事業体へのアクセス権限が付されていないようです。',
			'strNumValue' => ' : 勘定科目が設定されている場合は、1以上を設定する必要があるようです。',
		),
		'varsFiscalReport'  => array(
			'f1' => '年決',
			'f21' => '中決',
		),
	),
	'pathCss' => '',
	'portal' => array(
		'varsNavi' => array(),
		'varsList' => array(),
		'varsDetail' => array(
			'varsStart' => array(
				'strTitle' => '外部仕訳インポート',
				'strClass' => 'codeLibBaseImgDb',
				'varsEdit' => array( 'flagReloadUse' => 1, ),
			),
			'varsEnd' => array(
				'strTitle' => '処理完了',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array( 'flagReloadUse' => 0, ),
				'varsBtn' => array(
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Back',
						'vars' => array( 'flagBack' => 1, ),
						'strTitle' => '戻る',
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Hide',
						'vars' => array( 'flagHide' => 1, ),
						'strTitle' => '閉じる',
					),
				),
				'varsDetail' => array(
					array(
						'flagMustUse' => 0,
						'id' => 'End',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 0,
						'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '処理が無事完了しました。',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
						'flagFoldUse' => 0, 'flagFoldNow' => 0,
					),
				),
			),
			'varsEndDefer' => array(
				'strTitle' => '処理完了',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array( 'flagReloadUse' => 0, ),
				'varsBtn' => array(
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Back',
						'vars' => array( 'flagBack' => 1, ),
						'strTitle' => '戻る',
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Hide',
						'vars' => array( 'flagHide' => 1, ),
						'strTitle' => '閉じる',
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Defer',
						'vars' => array( 'flagDefer' => 1, ),
						'strTitle' => '留保ログ',
					),
				),
				'varsDetail' => array(
					array(
						'flagMustUse' => 0,
						'id' => 'End',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 0,
						'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '仕訳の新規作成が留保されたものがあるようです。留保中の仕訳は、収支管理の基本メニューにある『 留保ログ 』にあるので下記ボタンをクリックして呼び出したウィンドウの案内に従ってください。',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
						'flagFoldUse' => 0, 'flagFoldNow' => 0,
					),
				),
			),
			'varsEndDeferReject' => array(
				'strTitle' => '処理完了',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array( 'flagReloadUse' => 0, ),
				'varsBtn' => array(
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Back',
						'vars' => array( 'flagBack' => 1, ),
						'strTitle' => '戻る',
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Hide',
						'vars' => array( 'flagHide' => 1, ),
						'strTitle' => '閉じる',
					),
				),
				'varsDetail' => array(
					array(
						'flagMustUse' => 0,
						'id' => 'End',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 0,
						'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '仕訳の新規作成が留保されたものがあるようです。留保中の仕訳は、収支管理の基本メニューにある『 留保ログ 』にありますが、あなたには閲覧する権限が付与されていないようです。然るべき担当者に対処を依頼してください。',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
						'flagFoldUse' => 0, 'flagFoldNow' => 0,
					),
				),
			),
			'varsAccountTitle' => array(
				'strTitle' => '勘定科目作成',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array( 'flagReloadUse' => 0, ),
				'varsBtn' => array(
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Back',
						'vars' => array( 'flagBack' => 1, ),
						'strTitle' => '戻る',
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Hide',
						'vars' => array( 'flagHide' => 1, ),
						'strTitle' => '閉じる',
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'AccountTitle',
						'vars' => array( 'flagAccountTitle' => 1, ),
						'strTitle' => '勘定科目作成',
					),
				),
				'templateDetail' => array(
					array(
						'flagMustUse' => 0,
						'id' => 'DummyStatus',
						'strTitle' => '',
						'strExplain' => '',
						'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 0,
						'flagCommentUse' => 1, 'strCommentTitle' => '<span class="codeLibBaseFontOrange" style="float:none;">インポートデータに未知の勘定科目が確認されました。</span>', 'strComment' => '',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 1000,
						'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
						'varsTmpl' => array(),
					),
					array(
						'flagMustUse' => 1,
						'id' => 'StrTitle',
						'strTitle' => '勘定科目',
						'strExplain' => 'これから作成する勘定科目を選択してください。',
						'value' => 0,
						'flagErrorNow' => 0,
						'arrayError' => array(
							array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
						),
						'flagContentUse' => 0,
						'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
						'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
						'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
					),
					array(
						'flagMustUse' => 1,
						'id' => 'IdAccountTitle',
						'strTitle' => '区分',
						'strExplain' => 'これから作成する勘定科目の挿入先を指定してください。',
						'value' => 0,
						'flagErrorNow' => 0,
						'arrayError' => array(
							array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
						),
						'flagContentUse' => 0,
						'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
						'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
						'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
					),
				),
				'varsDetail' => array(),
			),
			'varsError' => array(
				'strTitle' => 'コメント',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array( 'flagReloadUse' => 0, ),
				'varsBtn' => array(
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Back',
						'vars' => array( 'flagBack' => 1, ),
						'strTitle' => '戻る',
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Hide',
						'vars' => array( 'flagHide' => 1, ),
						'strTitle' => '閉じる',
					),
				),
				'templateDetail' => array(
					array(
						'flagMustUse' => 0,
						'id' => 'End',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 0,
						'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
						'flagFoldUse' => 0, 'flagFoldNow' => 0,
						'varsTmpl' => array(
							'strTitle' => '【<%replace%>】',
							'strAccountTitle' => 'インポートデータに未知の勘定科目が確認されましたが、新たな勘定科目を作成する権限がないようです。権限がないため勘定科目が作成できない旨を管理者に連絡してください。',
							'strSubAccountTitle' => 'インポートデータに未知の補助科目が確認されましたが、新たな補助科目を作成する権限がないようです。権限がないため補助科目が作成できない旨を管理者に連絡してください。',
							'strTitleSubAccountTitle' => 'インポートデータで使われている補助科目のタイトル名が既にシステム上で使用されているようです。インポートデータにある以下の補助科目のタイトル名を別のタイトル名に書き換えてください。<br><%replace%>',
							'strDepartment' => 'インポートデータに未知の部門が確認されましたが、新たな部門を作成する権限がないようです。権限がないため部門が作成できない旨を管理者に連絡してください。',
							'strTitleDepartment' => 'インポートデータで使われている部門のタイトル名が既にシステム上で使用されているようです。インポートデータにある以下の部門のタイトル名を別のタイトル名に書き換えてください。<br><%replace%>',
						),
					),
				),
				'varsDetail' => array(),
			),
			'varsMake' => array(
				'strTitle' => 'インポート準備完了',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array( 'flagReloadUse' => 0, ),
				'varsBtn' => array(
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Back',
						'vars' => array( 'flagBack' => 1, ),
						'strTitle' => '戻る',
					),
					array(
						'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
						'id' => 'Hide',
						'vars' => array( 'flagHide' => 1, ),
						'strTitle' => '閉じる',
					),
				),
				'templateDetail' => array(
					array(
						'flagMustUse' => 0,
						'id' => 'End',
						'strTitle' => '', 'strExplain' => '', 'value' => '',
						'flagErrorNow' => 0,
						'arrayError' => array(),
						'flagContentUse' => 0,
						'flagCommentUse' => 1, 'strCommentTitle' => '', 'strComment' => '',
						'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 0,
						'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
						'arrayOption' => array(),
						'flagFoldUse' => 0, 'flagFoldNow' => 0,
						'varsTmpl' => array(
							'strAddAccountTitle' => 'インポートはまだされていません。<br>インポートデータにあった勘定科目の作成が完了しました。もう一度、戻るボタンを押してインポート作業をし直してください。',
							'strAddAccountTitleHoujin' => 'インポートはまだされていません。<br>インポートデータにあった勘定科目の作成が完了しましたが、まだキャッシュ・フロー科目の設定が済んでいません。戻るボタンを押してインポート作業をし直す前に勘定科目（CS）ウィンドウを開いて新規に作った勘定科目に関するキャッシュ・フロー科目(直接法及び間接法)の設定を済ます必要があります。',
						),
					),
				),
				'varsDetail' => array(),
			),
			'varsStatus' => array(
				'flagUse' => 1,
				'flagNow' => 'form',
				'flagCakeUse' => 1,
				'flagFoldUse' => 0,
				'flagViewUse' => 0,
				'flagFormUse' => 1,
				'switchList' => array('form'),
			),
			'varsEdit' => array( 'flagReloadUse' => 1, ),
			'templateDetail' => array(
				array(
					'flagMustUse' => 0,
					'id' => 'DummyEditCurrent',
					'strTitle' => '',
					'strExplain' => '',
					'value' => '',
					'flagErrorNow' => 0,
					'arrayError' => array(),
					'flagContentUse' => 0,
					'flagCommentUse' => 1, 'strCommentTitle' => '【お知らせ】', 'strComment' => '<span class="codeLibBaseFontOrange" style="float:none;">※ 既にデータが確定しているため使用できません。</span>',
					'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 100,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'FlagData',
					'strTitle' => 'データ形式',
					'strExplain' => 'データ形式を選択してください。',
					'value' => 'yayoi',
					'flagErrorNow' => 1,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(
						array( 'strTitle' => '弥生形式', 'value' => 'yayoi', ),
						array( 'strTitle' => 'RUCARO形式', 'value' => 'rucaro', ),
					),
					'numSize' => 0,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'Upload',
					'strTitle' => 'CSVファイルアップロード', 'strExplain' => '', 'value' => 'dummy',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
							'strFileType' => 'アップロードが許可されていない拡張子が選択されたようです。',
							'strBlank' => 'ファイルが選択されていないようです。',
							'strError' => 'ファイルをアップロードできなかったようです。',
							'strSize' => '許容されているファイルサイズを超えていたようです。',
						),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'input', 'flagInputType' => 'file', 'numMaxlength' => 0,
					'numWidth' => 90, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(),
					'arrayHidden' => array(),
					'arrFileType' => array('csv' => 1,),
				),
			),
			'varsPage' => array(),
			'varsDetail' => array(),
			'varsBtn' => array(
				array(
					'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
					'id' => 'SaveBtn',
					'vars' => array( 'idTarget' => 'save'),
					'strTitle' => 'インポート',
				),
			),
			'view' => array(),
			'form' => array(
				'varsFormat' => array(
					'id' => '',
					'flagType' => 'normalFormat',
					'numHeight' => 0,
					'numWidth' => 0,
					'flagHeaderLeftUse' => 1,
					'strTitleHeaderLeft' => '',
					'strClassHeaderLeft' => 'codeLibBaseImgBlank',
					'flagHeaderRightUse' => 1,
					'strTitleHeaderRight' => '',
					'strClassHeaderRight' => 'codeLibBaseImgUnloading',
					'flagBodyAutoUse' => 1,
					'strBody' => '',
					'strClassBody' => '',
					'flagFooderUse' => 1,
					'flagFooderLeftUse' => 1,
					'strTitleFooderLeft' => '',
					'strClassFooderLeft' => '',
					'flagFooderRightUse' => 1,
					'strTitleFooderRight' => '',
					'strClassFooderRight' => '',
					'flagHeaderLeftWidth' => 1,
					'numWidthHeaderLeft' => 0,
					'flagHeaderRightWidth' => 0,
					'numWidthHeaderRight' => 0,
					'flagFooderLeftWidth' => 0,
					'numWidthFooderLeft' => 0,
					'flagFooderRightWidth' => 0,
					'numWidthFooderRight' => 0,
				),
				'varsEdit' => array( 'flagReloadUse' => 1, ),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagBtnBottomUse' => 1,
					),
					'varsBtn' => array(),
					'varsDetail' => array(),
				),
			),
		),
		'varsTemplateLayout' => array(
			'varsStatus' => array(
				'flagNaviUse' => 0,
				'flagNaviToolUse' => 0,
				'flagListUse' => 0,
				'flagListToolUse' => 0,
				'flagDetailUse' => 1,
				'flagDetailToolUse' => 1,
			),
			'varsLayout' => array(
				'varsStatus' => array(
					'flagNow' => 1,
					'flagResizeUse' => 1,
					'flagMoveUse' => 0,
					'flagCakeUse' => 1,
					'flagSwitchUse' => 0,
					'flagSwitchNow' => 'Standard',
					'switchList' => array('Standard'),
				),
				'varsDetail' => array(
					array(
						'id' => 'Detail',
						'flagBoxUse' => 1,
						'numPriority' => 0,
						'numSort' => 2,
						'numWidth' => 200,			'numHeight' => 200,
						'numWidthMin' => 200,		'numHeightMin' => 200,
						'numWidthStandard' => 200,	'numHeightStandard' => 200,
						'numWidthWide' => 200,		'numHeightWide' => 200,
						'numWidthClassic' => 200,	'numHeightClassic' => 200,
						'numHeightBox' => 0,		'numWidthBox' => 0,//auto
					),
				)
			),
			'navi' => array(),
			'list' => array(),
			'detail' => array(
				'varsFormat' => array(
					'id' => '',
					'flagType' => 'normalFormat',
					'numHeight' => 0,
					'numWidth' => 0,
					'flagHeaderLeftUse' => 1,
					'strTitleHeaderLeft' => '',
					'strClassHeaderLeft' => '',
					'flagHeaderRightUse' => 1,
					'strTitleHeaderRight' => '',
					'strClassHeaderRight' => 'codeLibBaseImgUnloading',
					'flagBodyAutoUse' => 0,
					'strBody' => '',
					'strClassBody' => '',
					'flagFooderUse' => 1,
					'flagFooderLeftUse' => 1,
					'strTitleFooderLeft' => '',
					'strClassFooderLeft' => '',
					'flagFooderRightUse' => 1,
					'strTitleFooderRight' => '',
					'strClassFooderRight' => '',
					'flagHeaderLeftWidth' => 1,
					'numWidthHeaderLeft' => 0,
					'flagHeaderRightWidth' => 0,
					'numWidthHeaderRight' => 0,
					'flagFooderLeftWidth' => 0,
					'numWidthFooderLeft' => 0,
					'flagFooderRightWidth' => 1,
					'numWidthFooderRight' => 0,
				),
				'varsTool' => array(
					'varsDetail' => array(
						array(
							'flagUse' => 1,
							'flagNow' => 0,
							'id' => 'Reload',
							'strClass' => 'codeLibBtnImgReload',
							'strClassOver' => 'codeLibBtnImgReloadOver',
							'strClassNoactive' => 'codeLibBtnImgReloadNoactive',
							'strTitle' => '更新',
						),
					),
				),
			),
		),
	),
	'child' => array(),
);


