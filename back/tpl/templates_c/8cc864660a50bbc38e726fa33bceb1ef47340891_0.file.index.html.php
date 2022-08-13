<?php /* Smarty version 3.1.24, created on 2022-08-13 00:08:09
         compiled from "/var/www/html/accounting/back/tpl/templates/else/config/html/index.html" */ ?>
<?php
/*%%SmartyHeaderCode:49668359662f6eb69475267_53102989%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8cc864660a50bbc38e726fa33bceb1ef47340891' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/config/html/index.html',
      1 => 1272241336,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '49668359662f6eb69475267_53102989',
  'variables' => 
  array (
    'strLang' => 0,
    'headTitle' => 0,
    'loadJs' => 0,
    'caution' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_62f6eb6947ed20_50954616',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6eb6947ed20_50954616')) {
function content_62f6eb6947ed20_50954616 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '49668359662f6eb69475267_53102989';
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