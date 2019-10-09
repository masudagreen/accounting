<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Lib_Html_Table
{
	private $_selfExt = array(
		'idSelf'        => '#{idSelf}',
		'strClassBg'    => 'codeLibBaseBgLine',
		'numDummyWidth' => 1000,
		'numIdle'       => 10,
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
		));
		$temp['strHtml'] = $temp['domDoc']->saveHTML();

		return $temp;
	}

	private function _loopVars($arr)
	{
		$classTime = new Code_Else_Lib_Time();
		$classEscape = new Code_Else_Lib_Escape();

		$domDoc = &$arr['domDoc'];

		$strClassBgPrev = '';
		$numLine = 0;
		$arrayColumn = &$arr['varsColumn'];
		$array = &$arr['varsDetail'];
		$i = -1;
		foreach ($array as $key => $value) {
			$i++;

			//eleLine
			$eleLine = $domDoc->createElement('div');

			$attr = $domDoc->createAttribute('unselectable');
			$attr->value = 'on';
			$eleLine->appendChild($attr);

			$strClassEleLine = 'codeLibTableLine unselect';

			$id = $this->_selfExt['idSelf'] . 'Line' . $array[$key]['id'];

			$attr = $domDoc->createAttribute('id');
			$attr->value = $id;
			$eleLine->appendChild($attr);

			$attr = $domDoc->createAttribute('style');
			$attr->value = 'width:' . $this->_selfExt['numDummyWidth'] . 'px';
			$eleLine->appendChild($attr);

			$strClassBg = '';
			if ($arr['varsStatus']['flagBgUse']) {
				if ($value['strClassBg']) {
					$strClassEleLine .= ' ' . $value['strClassBg'];
					$strClassBg = $value['strClassBg'];

				} else {
					if (!$arr['flagBgNone']) {
						if ($numLine % 2 == 0) {
							$strClassEleLine .= ' ' . $this->_selfExt['strClassBg'];
							$strClassBg = $this->_selfExt['strClassBg'];
						}
					}
				}
			}
			$numLine++;

			$attr = $domDoc->createAttribute('class');
			$attr->value = $strClassEleLine;
			$eleLine->appendChild($attr);

			$eleWrap = $domDoc->createElement('span');

			$attr = $domDoc->createAttribute('class');
			$attr->value = 'codeLibBaseMarginLeftFive';
			$eleWrap->appendChild($attr);

			$eleImg = $domDoc->createElement('span');

			$attr = $domDoc->createAttribute('id');
			$attr->value = $id . '_eleImg';
			$eleImg->appendChild($attr);

			$strClass = 'codeLibTableLineImg';
			if ($arr['varsStatus']['flagMoveUse'] && $value['flagMoveUse']) {
				$strClass .= ' codeLibTableMove codeLibBaseCursorMove';
			}
			if ($value['strClassLoad']) {
				$strClass .= ' ' . $value['strClassLoad'];
			} else {
				$strClass .= ' ' . $value['strClass'];
			}

			$attr = $domDoc->createAttribute('class');
			$attr->value = $strClass;
			$eleImg->appendChild($attr);

			$eleWrap->appendChild($eleImg);
			$eleLine->appendChild($eleWrap);
			$domDoc->appendChild($eleLine);

			$j = -1;
			foreach ($arrayColumn as $keyColumn => $valueColumn) {
				$j++;
				//
				$eleWrapItem = $domDoc->createElement('span');

				$attr = $domDoc->createAttribute('id');
				$attr->value = $id . '_eleWrapItem_' . $valueColumn['id'];
				$eleWrapItem->appendChild($attr);

				$eleLine->appendChild($eleWrapItem);

				//
				$eleItemIdle = $domDoc->createElement('span');

				$attr = $domDoc->createAttribute('id');
				$attr->value = $id . '_eleItemIdle_' . $valueColumn['id'];
				$eleItemIdle->appendChild($attr);

				$attr = $domDoc->createAttribute('class');
				$attr->value = 'codeLibTableIdle';
				$eleItemIdle->appendChild($attr);

				$eleWrapItem->appendChild($eleItemIdle);

				//
				$eleItem = $domDoc->createElement('span');

				$attr = $domDoc->createAttribute('id');
				$attr->value = $id . '_eleItem_' . $valueColumn['id'];
				$eleItem->appendChild($attr);

				$attr = $domDoc->createAttribute('class');
				$attr->value = 'codeLibTableItem unselect';
				$eleItem->appendChild($attr);

				$attr = $domDoc->createAttribute('unselectable');
				$attr->value = 'on';
				$eleItem->appendChild($attr);

				$eleWrapItem->appendChild($eleItem);

				//
				$eleTitle = $domDoc->createElement('span');

				$attr = $domDoc->createAttribute('id');
				$attr->value = $id . '_eleTitle_' . $valueColumn['id'];
				$eleTitle->appendChild($attr);

				$eleItem->appendChild($eleTitle);

				$str = $classEscape->toLower(array('str' => $valueColumn['id']));

				$numWidthEleItem = $valueColumn['numWidth'] - $this->_selfExt['numIdle'];
				$attr = $domDoc->createAttribute('style');
				$attr->value = 'width:' . $numWidthEleItem . 'px';
				$eleItem->appendChild($attr);

				$strClass = '';
				if ($valueColumn['flagType'] == 'stamp') {
					if ($value['flagBtnUse']) {
						$strClass = 'codeLibBaseCursorPointer';
					} else {
						$strClass = 'codeLibBaseCursorDefault';
					}

					$data = '-';
					if ($value['varsColumnDetail'][$str]) {
						$classTime->setTimeZone(array('data' => $arr['numTimeZone']));
						$data = $classTime->getDisplay(array(
							'stamp'    => $value['varsColumnDetail'][$str],
							'flagType' => $valueColumn['flagTimeType'],
						));

					} else {
						if ($value['varsColumnDetail'][$str] == '') {
							$data = '';
						}
					}
					$strClass .= ' codeLibTableLineTitle';
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
					if ($value['flagBtnUse']) {
						$strClass = 'codeLibBaseCursorPointer';
					} else {
						$strClass = 'codeLibBaseCursorDefault';
					}
					$strClass .= ' codeLibTableLineTitle';

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

				} elseif ($valueColumn['flagType'] == 'checkbox') {

					if ($value['flagCheckboxUse']) {
						$eleForm = $domDoc->createElement('form');

						$eleTag = $domDoc->createElement('input');

						$attr = $domDoc->createAttribute('id');
						$attr->value = $id . 'Checkbox' . $valueColumn['id'];
						$eleTag->appendChild($attr);

						$attr = $domDoc->createAttribute('type');
						$attr->value = 'checkbox';
						$eleTag->appendChild($attr);

						if ($value['flagCheckboxNow']) {
							$attr = $domDoc->createAttribute('checked');
							$attr->value = true;
							$eleTag->appendChild($attr);
						}

						$eleForm->appendChild($eleTag);
						$eleTitle->appendChild($eleForm);
					}
				}

				$attr = $domDoc->createAttribute('class');
				$attr->value = $strClass;
				$eleTitle->appendChild($attr);
			}
			//
			$eleLineSort = $domDoc->createElement('div');

			$attr = $domDoc->createAttribute('id');
			$attr->value = $id . '_eleLineSort';
			$eleLineSort->appendChild($attr);

			$attr = $domDoc->createAttribute('unselectable');
			$attr->value = 'on';
			$eleLineSort->appendChild($attr);

			$strClassLineSort = 'unselect codeLibTableLineSort';
			$strClassBgNext = '';
			$numNext = $i + 1;
			$valueNext = $array[$numNext];
			if (!is_null($valueNext)) {
				if ($arr['varsStatus']['flagBgUse']) {
					if ($valueNext['strClassBg']) {
						$strClassBgNext = $valueNext['strClassBg'];

					} else {
						if (!$arr['flagBgNone']) {
							if ($numLine % 2 == 0) {
								$strClassNext = $this->_selfExt['strClassBg'];
							}
						}
					}
				}
			}
			if ($strClassBg == $strClassBgNext && $strClassBgNext) {
				$strClassLineSort .= ' ' . $strClassBgNext;
			}

			$attr = $domDoc->createAttribute('class');
			$attr->value = $strClassLineSort;
			$eleLineSort->appendChild($attr);

			$attr = $domDoc->createAttribute('style');
			$attr->value = 'width:' . $this->_selfExt['numDummyWidth'] . 'px';
			$eleLineSort->appendChild($attr);

			$domDoc->appendChild($eleLineSort);

		}

		return $arr;
	}
}
