<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Core_Base_Logout extends Code_Else_Core_Base_Base
{
	protected $_extSelf = array(
	);

	/**
	 *
	 */
	public function run()
	{
		$this->_removeSession();
		$this->sendValue(array(
			'flag'    => 1,
			'stamp'   => array(),
			'numNews' => array(),
			'vars'    => array(),
		));

	}

	/**
	 *
	 */
	protected function _removeSession()
	{

		global $varsSession;
		global $varsAccount;
		global $varsSession;
		global $classDb;
		$dbh = $classDb->getHandle();

		try {
			$dbh->beginTransaction();

			$stampLimit = TIMESTAMP - NUM_SESSION * 1;
			$stmt = $dbh->prepare('delete from baseToken where stampRegister < ?;');
			$stmt->execute(array($stampLimit));

			$stampLimit = TIMESTAMP - NUM_SESSION * 1;
			$stmt = $dbh->prepare('delete from baseSession where stampRegister < ?;');
			$stmt->execute(array($stampLimit));

			$stmt = $dbh->prepare('delete from baseSession where idAccount = ?;');
			$stmt->execute(array($varsAccount['id']));

			$stmt = $dbh->prepare('delete from baseToken where idAccount = ?;');
			$stmt->execute(array($varsAccount['id']));

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
