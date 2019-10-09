<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Lib_Html_TableSimple
{
	private $_selfExt = array(
		'idSelf'        => '#{idSelf}',
		'strClassBg'    => 'codeLibBaseBgLine',
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
			'varsDetail'  => array(),
			'varsColumn'  => array(),
			'varsStatus'  => array(),
	 * )
	 */
	private function _iniHtml($arr)
	{
		$domDoc = new DOMDocument('1.0');
		$temp = $this->_loopVars(array(
			'domDoc'      => $domDoc,
			'varsStatus'  => $arr['varsStatus'],
			'varsDetail'  => $arr['varsDetail'],
			'varsColumn'  => $arr['varsColumn'],
		));
		$temp['strHtml'] = $temp['domDoc']->saveHTML();

		return $temp;
	}

	private function _loopVars($arr)
	{
		$domDoc = &$arr['domDoc'];
		$eleTable = $domDoc->createElement('table');
		$domDoc->appendChild($eleTable);

		$attr = $domDoc->createAttribute('cellspacing');
		$attr->value = '1';
		$eleTable->appendChild($attr);

		$attr = $domDoc->createAttribute('cellpadding');
		$attr->value = '3';
		$eleTable->appendChild($attr);

		$attr = $domDoc->createAttribute('border');
		$attr->value = '0';
		$eleTable->appendChild($attr);

		$strBgcolor = '#ddd';
		if ($arr['varsStatus']['strBgcolor']) {
			$strBgcolor = $arr['varsStatus']['strBgcolor'];
		}
		$attr = $domDoc->createAttribute('bgcolor');
		$attr->value = $strBgcolor;
		$eleTable->appendChild($attr);

		$strWidth = '100%';
		if ($arr['varsStatus']['numWidthTable']) {
			$strWidth = $arr['varsStatus']['numWidthTable'] . 'px';
		}
		$attr = $domDoc->createAttribute('width');
		$attr->value = $strWidth;
		$eleTable->appendChild($attr);

		$eleTbody = $domDoc->createElement('tbody');
		$eleTable->appendChild($eleTbody);

		$eleTr = $domDoc->createElement('tr');

		$attr = $domDoc->createAttribute('valign');
		$attr->value = 'middle';
		$eleTr->appendChild($attr);

		$eleTbody->appendChild($eleTr);
		if (!$arr['varsStatus']['flagIdNoneUse']) {
			$attr = $domDoc->createAttribute('id');
			$attr->value = $this->_selfExt['idSelf'] . '_Body';
			$eleTbody->appendChild($attr);
		}

		$arrayColumn = &$arr['varsColumn'];
		foreach ($arrayColumn as $keyColumn => $valueColumn) {
			$eleTd = $domDoc->createElement('td');

			$attr = $domDoc->createAttribute('class');
			$attr->value = 'codeLibBaseTableColumnMiddle';
			$eleTd->appendChild($attr);

			$strStyle = '';
			if ($arr['varsStatus']['varsColumnWidth']) {
				$strStyle .= 'width:' . $arr['varsStatus']['varsColumnWidth'][$keyColumn] . 'px;';
			}

			if ($arr['varsStatus']['numFontSize']) {
				$strStyle .= 'font-size:' . $arr['varsStatus']['numFontSize'] . 'px;';
			}

			if ($strStyle) {
				$attr = $domDoc->createAttribute('style');
				$attr->value = $strStyle;
				$eleTd->appendChild($attr);
			}

			if ($arr['varsStatus']['flagOverflowUse']) {
				$eleDiv = $domDoc->createElement('div');

				$strStyle = 'overflow:hidden;white-space:nowrap;';
				if ($arr['varsStatus']['varsColumnWidth']) {
					$strStyle .= 'width:' . $arr['varsStatus']['varsColumnWidth'][$keyColumn] . 'px;';
				}

				$attr = $domDoc->createAttribute('style');
				$attr->value = $strStyle;
				$eleDiv->appendChild($attr);

				$attr = $domDoc->createAttribute('title');
				$attr->value = $valueColumn;
				$eleDiv->appendChild($attr);

				$eleDiv->nodeValue = $valueColumn;

				$eleTd->appendChild($eleDiv);

			} else {
				$eleTd->nodeValue = $valueColumn;
			}

			$eleTr->appendChild($eleTd);
		}

		$numLine = 0;
		$array = &$arr['varsDetail'];
		foreach ($array as $key => $value) {
			$eleTr = $domDoc->createElement('tr');

			$idTr = $this->_selfExt['idSelf'] . '_Tr' . $value['id'];

			if (!$arr['varsStatus']['flagIdNoneUse']) {
				$attr = $domDoc->createAttribute('id');
				$attr->value = $idTr;
				$eleTr->appendChild($attr);
			}

			$attr = $domDoc->createAttribute('valign');
			$attr->value = 'top';
			$eleTr->appendChild($attr);

			$eleTbody->appendChild($eleTr);

			$strClassBg = '';
			if ($arr['varsStatus']['flagBgUse']) {
				if ($value['strClassBg']) {
					$strClassBg = $value['strClassBg'];
				} else {
					$strClassBg = $this->_selfExt['strClassBg'];
				}
			}
			$numLine++;

			$arrayColumn = $arr['varsColumn'];
			if ($arr['varsStatus']['varsColumnId']) {
				$arrayColumn = $arr['varsStatus']['varsColumnId'];
			}
			foreach ($arrayColumn as $keyColumn => $valueColumn) {
				$strKey = $keyColumn;
				if ($arr['varsStatus']['varsColumnId']) {
					$strKey = $valueColumn;
				}
				$eleTd = $domDoc->createElement('td');

				$idTd = $idTr . '_Td' . $strKey;

				if (!$arr['varsStatus']['flagIdNoneUse']) {
					$attr = $domDoc->createAttribute('id');
					$attr->value = $idTd;
					$eleTd->appendChild($attr);
				}

				$strClass = 'codeLibBaseTableRow';
				if ($value['varsDetail'][$strKey]['strClass']) {
					$strClass = $value['varsDetail'][$strKey]['strClass'];

				} elseif ($strClassBg) {
					$strClass = $strClassBg;
				}

				$strStyle = '';
				if ($arr['varsStatus']['numFontSize']) {
					$strStyle .= 'font-size:' . $arr['varsStatus']['numFontSize'] . 'px;';
				}
				if ($strStyle) {
					$attr = $domDoc->createAttribute('style');
					$attr->value = $strStyle;
					$eleTd->appendChild($attr);
				}

				if ($value['varsDetail'][$strKey]['strClassFont']) {
					$strClass .= ' ' . $value['varsDetail'][$strKey]['strClassFont'];
				}

				$attr = $domDoc->createAttribute('class');
				$attr->value = $strClass;
				$eleTd->appendChild($attr);

				$flagOverflow = $value['varsDetail'][$strKey]['flagOverflowUse'];
				if ($flagOverflow) {
					$eleDiv = $domDoc->createElement('div');

					$strStyle = 'overflow:hidden;white-space:nowrap;';
					if ($arr['varsStatus']['varsColumnWidth']) {
						$strStyle .= 'width:' . $arr['varsStatus']['varsColumnWidth'][$keyColumn] . 'px;';
					}
					if ($strStyle) {
						$attr = $domDoc->createAttribute('style');
						$attr->value = $strStyle;
						$eleDiv->appendChild($attr);
					}

					$attr = $domDoc->createAttribute('title');
					$attr->value = $value['varsDetail'][$strKey]['value'];
					$eleDiv->appendChild($attr);

					$eleDiv->nodeValue = $value['varsDetail'][$strKey]['value'];

					$eleTd->appendChild($eleDiv);

				} else {
					$eleTd->nodeValue = $value['varsDetail'][$strKey]['value'];
				}




				$eleTr->appendChild($eleTd);
			}
		}

		return $arr;
	}
}
