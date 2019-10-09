<?php
/*
 * RUCARO (GPL License Version2)
 * Copyright(c) rucaro.org All Rights Reserved.
 * http://rucaro.org/
 */
class Code_Else_Lib_Mail
{
	protected $_self = array(
		'boundaryLine' => '--boundary-line--',
	);

	function __construct()
	{

	}

	/**
	 * $arr = array(
	 * 	'pathVars'    => string,
	 * 	'pathTpl'     => string,
	 * 	'arrValue'    => array,
	 * 	'mailTo'      => string,
	 * 	'mailFrom'    => string,
	 *  'strNameFrom' => string,
		'arrFile'    => array(
			array(
				'pathFile'        => PATH_BACK_DAT . 'pdf/bank.pdf',
				'strContentType'  => 'application/pdf',
				'strFileTitle'    => $vars['strFileTitle'],
			),
			array(
				'pathFile'        => PATH_BACK_DAT . 'pdf/bank_explain.pdf',
				'strContentType'  => 'application/pdf',
				'strFileTitle'    => $vars['strFileTitleInput'],
			),
		)
	 * )
	 */
	public function setMail($arr)
	{
		global $classEscape;

		$vars = $arr['vars'];

		if (!$vars) {
			$vars = $classEscape->getVars(array(
				'data' => $arr['pathVars'],
				'arr'  => array(
					array('before' => '<strLang>', 'after' => STR_LANG,),
				),
			));
		}
		$path = $classEscape->loopReplace(array(
			'data' => $arr['pathTpl'],
			'arr'  => array(
				array('before' => '<strLang>', 'after' => STR_LANG,),
			),
		));
		$output = file_get_contents($path);

		$strSubject = '';
		$array = $vars;
		foreach ($array as $key => $value) {
			if ($key == 'strSubject') {
				$strSubject = $value;
				continue;

			} elseif (!is_null($arr['arrValue'][$key])) {
				$value = $arr['arrValue'][$key];

			}
			$str = '{$' . $key . '}';
            $output = str_replace($str, $value, $output);
		}

		$data = array(
			'mailTo'        => $arr['mailTo'],
			'mailFrom'      => $arr['mailFrom'],
			'strNameFrom'   => $arr['strNameFrom'],
			'strSubject'    => $strSubject,
			'strMessage'    => $output,
		);

		if ($arr['arrFile']) {
			$data['arrFile'] = $arr['arrFile'];

			return $this->sendMailFile($data);
		}

		return $this->sendMail($data);
	}

	/**
	 * $arr = array(
	 * 	'mailTo'   => string,
	 * 	'mailFrom'    => string,
	 *  'strNameFrom' => string,
	 * 	'strSubject'  => string,
	 * 	'strMessage'  => string,
	 * )
	 */
	public function sendMail($arr, $strTo ='')
	{
		$strTo = $this->_setHeader(array(
			'strTitle' => $arr['strNameFrom'],
			'arr'      => array($arr['mailFrom']),
		));
		$header = 'From: ' . $strTo . "\n";

		if(!@mb_send_mail($arr['mailTo'], $arr['strSubject'], $arr['strMessage'], $header)){
			return 0;

		}

		return 1;
	}

	/**
	 * $arr = array(
	 * 	'mailTo'      => string,
	 * 	'mailFrom'    => string,
	 *  'strNameFrom' => string,
	 * 	'strSubject'  => string,
	 * 	'strMessage'  => string,
	 * 	'arrFile'    => array,
	 * )
	 */
	public function sendMailFile($arr, $strTo ='')
	{

		$strFrom = $this->_setHeader(array(
			'strTitle' => $arr['strNameFrom'],
			'arr'      => array($arr['mailTo']),
		));

		//ヘッダ情報
		$sendto   = $arr['mailTo'];
		$subject  = $arr['strSubject'];
		$headers  = "FROM:" . $strFrom . "\r\n";
		$headers .= 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-Type: multipart/mixed;boundary="1000000000"' . "\r\n";

		$strMessage = $arr['strMessage'];



$message =<<<END

--1000000000
Content-Type: text/plain; charset=iso-2022-jp
Content-Transfer-Encoding: 7bit

$strMessage



END;
		$array = $arr['arrFile'];

		foreach ($array as $key => $value) {
			$strContentType = $value['strContentType'];
			$strFileTitle = $value['strFileTitle'];
			$img = file_get_contents($value['pathFile']);
			$img_encode64_000 = chunk_split(base64_encode($img));
$message .=<<<END

--1000000000
Content-Type: $strContentType; name="$strFileTitle"
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename="$strFileTitle"

$img_encode64_000

END;




		}
$message .=<<<END

--1000000000--

END;

		$subject = mb_encode_mimeheader($subject);
		$message = mb_convert_encoding($message, "JIS");

		mail( $sendto, $subject, $message, $headers);

		return 1;
	}

	/**
	 *array(
	 * strTitle => string,
	 * arr => array
	 *)
	 */
	protected function _setHeader($arr)
	{
		$array = $arr['arr'];
		$arrayNew = array();
		$num = 0;
		foreach ($array as $key => $value) {
			$str = mb_encode_mimeheader($arr['strTitle']) . '<' . $value . '>';
			$arrayNew[$num] = $str;
			$num++;
		}
		$str = join(',', $arrayNew);

		return $str;

	}
}
