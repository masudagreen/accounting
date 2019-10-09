<?php

$vars = array(

	//有形固定資産
	//建物
	'buildings' => array(
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
			//余り処理
			'flagFraction' => 'numRatioSellingAdminCost',
	),
	//附属設備
	'buildingsAndAccessoyEquipment' => array(
		//償却資産税
			'flagTaxFixed' => 'tax',
			//種類
			'flagTaxFixedType' => 'structures',
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
	//構築物
	'structures' => array(
		//償却資産税
			'flagTaxFixed' => 'tax',
			//種類
			'flagTaxFixedType' => 'structures',
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
	//機械装置
	'machineryAndEquipment' => array(
		//償却資産税
			'flagTaxFixed' => 'tax',
			//種類
			'flagTaxFixedType' => 'machinerys',
		//償却方法
		'flagDepMethod' => 'declining',
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
	//車両運搬具
	'car' => array(
		//償却資産税
			'flagTaxFixed' => 'free',
			//種類
			'flagTaxFixedType' => 'none',
		//償却方法
		'flagDepMethod' => 'declining',
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
	//工具器具備品
	'furnitureAndFixture' => array(
		//償却資産税
			'flagTaxFixed' => 'tax',
			//種類
			'flagTaxFixedType' => 'tools',
		//償却方法
		'flagDepMethod' => 'declining',
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
	//一括償却資産
	'lumpSumDepreciableAsset' => array(
		//償却資産税
			'flagTaxFixed' => 'none',
			//種類
			'flagTaxFixedType' => 'none',
		//償却方法
		'flagDepMethod' => 'sum',
		//残存割合
		'numSurvivalRate' => 0,
		//残存可能限度割合
		'numSurvivalRateLimit' => 0,
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
	//土地
	'land' => array(
		//償却資産税
			'flagTaxFixed' => 'none',
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
	//建設仮勘定
	'constructionInProgress' => array(
		//償却資産税
			'flagTaxFixed' => 'none',
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

	//無形固定資産
	//電話加入権
	'telephoneSubscriptionRight' => array(
		//償却資産税
			'flagTaxFixed' => 'none',
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
		'lossOnDisposalOfFixedAssets' => 'lossOnDisposalOfFixedAssets',
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
	//営業権
	'goodwill' => array(
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
	//借地権
	'leaseholdRight' => array(
		//償却資産税
			'flagTaxFixed' => 'none',
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
	//ソフトウェア
	'software' => array(
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
	'investmentsInSecurities' => array(
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
	//関係会社株式
	'stocksOfAffiliatedCompanies' => array(
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
	//出資金
	'investments' => array(
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
	//関係会社出資金
	'investmentsOfAffiliatedCompanie' => array(
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
	//敷金
	'leaseDepositsAndSecurityDeposits' => array(
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
	//差入保証金
	'guaranteeDeposits' => array(
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
	//長期貸付金
	'longTermLoansReceivable' => array(
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
	//長期性預金
	'longTermDeposits' => array(
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
	//長期滞留債権
	'stayCredit' => array(
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
	//破産更生債権等
	'bankruptcyReorganizationClaim' => array(
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
	//長期前払費用
	'longTermPrepaidExpenses' => array(
		//償却資産税
			'flagTaxFixed' => 'free',
			//種類
			'flagTaxFixedType' => 'none',
		//償却方法
		'flagDepMethod' => 'average',
		//残存割合
		'numSurvivalRate' => 0,
		//残存可能限度割合
		'numSurvivalRateLimit' => 0,
		//備忘
		'numValueRemainingBook' => 0,
		//除却損
		'lossOnDisposalOfFixedAssets' => 'none',
		//減価償却累計額
		'accumulatedDepreciation' => 'none',
		//販売管理費
		'sellingAdminCost' => 'longTermPrepaidExpenses',
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
	//繰延税金資産(固)
	'longTermDefferedTaxAssets' => array(
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
	//預託金
	'deposit' => array(
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
	//未収消費税等
	'accruedConsumptionTaxes' => array(
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
