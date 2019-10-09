<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
/*
 * 20191001 start
 */
/* 改訂注意点
 * 定数排除、Batchを先頭に付ける
 * */
//require_once(PATH_BACK_DAT_VERSION . 'Batch14800/class/Accounting.php');
//require_once(PATH_BACK_DAT_VERSION . 'Batch14800/class/jpn/Jpn.php');
//class Code_Batch14800 extends Code_Else_Plugin_Accounting_Jpn_JpnBatch14800

class Code_Batch14800
{
    protected $_selfBatch = array(
        'numVersion'     => 0,
        'numVersionThis' => 14800,
    );

    function __construct()
    {
        $arr = @func_get_arg(0);
        if (!$arr) {
            if (FLAG_TEST) {
                var_dump(__CLASS__ . '/' .__FUNCTION__. '/' .__LINE__);
            }
            exit;
        }
        $this->_selfBatch['numVersion'] = $arr['numVersion'];
    }

    /**
     *
     */
    public function run()
    {
        if ($this->_selfBatch['numVersion'] >= $this->_selfBatch['numVersionThis']) {
            return;
        }

        //$this->_setBatchJpn();
        //$this->_setBatchPath();
        if (FLAG_TEST) {
            //$this->_setBatchColumn();
            //$this->_setBatchCopyJsonConsumptionTax();
            //exit;

        } else {
            $this->_setBatchColumn();
            $this->_setBatchCopyJsonConsumptionTax();
        }
    }

    /**
    protected function _setBatchJpn()
    {
        global $classTime;

        define('PLUGIN_ACCOUNTING_NUM_TIME_ZONE', 9);
        $classTime->setTimeZone(array('data' => PLUGIN_ACCOUNTING_NUM_TIME_ZONE));
    }
    */

    /*
     *
     *
    protected function _setBatchPath()
    {
        define('PATH_BATCH14800_CLASS',   PATH_BACK_DAT_VERSION . 'Batch14800/class/');
        define('PATH_BATCH14800_VARS',   PATH_BACK_DAT_VERSION . 'Batch14800/vars/');
        define('PATH_BATCH14800_TEMPLATES',   PATH_BACK_DAT_VERSION . 'Batch14800/templates/');
    }*/

    /*
     *
     * */
    protected function _setBatchColumn()
    {
        global $classDb;
        $dbh = $classDb->getHandle();

        //column add
        $stmt = $dbh->prepare('alter table accountingLogCalcJpn add flagRateConsumptionTaxReduced int unsigned default 0 after numValue;');
        $stmt->execute();

        //column add
        $stmt = $dbh->prepare('alter table accountingFSValueJpn add jsonConsumptionTaxDetail longtext after jsonConsumptionTax;');
        $stmt->execute();


    }


    /**
    (array(

    ))
    */
    protected function _setBatchCopyJsonConsumptionTax()
    {
        $varsFSValues = $this->_getBatchVarsFSValues(array());

        $varsFSValues = $this->_loopBatchVarsFSValue(array(
            'varsFSValues'     => $varsFSValues,
        ));

        $arrayFSValue = $varsFSValues;
        foreach ($arrayFSValue as $keyFSValue => $valueFSValue) {
            $this->_updateBatchDb(array(
                'varsFSValue' => $valueFSValue,
            ));
        }
    }

    /**
    (array(

    ))
    */
    protected function _updateBatchDb($arr)
    {
        global $classDb;
        $dbh = $classDb->getHandle();

        $arrColumn = array();
        $arrValue = array();

        $arrColumn[] = 'jsonConsumptionTaxDetail';
        $arrValue[] = json_encode($arr['varsFSValue']['jsonConsumptionTaxDetail']);

        $classDb->updateRow(array(
            'idModule'  => 'accounting',
            'strTable' => 'accountingFSValueJpn',
            'arrColumn' => $arrColumn,
            'flagAnd'  => 1,
            'arrWhere'  => array(
                array(
                    'flagType'      => '',
                    'strColumn'     => 'id',
                    'flagCondition' => 'eq',
                    'value'         => $arr['varsFSValue']['id'],
                ),
            ),
            'arrValue'  => $arrValue,
        ));

    }

    /**
    (array(
    'varsFSValues'     => $varsFSValues,
    ))
    */
    protected function _loopBatchVarsFSValue($arr)
    {
        $arrayNew = array();
        $arrayFSValue = $arr['varsFSValues'];
        foreach ($arrayFSValue as $keyFSValue => $valueFSValue) {
            if (!$valueFSValue['jsonConsumptionTax']) {
                continue;
            }
            $varsTemp = array(
                'varsReduced' => array(),
                'varsOther'   => $valueFSValue['jsonConsumptionTax'],
            );
            $valueFSValue['jsonConsumptionTaxDetail'] = $varsTemp;
            $arrayNew[] = $valueFSValue;

        }

        return $arrayNew;

    }

    /**
    (array(
    'idDepartment'    => $key,
    'numFiscalPeriod' => $arr['numFiscalPeriod'],
    ))

    */
    protected function _getBatchVarsFSValues($arr)
    {
        global $classDb;

        $rows = $classDb->getSelect(array(
            'idModule' => 'accounting',
            'strTable' => 'accountingFSValueJpn',
            'arrLimit' => array(),
            'arrOrder' => array(),
            'flagAnd'  => 1,
            'arrWhere' => array(
            ),
        ));

        return $rows['arrRows'];
    }

}
/*
 * 20191001 end
 */