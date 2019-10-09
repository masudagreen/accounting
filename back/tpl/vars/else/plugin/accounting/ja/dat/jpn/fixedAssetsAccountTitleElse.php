<?php

$vars = array(

	//有形固定資産
	//デフォルトもデータもないとき
	//附属設備
	'tangibleFixedAssets' => array(
		//償却資産税
			'flagTaxFixed' => 'free',
			//種類
			'flagTaxFixedType' => 'none',
		//償却方法
		'flagDepMethod' => 'straight',
		//残存割合
		'numSurvivalRate' => 10,
		//残存可能限度割合
		'numSurvivalRateLimit' => 5,
		//備忘
		'numValueRemainingBook' => 1,
		//除却損
		'lossOnDisposalOfFixedAssets' => 'lossOnDisposalOfFixedAssets',
		//減価償却累計額
		'accumulatedDepreciation' => 'accumulatedDepreciation',
		//販売管理費
		'sellingAdminCost' => 'depreciation',
		//製造原価
		'productsCost' => 'manufactureDepreciation',
		//営業外費用
		'nonOperatingExpenses' => 'none',
		//生産原価
		'agricultureCost' => 'none',
		//負担比率
			//販売管理費
			'numRatioSellingAdminCost' => 100.00,
			//製造原価
			'numRatioProductsCost' => 0.00,
			//営業外費用
			'numRatioNonOperatingExpenses' => 0.00,
			//生産原価
			'numRatioAgricultureCost' => 0.00,
			//余り負担
			'flagFraction' => 'numRatioSellingAdminCost',
	),
	//無形固定資産
	'intangibleFixedAssets' => array(
		//償却資産税
			'flagTaxFixed' => 'none',
			//種類
			'flagTaxFixedType' => 'none',
		//償却方法
		'flagDepMethod' => 'straight',
		//残存割合
		'numSurvivalRate' => 0,
		//残存可能限度割合
		'numSurvivalRateLimit' => 0,
		//備忘
		'numValueRemainingBook' => 0,
		//除却損
		'lossOnDisposalOfFixedAssets' => 'lossOnDisposalOfFixedAssets',
		//減価償却累計額
		'accumulatedDepreciation' => 'none',
		//販売管理費
		'sellingAdminCost' => 'depreciation',
		//製造原価
		'productsCost' => 'none',
		//営業外費用
		'nonOperatingExpenses' => 'none',
		//生産原価
		'agricultureCost' => 'none',
		//負担比率
			//販売管理費
			'numRatioSellingAdminCost' => 100.00,
			//製造原価
			'numRatioProductsCost' => 0.00,
			//営業外費用
			'numRatioNonOperatingExpenses' => 0.00,
			//生産原価
			'numRatioAgricultureCost' => 0.00,
			//余り負担
			'flagFraction' => 'numRatioSellingAdminCost',
	),
	//投資その他の資産
	//投資有価証券
	'investmentsAndOtherAssets' => array(
		//償却資産税
			'flagTaxFixed' => 'free',
			//種類
			'flagTaxFixedType' => 'none',
		//償却方法
		'flagDepMethod' => 'noneDep',
		//残存割合
		'numSurvivalRate' => 100,
		//残存可能限度割合
		'numSurvivalRateLimit' => 100,
		//備忘
		'numValueRemainingBook' => 0,
		//除却損
		'lossOnDisposalOfFixedAssets' => 'none',
		//減価償却累計額
		'accumulatedDepreciation' => 'none',
		//販売管理費
		'sellingAdminCost' => 'none',
		//製造原価
		'productsCost' => 'none',
		//営業外費用
		'nonOperatingExpenses' => 'none',
		//生産原価
		'agricultureCost' => 'none',
		//負担比率
			//販売管理費
			'numRatioSellingAdminCost' => 100.00,
			//製造原価
			'numRatioProductsCost' => 0.00,
			//営業外費用
			'numRatioNonOperatingExpenses' => 0.00,
			//生産原価
			'numRatioAgricultureCost' => 0.00,
			//余り負担
			'flagFraction' => 'numRatioSellingAdminCost',
	),
);
