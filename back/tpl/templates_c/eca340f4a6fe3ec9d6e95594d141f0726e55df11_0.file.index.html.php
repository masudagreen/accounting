<?php /* Smarty version 3.1.24, created on 2019-10-06 02:28:15
         compiled from "/app/rucaro/back/tpl/templates/else/config/html/index.html" */ ?>
<?php
/*%%SmartyHeaderCode:2010553435d99513f706f89_62243487%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eca340f4a6fe3ec9d6e95594d141f0726e55df11' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/config/html/index.html',
      1 => 1570328738,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2010553435d99513f706f89_62243487',
  'variables' => 
  array (
    'strLang' => 0,
    'headTitle' => 0,
    'loadJs' => 0,
    'caution' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d99513f854712_77498146',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d99513f854712_77498146')) {
function content_5d99513f854712_77498146 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '2010553435d99513f706f89_62243487';
?>
<!DOCTYPE html>
<html lang=<?php echo $_smarty_tpl->tpl_vars['strLang']->value;?>
>
<head>
<meta charset="utf-8"/>
<title><?php echo $_smarty_tpl->tpl_vars['headTitle']->value;?>
</title>
<link href="front/else/lib/css/style.css" rel="stylesheet" type="text/css" />
<link href="front/else/config/css/style.css" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="front/else/core/base/img/favicon.ico">
<?php echo '<script'; ?>
 type="text/javascript" src="front/else/lib/ext/prototype.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="front/else/lib/ext/scriptaculous.js?load=effects,dragdrop"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="front/else/lib/js/<?php echo $_smarty_tpl->tpl_vars['strLang']->value;?>
/code.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript">
	<?php echo $_smarty_tpl->tpl_vars['loadJs']->value;?>

var insRoot;

	document.observe('dom:loaded', function()
	{
		insRoot = new Code_Config();
	});

<?php echo '</script'; ?>
>
</head>
<body id='Root' onunload=''>
<noscript><?php echo $_smarty_tpl->tpl_vars['caution']->value;?>
</noscript>
</body>
</html>
<?php }
}
?>