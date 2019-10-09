<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Lib_Html_TableTree
{
	private $_selfExt = array(
		'idSelf'        => '#{idSelf}',
		'strClassBg'    => 'codeLibBaseBgLine',
		'numDummyWidth' => 1000,
		'numBlock'      => 16,
		'numIdleFive'   => 5,
		'numIdle'       => 10,
		'numHeight'     => 16,
		'numLength'     => 25,
		'numPadding'    => 5,
	);

	public function allot($arr)
	{
		$method = '_ini' . ucwords($arr['flagStatus']);
		if (method_exists($this, $method)) {
			return $this->$method($arr);

		} else {
			if (FLAG_TEST) {
				var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
			}
			exit;
		}
	}

	/**
	 * array(
			'numTimeZone' => $numTimeZone,
			'varsDetail'  => $varsDetail,
			'varsColumn'  => $vars['portal']['varsList']['tableTree']['varsDetail']['varsColumn'],
			'varsStatus'  => $vars['portal']['varsList']['tableTree']['varsDetail']['varsStatus'],
	 * )
	 */
	private function _iniHtml($arr)
	{
		$domDoc = new DOMDocument('1.0');
		$temp = $this->_loopVars(array(
			'domDoc'      => $domDoc,
			'idParent'    => $this->_selfExt['idSelf'],
			'numTimeZone' => $arr['numTimeZone'],
			'varsDetail'  => $arr['varsDetail'],
			'varsColumn'  => $arr['varsColumn'],
			'varsStatus'  => $arr['varsStatus'],
			'flagBgNone'  => $arr['flagBgNone'],
			'numLine'     => 0,
		));
		$temp['strHtml'] = $temp['domDoc']->saveHTML();

		return $temp;
	}

	private function _loopVars($arr)
	{
		$classTime = new Code_Else_Lib_Time();
		$classEscape = new Code_Else_Lib_Escape();

		$idParent = &$arr['idParent'];
		$domDoc = &$arr['domDoc'];

		if (!$arr['eleParent']) {
			$eleTree = $domDoc->createElement('ul');

			$attr = $domDoc->createAttribute('unselectable');
			$attr->value = 'on';
			$eleTree->appendChild($attr);

			$attr = $domDoc->createAttribute('class');
			$attr->value = 'codeLibBaseCursorDefault codeLibTableTreeLineTop';
			$eleTree->appendChild($attr);

			$domDoc->appendChild($eleTree);

			$arr['eleParent'] = $eleTree;
		}

		$arrayColumn = &$arr['varsColumn'];
		$array = &$arr['varsDetail'];
		$i = -1;
		foreach ($array as $key => $value) {
			$i++;

			$array[$key]['id'] = $idParent . '-' . $i;
			$arrLevel = preg_split("/-/", $array[$key]['id']);
			$num = count($arrLevel) - 2;

			//eleLine
			$eleLine = $domDoc->createElement('div');

			$attr = $domDoc->createAttribute('unselectable');
			$attr->value = 'on';
			$eleLine->appendChild($attr);

			$strClassEleLine = 'codeLibTableTreeLine unselect';

			$attr = $domDoc->createAttribute('id');
			$attr->value = $array[$key]['id'];
			$eleLine->appendChild($attr);

			$attr = $domDoc->createAttribute('style');
			$attr->value = 'width:' . $this->_selfExt['numDummyWidth'] . 'px';
			$eleLine->appendChild($attr);

			if ($arr['varsStatus']['flagBgUse']) {
				if ($value['strClassBg']) {
					$strClassEleLine .= ' ' . $value['strClassBg'];
				} else {
					if (!$arr['flagBgNone']) {
						if ($arr['numLine'] % 2 == 0) {
							$strClassEleLine .= ' ' . $this->_selfExt['strClassBg'];
						}
					}
				}
			}
			$arr['numLine']++;

			$attr = $domDoc->createAttribute('class');
			$attr->value = $strClassEleLine;
			$eleLine->appendChild($attr);

			$arr['eleParent']->appendChild($eleLine);

			//
			$eleLineSort = $domDoc->createElement('div');

			$attr = $domDoc->createAttribute('id');
			$attr->value = $array[$key]['id'] . '_eleLineSort';
			$eleLineSort->appendChild($attr);

			$attr = $domDoc->createAttribute('unselectable');
			$attr->value = 'on';
			$eleLineSort->appendChild($attr);

			$attr = $domDoc->createAttribute('class');
			$attr->value = 'unselect codeLibTableTreeLineSort';
			$eleLineSort->appendChild($attr);

			$attr = $domDoc->createAttribute('style');
			$attr->value = 'width:' . $this->_selfExt['numDummyWidth'] . 'px';
			$eleLineSort->appendChild($attr);

			$arr['eleParent']->appendChild($eleLineSort);

			//
			$eleUl = $domDoc->createElement('ul');

			$attr = $domDoc->createAttribute('id');
			$attr->value = $array[$key]['id'] . '_eleUl';
			$eleUl->appendChild($attr);

			$arr['eleParent']->appendChild($eleUl);

			$strChildren = ' (' . count($value['child']) . ')';

			$j = -1;
			foreach ($arrayColumn as $keyColumn => $valueColumn) {
				$j++;
				//
				$eleWrapItem = $domDoc->createElement('div');

				$attr = $domDoc->createAttribute('id');
				$attr->value = $array[$key]['id'] . '_eleWrapItem_' . $valueColumn['id'];
				$eleWrapItem->appendChild($attr);

				$attr = $domDoc->createAttribute('class');
				$attr->value = 'codeLibTableTreeItemWrap';
				$eleWrapItem->appendChild($attr);

				$eleLine->appendChild($eleWrapItem);

				//
				$eleItemIdle = $domDoc->createElement('span');

				$attr = $domDoc->createAttribute('id');
				$attr->value = $array[$key]['id'] . '_eleItemIdle_' . $valueColumn['id'];
				$eleItemIdle->appendChild($attr);

				$eleWrapItem->appendChild($eleItemIdle);

				//
				$eleItem = $domDoc->createElement('span');

				$attr = $domDoc->createAttribute('id');
				$attr->value = $array[$key]['id'] . '_eleItem_' . $valueColumn['id'];
				$eleItem->appendChild($attr);

				$attr = $domDoc->createAttribute('class');
				$attr->value = 'codeLibTableTreeItem unselect';
				$eleItem->appendChild($attr);

				$attr = $domDoc->createAttribute('unselectable');
				$attr->value = 'on';
				$eleItem->appendChild($attr);

				$eleWrapItem->appendChild($eleItem);

				$numWidthEleItem = 0;
				if ($valueColumn['id'] == 'Tree') {
					//
					$eleSortSepatate = $domDoc->createElement('span');

					$attr = $domDoc->createAttribute('id');
					$attr->value = $array[$key]['id'] . '_eleSortSepatate_' . $valueColumn['id'];
					$eleSortSepatate->appendChild($attr);

					$attr = $domDoc->createAttribute('class');
					$attr->value = 'codeLibTableTreeSeparate';
					$eleSortSepatate->appendChild($attr);

					$attr = $domDoc->createAttribute('style');
					$attr->value = 'width:' . ($valueColumn['numWidth'] + $this->_selfExt['numBlock'] * 2) . 'px';
					$eleSortSepatate->appendChild($attr);

					$eleLineSort->appendChild($eleSortSepatate);

					//
					$eleSeparateSort = $domDoc->createElement('span');

					$attr = $domDoc->createAttribute('class');
					$attr->value = 'codeLibTableTreeSeparateDoubleSort';
					$eleSeparateSort->appendChild($attr);

					$eleLineSort->appendChild($eleSeparateSort);

					$numWidthEleItem = $valueColumn['numWidth']
						- $this->_selfExt['numIdleFive']
						+ $this->_selfExt['numBlock'] * 2;

					$attr = $domDoc->createAttribute('style');
					$attr->value = 'width:' . $numWidthEleItem . 'px';
					$eleItem->appendChild($attr);

					$attr = $domDoc->createAttribute('style');
					$attr->value = 'width:' . $this->_selfExt['numIdleFive'] . 'px'
								. ';height:' . $this->_selfExt['numHeight'] . 'px';
					$eleItemIdle->appendChild($attr);

					for ($k = 0; $k < $num; $k++) {
						$eleSpan = $domDoc->createElement('span');

						$attr = $domDoc->createAttribute('unselectable');
						$attr->value = 'on';
						$eleSpan->appendChild($attr);

						$attr = $domDoc->createAttribute('class');
						$attr->value = 'codeLibTableTreeBlock';
						$eleSpan->appendChild($attr);

						$eleItem->appendChild($eleSpan);
					}

					if ($value['flagFoldUse'] && $arr['varsStatus']['flagFoldUse'] && $value['child']) {
						$eleFold = $domDoc->createElement('span');

						$attr = $domDoc->createAttribute('id');
						$attr->value = $array[$key]['id'] . '_eleFold_' . $valueColumn['id'];
						$eleFold->appendChild($attr);

						$attr = $domDoc->createAttribute('unselectable');
						$attr->value = 'on';
						$eleFold->appendChild($attr);

						$strClass = 'codeLibBaseCursorPointer codeLibTableTreeFold';
						if ($value['flagFoldNow']) {
							$strClass .= ' codeLibTableTreeFoldOpen';

						} else {
							$strClass .= ' codeLibTableTreeFoldClose';
						}

						$attr = $domDoc->createAttribute('class');
						$attr->value = $strClass;
						$eleFold->appendChild($attr);

						$eleItem->appendChild($eleFold);

					} else {
						if ($arr['varsStatus']['flagFoldUse']) {
							$eleSpan = $domDoc->createElement('span');

							$attr = $domDoc->createAttribute('unselectable');
							$attr->value = 'on';
							$eleSpan->appendChild($attr);

							$attr = $domDoc->createAttribute('class');
							$attr->value = 'codeLibTableTreeBlock';
							$eleSpan->appendChild($attr);

							$eleItem->appendChild($eleSpan);
						}
					}

					$eleImg = $domDoc->createElement('span');

					$attr = $domDoc->createAttribute('unselectable');
					$attr->value = 'on';
					$eleImg->appendChild($attr);

					$attr = $domDoc->createAttribute('class');
					$attr->value = $value['strClass'] . ' ' . 'codeLibTableTreeLineImg';
					$eleImg->appendChild($attr);

					$eleItem->appendChild($eleImg);

					//
					$eleTitle = $domDoc->createElement('div');

					$attr = $domDoc->createAttribute('id');
					$attr->value = $array[$key]['id'] . '_eleTitle_' . $valueColumn['id'];
					$eleTitle->appendChild($attr);

					$attr = $domDoc->createAttribute('unselectable');
					$attr->value = 'on';
					$eleTitle->appendChild($attr);

					$strClass = '';
					if ($arr['varsStatus']['flagBtnUse'] && $value['flagBtnUse']) {
						$strClass = 'codeLibBaseCursorPointer';
					}
					$strClass .= ' codeLibTableTreeTitle codeLibTableTreeTitleTree codeLibTableTreeLineTitle';


					$strTitle = '';
					if (mb_strlen($value['strTitle']) > $this->_selfExt['numLength']) {
						$strTitle = mb_substr($value['strTitle'], 0, $this->_selfExt['numLength']);
						$strTitle = htmlspecialchars($strTitle, ENT_QUOTES);
						if ($value['flagChildrenUse']) {
							$eleTitle->nodeValue = $strTitle . $strChildren;

						} else {
							$eleTitle->nodeValue = $strTitle;
						}

					} else {
						if ($value['flagChildrenUse']) {
							$eleTitle->nodeValue = $value['strTitle'] . $strChildren;

						} else {
							$eleTitle->nodeValue = $value['strTitle'];
						}
					}

					if ($arr['varsStatus']['flagFontUse'] && $value['strClassFont']) {
						$strClass .= ' ' . $value['strClassFont'];
					}
					if ($arr['varsStatus']['flagBoldUse'] && $value['flagBoldNow']) {
						$strClass .= ' codeLibBaseFontBold';
					}

					$attr = $domDoc->createAttribute('class');
					$attr->value = $strClass;
					$eleTitle->appendChild($attr);

					$attr = $domDoc->createAttribute('title');
					if ($value['flagChildrenUse']) {
						$attr->value = $value['strTitle'] . $strChildren;

					} else {
						$attr->value = $value['strTitle'];
					}
					$eleTitle->appendChild($attr);

					$eleItem->appendChild($eleTitle);

					$eleSeparate = $domDoc->createElement('span');

					$attr = $domDoc->createAttribute('class');
					$attr->value = 'codeLibTableTreeSeparateDouble';
					$eleSeparate->appendChild($attr);

					$attr = $domDoc->createAttribute('class');
					$attr->value = 'codeLibTableTreeItemIdle';
					$eleItemIdle->appendChild($attr);

					$eleWrapItem->appendChild($eleSeparate);
					continue;
				}

				$eleSortSepatate = $domDoc->createElement('span');

				$attr = $domDoc->createAttribute('id');
				$attr->value = $array[$key]['id'] . '_eleSortSepatate_' . $valueColumn['id'];
				$eleSortSepatate->appendChild($attr);

				$attr = $domDoc->createAttribute('class');
				$attr->value = 'codeLibTableTreeSeparate';
				$eleSortSepatate->appendChild($attr);

				$attr = $domDoc->createAttribute('style');
				$attr->value = 'width:' . ($valueColumn['numWidth'] + $this->_selfExt['numIdle']) . 'px';
				$eleSortSepatate->appendChild($attr);

				$eleLineSort->appendChild($eleSortSepatate);

				//
				$eleSeparateSort = $domDoc->createElement('span');

				$attr = $domDoc->createAttribute('id');
				$attr->value = $array[$key]['id'] . '_eleSeparateSort_' . $valueColumn['id'];
				$eleSeparateSort->appendChild($attr);

				$attr = $domDoc->createAttribute('class');
				$attr->value = 'codeLibTableTreeSeparateNone';
				$eleSeparateSort->appendChild($attr);

				$eleLineSort->appendChild($eleSeparateSort);

				$str = $classEscape->toLower(array('str' => $valueColumn['id']));

				$numWidthEleItem = $valueColumn['numWidth'] - $this->_selfExt['numIdle'];
				$attr = $domDoc->createAttribute('style');
				$attr->value = 'width:' . $numWidthEleItem . 'px';
				$eleItem->appendChild($attr);

				$strClass = 'codeLibTableTreeItemIdle';
				if ($j == 1) {
					$strClass .= ' codeLibTableTreeIdleFive';

				} else {
					$strClass .= ' codeLibTableTreeIdle';
				}

				$attr = $domDoc->createAttribute('class');
				$attr->value = $strClass;
				$eleItemIdle->appendChild($attr);

				//
				$eleTitle = $domDoc->createElement('span');

				$attr = $domDoc->createAttribute('id');
				$attr->value = $array[$key]['id'] . '_eleTitle_' . $valueColumn['id'];
				$eleTitle->appendChild($attr);

				$eleItem->appendChild($eleTitle);

				$strClass = '';
				if ($valueColumn['flagType'] == 'stamp') {
					$strClass = 'codeLibBaseCursorPointer';
					$data = '-';
					if ($value['varsColumnDetail'][$str]) {
						$classTime->setTimeZone(array('data' => $arr['numTimeZone']));
						$data = $classTime->getDisplay(array(
							'stamp'    => $value['varsColumnDetail'][$str],
							'flagType' => $valueColumn['flagTimeType'],
						));
					}
					$strClass .= ' codeLibTableTreeLineTitle';
					$eleTitle->nodeValue = $data;

					if ($arr['varsStatus']['flagFontUse'] && $value['strClassFont']) {
						$strClass .= ' ' . $value['strClassFont'];
					}
					if ($arr['varsStatus']['flagBoldUse'] && $value['flagBoldNow']) {
						$strClass .= ' codeLibBaseFontBold';
					}

					$attr = $domDoc->createAttribute('title');
					$attr->value = $data;
					$eleTitle->appendChild($attr);

				} elseif ($valueColumn['flagType'] == 'str') {

					if ($arr['varsStatus']['flagBtnUse'] && $value['flagBtnUse']) {
						$strClass = 'codeLibBaseCursorPointer';
					}
					$strClass .= ' codeLibTableTreeLineTitle';

					$strTitle = '';
					if (mb_strlen($value['varsColumnDetail'][$str]) > $this->_selfExt['numLength']) {
						$strTitle = mb_substr($value['varsColumnDetail'][$str], 0, $this->_selfExt['numLength']);

					} else {
						$strTitle = $value['varsColumnDetail'][$str];
					}
					$strTitle = htmlspecialchars($strTitle, ENT_QUOTES);
					$eleTitle->nodeValue = $strTitle;

					if ($valueColumn['flagAlign']) {
						$numWidthEleItem = $valueColumn['numWidth']
										- $this->_selfExt['numIdle']
										- $this->_selfExt['numPadding'];

						$attr = $domDoc->createAttribute('style');
						$attr->value = 'width:' . $numWidthEleItem . 'px'
							. ';padding-right:' . $this->_selfExt['numPadding'] . 'px'
							. ';text-align:' . $valueColumn['flagAlign'];
						$eleTitle->appendChild($attr);
					}

					if ($arr['varsStatus']['flagFontUse'] && $value['strClassFont']) {
						$strClass .= ' ' . $value['strClassFont'];
					}
					if ($arr['varsStatus']['flagBoldUse'] && $value['flagBoldNow']) {
						$strClass .= ' codeLibBaseFontBold';
					}

					$attr = $domDoc->createAttribute('title');
					$attr->value = $strTitle;
					$eleTitle->appendChild($attr);
				}

				$attr = $domDoc->createAttribute('class');
				$attr->value = $strClass;
				$eleTitle->appendChild($attr);
			}
			if ($value['child']) {
				$array[$key]['child'] = $this->_loopVars(array(
					'varsDetail'  => $array[$key]['child'],
					'numLine'     => &$arr['numLine'],
					'flagBgNone'  => $arr['flagBgNone'],
					'varsColumn'  => $arr['varsColumn'],
					'varsStatus'  => $arr['varsStatus'],
					'eleParent'   => $eleUl,
					'idParent'    => $array[$key]['id'],
					'domDoc'      => $arr['domDoc'],
					'numTimeZone' => $arr['numTimeZone'],
				));
			}
		}
		return $arr;
	}
}
