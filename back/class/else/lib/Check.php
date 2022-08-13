<?php

/**
 *
 */
class Code_Else_Lib_Check
{
	public $self = array(
    	'pathLoad' => 'back/class/else/lib/Check/<strLang>.php',
    	'strLang'  => 'ja',
    );

    function __construct()
    {
    	$strLang = $this->self['strLang'];
    	$this->setVarsEscape(array('strLang' => $strLang));
        $arr = @func_get_arg(0);
        if (!$arr) {
            return;
        }
		foreach ($arr as $key => $value) {
			if (empty($this->self[$key])) {
				$this->self[$key] = $value;
			}
        }
        if ($strLang != $this->self['strLang']) {
        	$this->setVarsEscape(array('strLang' => $this->self['strLang']));
        }
    }

    /**
     * $arr = array(
     *     strLang  => str,
     * )
     */
    private function setVarsEscape($arr)
    {
    	$path = str_replace('<strLang>', $arr['strLang'], $this->self['pathLoad']);
        if (!file_exists($path)) {
        	return;
        }
        require($path);
        $array = $vars;
        foreach ($array as $key => $value) {
        	if (empty($this->self[$key])) {
        		$this->self[$key] = $value;
        	}
        }
    }

    /**
	 * arr => array(
	 *     array(
	 *         'flagMustUse' => int,
	 *         'value' => mixed,
	 *         'flagErrorNow' => int,
	 *         'arrayError' => array(),
	 *     ),
	 * ),
	 */
	public function checkValue($arr)
	{
		$array = $arr['arr'];
		$numAll = count($array);
		for ($j = 0; $j < $numAll; $j++) {
			$array[$j]['flagErrorNow'] = 0;
			$arrays = $array[$j]['arrayError'];
			$numAlls = count($arrays);
			for ($i = 0; $i < $numAlls; $i++) {
				$arrays[$i]['flagNow'] = 0;
				if ($arrays[$i]['flagCheck'] == 'blank') {
					if ($arrays[$i]['flagUse'] && $array[$j]['flagMustUse']) {
						$arrays[$i]['flagNow'] = $this->checkValueBlank(array(
                            'flagType' => $arrays[$i]['flagType'],
                            'flagArr'  => $arrays[$i]['flagArr'],
                            'value'    => $array[$j]['value']
						));
						if ($arrays[$i]['flagNow']) {
							$array[$j]['flagErrorNow'] = 1;
							continue;
						}
					}
				} else {
					if ($arrays[$i]['flagUse']) {
						$flag = $this->checkValueBlank(array(
                            'flagType' => 'empty',
                            'flagArr'  => $arrays[$i]['flagArr'],
                            'value'    => $array[$j]['value']
						));
						if ($arrays[$i]['flagCheck'] == 'word') {
							if ($array[$j]['flagMustUse'] || (!$array[$j]['flagMustUse'] && !$flag)) {
								$arrays[$i]['flagNow'] = $this->checkValueWord(array(
                                    'flagType' => $arrays[$i]['flagType'],
                                    'flagArr'  => $arrays[$i]['flagArr'],
                                    'value'    => $array[$j]['value']
								));
							}
							if ($arrays[$i]['flagNow']) {
								$array[$j]['flagErrorNow'] = 1;
							}
						} elseif ($arrays[$i]['flagCheck'] == 'format') {
							if ($array[$j]['flagMustUse'] || (!$array[$j]['flagMustUse'] && !$flag)) {
								$arrays[$i]['flagNow'] = $this->checkValueFormat(array(
                                    'flagType' => $arrays[$i]['flagType'],
                                    'flagArr'  => $arrays[$i]['flagArr'],
                                    'value'    => $array[$j]['value']
								));
							}
							if ($arrays[$i]['flagNow']) {
								$array[$j]['flagErrorNow'] = 1;
							}
						} elseif ($arrays[$i]['flagCheck'] == 'max') {
							if ($array[$j]['flagMustUse'] || (!$array[$j]['flagMustUse'] && !$flag)) {
								$arrays[$i]['flagNow'] = $this->checkValueMax(array(
                                    'flagType' => $arrays[$i]['flagType'],
                                    'value'    => $array[$j]['value'],
                                    'flagArr'  => $arrays[$i]['flagArr'],
                                    'num'      => $arrays[$i]['num']
								));
							}



							if ($arrays[$i]['flagNow']) {
								$array[$j]['flagErrorNow'] = 1;
							}
						} elseif ($arrays[$i]['flagCheck'] == 'min') {
							if ($array[$j]['flagMustUse'] || (!$array[$j]['flagMustUse'] && !$flag)) {
								$arrays[$i]['flagNow'] = $this->checkValueMin(array(
                                    'flagType' => $arrays[$i]['flagType'],
                                    'value'    => $array[$j]['value'],
                                    'flagArr'  => $arrays[$i]['flagArr'],
                                    'num'      => $arrays[$i]['num']
								));
							}
							if ($arrays[$i]['flagNow']) {
								$array[$j]['flagErrorNow'] = 1;
							}
						} elseif ($arrays[$i]['flagCheck'] == 'strUnique') {
							if ($array[$j]['flagMustUse'] || (!$array[$j]['flagMustUse'] && !$flag)) {
								$arrays[$i]['flagNow'] = $this->getValueStrUnique(array(
                                    'value' => $array[$j]['value']
								));
							}
							if ($arrays[$i]['flagNow']) {
								$array[$j]['flagErrorNow'] = 1;
							}
						}

					}
				}
			}
		}

		return $array;
	}

	/**
	 * $arr => array(
	 *     'flagType' => string
	 *     'flagArr' => string
	 *     'value' => mixed
	 * ),
	 */
	public function checkValueBlank($arr)
	{
		global $classEscape;

		if ($arr['flagArr']) {
			$array = array();
			if (is_array($arr['value'])) {
				$array = $arr['value'];
			} elseif ($arr['flagArr'] == 'json') {
				$array = json_decode($arr['value']);
			} elseif ($arr['flagArr'] == 'comma') {
				$array  = $classEscape->splitCommaArray(array('data' => $arr['value']));
			}

			$numAll = count($array);
			for ($j = 0; $j < $numAll; $j++) {
				if ($arr['flagType'] == 'blank') {
					return ( preg_match( "/[^ |　]/", $array[$j]) )? 0: 1;
				} elseif ($arr['flagType'] == 'empty') {
					return (is_null($array[$j]))? 1: 0;
				}
			}
		} else {
			if ($arr['flagType'] == 'blank') {
				return ( preg_match( "/[^ |　]/", $arr['value']) )? 0: 1;
			} elseif ($arr['flagType'] == 'empty') {
				return ($arr['value'] == '' || is_null($arr['value']))? 1: 0;
			}
		}
	}

	/**
	 * $arr => array(
	 *     'flagType' => string
	 *     'flagArr' => string
	 *     'value' => mixed
	 * ),
	 */
	public function checkValueWord($arr)
	{
		global $classEscape;

		if ($arr['flagArr']) {
			$array = array();
			if (is_array($arr['value'])) {
				$array = $arr['value'];
			} elseif ($arr['flagArr'] == 'json') {
				$array = json_decode($arr['value']);
			} elseif ($arr['flagArr'] == 'comma') {
				$array  = $classEscape->splitCommaArray(array('data' => $arr['value']));
			}
			$numAll = count($array);
			for ($j = 0; $j < $numAll; $j++) {
				if ($arr['flagType'] == 'half'
				&& preg_match( "/\W/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'url'
				&& preg_match( "/[^0-9a-zA-Z_\:\-\.\/\~]+/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'ip'
				&& preg_match( "/[^0-9\.]+/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'ipSubnet'
				&& preg_match( "/[^0-9\.\/\-]+/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'num'
				&& preg_match( "/[^0-9]+/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'number'
				&& !preg_match( "/^(0|-?[1-9][0-9]*|-?(0|[1-9][0-9]*)\.[0-9]+)$/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'mail'
				&& preg_match( "/[^0-9a-zA-Z_\-\.\@\~]/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'mailHost'
				&& preg_match( "/[^0-9a-zA-Z_\-\.\~]/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'file'
				&& preg_match( "/[^0-9a-zA-Z_\-\.]/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'stamp'
				&& preg_match( "/[^0-9]+/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'space'
				&& preg_match( "/\s/", $array[$j])
				) {
					return 1;
				}
			}
		} else {
			if ($arr['flagType'] == 'half') {
				return ( preg_match( "/\W/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'url') {
				return ( preg_match( "/[^0-9a-zA-Z_\:\-\.\/\~]+/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'ip') {
				return ( preg_match( "/[^0-9\.]+/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'ipSubnet') {
				return ( preg_match( "/[^0-9\.\/\-]+/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'num') {
				return ( preg_match( "/[^0-9]+/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'number') {
				return ( !preg_match( "/^(0|-?[1-9][0-9]*|-?(0|[1-9][0-9]*)\.[0-9]+)$/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'mail') {
				return ( preg_match( "/[^0-9a-zA-Z_\-\.\@\~]/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'mailHost') {
				return ( preg_match( "/[^0-9a-zA-Z_\-\.\~]/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'file') {
				return ( preg_match( "/[^0-9a-zA-Z_\-\.]/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'termStamp') {
				return ( preg_match( "/[^0-9-]+/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'stamp') {
				return ( preg_match( "/[^0-9]+/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'password') {
				return ( preg_match( '/[^a-zA-Z0-9!"#$%\'()=~|^@[;:\],.\/`{+*}?-]+/', $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'space') {
				return ( preg_match( '/\s/', $arr['value']) )? 1: 0;
			}
		}
	}

	/**
$classCheck->checkValueFormat(array(
	'flagType' => 'url',
	'flagArr'  => 0,
	'value'    => ''
));
	 */
	public function checkValueFormat($arr)
	{
		global $classEscape;

		if ($arr['flagArr']) {
			$array = array();
			if (is_array($arr['value'])) {
				$array = $arr['value'];
			} elseif ($arr['flagArr'] == 'json') {
				$array = json_decode($arr['value']);
			} elseif ($arr['flagArr'] == 'comma') {
				$array  = $classEscape->splitCommaArray(array('data' => $arr['value']));
			}

			$numAll = count($array);
			for ($j = 0; $j < $numAll; $j++) {
				if ($arr['flagType'] == 'ip'
				&& !preg_match( "/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/", $array[$j])
				) {
					return 1;
				}     elseif ($arr['flagType'] == 'ipSubnet'
				&& !preg_match( "/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}\/[0-9]{1,2}$/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'post'
				&& !preg_match( "/^[0-9]{3}\-+[0-9]{4}$/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'born'
				&& !preg_match( "/^[0-9]{4}\-+[0-9]{2}\-+[0-9]{2}$/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'phone'
				&& !preg_match( "/^[0-9]{2,4}\-+[0-9]{2,4}\-+[0-9]{2,4}$/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'mail'
				&& !preg_match( "/[0-9a-zA-Z_\-\~]+@[0-9a-zA-Z_\-\~]+\.[0-9a-zA-Z_\-\~\.]+/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'mailHost'
				&& !preg_match( "/[0-9a-zA-Z_\-\~]+\.[0-9a-zA-Z_\-\~\.]+/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'url'
				&& !preg_match( "/^(http|https|ftp|telnet|nttp|file|news):\/\/.+\..+/", $array[$j])
				) {
					return 1;
				} elseif ($arr['flagType'] == 'stamp' && !preg_match( "/^[0-9]{10}$/", $array[$j])
				) {
					return 1;
				}
			}
		} else {
			if ($arr['flagType'] == 'ip') {
				return ( !preg_match( "/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'ipSubnet') {
				$pattern = "/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}\/[0-9]{1,2}$/";
				return ( !preg_match( $pattern, $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'post') {
				return ( !preg_match( "/^[0-9]{3}\-+[0-9]{4}$/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'born') {
				return ( !preg_match( "/^[0-9]{4}\-+[0-9]{2}\-+[0-9]{2}$/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'phone') {
				return ( !preg_match( "/^[0-9]{2,4}\-+[0-9]{2,4}\-+[0-9]{2,4}$/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'mail') {
				$pattern = "/[0-9a-zA-Z_\-\~]+@[0-9a-zA-Z_\-\~]+\.[0-9a-zA-Z_\-\~\.]+/";
				return ( !preg_match( $pattern, $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'mailHost') {
				return ( !preg_match( "/[0-9a-zA-Z_\-\~]+\.[0-9a-zA-Z_\-\~\.]+/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'url') {
				return ( !preg_match( "/^(http|https|ftp|telnet|nttp|file|news):\/\/.+\..+/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'stamp') {
				return ( !preg_match( "/^[0-9]{10}$/", $arr['value']) )? 1: 0;
			} elseif ($arr['flagType'] == 'termStamp') {
				list($min, $max) = preg_split("/-/", $arr['value']);
				if ( !preg_match( "/^[0-9]{10}$/", $min) ) {
					return 'stamp_min';
				}
				if ( !preg_match( "/^[0-9]{10}$/", $max) ) {
					return 'stamp_max';
				}
				return ($min > $max)? 1: 0;
			} elseif ($arr['flagType'] == 'password') {
				$pattern = "/[a-z]+/";
				$flaga = (preg_match($pattern, $arr['value']))? 1: 0;
				$pattern = "/[A-Z]+/";
				$flagA = (preg_match($pattern, $arr['value']))? 1: 0;
				$pattern = "/[0-9]+/";
				$flag0 = (preg_match($pattern, $arr['value']))? 1: 0;
				$pattern = '/[!"#$%\'()=~|^@[;:\],.\/`{+*}?-]+/';
				$flagMark = (preg_match($pattern, $arr['value']))? 1: 0;
				$flag = ($flaga && $flagA && $flag0 && $flagMark)? 0: 1;
				return $flag;

			} elseif ($arr['flagType'] == 'date-time-date') {
				if (preg_match( "/^[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}-[0-9]{1,2}:[0-9]{1,2}$/", $arr['value']) ) {
					list($year, $month, $date, $hour, $min) = preg_split("/\//", $arr['value']);
					if ($year < 1970 ) {
						return 'year';
					}
					if (preg_match( "/^0[1-9]{1}$/", $month)) {
						$month = (int) $month;
					}
					if ($month > 12 || $month < 1) {
						return 'month';
					}
					if (preg_match( "/^0[1-9]{1}$/", $date)) {
						$date = (int) $date;
					}
					if (!checkdate($month, $date, $year)) {
						return 'date';
					}
					if ($hour > 23 || $hour < 0) return 'hour';
					if ($min > 59 || $min < 0) return 'min';

				} elseif (preg_match( "/^[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}$/", $arr['value']) ) {
					list($year, $month, $date) = preg_split("/\//", $arr['value']);
					if ($year < 1970 ) {
						return 'year';
					}
					if (preg_match( "/^0[1-9]{1}$/", $month)) {
						$month = (int) $month;
					}
					if ($month > 12 || $month < 1) {
						return 'month';
					}
					if (preg_match( "/^0[1-9]{1}$/", $date)) {
						$date = (int) $date;
					}
					if (!checkdate($month, $date, $year)) {
						return 'date';
					}

				} else {
					return 'format';
				}

			} elseif ($arr['flagType'] == 'date') {
				if ( !preg_match( "/^[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}$/", $arr['value']) ) {
					return 'format';
				}
				list($year, $month, $date) = preg_split("/\//", $arr['value']);
				if ($year < 1970 ) {
					return 'year';
				}
				if (preg_match( "/^0[1-9]{1}$/", $month)) {
					$month = (int) $month;
				}
				if ($month > 12 || $month < 1) {
					return 'month';
				}
				if (preg_match( "/^0[1-9]{1}$/", $date)) {
					$date = (int) $date;
				}
				if (!checkdate($month, $date, $year)) {
					return 'date';
				}
			}
		}
	}

	/**
	 * $arr => array(
	 *     'flagType' => string
	 *     'flagArr' => string
	 *     'value' => mixed
	 *     'num' => int
	 * ),
	 */
	public function checkValueMax($arr)
	{
		global $classEscape;

		if ($arr['flagArr']) {
			$array = array();
			if (is_array($arr['value'])) {
				$array = $arr['value'];
			} elseif ($arr['flagArr'] == 'json') {
				$array = json_decode($arr['value']);
			} elseif ($arr['flagArr'] == 'comma') {
				$array  = $classEscape->splitCommaArray(array('data' => $arr['value']));
			}
			$numAll = count($array);
			for ($j = 0; $j < $numAll; $j++) {
				if ($arr['flagType'] == 'num' && $array[$j] > $arr['num'] ) {
					return 1;
				} elseif ($arr['flagType'] == 'str' && mb_strlen($array[$j]) > $arr['num'] ) {
					return 1;
				}
			}
		} else {
			if ($arr['flagType'] == 'num') {
				return ($arr['value'] > $arr['num'] )? 1: 0;
			} elseif ($arr['flagType'] == 'str') {
				return (mb_strlen($arr['value']) > $arr['num'] )? 1: 0;
			}
		}
	}

	/**
	 * $arr => array(
	 *     'flagType' => string
	 *     'flagArr' => string
	 *     'value' => mixed
	 *     'num' => int
	 * ),
	 */
	public function checkValueMin($arr)
	{
		global $classEscape;

		if ($arr['flagArr']) {
			$array = array();
			if (is_array($arr['value'])) {
				$array = $arr['value'];
			} elseif ($arr['flagArr'] == 'json') {
				$array = json_decode($arr['value']);
			} elseif ($arr['flagArr'] == 'comma') {
				$array  = $classEscape->splitCommaArray(array('data' => $arr['value']));
			}
			$numAll = count($array);
			for ($j = 0; $j < $numAll; $j++) {
				if ($arr['flagType'] == 'num' && $array[$j] < $arr['num'] ) {
					return 1;
				} elseif ($arr['flagType'] == 'str' && mb_strlen($array[$j]) < $arr['num'] ) {
					return 1;
				}
			}
		} else {
			if ($arr['flagType'] == 'num') {
				return ($arr['value'] < $arr['num'] )? 1: 0;
			} elseif ($arr['flagType'] == 'str') {
				return (mb_strlen($arr['value']) < $arr['num'] )? 1: 0;
			}
		}
	}

	/**
	 * $arr => array(
	 *     'value' => mixed
	 * ),
	 */
	public function getValueStrUnique($arr)
	{
		$array = $this->self['arrStrUnique'];
		$numAll = count($array);
		for ($j = 0; $j < $numAll; $j++) {
			if ( preg_match( "/$array[$j]/u", $arr['value']) ) {
				return 1;
			}
		}
	}

	/**
	 * $arr => array(
	 *     'flagType' => string
	 *     'numByte'  => int
	 * ),
	 */
	public function getDisc($arr)
	{
		$b = $arr['numByte'];
		$kb = round($arr['numByte']/1024);
		$mb = round($arr['numByte']/1024/1024);
		$gb = round($arr['numByte']/1024/1024/1024);
		$tb = round($arr['numByte']/1024/1024/1024/1024);

		if ($arr['flagType'] == 'arr') {
			return array(
                'numB' => $b, 'numKb' => $kb, 'numMb' => $mb, 'numGb' => $gb, 'numTb' => $tb,
			);

		} elseif ($arr['flagType'] == 'str') {
			$str = '';
			if (1024 > $arr['numByte']) {
				$b = number_format($b);
				$str .= $b . 'B';

			} elseif (1024 <= $arr['numByte'] && 1024*1024 > $arr['numByte']) {
				$b = number_format($b);
				$kb = number_format($kb);
				$str .= $kb.'KB'.' ( '.$b.'B ) ';

			} elseif (1024*1024 <= $arr['numByte'] && 1024*1024*1024 > $arr['numByte']) {
				$kb = number_format($kb);
				$mb = number_format($mb);
				$str .= $mb.'MB'.' ( '.$kb.'KB ) ';

			} elseif (1024*1024*1024 <= $arr['numByte'] && 1024*1024*1024*1024 > $arr['numByte']) {
				$mb = number_format($mb);
				$gb = number_format($gb);
				$str .= $gb.'GB'.' ( '.$mb.'MB ) ';

			} elseif (1024*1024*1024*1024 <= $arr['numByte']) {
				$tb = number_format($tb);
				$gb = number_format($gb);
				$str .= $tb.'TB'.' ( '.$gb.'GB ) ';
			}
			return $str;
		}
	}

	/**
	 * $arr => array(
	 *     'path' => string
	 * ),
	 */
	public function getByteDir($arr)
	{
		$array = scandir($arr['path']);
		$numAll = count($array);
		for ($j = 0; $j < $numAll; $j++) {
			$name = $array[$j];
			if ( preg_match( "/^\.{1,2}$/", $name) ) {
				continue;
			}
			$path = $arr['path'] . '/' . $name;
			if (is_dir($path)) {
				$byte += $this->getByteDir(array('path' => $path));
			} else {
				$byte += filesize($path);
			}
		}

		return $byte;
	}

	/**
	 * $arr => array(
	 *     'ip' => string
	 *     'arr' => array('', '')
	 * ),
	 */
	public function ipRange($arr, $flag = 0) {

		$now = $this->_ipRangeIp($arr['ip']);
		$array = $arr['arr'];

		$numAll = count($array);
		for ($j = 0; $j < $numAll; $j++) {
			if ( preg_match("/^\#/", $array[$j]) ) {
				continue;
			}

			if ( preg_match("/\-/", $array[$j]) ) {
				list($ipMax, $ipMin) = preg_split("/\-/", $array[$j]);
				$max = $this->_ipRangeIp($ipMax);
				$min = $this->_ipRangeIp($ipMin);
				if ( $min <= $now && $now < $max) {
					$flag = 1;
					break;
				}

			} elseif ( preg_match("/\//", $array[$j]) ) {
				list($ip, $subnet) = preg_split("/\//", $array[$j]);
				$subnet = $this->_ipRangeSubnet($subnet);
				$min = $this->_ipRangeIp($ip);
				$max = $min + $subnet;
				if ( $min <= $now && $now < $max) {
					$flag = 1;
					break;
				}

			} else {
				if ($array[$j] == $arr['ip']) {
					$flag = 1;
					break;
				}

			}
		}

		return $flag;
	}

	/**
	 * $ip => string
	 */
	protected function _ipRangeIp($ip)
	{
		list($a, $b, $c, $d) = preg_split("/\./", $ip);
		$a = (int) $a * 256 * 256 * 256;
		$b = (int) $b * 256 * 256;
		$c = (int) $c * 256;
		$sum = $a + $b + $c + (int) $d;

		return $sum;
	}

	/**
	 * $subnet => string
	 */
	protected function _ipRangeSubnet($subnet)
	{
		$subnet = 32 - (int) $subnet;
		$numAll = $subnet;
		$num = 1;
		for ($j = 0; $j < $numAll; $j++) {
			$num = $num * 2;
		}
		$num--;

		return $num;
	}

	/**
	 * $arr = array(
	 *    'flagType' => string,
	 *    'idModule' => string,
	 * )
	 */
	public function checkModule($arr)
	{
		global $varsPreference;
		global $varsAccount;
		global $varsModule;

		if ($varsAccount['flagWebmaster']) {
			return 'webmaster';
		}

		$id = $varsAccount['idModule'];
		$vars = $varsModule[$id];

		$str = strtolower($arr['idModule']);

		if ($arr['flagType'] == 'Admin') {
			if (preg_match( "/,$str,|,base,/", $vars['arrCommaIdModuleAdmin'])) {
				return 'admin';
			}

		} elseif ($arr['flagType'] == 'User') {
			if (preg_match( "/,$str,/", $vars['arrCommaIdModuleUser'])) {
				return 'user';
			}

		}

		return 0;
	}

	/**
	 * $arr = array(
	 *    'idModule' => string,
	 * )
	 */
	public function checkModuleAuthority($arr)
	{
		global $varsPreference;
		global $varsAccount;
		global $varsModule;

		if ($varsAccount['flagWebmaster']) {
			return 'webmaster';

		}

		$id = $varsAccount['idModule'];
		$vars = $varsModule[$id];

		$str = strtolower($arr['idModule']);

		if (preg_match( "/,$str,/", $vars['arrCommaIdModuleAdmin'])) {
			return 'admin';

		} elseif (preg_match( "/,$str,/", $vars['arrCommaIdModuleUser'])) {
			return 'user';

		}

		return 0;
	}

	/**
	 * $arr = array(
	 *    'strUrl' => string,
	 * )
	 */
	public function checkUrl($arr)
	{
		$path = $arr['strUrl'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $path);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$output = curl_exec($ch);
		curl_close($ch);

		if(!$output){
			return 1;
		}
		return 0;
	}

	/**
	 * $arr => array(
	 *     'data'    => mixed
	 *     'flagRep' => str
	 * ),
	 */
	public function checkStr($arr)
	{
		$flagRep = $this->self[$arr['flagRep']];
		if (is_null($flagRep)) {
			return false;
		}

		if (preg_match( "/$flagRep/u", $arr['data']) ) {
			return true;
		}

		return false;
	}
}
