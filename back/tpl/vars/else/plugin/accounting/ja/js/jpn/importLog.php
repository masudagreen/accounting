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
			'numValueConsumptionTaxDebit' => '借方消費税金額',
			'idSubAccountTitleDebit' => '借方補助科目',
			'idDepartmentDebit' => '借方部門',
			'flagConsumptionTaxDebit' => '借方消費税区分',
			'flagConsumptionTaxWithoutCalcDebit' => '借方消費税入力方法',
			'numValueCredit' => '貸方金額',
			'numValueConsumptionTaxCredit' => '貸方消費税金額',
			'idDepartmentCredit' => '貸方部門',
			'idAccountTitleCredit' => '貸方勘定科目',
			'flagFSCredit' => '貸方F/S',
			'idSubAccountTitleCredit' => '貸方補助科目',
			'flagConsumptionTaxCredit' => '貸方消費税区分',
			'flagConsumptionTaxWithoutCalcCredit' => '貸方消費税入力方法',
			'idAccount' => '担当者',
			'arrSpaceStrTag' => 'タグ',
		),
		'varsComment' => array(
			'strEnd' => '<p class="codeLibBasePaddingFive">無事処理が完了したようです。<p>',
			'strStart' => '
				<div class="codeLibBasePaddingFive">
					<p>※ インポートできるファイルは、CSV(カンマ区切り)ファイルのみとなります。</p>
					<p>※ 外部仕訳エクスポートで出力されたファイルの文字コードは、SJISです。</p>
					<p>※ インポートファイルのフォーマットは、オンラインマニュアルで参照してください。</p>
					<p>※ 複数行の最大行数は10行までです。</p><p>※ インポートできる仕訳は、当期の会計期間に属する仕訳のみとなります。</p>
					<p>※ インポートした仕訳は全て確定仕訳として扱われます。</p>
			</div>',
			'strRowMax' => '仕訳行数は、10行までが上限となっています。',
			'strSpaceMax' => '許容データ領域をオーバーしているため登録できないようです。',

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
			'strTaxOut' => ' : 別記の場合、消費税額は0にしなければならないようです。',
			'strAccountTitle' => ' : 勘定科目がないためエラーになっているようです。',
			'strAuthority' => ' : 必要なアクセス権限が付されていないようです。',
			'strAuthorityEntity' => ' : 当該事業体へのアクセス権限が付されていないようです。',
			'strNumValue' => ' : 勘定科目が設定されている場合は、1以上を設定する必要があるようです。',
		),
		'varsRequest' => array(
			'flagFiscalReport' => '',
			'stampBook' => '',
			'strTitle' => '',
			'jsonDetail' => array(
				'numSum' => '',
				'numSumDebit' => '',
				'numSumCredit' => '',
				'idAccountTitleCredit' => '',
				'idAccountTitleDebit' => '',
				'varsDetail' => array(),
				'arrCommaIdLogFile' => '',
				'arrCommaIdAccountPermit' => '',
				'numSumMax' => 0,
				'arrSpaceStrTag' => '',
			),
			'id' => '',
			'idAccount' => '',
		),
		'varsDetail' => array(
			'id' => '',
			'arrDebit' => array(
				'idAccountTitle' => '',
				'numValue' => '',
				'numValueConsumptionTax' => '',
				'idDepartment' => '',
				'idSubAccountTitle' => '',
				'flagConsumptionTaxFree' => '',
				'flagConsumptionTaxIncluding' => '',
				'flagConsumptionTaxGeneralRuleEach' => '',
				'flagConsumptionTaxGeneralRuleProration' => '',
				'flagConsumptionTaxSimpleRule' => '',
				'flagConsumptionTaxWithoutCalc' => '',
				'flagConsumptionTaxCalc' => '',
			),
			'arrCredit' => array(
				'idAccountTitle' => '',
				'numValue' => '',
				'numValueConsumptionTax' => '',
				'idDepartment' => '',
				'idSubAccountTitle' => '',
				'flagConsumptionTaxFree' => '',
				'flagConsumptionTaxIncluding' => '',
				'flagConsumptionTaxGeneralRuleEach' => '',
				'flagConsumptionTaxGeneralRuleProration' => '',
				'flagConsumptionTaxSimpleRule' => '',
				'flagConsumptionTaxWithoutCalc' => '',
				'flagConsumptionTaxCalc' => '',
			),
		),
	),
	'pathCss' => '',
	'portal' => array(
		'varsNavi' => array(
			'varsStatus' => array(
				'flagNow' => 'form',
				'flagCakeUse' => 0,
				'flagViewUse' => 0,
				'flagFormUse' => 1,
				'switchList' => array('form'),
			),
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
					'arrayOption' => array(), 'numOptionMust' => 0, 'numOptionMay' => 0,
					'flagFoldUse' => 1, 'flagFoldNow' => 0,
				),
				array(
					'flagMustUse' => 1,
					'id' => 'StrCode',
					'strTitle' => '文字コード',
					'strExplain' => 'インポートファイルの文字コードを選択してください。',
					'value' => 'SJIS-win',
					'flagErrorNow' => 0,
					'arrayError' => array(
						array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
					),
					'flagContentUse' => 0,
					'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
					'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
					'numWidth' => 80, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
					'arrayOption' => array(
						array( 'strTitle' => 'SJIS', 'value' => 'SJIS-win', ),
						array( 'strTitle' => 'UTF8', 'value' => 'utf8', ),
						array( 'strTitle' => 'EUC', 'value' => 'EUC-JP', ),
						array( 'strTitle' => 'JIS', 'value' => 'JIS', ),
					),
				),
				array(
					'flagMustUse' => 1,
					'id' => 'Upload',
					'strTitle' => 'CSVファイルアップロード', 'strExplain' => 'アップロードするファイルを選択してください。', 'value' => 'dummy',
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
					'arrayOption' => array(), 'numOptionMust' => 0, 'numOptionMay' => 0,
					'arrayHidden' => array(),
					'arrFileType' => array('csv' => 1,),
				),
			),
			'varsPage' => array(),
			'varsDetail' => array(),
			'varsBtn' => array(
				array(
					'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
					'id' => 'EventFormBtn',
					'vars' => array( 'idTarget' => 'search', ),
					'strTitle' => 'インポート',
				),
			),
			'varsStart' => array(
				'strTitle' => '外部仕訳インポート',
				'strClass' => 'codeLibBaseImgDb',
				'varsEdit' => array( 'flagReloadUse' => 1, ),
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
					'strClassHeaderLeft' => 'codeLibBaseImgDb',
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
		'varsList' => array(),
		'varsDetail' => array(
			'varsStatus' => array(
				'flagUse' => 1,
				'flagNow' => 'sheet',
				'flagCakeUse' => 0,
				'flagFoldUse' => 0,
				'flagViewUse' => 0,
				'flagSheetUse' => 1,
				'flagFormUse' => 0,
				'flagMoveUse' => 0,
				'switchList' => array('sheet'),
			),
			'varsPage' => array(),
			'varsDetail' => array(
				'varsHtml' => 'test',
			),
			'varsBtn' => array(),
			'varsStart' => array(
				'strTitle' => 'コメント',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array(
					'flagReloadUse' => 1,
				),
			),
			'sheet' => array(
				'varsFormat' => array(
					'id' => '',
					'flagType' => 'normalFormat',
					'numHeight' => 0,
					'numWidth' => 0,
					'flagHeaderLeftUse' => 1,
					'strTitleHeaderLeft' => 'コメント',
					'strClassHeaderLeft' => 'codeLibBaseImgSheet',
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
				'varsEdit' => array(
					'flagReloadUse' => 1,
				),
				'varsPage' => array(),
				'varsBtn' => array(),
				'varsDetail' => array(),
			),
			'view' => array(),
			'form' => array(),
		),
		'varsTemplateLayout' => array(
			'varsStatus' => array(
				'flagNaviUse' => 1,
				'flagNaviToolUse' => 1,
				'flagListUse' => 0,
				'flagListToolUse' => 0,
				'flagDetailUse' => 1,
				'flagDetailToolUse' => 1,
			),
			'varsLayout' => array(
				'varsStatus' => array(
					'flagNow' => 2,
					'flagResizeUse' => 1,
					'flagMoveUse' => 1,
					'flagCakeUse' => 1,
					'flagSwitchUse' => 1,
					'flagSwitchNow' => 'Standard',
					'switchList' => array('Standard','Wide'),
				),
				'varsDetail' => array(
					array(
						'id' => 'Navi',
						'flagBoxUse' => 1,
						'numPriority' => 0,
						'numSort' => 0,
						'numWidth' => 250,			'numHeight' => 200,
						'numWidthMin' => 200,		'numHeightMin' => 200,
						'numWidthStandard' => 200,	'numHeightStandard' => 200,
						'numWidthWide' => 200,		'numHeightWide' => 200,
						'numWidthClassic' => 200,	'numHeightClassic' => 200,
						'numHeightBox' => 0,		'numWidthBox' => 0,//auto
					),
					array(
						'id' => 'Detail',
						'flagBoxUse' => 1,
						'numPriority' => 2,
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
			'navi' => array(
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

