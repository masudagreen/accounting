<?php
/*
 * RUCARO (GPL License Version2)
 * RUCARO Accounting(GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Plugin_Accounting_Routine
{

	protected $_extSelf = array(

	);

	/**
	 * $arr = array(
	 * 	'flagType' => string,
	 * )
	 */
	public function run($arr)
	{
		if ($arr['routine']['flagMonth']) {
			$this->_setMonth();
		}
		if ($arr['routine']['flagDate']) {
			$this->_setDate();
		}
		if ($arr['routine']['flagHour']) {
			$this->_setHour();
		}
	}

	/**
	 *
	 */
	protected function _setMonth()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		try {
			$dbh->beginTransaction();


			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
	}

	/**
	 *
	 */
	protected function _setDate()
	{
		global $classDb;
		$dbh = $classDb->getHandle();


		try {
			$dbh->beginTransaction();


			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}

	}

	/**
	 *
	 */
	protected function _setHour()
	{
		global $classDb;
		$dbh = $classDb->getHandle();

		try {
			$dbh->beginTransaction();


			$dbh->commit();
		} catch (PDOException $e) {
			$dbh->rollBack();
			if (FLAG_TEST) {
				var_dump($e->getMessage());
			}
			exit;
		}
	}





}
