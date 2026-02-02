<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
require_once(PATH_BACK_CLASS_ELSE_LIB . "/Check.php");
require_once(PATH_BACK_CLASS_ELSE_LIB . "/File.php");

/**
 *
 */
class Code_Else_Lib_Media
{
    protected $_self = array(
        'flagRobotReject' => 1,
        'path' => array(
            'file' => array(
                'ip' => 'back/dat/ip/device.csv',
            ),
        ),
    );

    function __construct($arr = null)
    {
        // $arr = @func_get_arg(0);
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
     *
     */
    public function getDetail()
    {
        return array(
            'ip' => $this->getIp(),
            'host' => $this->getHost(),
            'device' => $this->getDevice(),
        );
    }

    /**
     *
     */
    public function getIp()
    {
        return getenv("REMOTE_ADDR");
    }

    /**
     *
     */
    public function getHost()
    {

        $ip = $this->getIp();
        $host = getenv("REMOTE_HOST");

        if (!$host || $host == $ip) {
            $host = gethostbyaddr($ip);
        }

        return $host;
    }

    /**
     *
     */
    public function getDevice()
    {

        $ip = $this->getIp();
        $classCheck = new Code_Else_Lib_Check();
        $classFile = new Code_Else_Lib_File();
        $array = $classFile->getCsvRows(array('path' => $this->_self['path']['file']['ip']));
        $numAll = count($array);
        $device = 'else';
        for ($j = 0; $j < $numAll; $j++) {
            $flag = $classCheck->ipRange(array(
                'ip' => $ip,
                'arr' => array($array[$j]['ip']),
            ));
            if ($flag) {
                $device = $array[$j]['device'];
            }
        }

        return $device;
    }
}
