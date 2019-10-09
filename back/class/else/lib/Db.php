<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Lib_Db
{
    protected $_self = array(
		'dbh'      => null,
		'driver'   => 'mysql',
		'username' => '',
		'password' => '',
		'host'     => '',
		'dbname'   => '',
		'dbtype'   => '',
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
     * 	'key' => string,
     * )
     */
    public function getSelf($arr)
    {
        return $this->_self[$arr['key']];
    }

    /**
     *
     */
    public function getHandle()
    {
        return $this->_self['dbh'];
    }

    /**
     * $arr = array(
     * 	'driver'   => string,
     *  'username' => string,
     *  'password' => string,
     *  'host'     => string,
     *  'dbname'   => string,
     *  'dbtype'   => string,
     * )
     */
    public function setHandle($arr)
    {
		$this->setVar($arr);
		$dsn = $this->_self['driver'] . ':'
			. 'host=' . $this->_self['host'] . ';'
			. 'dbname=' . $this->_self['dbname'] . ';';
		try {
		    if (version_compare(PHP_VERSION, '5.3.6') >= 0) {
		    	$dsn .= 'charset=utf8';
		    	$dbh = new PDO($dsn, $this->_self['username'], $this->_self['password']);

		    } else {
		    	$options = array(
						PDO::MYSQL_ATTR_READ_DEFAULT_FILE  => '/etc/my.cnf',
						PDO::MYSQL_ATTR_READ_DEFAULT_GROUP => 'php',
					);
		    	$dbh = new PDO($dsn, $this->_self['username'], $this->_self['password'], $options);
		    }

		} catch (PDOException $e) {
		    exit;
		}
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->_self['dbh'] = &$dbh;

        return $dbh;
    }

    /**
     *
     */
    public function checkConnect()
    {
		$dsn = $this->_self['driver'] . ':'
			. 'host=' . $this->_self['host'] . ';'
			. 'dbname=' . $this->_self['dbname'] . ';';

		try {
			if (version_compare(PHP_VERSION, '5.3.6') >= 0) {
		    	$dsn .= 'charset=utf8';
		    	$dbh = new PDO($dsn, $this->_self['username'], $this->_self['password']);

		    } else {
		    	$options = array(
						PDO::MYSQL_ATTR_READ_DEFAULT_FILE  => '/etc/my.cnf',
						PDO::MYSQL_ATTR_READ_DEFAULT_GROUP => 'php',
					);
		    	$dbh = new PDO($dsn, $this->_self['username'], $this->_self['password'], $options);
		    }

			$dbh = null;
		} catch (PDOException $e) {
			$dbh = null;
		    return 0;
		}

		return 1;
    }

    /**
     * $arr = array(
     * 	'driver'   => string,
     *  'username' => string,
     *  'password' => string,
     *  'host'     => string,
     *  'dbname'   => string,
     *  'dbType'   => string,
     * )
     */
    public function setVar($arr)
    {
        foreach ($arr as $key => $value) {
           // if (empty($this->_self[$key])) {
				$this->_self[$key] = $value;
			//}
        }
    }

    /**
     * $arr = array(
     *     strSql    => string,
     *     arrValue  => array,
     *     strColumn => string,
     * )
     */
    public function getColumnValue($arr)
    {
		if (is_null($this->_self['dbh'])) {
			return;
		}
		$dbh = $this->getHandle();

		$stmt = $dbh->prepare($arr['strSql']);
		$stmt->execute($arr['arrValue']);

		$data;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$str = $arr['strColumn'];
			if (preg_match("/^json/", $str)) {
				$data = (!is_null($row[$str]))? json_decode($row[$str], true) : null;
			} else {
				$data = (!is_null($row[$str]))? $row[$str] : null;
			}
			break;
		}

		return $data;
	}

    /**
     * $arr = array(
     *     strSql    => string,
     *     arrValue  => array,
     *     strColumn => string,
     * )
     */
    public function getColumnArrValue($arr)
    {
		if (is_null($this->_self['dbh'])) {
			return;
		}
		$dbh = $this->getHandle();

		$stmt = $dbh->prepare($arr['strSql']);
		$stmt->execute($arr['arrValue']);

		$array = array();
		$num = 0;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$data;
			$str = $arr['strColumn'];
			if (preg_match("/^json/", $str)) {
				$data = (!is_null($row[$str]))? json_decode($row[$str], true) : null;

			} else {
				$data = (!is_null($row[$str]))? $row[$str] : null;
			}
			$array[$num] = $data;
			$num++;
		}

		return $array;
	}

    /**
     * $arr = array(
     *     strDbName => string,
     * )
     */
    public function getDbSize($arr)
    {
		if (is_null($this->_self['dbh'])) {
			return;
		}
		$dbh = $this->getHandle();

		$stmt = $dbh->prepare('show tables;');
		$stmt->execute();
		$array = array();
		$num = 0;
		while ($row = $stmt->fetch()) {
			$array[$num] = $row[0];
			$num++;
		}

		$all = 0;
		$rowData = array();
        foreach ($array as $key => $value) {
			$sql = 'show table status from ' . $arr['strDbName'] . ' like ?;';
			$stmt = $dbh->prepare($sql);
			$stmt->execute(array($value));
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$rowData[$value] = $row['Data_length'];
				$all += $row['Data_length'];
			}
		}
		$rowData['all'] = $all;

		return $rowData;
	}

    /**
     *
     */
    public function getTableList()
    {
		if (is_null($this->_self['dbh'])) {
			return array();
		}
		$dbh = $this->getHandle();

		$stmt = $dbh->prepare('show tables;');
		$stmt->execute();
		$array = array();
		while ($row = $stmt->fetch()) {
			$array[$row[0]] = 1;
		}

		return $array;
	}

    /**
		(array(
			'flagDbType' => '',
		))
     */
    public function getDbh($arr)
    {
		global $classFile;

		$this->_checkBatch13000();
		$array = $classFile->getCsvRows(array('path' => PATH_BACK_DAT_CONNECT));
		$num = null;
		foreach ($array as $key => $value) {
			if ($value['dbtype'] == $arr['flagDbType']) {
				$num = $key;
				break;
			}
		}

		$dbh = $this->setHandle(array(
			'driver'   => $array[$num]['driver'],
			'username' => $array[$num]['username'],
			'password' => $array[$num]['password'],
			'host'     => $array[$num]['host'],
			'dbname'   => $array[$num]['dbname'],
			'dbtype'   => $array[$num]['dbtype'],
		));

		return $dbh;
	}

	/**
	 */
	private function _checkBatch13000()
	{
		//for batch version < 1.30.00
		$pathUnder13000 = PATH_BACK_DAT . "db/connect.csv";
    	if (file_exists($pathUnder13000)) {
    		copy( PATH_BACK_DAT . "db/connect.csv", PATH_BACK_DAT . "db/connect.cgi" );
    		unlink(PATH_BACK_DAT . "db/connect.csv");
		}
	}

	/**
	 */
	public function setDbhMaster()
	{
		return $this->getDbh(array('flagDbType' => 'master'));
	}

	/**
	 */
	public function getFlagMaster()
	{
		$strDbType = $this->getSelf(array('key' => 'dbtype'));

		if ($strDbType == 'master') {
			return 1;
		}
		return 0;
	}

	/**
	 */
	public function getDbhLog()
	{
		global $classFile;

		$array = $classFile->getCsvRows(array('path' => PATH_BACK_DAT_CONNECT));
		$num = null;
		foreach ($array as $key => $value) {
			if ($value['dbtype'] == 'log') {
				$num = $key;
				break;
			}
		}

		$dsn = $array[$num]['driver'] . ':'
			. 'host=' . $array[$num]['host'] . ';'
			. 'dbname=' . $this->_self['dbname'] . ';';

		try {
			if (version_compare(PHP_VERSION, '5.3.6') >= 0) {
		    	$dsn .= 'charset=utf8';
		    	$dbh = new PDO($dsn, $array[$num]['username'], $array[$num]['password']);

		    } else {
		    	$options = array(
						PDO::MYSQL_ATTR_READ_DEFAULT_FILE  => '/etc/my.cnf',
						PDO::MYSQL_ATTR_READ_DEFAULT_GROUP => 'php',
					);
		    	$dbh = new PDO($dsn, $array[$num]['username'], $array[$num]['password'], $options);
		    }

		} catch (PDOException $e) {
		    exit;
		}
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $dbh;
	}

	/**
	 * $arr = array(
	 * 	'flagSql'             => int,
	 * 	'idModule'           => string,
	 *  'flagType'            => string,ex)Admin,User
	 *  'insCurrent'          => ins,
	 * 	'strTable'            => string,
	 * 	'arrJoin'             => array(),
	 * 	'arrJoins'             => array(),
	 * 	'arrLimit'            => array(
	 * 		'numStart' => int, 'numEnd' => int,
	 * 	),
	 * 	'arrOrder'            => array(
	 * 		'strColumn' => string, 'flagDesc' => int,
	 * 	),
	 *  'flagAnd'             => int,
	 * 	'arrWhere'            => array(
	 * 		array(
	 * 			'flagType' => string,
	 * 			'strColumn' => string,
	 * 			'flagCondition' => string,
	 * 			'value' => mix,
	 * 		),
	 * 	),
	 * 	'arrColumn'  => array(),
	 * 	'arrData'    => array(),
	 * 	'insCurrent' => ins,
	 * 'flagJsonNone' => int,
	 * )
	 */
	public function getSelect($arr)
	{
		if (is_null($this->_self['dbh'])) {
			return;
		}
		$dbh = $this->getHandle();

		$strOrder = $this->_getOrder($arr['arrOrder']);
		$strLimit = $this->_getLimit($arr['arrLimit']);
		$arrWhere = $this->_getWhere(array(
			'idModule'    => $arr['idModule'],
			'flagAnd'     => ($arr['flagAnd'])? 1 : 0,
			'arrWhere'    => $arr['arrWhere'],
			'flagType'    => $arr['flagType'],
			'flagSqlType' => 'Select',
			'insCurrent'  => ($arr['insCurrent'])? $arr['insCurrent'] : '',
			'arrData'     => ($arr['arrData'])? $arr['arrData'] : '',
		));

		$strColumn = '';
		if ($arr['arrColumn']) {
			$strColumn = join(',', $arr['arrColumn']);

		} else {
			$strColumn = '*';

		}

		if ($arrWhere == 0) {
			if ($arr['flagSql']){
				$array = array(
					'strSql' => '',
					'value'  => '',
				);
				return $array;
			} else {
				$array = array(
					'numRows' => 0,
					'arrRows' => array(),
				);
				return $array;
			}
		}

		$strWhere = $arrWhere['strSql'];
		$value = $arrWhere['arrValue'];

		$strTable = $arr['strTable'];

		if ($arr['arrJoin']) {
			$arrJoin = ($arr['arrJoin'])? $arr['arrJoin'] : array();
			$strTable = $arrJoin['strLeftTable']
					. ' left join '
					. $arrJoin['strRightTable']
					. ' on '
					. $arrJoin['strLeftTable']
					. '.'
					. $arrJoin['strLeftKey']
					. ' = '
					. $arrJoin['strRightTable']
					. '.'
					. $arrJoin['strRightKey'];

		} elseif ($arr['arrJoins']) {
			$arrJoins = ($arr['arrJoins'])? $arr['arrJoins'] : array();
			$strTable = '('
					. $arrJoins['strLeftTable']
					. ' left join '
					. $arrJoins['strRightTable']
					. ' on '
					. $arrJoins['strLeftTable']
					. '.'
					. $arrJoins['strLeftKey']
					. ' = '
					. $arrJoins['strRightTable']
					. '.'
					. $arrJoins['strRightKey']
					. ')'
					.' left join '
					. $arrJoins['strThirdTable']
					. ' on '
					. $arrJoins['strLeftTable']
					. '.'
					. $arrJoins['strLeftKey']
					. ' = '
					. $arrJoins['strThirdTable']
					. '.'
					. $arrJoins['strThirdKey'];
		}

		$strSql = 'select sql_calc_found_rows '
				. $strColumn
				. ' from '
				. $strTable . ' '
				. $strWhere . ' '
				. $strOrder . ' '
				. $strLimit . ';';

//var_dump($strSql, $value);

		if ($arr['flagSql']){

			$array = array(
				'strSql' => $strSql,
				'value'  => $arrValue,
			);
//var_dump($array);
			return $array;
		}

		$stmt = $dbh->prepare($strSql);
		if (!$value) {
			$stmt->execute();
		} else {
			$stmt->execute($value);
		}

		$stmtRows = $dbh->prepare('SELECT FOUND_ROWS();');
		$stmtRows->execute();
		while ($row = $stmtRows->fetch(PDO::FETCH_ASSOC)) {
			$numRows = (int) $row['FOUND_ROWS()'];
		}

		$arrRows = array();
		$num = 0;
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			foreach ($row as $key => $value) {
				if (preg_match("/^json/", $key)) {
					if ($arr['flagJsonNone']) {
						$row[$key] = (!is_null($value))? $value : '';
					} else {
						$row[$key] = (!is_null($value))? json_decode($value, true) : array();
					}

				}
			}
			$arrRows[$num] = $row;
			$num++;
		}

		$array = array(
			'numRows' => $numRows,
			'arrRows' => $arrRows,
		);

		return $array;
	}


	/**
	 *
	 */
	protected function _getOrder($arr)
	{
		if (!$arr) {
			return '';
		}

		$str = ' order by ' . $arr['strColumn'];
		if ($arr['flagDesc']) {
			$str .= ' desc';
		}

		return $str;
	}

	/**
	 *
	 */
	protected function _getLimit($arr, $str = '')
	{
		if (!$arr) {
			return '';
		}

		global $classCheck;

		if ($arr['numStart'] >= 0 && $arr['numEnd'] >= 0) {
			$flag = $classCheck->checkValueWord(array(
				'flagType' => 'num',
				'value'    => $arr['numStart'],
			));
			if ($flag) {
				return '';
			}
			$flag = $classCheck->checkValueWord(array(
				'flagType' => 'num',
				'value'    => $arr['numEnd'],
			));
			if ($flag) {
				return '';
			}
			$str = 'limit ' . $arr['numStart'] . ',' . $arr['numEnd'];
		}

		return $str;
	}

	/**
		$this->_getAuthority(array(
			'flagSqlType' => $arr['flagSqlType'],
			'insCurrent'  => ($arr['insCurrent'])? $arr['insCurrent'] : '',
			'arrData'     => ($arr['arrData'])? $arr['arrData'] : '',
		));
	*/
	protected function _getAuthority($arr)
	{
		if (!$arr['insCurrent']) {
			$array = array(
				'strSql'   => '',
				'arrValue' => array(),
			);
			return $array;
		}
		$insCurrent = $arr['insCurrent'];
		$data = $insCurrent->getDBAuthority(array(
			'flagSqlType' => $arr['flagSqlType'],
			'arrData'     => ($arr['arrData']),
		));

		return $data;
	}

	/**
	 * $arr = array(
	 *  'flagAnd'      => int,
	 * 	'arrWhere'     => array,
	 * 	'idModule'     => string,
	 * 	'flagSqlType'  => string, ex) Select,Update,Delete,Output
	 *  'insCurrent'   => ins,
	 *  'arrData'      => array(),
	 * )
	*/
	protected function _getWhere($arr)
	{
		global $classEscape;
		global $classCheck;

		global $varsAccount;

		$flagAnd = $arr['flagAnd'];

		$flagAuthority = $classCheck->checkModuleAuthority(array(
			'idModule' => $arr['idModule'],
		));

		if (!$flagAuthority) {
			return 0;
		}

		$arrAuthority = $this->_getAuthority(array(
			'flagSqlType' => $arr['flagSqlType'],
			'insCurrent'  => ($arr['insCurrent'])? $arr['insCurrent'] : '',
			'arrData'     => ($arr['arrData'])? $arr['arrData'] : '',
		));

		if (!$arrAuthority) {
			return 0;
		}

		if (!$arr['arrWhere']) {
			$data = array(
				'strSql'    => ($arrAuthority['strSql'])? 'where ' . $arrAuthority['strSql'] : '',
				'arrValue'  => $arrAuthority['arrValue'],
			);

			return $data;
		}

		$arr = $arr['arrWhere'];

		foreach ($arr as $key => $value) {
			if (!$arr[$key]['flagType']) {
				continue;

			}

			$flag = $classCheck->checkValueWord(array(
				'flagType' => $arr[$key]['flagType'],
				'flagArr'  => 0,
				'value'    => $arr[$key]['value'],
			));

			if ($flag) {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;

			}
		}

		foreach ($arr as $key => $value) {
			if (preg_match( "/^eq$/", $arr[$key]['flagCondition'])) {
				$arr[$key]['flagCondition'] = '=' ;
				$arr[$key]['value'] = $arr[$key]['value'];

			} elseif (preg_match( "/^regexp$/", $arr[$key]['flagCondition'])) {
				$arr[$key]['flagCondition'] = 'regexp' ;
				$arr[$key]['value'] = $arr[$key]['value'];

			} elseif (preg_match( "/^ne$/", $arr[$key]['flagCondition'])) {
				$arr[$key]['flagCondition'] = '<>' ;
				$arr[$key]['value'] = $arr[$key]['value'];

			} elseif (preg_match( "/^like$/", $arr[$key]['flagCondition'])) {
				$arr[$key]['value'] = str_replace('\\', '\\\\', $arr[$key]['value']);
				$arr[$key]['value'] = str_replace('%', '\%', $arr[$key]['value']);
				$arr[$key]['value'] = str_replace('_', '\_', $arr[$key]['value']);
				$arr[$key]['flagCondition'] = 'like' ;
				$arr[$key]['value'] = '%' . $arr[$key]['value'] . '%';

			} elseif (preg_match( "/^notlike$/", $arr[$key]['flagCondition'])) {
				$arr[$key]['value'] = str_replace('\\', '\\\\', $arr[$key]['value']);
				$arr[$key]['value'] = str_replace('%', '\%', $arr[$key]['value']);
				$arr[$key]['value'] = str_replace('_', '\_', $arr[$key]['value']);
				$arr[$key]['flagCondition'] = 'not like';
				$arr[$key]['value'] = '%' . $arr[$key]['value'] . '%';

			} elseif (preg_match( "/^start$/", $arr[$key]['flagCondition'])) {
				$arr[$key]['value'] = str_replace('\\', '\\\\', $arr[$key]['value']);
				$arr[$key]['value'] = str_replace('%', '\%', $arr[$key]['value']);
				$arr[$key]['value'] = str_replace('_', '\_', $arr[$key]['value']);
				$arr[$key]['flagCondition'] = 'like';
				$arr[$key]['value'] = $arr[$key]['value'] . '%';

			} elseif (preg_match( "/^end$/", $arr[$key]['flagCondition'])) {
				$arr[$key]['value'] = str_replace('\\', '\\\\', $arr[$key]['value']);
				$arr[$key]['value'] = str_replace('%', '\%', $arr[$key]['value']);
				$arr[$key]['value'] = str_replace('_', '\_', $arr[$key]['value']);
				$arr[$key]['flagCondition'] = 'like';
				$arr[$key]['value'] = '%' . $arr[$key]['value'];

			} elseif (preg_match( "/^before|small$/", $arr[$key]['flagCondition'])) {
				$arr[$key]['flagCondition'] = '<' ;
				$arr[$key]['value'] = $arr[$key]['value'];

			} elseif (preg_match( "/^after|big$/", $arr[$key]['flagCondition'])) {
				$arr[$key]['flagCondition'] = '>' ;
				$arr[$key]['value'] = $arr[$key]['value'];

			} elseif (preg_match( "/^eqBefore|eqSmall$/", $arr[$key]['flagCondition'])) {
				$arr[$key]['flagCondition'] = '<=' ;
				$arr[$key]['value'] = $arr[$key]['value'];

			} elseif (preg_match( "/^eqAfter|eqBig$/", $arr[$key]['flagCondition'])) {
				$arr[$key]['flagCondition'] = '>=' ;
				$arr[$key]['value'] = $arr[$key]['value'];

			} else {
				if (FLAG_TEST) {
					var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
				}
				exit;
			}
		}

		$array = array();
		$arrayValue = array();
		$stamp = 86400;

		foreach ($arr as $key => $value) {
			$str = '';

			if (preg_match( "/^stamp$/", $arr[$key]['flagType'])) {
				$arr[$key]['value'] = $arr[$key]['value'];
				if (preg_match( "/^=$/", $arr[$key]['flagCondition'])) {

					$arr[$key]['flagCondition'] = '>=';
					$value = $arr[$key]['value'] + $stamp;
					$condition = '<';

					$str = ' ( ' . $arr[$key]['strColumn'] . ' ' . $arr[$key]['flagCondition']
						. ' ? and ' . $arr[$key]['strColumn'] . ' ' . $condition.' ? ) ';

					$arrayValue[] = $arr[$key]['value'];
					$arrayValue[] = $value;

				} elseif (preg_match( "/^<>$/", $arr[$key]['flagCondition'])) {

					$arr[$key]['flagCondition'] = '<';
					$value = $arr[$key]['value'] + $stamp;
					$condition = '>';

					$str = ' ( ' . $arr[$key]['strColumn'] . ' ' . $arr[$key]['flagCondition']
						. ' ? or ' . $arr[$key]['strColumn'] . ' ' . $condition . ' ? ) ';

					$arrayValue[] = $arr[$key]['value'];
					$arrayValue[] = $value;

				} elseif (preg_match( "/^>$/", $arr[$key]['flagCondition'])) {
					$arr[$key]['value'] += $stamp;
					$str = $arr[$key]['strColumn'] . ' ' . $arr[$key]['flagCondition'] . ' ?';
					$arrayValue[] = $arr[$key]['value'];

				} elseif (preg_match( "/^<=$/", $arr[$key]['flagCondition'])) {
					$arr[$key]['flagCondition'] = '<';
					$arr[$key]['value'] += $stamp;
					$str = $arr[$key]['strColumn'] . ' ' . $arr[$key]['flagCondition'] . ' ?';
					$arrayValue[] = $arr[$key]['value'];

				} else {
					$str = $arr[$key]['strColumn'] . ' ' . $arr[$key]['flagCondition'] . ' ?';
					$arrayValue[] = $arr[$key]['value'];
				}

			} else {
				$str = $arr[$key]['strColumn'] . ' ' . $arr[$key]['flagCondition'] . ' ?';
				$arrayValue[] = $arr[$key]['value'];
			}
			$array[] = $str;
		}

		if ($flagAnd) {
			$strs = join(' and ', $array);

		} else {
			$strs = join(' or ', $array);

		}

		if ($arrAuthority['strSql']) {
			$strSql = '(' . $strs . ') and (' . $arrAuthority['strSql'] . ')';
			$arrayValue = array_merge($arrayValue, $arrAuthority['arrValue']);

		} else {
			$strSql = $strs;
		}

		$data = array(
			'strSql'    => 'where ' . $strSql,
			'arrValue'  => $arrayValue,
		);

		return $data;
	}

	/**
	 * $arr = array(
	 * 	'flagSql'    => int,
	 * 	'idModule'   => string,
	 *  'flagType'   => string,ex)Admin,User
	 *  'insCurrent' => ins,
	 * 	'strTable'   => string,
	 *  'flagAnd'    => int,
	 * 	'arrWhere'   => array(
	 * 		array(
	 * 			'flagType' => string,
	 * 			'strColumn' => string,
	 * 			'flagCondition' => string,
	 * 			'value' => mix,
	 * 		),
	 * 	),
	 * 	'arrData'    => array(),
	 * 	'insCurrent' => ins,
	 * )
	 */
	public function deleteRow($arr)
	{
		if (is_null($this->_self['dbh'])) {
			return;
		}
		if (!$this->getFlagMaster()) {
			return;
		}
		$dbh = $this->getHandle();

		$strDelete = 'delete from ' . $arr['strTable'];

		$arrWhere = $this->_getWhere(array(
			'idModule'    => $arr['idModule'],
			'flagAnd'     => ($arr['flagAnd'])? 1 : 0,
			'arrWhere'    => $arr['arrWhere'],
			'flagType'    => $arr['flagType'],
			'flagSqlType' => 'Delete',
			'insCurrent'  => ($arr['insCurrent'])? $arr['insCurrent'] : '',
			'arrData'     => ($arr['arrData'])? $arr['arrData'] : '',
		));

		if ($arrWhere == 0) {
			if ($arr['flagSql']){
				$array = array(
					'strSql' => '',
					'value'  => array(),
				);

				return $array;

			} else {
				return;
			}
		}
		$strWhere = $arrWhere['strSql'];

		$strSql = $strDelete . ' ' . $strWhere;
		$arr['arrValue'] = $arrWhere['arrValue'];

		if($arr['flagSql']){
			$array = array(
				'strSql' => $strSql,
				'value'  => $arr['arrValue'],
			);
			return $array;
		}

		$stmt = $dbh->prepare($strSql);
		$stmt->execute($arr['arrValue']);

	}

	/**
	 * $arr = array(
	 * 	'flagSql'    => int,
	 * 	'idModule'   => string,
	 *  'flagType'   => string,ex)Admin,User
	 *  'insCurrent' => int,
	 * 	'strTable'   => string,
	 * 	'arrColumn'  => array(),
	 *  'flagAnd'    => int,
	 * 	'arrWhere'   => array(
	 * 		array(
	 * 			'flagType' => string,
	 * 			'strColumn' => string,
	 * 			'flagCondition' => string,
	 * 			'value' => mix,
	 * 		),
	 * 	),
	 * 	arrValue'    => array(),
	 * 	'arrData'    => array(),
	 * 	'insCurrent' => ins,
	 * )
	 */

	public function updateRow($arr)
	{
		if (is_null($this->_self['dbh'])) {
			return;
		}
		$dbh = $this->getHandle();

		$flag = $this->getFlagMaster();
		if (!$flag) {
			return;
		}

		$stmt = $dbh->prepare('show columns from '. $arr['strTable'] . ';');
		$stmt->execute();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($row['Field'] == 'stampUpdate') {
				$arr['arrColumn'][] = 'stampUpdate';
				$arr['arrValue'][] = TIMESTAMP;
				break;
			}
		}

		$strUpdate = 'update ' . $arr['strTable'] . ' set ';

		$array = $arr['arrColumn'];
		$arrColumn = array();
		foreach ($array as $key => $value) {
			$str = $array[$key] . '=?';
			$arrColumn[] = $str;
		}
		$strSet = join(',', $arrColumn);

		$arrWhere = $this->_getWhere(array(
			'idModule'    => $arr['idModule'],
			'flagAnd'     => ($arr['flagAnd'])? 1 : 0,
			'arrWhere'    => $arr['arrWhere'],
			'flagType'    => $arr['flagType'],
			'flagSqlType' => 'Update',
			'insCurrent'  => ($arr['insCurrent'])? $arr['insCurrent'] : '',
			'arrData'     => ($arr['arrData'])? $arr['arrData'] : '',
		));

		if ($arrWhere == 0) {
			if ($arr['flagSql']){
				$array = array(
					'strSql' => '',
					'value'  => array(),
				);

				return $array;

			} else {

				return;

			}
		}
		$strWhere = $arrWhere['strSql'];

		if ($arrWhere['arrValue']) {
			$arr['arrValue'] = array_merge($arr['arrValue'], $arrWhere['arrValue']);
		}

		$strSql = $strUpdate . $strSet . ' ' . $strWhere;

		if($arr['flagSql']){
			$array = array(
				'strSql' => $strSql,
				'value'  => $arr['arrValue'],
			);

			return $array;

		}

		$stmt = $dbh->prepare($strSql);
		$stmt->execute($arr['arrValue']);

	}

	/**
	 * $arr = array(
	 * 	'flagSql'             => int,
	 * 	'idModule'            => string,
	 * 	'strTable'            => string,
	 * 	'arrColumn'           => array(),
	 * 	'arrValue'            => array(),
	 * )
	 */
	public function insertRow($arr)
	{
		if (is_null($this->_self['dbh'])) {
			return;
		}
		$dbh = $this->getHandle();

		$flag = $this->getFlagMaster();
		if (!$flag) {
			return;
		}

		global $classCheck;

		$flagAuthority = $classCheck->checkModuleAuthority(array(
			'idModule' => $arr['idModule'],
		));

		if (!$flagAuthority) {
			return 0;
		}

		$array = $arr['arrValue'];
		$arrValue = array();
		foreach ($array as $key => $value) {
			$arrValue[] = '?';
		}

		$strColumn = join(',', $arr['arrColumn']);
		$strValue = join(',', $arrValue);
		$strSql = 'insert into ' . $arr['strTable'] . '(' . $strColumn . ') values (' . $strValue . ');';

		if($arr['flagSql']){
			$array = array(
				'strSql' => $strSql,
				'value'  => $arr['arrValue'],
			);

			return $array;
		}

		$stmt = $dbh->prepare($strSql);
		$stmt->execute($arr['arrValue']);

		$stmtRows = $dbh->prepare('SELECT LAST_INSERT_ID();');
		$stmtRows->execute();
		while ($row = $stmtRows->fetch(PDO::FETCH_ASSOC)) {
			$id = $row['LAST_INSERT_ID()'];

			return $id;
		}
	}

	/**
		(array(
		))
	 */
	public function checkVersion55()
	{
		if (is_null($this->_self['dbh'])) {
			return;
		}
		$dbh = $this->getHandle();



		$strTarget = $arr['strVersion'];
		$stmt = $dbh->prepare('select version();');
		$stmt->execute();
		$strVarsion = '5';
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$strVarsion = $row['version()'];
		}
		$arrVersion = preg_split("/\./", $strVarsion);
		$numFirst = $arrVersion[0];
		$numSecond = $arrVersion[1];

		if ($numFirst > 5) {
			return 1;
		}

		if (is_null($numSecond)) {
			return 0;
		}

		if ($numSecond >= 5) {
			return 1;
		}

		return 0;
	}

	/**
		(array(
			'strTable' => $arr['strTable'],
		))
	*/
	public function getTableColumn($arr)
	{
		if (is_null($this->_self['dbh'])) {
			return;
		}
		$dbh = $this->getHandle();

		$stmt = $dbh->prepare('show columns from '. $arr['strTable'] . ';');
		$stmt->execute();

		$arrayNew = array();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$arrayNew[$row['Field']] = 1;
		}

		return $arrayNew;
	}
}
