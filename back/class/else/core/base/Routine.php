<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Routine
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

			$stampLimit = TIMESTAMP - NUM_SESSION * 365;
			$stmt = $dbh->prepare('delete from baseLoginMiss where stampRegister < ?;');
			$stmt->execute(array($stampLimit));

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

			$stampLimit = TIMESTAMP - 86400 * 30;
			$stmt = $dbh->prepare('delete from baseAccessLog where stampRegister < ?;');
			$stmt->execute(array($stampLimit));

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

		$tm = TIMESTAMP;
		try {
			$dbh->beginTransaction();

			$stampLimit = TIMESTAMP - NUM_SESSION * 1;
			$stmt = $dbh->prepare('delete from baseToken where stampRegister < ?;');
			$stmt->execute(array($stampLimit));

			$stampLimit = TIMESTAMP - NUM_SESSION * 1;
			$stmt = $dbh->prepare('delete from baseSession where stampRegister < ?;');
			$stmt->execute(array($stampLimit));

			$stmt->execute(array($stampLimit));
			if (FLAG_APC) {
				$stmt = $dbh->prepare('select * from baseSession;');
				$stmt->execute();
				$array = array();
				$num = 1;
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$str = ($row['idCookie'])? $row['idCookie'] : $row['idMobile'];
					$array[$str] = $row;
				}
				$varsSession = $array;
				apc_store('varsSession', $varsSession);
			}

			$stampLimit = $tm - NUM_SESSION * 1;
			$stmt = $dbh->prepare('delete from baseApplySign where stampRegister < ? and flagAttest = 0;');
			$stmt->execute(array($stampLimit));

			$stampLimit = $tm - NUM_SESSION * 1;
			$stmt = $dbh->prepare('delete from baseApplyChange where stampRegister < ? and flagAttest = 0;');
			$stmt->execute(array($stampLimit));

			$stampLimit = $tm - NUM_SESSION * 1;
			$stmt = $dbh->prepare('delete from baseApplyForgot where stampRegister < ?');
			$stmt->execute(array($stampLimit));

			$stampLimit = $tm - NUM_SESSION * 7;
			$stmt = $dbh->prepare('delete from basePublish where stampRegister < ? and stampRegister > 0');
			$stmt->execute(array($stampLimit));

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
