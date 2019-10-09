<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Lib_File
{
    protected $_self = array(
        'strDelimiter' => ',',
        'strCover'     => '"',
    	'nkf'          => '/usr/bin/nkf',
    );

    function __construct()
    {
        $arr = @func_get_arg(0);
        if (!$arr) {
            return;
        }
        foreach ($arr as $key => $value) {
            if (empty($this->_self[$key])) {
				$this->_self[$key] = $value;
			}
        }
    }

    /**
     * $arr = array(
     *     data => mixed,
     *     path => string,
     * )
     */
    public function setData($arr)
    {
        $fp = fopen ($arr['path'], "w") or die;
        flock($fp, LOCK_EX);
        fputs($fp, $arr['data']);
        flock($fp, LOCK_UN);
        fclose ($fp);
    }

    /**
     * $arr = array(
     *     data => mixed,
     *     path => string,
     * )
     */
    public function addData($arr)
    {
        $fp = fopen ($arr['path'], "a") or die;
        flock($fp, LOCK_EX);
        fwrite($fp, $arr['data']);
        flock($fp, LOCK_UN);
        fclose ($fp);
    }

    /**
     * $arr = array(
     *     'path' => string,
     * )
     */
    public function getArray($arr, $array = array())
    {
        $fp = fopen ($arr['path'], "r") or die;
        flock($fp, LOCK_EX);
        $j = 0;
        while ($line = fgets($fp)) {
            $array[$j] = $line;
            $j++;
        }
        flock($fp, LOCK_UN);
        fclose ($fp);

        return $array;
    }

    /**
     * $arr = array(
     *     'path' => string,
     * )
     */
    public function getArrayFirst($arr, $rowData = '')
    {
        $fp = fopen ($arr['path'], "r") or die;
        flock($fp, LOCK_EX);
        while ($line = fgets($fp)) {
            $rowData = $line;
            break;
        }
        flock($fp, LOCK_UN);
        fclose ($fp);

        return $rowData;
    }

    /**
     * $rowData = string
     */
    public function csvConvert($rowData)
	{
        $strCover = $this->_self['strCover'];
        $strDelimiter = $this->_self['strDelimiter'];
        $pattern = '/(?:\r\n|[\r\n])?$/';
        $rowData = preg_replace($pattern, $strDelimiter, trim($rowData));
        $pattern = "/(" . $strCover . "[^" . $strCover . "]*(?:" . $strCover
                . $strCover . "[^" . $strCover . "]*)*" . $strCover
                . "|[^" . $strDelimiter . "]*)" . $strDelimiter . "/";
        preg_match_all($pattern, $rowData, $match);
        $array = $match[1];
        $numAll = count($array);
        for ($j = 0; $j < $numAll; $j++) {
            $pattern = "/^" . $strCover . "(.*)" . $strCover . "$/";
            $array[$j] = preg_replace($pattern, '$1', $array[$j]);
            $pattern = $strCover . $strCover;
            $array[$j] = str_replace($pattern, $strCover, $array[$j]);
        }
        @mb_convert_variables(mb_internal_encoding(), mb_detect_order(), $array);

        return $array;
    }

    /**
     * $arr = array(
     *     'path'      => string,
     *     'strColumn' => string,
     *     'value'     => mixed,
     * )
     */
    public function getCsvRow($arr, $flag = 0, $flagGet = 0, $strCoverolumns = 0,
        $arrayRow = array(), $arrayColumn = array()
    ) {
        $fp = fopen($arr['path'], "r") or die;
        flock($fp, LOCK_EX);
        while ($rowData = fgets($fp)) {
        	if (preg_match("/^\n/", $rowData)) {
        		continue;
        	}
        	$rowData = $this->csvConvert($rowData);
            if (!$flag) {
                $strCoverolumns = count($rowData);
                for ($j = 0; $j < $strCoverolumns; $j++) {
                    $arrayColumn[$j] = $rowData[$j];
                }
                $flag = 1;
            } elseif(!$flagGet) {
                for ($j = 0; $j < $strCoverolumns; $j++) {
                    if ($arrayColumn[$j] == $arr['strColumn'] && $rowData[$j] == $arr['value']) {
                        for ($k = 0; $k < $strCoverolumns; $k++) {
                            $arrayRow{$arrayColumn[$k]} = $rowData[$k];
                        }
                        break;
                    }
                }
            } else {
                break;
            }
        }
        flock($fp, LOCK_UN);
        fclose ($fp);

        return $arrayRow;
    }

    /**
     * $arr = array(
     *     'path' => string,
     * )
     */
    public function getCsvRows($arr, $flag = 0, $rows = 0, $arrayRow = array(),
        $strCoverolumns = 0, $arrayColumn = array()
    ) {
        $fp = fopen($arr['path'], "r") or die;
        flock($fp, LOCK_EX);
        while ($rowData = fgets($fp)) {
			if (preg_match("/^\n/", $rowData)) {
				continue;
			}
        	$rowData = $this->csvConvert($rowData);
            if (!$flag) {
                $strCoverolumns = count($rowData);
                for ($j = 0; $j < $strCoverolumns; $j++) {
                    $arrayColumn[$j] = $rowData[$j];
                }
                $flag = 1;
                continue;
            } else {
                for ($j = 0; $j < $strCoverolumns; $j++) {
                    $arrayRow[$rows]{$arrayColumn[$j]} = $rowData[$j];
                }
            }
            $rows++;
        }
        flock($fp, LOCK_UN);
        fclose ($fp);

        return $arrayRow;
    }

    /**
	 * $arr = array(
	 *     'arrRows' => array(),
	 * )
	 */
	public function getCsvArrRows($arr, $flag = 0, $rows = 0, $arrayRow = array(),
	$strCoverolumns = 0, $arrayColumn = array()
	) {
		$array = $arr['arrRows'];
		foreach ($array as $key => $rowData) {
			$rowData = $this->csvConvert($rowData);
			if (!$flag) {
				$strCoverolumns = count($rowData);
				for ($j = 0; $j < $strCoverolumns; $j++) {
					$arrayColumn[$j] = $rowData[$j];
				}
				$flag = 1;
				continue;
			} else {
				for ($j = 0; $j < $strCoverolumns; $j++) {
					$arrayRow[$rows]{$arrayColumn[$j]} = $rowData[$j];
				}
			}
			$rows++;
		}

		return $arrayRow;
	}

    /**
     * $arr = array(
     *     'path'      => string,
     *     'strColumn' => string,
     *     'value'     => mixed,
     * )
     */
    public function remove_csv_row($arr, $flag = 0, $flagRemove = 0,
        $strCoverolumns = 0, $text = '', $arrayColumn = array(),
        $arrayColumnText = array()
    ) {
		$strCover = $this->_self['strCover'];
        $strDelimiter = $this->_self['strDelimiter'];
        $fp = fopen($arr['path'], "r") or die;
        flock($fp, LOCK_EX);
        while ($rowData = fgets($fp)) {
            $rowData = $this->csvConvert($rowData);
            if (!$flag) {
                $strCoverolumns = count($rowData);
                for ($j = 0; $j < $strCoverolumns; $j++) {
                    $arrayColumn[$j] = $rowData[$j];
                    $arrayColumnText[$j] = $strCover . $rowData[$j] . $strCover;
                }
                $str = join($strDelimiter, $arrayColumnText);
                $text .= $str."\n";
                $flag = 1;
            } elseif (!$flagRemove) {
                for ( $j = 0; $j < $strCoverolumns; $j++) {
                    if($arrayColumn[$j] == $arr['strColumn']
                        && $rowData[$j] == $arr['value']
                    ) {
                        $flagRemove = 1;
                    }
                }
            }
            else{
                $array = array();
                for ( $j = 0; $j < $strCoverolumns; $j++) {
                    $array[$j] = $strCover . $rowData[$j] . $strCover;
                }
                $str = join($strDelimiter, $array);
                $text .= $str . "\n";
            }
        }
        flock($fp, LOCK_UN);
        fclose ($fp);
        $this->setData(array( 'path' => $arr['path'], 'data' => $text ));
    }

	/**
		$classFile->getIdModule(array(
			'flagPlugin' => ''
		))
	 */
	public function getIdModule($arr, $arrayNew = array())
	{
		$array = scandir(PATH_BACK_CLASS_ELSE_PLUGIN);
		foreach ($array as $key => $value) {
			if ( preg_match( "/^\.{1,2}$/", $value)) {
				continue;
			}
			$arrayNew[$value] = 1;
		}
		if ($arr['flagPlugin']) {
			return $arrayNew;
		}

		$arrayNew['base'] = 1;

		return $arrayNew;
	}

	/**
		(array(
			'path' => ''
		))
	 */
	public function deleteDirFile($arr) {

		$pathDir = $arr['path'];
		if(is_dir($pathDir)) {
			$hanDir = opendir($pathDir);
			while($strFile = readdir($hanDir)) {
				if ($strFile == "." || $strFile == "..") {
					continue;
				}
				$path = $pathDir . '/' . $strFile;
				if (filetype($path) == "file") {
					unlink($path);

				} elseif (filetype($path) == "dir") {
					$this->deleteDirFile(array('path' => $path));
				}
			}
			closedir($hanDir);
			rmdir($pathDir);

		}
	}

    /**

		(array(
			'delimiter' => '\t',
			'rows' => array(
				array('id', 'name', 'date'),
				array(1, 'mike', '2004/12/7'),
				array(2, 'john', '2005/12/7'),
			)
		))
     */
    public function getCsvText($arr)
	{
		$text = '';
		$array = $arr['rows'];
		foreach ($array as $key => $value) {
			$str = implode('"' . $arr['delimiter'] . '"', $value);
			$str = '"' . $str . '"' . "\n";
			$text .= $str;
		}

		return $text;
    }

    /**
		(array(
			'pathFrom'    => '',
			'pathTo'      => '',
			'strFromCode' => '',
			'strToCode'   => '',
		))
     */
    public function setConvert($arr)
	{
		$str = file_get_contents($arr['pathFrom']);
		if ($str == FALSE) {
			return FALSE;
		}
		$strFromCode = $arr['strFromCode'];
		if (!$strFromCode) {
			$strFromCode = 'auto';
		}
		$str = mb_convert_encoding($str, $arr['strToCode'], $strFromCode);
		$this->setData(array(
			'path' => ($arr['pathTo'])? $arr['pathTo'] : $arr['pathFrom'],
			'data' => $str,
		));
    }

	/**
		setVarDump(array(
			'path'    => '',
			'vars'    => array(),
		))
	 */
	public function setVarDump($arr)
	{
		ob_start();
		var_dump($arr['vars']);
		$output = ob_get_contents();
		ob_end_clean();
		$path = $arr['path'];
		if (!$path) {
			$path = 'var_dump.txt';
		}
		file_put_contents($path, $output, FILE_APPEND);
	}

	/**

	 */
	public function copyAll($pathDir, $pathDirNew)
	{
		if (!empty($pathDirNew) && !file_exists($pathDirNew)) {
			mkdir($pathDirNew);
		}
		$handle = opendir($pathDir);
		while (false !== ($item = readdir($handle))) {
			if (preg_match( "/^\.{1,2}$/", $item)) {
				continue;
			}
			$pathFile = $pathDir . '/' . $item;
			$pathFileNew = $pathDirNew . '/' . $item;
			if (is_dir($pathFile)) {
				if (!empty($pathFileNew) && !file_exists($pathFileNew)) {
					mkdir($pathFileNew);
				}
				$this->copyAll($pathFile, $pathFileNew);
			} else {
				if (file_exists($pathFileNew)) {
					unlink($pathFileNew);
				}
				copy($pathFile, $pathFileNew);
			}
		}
		closedir($handle);
	}

	/**

	 */
	public function deleteAll($pathDir)
	{
		$handle = opendir($pathDir);
		while (false !== ($item = readdir($handle))) {
			if (preg_match( "/^\.{1,2}$/", $item)) {
				continue;
			}
			$pathFile = $pathDir . '/' . $item;
			if (is_dir($pathFile)) {
				$this->deleteAll($pathFile);

			} else {
				unlink($pathFile);
			}
		}
		closedir($handle);
		rmdir($pathDir);
	}

}
