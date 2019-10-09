<?php

$vars = array(
//増加理由
	'flagDepUp' => array(
		'new' => '新品取得',
		'old' => '中古取得',
		'move' => '移動受入',
		'else' => 'その他',
	),
//減少理由
	'flagDepDown' => array(
		'none' => '未選択',
		'sell' => '売却',
		'exclusion' => '除却',
		'move' => '移動',
		'else' => 'その他',
	),
//償却資産税 可否区分
	'flagTaxFixed' => array(
		'none' => '未選択',
		'tax' => '課税',
		'free' => '非課税',
	),
//償却資産税 種類
	'flagTaxFixedType' => array(
		'none' => '未選択',
		'structures' => '構築物',
		'machinerys' => '機械及び装置',
		'ship' => '船舶',
		'aircraft' => '航空機',
		'vehicles' => '車両運搬具',
		'tools' => '工具器具備品',
	),
//償却方法
	'flagDepMethod' => array(
		'none' => '未選択',
		'straight' => '定額法',
		'declining' => '定率法',
		'average' => '均等償却',
		'one' => '即時償却',
		'sum' => '一括償却',
		'voluntary' => '任意償却',
		'noneDep' => '非償却',
	),
//余り処理
	'flagFraction' => array(
		'numRatioSellingAdminCost' => '販売管理費',
		'numRatioNonOperatingExpenses' => '営業外費用',
		'numRatioProductsCost' => '製造原価',
		'numRatioAgricultureCost' => '生産原価',
	),
);
