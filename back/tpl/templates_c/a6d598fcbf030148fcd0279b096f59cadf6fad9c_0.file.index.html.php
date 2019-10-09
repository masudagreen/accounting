<?php /* Smarty version 3.1.24, created on 2016-08-18 12:49:30
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/config/html/index.html" */ ?>
<?php
/*%%SmartyHeaderCode:166541213557b5aeda5e1ab3_64214412%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a6d598fcbf030148fcd0279b096f59cadf6fad9c' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/config/html/index.html',
      1 => 1471523676,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '166541213557b5aeda5e1ab3_64214412',
  'variables' => 
  array (
    'strLang' => 0,
    'headTitle' => 0,
    'loadJs' => 0,
    'caution' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_57b5aeda610942_66369299',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_57b5aeda610942_66369299')) {
function content_57b5aeda610942_66369299 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '166541213557b5aeda5e1ab3_64214412';
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