<?php

$vars = array(
	'varsItem'  => array(
		'varsMenu' => array(
			'strList' => '一括償却内訳表',
		),
	),
	'portal' => array(
		'varsNavi' => array(
			'varsStatus' => array(
				'flagNow' => 'tree',
				'flagCakeUse' => 1,
				'flagTreeUse' => 1,
				'flagSearchUse' => 0,
				'flagFolderUse' => 0,
				'switchList' => array('tree'),
			),
			'varsDetail' => array(),
			'tree' => array(
				'varsFormat' => array(
					'id' => '',
					'flagType' => 'normalFormat',
					'numHeight' => 0,
					'numWidth' => 0,
					'flagHeaderLeftUse' => 1,
					'strTitleHeaderLeft' => 'メニュー',
					'strClassHeaderLeft' => 'codeLibBaseImgDb',
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
					'flagFooderRightWidth' => 0,
					'numWidthFooderRight' => 0,
				),
				'varsEdit' => array(
					'flagReloadUse' => 1,
				),
				'varsDetail' => array(
					'varsStatus' => array(
						'flagUse' => 1,
						'flagMoveUse' => 0,
						'flagInsertUse' => 0,
						'flagFoldNow' => 0,
						'flagFoldUse' => 1,
						'flagCakeUse' => 1,
						'flagRemoveUse' => 0,
						'flagCheckUse' => 0,
						'flagCheckNow' => 1,
						'flagBarUse' => 1,
						'flagBtnBottomUse' => 0,
						'flagBtnUse' => 1,
						'flagLockUse' => 0,
						'flagLockNow' => 0,
						'flagPageUse' => 0,
						'flagInnerPageUse' => 0,
						'flagFindUse' => 1,
						'id' => 'tree',
					),
					'varsFormat' => array(
						'id' => '',
						'flagType' => 'singleFormat',
						'numHeight' => 0,
						'numWidth' => 0,
						'flagHeaderUse' => 1,
						'flagHeaderLeftUse' => 1,
						'strTitleHeaderLeft' => '',
						'strClassHeaderLeft' => '',
						'flagHeaderRightUse' => 1,
						'strTitleHeaderRight' => '',
						'strClassHeaderRight' => '',
						'flagBodyAutoUse' => 1,
						'strBody' => '',
						'strClassBody' => '',
						'flagFooderUse' => 0,
						'flagFooderLeftUse' => 0,

						'strTitleFooderLeft' => '',
						'strClassFooderLeft' => '',
						'flagFooderRightUse' => 0,

						'strTitleFooderRight' => '',
						'strClassFooderRight' => '',

						'flagHeaderLeftWidth' => 0,
						'numWidthHeaderLeft' => 0,
						'flagHeaderRightWidth' => 0,
						'numWidthHeaderRight' => 0,
						'flagFooderLeftWidth' => 0,
						'numWidthFooderLeft' => 0,
						'flagFooderRightWidth' => 0,
						'numWidthFooderRight' => 0,
					),
					'varsFind' => array(
						'value' => '',
						'flag' => 0,
						'num' => 0,
						'numWidth' => 70,
						'unitWidth' => '%',
						'strTitle' => '絞込検索',
					),
					'varsBtn' => array(),
					'varsDetail' => array(
						array(
							'id' => 'FlagDepWrite', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
							'strTitle' => '減価償却費計算',
							'strClass' => 'codeLibBaseImgSheet',
							'vars' => array(
								'idTarget' => 'flagDepWrite', 'flagAccessUse' => 0, 'flagAllUse' => 1,
								'varsEdit' => array( 'flagReloadUse' => 1, ),
								'varsBtn' => array(
									array(
										'flagUse' => 1, 'flagLeftUse' => 0, 'flagRightUse' => 1, 'flagTextUse' => 0, 'flagBtnUse' => 1,
										'id' => 'FlagDepWrite',
										'vars' => array( 'idTarget' => 'flagDepWrite',  ),
										'strTitle' => '保存',
									),
								),
								'varsDetail' => array(
									array(
										'flagMustUse' => 1,
										'id' => 'FlagDepWrite',
										'strTitle' => '減価償却費の書出方法',
										'strExplain' => '',
										'value' => 'f1',
										'flagErrorNow' => 0,
										'arrayError' => array(
											array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
										),
										'flagContentUse' => 0,
										'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
										'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
										'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
										'arrayOption' => array(),
										'flagDisabled' => 0,
										'numSize' => 7,
										'varsTmpl' => array(
											'varsPeriod' => array( 'strTitle' => '年度決算期末', 'value' => 'f1', ),
											'arrayOption' => array(
												array( 'strTitle' => '年度決算仕訳として一括計上', 'value' => 'f1', ),
												array( 'strTitle' => '中間決算仕訳として前半期分を計上', 'value' => 'f2', ),
												array( 'strTitle' => '前半期末日に前半期分を計上', 'value' => 'f21', ),
												array( 'strTitle' => '後半期末日に後半期分を計上', 'value' => 'f22', ),
												array( 'strTitle' => '第1四半期末に第1四半期分を計上', 'value' => 'f41', ),
												array( 'strTitle' => '第2四半期末に第2四半期分を計上', 'value' => 'f42', ),
												array( 'strTitle' => '第3四半期末に第3四半期分を計上', 'value' => 'f43', ),
												array( 'strTitle' => '第4四半期末に第4四半期分を計上', 'value' => 'f44', ),
											),
											'strMonth' => '月期末に当該月分を計上',
											'strNormal' => '減価償却費の書出方法を設定してください。分割計算によって余りが生じた場合は最終期間に割り当てられます。',
											'strDone'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
										),
									),
									array(
										'flagMustUse' => 1,
										'id' => 'FlagLossWrite',
										'strTitle' => '除却損書出し',
										'strExplain' => '',
										'value' => 1,
										'flagErrorNow' => 0,
										'arrayError' => array(
											array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
											array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'num', 'strComment' => array( 'common' => '半角数字以外の文字が混入したようです。', ), ),
										),
										'flagContentUse' => 0,
										'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
										'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
										'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
										'arrayOption' => array(
											array( 'strTitle' => '除却損を書き出す', 'value' => 1, ),
											array( 'strTitle' => '除却損を書き出さない', 'value' => 0, ),
										),
										'flagDisabled' => 0,
										'varsTmpl' => array(
											'strNormal' => '減価償却費を書出す際に除却損も一緒に書き出すかどうか設定してください。<br>※ 減価償却費の書出方法の期間に除却日があれば除却損が計上されます。',
											'strDone'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
										),
									),
									array(
										'flagMustUse' => 1,
										'id' => 'FlagFractionDepWrite',
										'strTitle' => '分割書出負担額-端数処理',
										'strExplain' => '',
										'value' => 'ceil',
										'flagErrorNow' => 0,
										'arrayError' => array(
											array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
											array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'half', 'strComment' => array( 'common' => '半角英数以外の文字が混入したようです。', ), ),
										),
										'flagContentUse' => 0,
										'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
										'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
										'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
										'arrayOption' => array(
											array( 'strTitle' => '切捨て', 'value' => 'floor', ),
											array( 'strTitle' => '四捨五入', 'value' => 'round', ),
											array( 'strTitle' => '切上げ', 'value' => 'ceil', ),
										),
										'flagDisabled' => 0,
										'varsTmpl' => array(
											'strNormal' => '分割書出負担額計算で生じた端数処理方法を設定してください。',
											'strDone'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
										),
									),
									array(
										'flagMustUse' => 1,
										'id' => 'FlagFractionDep',
										'strTitle' => '償却計算-端数処理',
										'strExplain' => '',
										'value' => 'ceil',
										'flagErrorNow' => 0,
										'arrayError' => array(
											array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
											array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'half', 'strComment' => array( 'common' => '半角英数以外の文字が混入したようです。', ), ),
										),
										'flagContentUse' => 0,
										'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
										'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
										'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
										'arrayOption' => array(
											array( 'strTitle' => '切捨て', 'value' => 'floor', ),
											array( 'strTitle' => '四捨五入', 'value' => 'round', ),
											array( 'strTitle' => '切上げ', 'value' => 'ceil', ),
										),
										'flagDisabled' => 0,
										'varsTmpl' => array(
											'strNormal' => '償却計算で生じた端数処理方法を設定してください。',
											'strDone'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
										),
									),
									array(
										'flagMustUse' => 1,
										'id' => 'FlagFractionDepSurvivalRate',
										'strTitle' => '残存価額-端数処理',
										'strExplain' => '',
										'value' => 'floor',
										'flagErrorNow' => 0,
										'arrayError' => array(
											array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
											array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'half', 'strComment' => array( 'common' => '半角英数以外の文字が混入したようです。', ), ),
										),
										'flagContentUse' => 0,
										'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
										'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
										'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
										'arrayOption' => array(
											array( 'strTitle' => '切捨て', 'value' => 'floor', ),
											array( 'strTitle' => '四捨五入', 'value' => 'round', ),
											array( 'strTitle' => '切上げ', 'value' => 'ceil', ),
										),
										'flagDisabled' => 0,
										'varsTmpl' => array(
											'strNormal' => '残存価額計算で生じた端数処理方法を設定してください。',
											'strDone'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
										),
									),
									array(
										'flagMustUse' => 1,
										'id' => 'FlagFractionDepSurvivalRateLimit',
										'strTitle' => '残存可能限度額-端数処理',
										'strExplain' => '',
										'value' => 'floor',
										'flagErrorNow' => 0,
										'arrayError' => array(
											array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
											array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'half', 'strComment' => array( 'common' => '半角英数以外の文字が混入したようです。', ), ),
										),
										'flagContentUse' => 0,
										'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
										'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
										'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
										'arrayOption' => array(
											array( 'strTitle' => '切捨て', 'value' => 'floor', ),
											array( 'strTitle' => '四捨五入', 'value' => 'round', ),
											array( 'strTitle' => '切上げ', 'value' => 'ceil', ),
										),
										'flagDisabled' => 0,
										'varsTmpl' => array(
											'strNormal' => '残存可能限度額計算で生じた端数処理方法を設定してください。',
											'strDone'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
										),
									),
									array(
										'flagMustUse' => 1,
										'id' => 'FlagFractionRatioOperate',
										'strTitle' => '事業専用割合-端数処理',
										'strExplain' => '',
										'value' => 'ceil',
										'flagErrorNow' => 0,
										'arrayError' => array(
											array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
											array( 'flagCheck' => 'word', 'flagUse' => 1, 'flagNow' => 0, 'flagArr' => 0, 'flagType' => 'half', 'strComment' => array( 'common' => '半角英数以外の文字が混入したようです。', ), ),
										),
										'flagContentUse' => 0,
										'flagCommentUse' => 0, 'strCommentTitle' => '', 'strComment' => '',
										'flagTag' => 'select', 'flagInputType' => '', 'numMaxlength' => 0,
										'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
										'arrayOption' => array(
											array( 'strTitle' => '切捨て', 'value' => 'floor', ),
											array( 'strTitle' => '四捨五入', 'value' => 'round', ),
											array( 'strTitle' => '切上げ', 'value' => 'ceil', ),
										),
										'flagDisabled' => 0,
										'varsTmpl' => array(
											'strNormal' => '事業専用割合計算で生じた端数処理方法を設定してください。',
											'strDone'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。</span><br>現在選択しているのは、『 <%replace%> 』です。',
										),
									),
									array(
										'flagMustUse' => 1,
										'id' => 'NumRatioOperateDepSum',
										'strTitle' => '一括償却-事業専用割合',
										'strExplain' => '',
										'value' => '100.00',
										'flagErrorNow' => 0,
										'arrayError' => array(
											array( 'flagCheck' => 'blank', 'flagUse' => 1, 'flagNow' => 0, 'flagType' => 'empty', 'strComment' => array( 'common' => '記入漏れがあるようです。', ),),
											array( 'flagCheck' => 'attest', 'flagUse' => 1, 'strComment' => array(
													'strFormat' => '半角数字で小数点第二位まで入力する必要があるようです。<br> 例) 100.00',
											),),
										),
										'flagTag' => 'input', 'flagInputType' => 'text', 'numMaxlength' => 6,
										'numWidth' => 40, 'unitWidth' => '%', 'numHeight' => 0, 'unitHeight' => '',
										'arrayOption' => array(),
										'flagDisabled' => 0,
										'varsTmpl' => array(
											'strNormal' => '一括償却の事業専用割合を設定してください。',
											'strDone'   => '<span class="codeLibBaseFontOrange" style="float:none;">※ データが確定しているため変更できません。',
										),
									),
								),
							),
							'child' => array(),
						),
						array(
							'id' => '', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
							'strTitle' => '入力初期値',
							'strClass' => 'codeLibBaseImgDetail',
							'vars' => array(
								'idTarget' => 'fixedAssetsAccountTitleWindow', 'flagAccessUse' => 1, 'flagAllUse' => 1,
							),
							'child' => array(),
						),
						array(
							'id' => 'SumList', 'flagUse' => 1, 'flagMoveUse' => 1, 'flagInsertUse' => 1, 'flagBtnUse' => 1, 'flagFoldUse' => 0, 'flagFoldNow' => 0, 'flagChildrenUse' => 0, 'flagCheckUse' => 0, 'flagCheckNow' => 0, 'flagRemoveUse' => 1, 'flagEditUse' => 1,
							'strTitle' => '一括償却内訳表',
							'strClass' => 'codeLibBaseImgSheet',
							'vars' => array(
								'idTarget' => 'sumList',
								'varsEdit' => array( 'flagReloadUse' => 1, 'flagOutputUse' => 1,),
								'varsBtn' => array(
								),
								'varsDetail' => array(
									array(
										'flagMustUse' => 0,
										'id' => 'DummyList',
										'strTitle' => '', 'strExplain' => '', 'value' => 0,
										'flagErrorNow' => 0,
										'arrayError' => array(),
										'flagContentUse' => 0,
										'flagCommentUse' => 1, 'strComment' => '',
										'flagTag' => '', 'flagInputType' => '', 'numMaxlength' => 0,
										'unitWidth' => '', 'numWidth' => 0, 'unitHeight' => '', 'numHeight' => 0,
										'arrayOption' => array(),
										'varsTmpl' => array(
											'varsCorporation' => array(
												'strPeriod' => '事業期間',
												'strNumValue' => '取得価額(合計額)',
												'strNumValueCompression' => '圧縮記帳額(合計額)',
												'strNumValueNet' => '差引取得価額',
												'strNumValueDepLimit' => '損金算入限度額',
												'strNumValueNetClosing' => '繰越額',
												'strNumValueDep' => '当期償却額　(　損金算入限度額合計　)',
												'strNumValueNetClosingSum' => '未償却残高　(　繰越額合計　)',
												'strPeriodEnd' => '末',
												'strHeisei' => '平',
											),
										),
										'varsItem' => array(
											'strEntity' => '事業体(<%replace%>)',
											'strNum' => '会期(第<%replace%>期)',
											'strPeriod' => '会計期間(自 平成<%strStartHeisei%>年<%strStartMonth%>月1日　至 平成<%strEndHeisei%>年<%strEndMonth%>月末日)',
											'strUnit' => '単位(円)',
											'varsCorporation' => array(
												'strPeriod' => '事業期間',
												'numValue' => '取得価額',
												'numValueCompression' => '圧縮記帳額',
												'numValueNet' => '差引取得価額',
												'numValueDepLimit' => '損金算入限度額',
												'numValueNetClosing' => '繰越額',
											),
										),
									),
								),
							),
							'child' => array(),
						),
					),
				),
			),
			'item' => array(),
			'folder' => array(),
		),
		'varsDetail' => array(
			'varsEnd' => array(
				'strTitle' => '処理完了',
				'strClass' => 'codeLibBaseImgSheet',
				'varsEdit' => array( 'flagReloadUse' => 0, 'flagOutputUse' => 0,),
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
			'varsStatus' => array(
				'flagUse' => 1,
				'flagNow' => 'form',
				'flagCakeUse' => 0,
				'flagViewUse' => 0,
				'flagFormUse' => 1,
				'switchList' => array('form'),
			),
			'varsBtn' => array(),
			'varsPage' => array(),
			'varsDetail' => array(),
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
				'varsEdit' => array( 'flagReloadUse' => 0, 'flagOutputUse' => 0,),
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
						array(
							'flagUse' => 1,
							'flagNow' => 1,
							'id' => 'Output',
							'strClass' => 'codeLibBtnImgDownload',
							'strClassOver' => 'codeLibBtnImgDownloadOver',
							'strClassNoactive' => 'codeLibBtnImgDownloadNoactive',
							'strTitle' => 'エクスポート',
						),
					),
				),
			),
		),
	),
	'child' => array(
		'templateWindow' => array(
			'id' => '',
			'strTitle' => '<%child%> - <%parent%>',
			'strClass' => 'codePluginAccountingImgIcon',
			'flagLockUse' => 0,
			'flagLockNow' => '',
			'flagCakeUse' => 1,
			'flagRemoveUse' => 0,
			'flagCoverUse' => 1,
			'flagHideUse' => 1,
			'flagHideNow' => 0,
			'flagReloadUse' => 0,
			'flagFoldUse' => 1,
			'flagFoldNow' => 0,
			'flagMoveUse' => 1,
			'flagZIndexUse' => 0,
			'flagResizeUse' => 1,
			'flagResizeIni' => 'all',
			'flagResizeNow' => 'all',
			'flagSkeletonUse' => 0,
			'flagBootUse' => 'auto',
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

);