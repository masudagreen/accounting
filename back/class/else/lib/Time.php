<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Lib_Time
{

	protected $_self = array(
		'numTimeZone' => 9,
		'pathLoad'    => 'back/class/else/lib/Time/<strLang>.php',
		'strLang'     => 'ja',
		'varsLoad'    => array(),
	);

    function __construct()
    {
    	$strLang = $this->_self['strLang'];
    	$this->setVarsTime(array('strLang' => $strLang));
        $arr = @func_get_arg(0);
        if (!$arr) {
            return;
        }
		foreach ($arr as $key => $value) {
			if (empty($this->_self[$key])) {
				$this->_self[$key] = $value;
			}
        }
        if ($strLang != $this->_self['strLang']) {
        	$this->setVarsTime(array('strLang' => $this->_self['strLang']));
        }
    }

    /**
     * $arr = array(
     *     strLang  => str,
     * )
     */
    public function setVarsTime($arr)
    {
    	$path = str_replace('<strLang>', $arr['strLang'], $this->_self['pathLoad']);
        if (!file_exists($path)) {
        	return;
        }
        require($path);
        $this->_self['varsLoad'] = $vars;
    }

    /**
     * $arr = array(
     *     data  => int,
     * )
     */
	public function setTimeZone($arr)
	{
		$this->_self['numTimeZone'] = $arr['data'];
	}

    /**
     * $arr = array(
     *     stamp => stamp,
     * )
     */
	public function getLocal($arr)
	{
		$timeZone = $this->_self['numTimeZone'] * 60 * 60;

		$stamp = $arr['stamp'] + $timeZone;
		$dateTime = new DateTime('@' . $stamp);

		list($sec, $min, $hour, $date, $mon, $year, $day, $strDate) = preg_split('/,/', $dateTime->format("s,i,H,j,m,Y,w,d"));
		$data = array(
			'stamp' => $arr['stamp'],
			'year'  => $year,
			'month' => (int) $mon,
			'date'  => $date,
			'day'   => $day,
			'hour'  => (int) $hour,
			'min'   => (int) $min,
			'sec'   => (int) $sec,
			'numYear'  => $year,
			'numMonth' => (int) $mon,
			'numDate'  => $date,
			'numDay'   => $day,
			'numHour'  => (int) $hour,
			'numMin'   => (int) $min,
			'numSec'   => (int) $sec,
			'strYear'  => $year,
			'strMonth' => $mon,
			'strDate'  => $strDate,
			'strHour'  => $hour,
			'strMin'   => $min,
			'strSec'   => $sec,
		);

		return $data;
	}

    /**
     * $arr = array(
     *     stamp => stamp,
     * )
     */
	public function getList($arr)
	{
		$timeZone = $this->_self['numTimeZone'] * 60 * 60;

		$stamp = $arr['stamp'] + $timeZone;
		$dateTime = new DateTime('@' . $stamp);

		list($sec, $min, $hour, $date, $mon, $year, $day, $strDate) = preg_split('/,/', $dateTime->format("s,i,H,j,m,Y,w,d"));
		$data = array(
			'stamp' => $arr['stamp'],
			'year'  => (int) $year,
			'month' => (int) $mon,
			'date'  => (int) $date,
			'day'   => (int) $day,
			'hour'  => (int) $hour,
			'min'   => (int) $min,
			'sec'   => (int) $sec,
			'numYear'  => (int) $year,
			'numMonth' => (int) $mon,
			'numDate'  => (int) $date,
			'numDay'   => (int) $day,
			'numHour'  => (int) $hour,
			'numMin'   => (int) $min,
			'numSec'   => (int) $sec,
			'strYear'  => $year,
			'strMonth' => $mon,
			'strDate'  => $strDate,
			'strHour'  => $hour,
			'strMin'   => $min,
			'strSec'   => $sec,
		);

		return $data;
	}

    /**
$classTime->getDisplay(array(
	'flagType' => 'year/date',
	'stamp'    => 0,
));
     */
	public function getDisplay($arr)
	{
		$timeZone = $this->_self['numTimeZone'] * 60 * 60;

		$stamp = $arr['stamp'] + $timeZone;
		$dateTime = new DateTime('@' . $stamp);

		if($arr['flagType'] == 'rdf') {
			return $dateTime->format("r");

		} elseif($arr['flagType'] == 'year-sec') {
			return $dateTime->format("Y/m/d H:i:s");

		} elseif($arr['flagType'] == 'year-min') {
			return $dateTime->format("Y/m/d H:i");

		} elseif($arr['flagType'] == 'yearmin') {
			return $dateTime->format("Y/m/d-H:i");

		} elseif($arr['flagType'] == 'yearmonth') {
			return $dateTime->format("Ym");

		} elseif($arr['flagType'] == 'year/date') {
			return $dateTime->format("Y/m/d");

		} elseif($arr['flagType'] == 'year-date') {
			return $dateTime->format("Y-m-d");

		} elseif($arr['flagType'] == 'month') {
			return $dateTime->format("m");

		} elseif($arr['flagType'] == 'date') {
			return $dateTime->format("j");

		} elseif($arr['flagType'] == 'hour') {
			return $dateTime->format("H");

		} else {
			$varsDate = $this->getList(array('stamp' => $arr['stamp']));
			if ($arr['flagType'] == 1) {
				return $varsDate['strYear']
					. $this->_self['varsLoad']['strYear']
					. $varsDate['strMonth']
					. $this->_self['varsLoad']['strMonth']
					. $varsDate['strDate']
					. $this->_self['varsLoad']['strDate']
					. '(' . $this->_self['varsLoad']['arrayWeek'][$varsDate['numDay']] . ') '
					. $varsDate['strHour']
					. $this->_self['varsLoad']['strHour']
					. $varsDate['strMin']
					. $this->_self['varsLoad']['strMin'];

			} elseif ($arr['flagType'] == 2) {
				return $varsDate['strYear']
					. $this->_self['varsLoad']['strYear']
					. $varsDate['strMonth']
					. $this->_self['varsLoad']['strMonth']
					. $varsDate['strDate']
					. $this->_self['varsLoad']['strDate']
					. '(' . $this->_self['varsLoad']['arrayWeek'][$varsDate['numDay']] . ')';

			} elseif ($arr['flagType'] == 3) {
				return $varsDate['strYear']
					. '/' . $varsDate['strMonth']
					. '/' . $varsDate['strDate']
					. '  ' . $varsDate['strHour']
					. ':' . $varsDate['strMin'];

			} elseif ($arr['flagType'] == 4) {
				return $varsDate['strYear']
					. '/' . $varsDate['strMonth']
					. '/' . $varsDate['strDate'];

			} elseif ($arr['flagType'] == 5) {
				return $varsDate['strHour']
					. $this->_self['varsLoad']['strHour']
					. $varsDate['strMin']
					. $this->_self['varsLoad']['strMin']
					. '(' .$varsDate['strSec'] . $this->_self['varsLoad']['strSec'] . ')';

			} elseif ($arr['flagType'] == 6) {
				return $varsDate['strMonth']
					. $this->_self['varsLoad']['strMonth']
					. $varsDate['strDate']
					. $this->_self['varsLoad']['strDate']
					. '(' . $this->_self['varsLoad']['arrayWeek'][$varsDate['numDay']] . ') '
					. $varsDate['strHour']
					. $this->_self['varsLoad']['strHour']
					. $varsDate['strMin']
					. $this->_self['varsLoad']['strMin']
					. $varsDate['strSec']
					. $this->_self['varsLoad']['strSec'];

			} elseif ($arr['flagType'] == 7) {
				return $varsDate['strYear']
					. '/' . $varsDate['strMonth']
					. '/' . $varsDate['strDate'];

			} elseif ($arr['flagType'] == 8) {
				return $varsDate['strYear']
					. $this->_self['varsLoad']['strYear']
					. $varsDate['strMonth']
					. $this->_self['varsLoad']['strMonth']
					. $varsDate['strDate']
					. $this->_self['varsLoad']['strDate'];

			} elseif ($arr['flagType'] == 9) {
				return $varsDate['strYear']
				. '/' . $varsDate['strMonth']
				. '/' . $varsDate['strDate']
				. '-' . $varsDate['strHour']
				. ':' . $varsDate['strMin'];
			}
		}

	}

	/*
	(array(
	'stamp' => num
	'numYear' => num
	))
	* */
	public function getStrNengoYear($arr)
	{
	    $numYear = $arr['numYear'];
	    $flag = $this->getFlagNengo(array('stamp' => $arr['stamp']));
	    if ($flag == 'Meiji') {
	        $numYear -= 1867;

	    } elseif ($flag == 'Taishou') {
	        $numYear -= 1911;

	    } elseif ($flag == 'Shouwa') {
	        $numYear -= 1925;

	        /*20190401 start*/
	    } elseif ($flag == 'Heisei') {
	        $numYear -= 1988;

	    } elseif ($flag == 'Reiwa') {
	        $numYear -= 2018;
	    }
	    /*20190401 end*/

	    if ($numYear < 1) {
	        return '';
	    }


	    $strYear = $numYear;
	    if ($numYear == 1) {
	        $strYear = $this->_self['varsLoad']['strGan'];
	    }

	    $flagNengo = 'str' . $flag;
	    $strNengoYear = $this->_self['varsLoad'][$flagNengo] . $strYear;

	    return $strNengoYear;
	}

	/*
		(array(
			'stamp' => num
			'numYear' => num
		))
	 * */
	public function getNengoYear($arr)
	{
		$numYear = $arr['numYear'];
		$flag = $this->getFlagNengo(array('stamp' => $arr['stamp']));
		if ($flag == 'Meiji') {
			$numYear -= 1867;

		} elseif ($flag == 'Taishou') {
			$numYear -= 1911;

		} elseif ($flag == 'Shouwa') {
			$numYear -= 1925;

		/*20190401 start*/
		} elseif ($flag == 'Heisei') {
			$numYear -= 1988;

		} elseif ($flag == 'Reiwa') {
		    $numYear -= 2018;
		}
		/*20190401 end*/

		if ($numYear < 1) {
			return '';
		}

		return $numYear;
	}

	/*
		meiji 1868/09/08 -3197178000
		taishou 1912/07/30 -1812186000
		shouwa 1926/12/25 -1357635600
		heisei 1989/01/08   600188400
		reiwa 2019/05/01   1556636400
		(array(
			'stamp' => num
		))
	 * */
	public function getFlagNengo($arr)
	{
		$stamp = $arr['stamp'];
		if (-3197178000 <= $stamp && $stamp < -1812186000) {
			return 'Meiji';

		} elseif (-1812186000 <= $stamp && $stamp < -1357635600) {
			return 'Taishou';

		} elseif (-1357635600 <= $stamp && $stamp < 600188400) {
			return 'Shouwa';

		/*20190401 start*/
		} elseif (600188400 <= $stamp && $stamp < 1556636400) {
		    return 'Heisei';

		} elseif (1556636400 <= $stamp) {
		    return 'Reiwa';
		}
		/*20190401 end*/

		return '';
	}


	/*
		(array(
				'stamp' => num
		))
	* */
	public function checkRateConsumptionTax($arr)
	{
		$stamp = $arr['stamp'];
		$stamp20140401 = 1396278000;
		$stamp20151001 = 1443625200;

		$num = 5;
		if ($stamp20140401 <= $stamp && $stamp < $stamp20151001) {
			$num = 8;

		} else if ($stamp20151001 <= $stamp) {
			$num = 10;
		}

		return $num;
	}




}
