<?php /* Smarty version 3.1.24, created on 2017-01-06 11:11:44
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/login/html/index.html" */ ?>
<?php
/*%%SmartyHeaderCode:740614606586f7b70c4cea5_40651375%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a77b05922fd16fa63c1ffccf79c726b46222bbcb' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/login/html/index.html',
      1 => 1483698222,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '740614606586f7b70c4cea5_40651375',
  'variables' => 
  array (
    'strLang' => 0,
    'headTitle' => 0,
    'caution' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_586f7b70d372b7_51111521',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_586f7b70d372b7_51111521')) {
function content_586f7b70d372b7_51111521 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '740614606586f7b70c4cea5_40651375';
?>
<!DOCTYPE html>
<html lang="<?php echo $_smarty_tpl->tpl_vars['strLang']->value;?>
">
<head>
<META NAME="ROBOTS,Google,BingBot,Slurp,Baiduspider" CONTENT="NOINDEX,NOFOLLOW,NOARCHIVE,NOSNIPPET,NOODP,NOYDIR,NOPREVIEW,NOIMAGEINDEX"> 
<meta charset="utf-8"/>
<title><?php echo $_smarty_tpl->tpl_vars['headTitle']->value;?>
</title>
<link href="front/else/lib/css/style.css" rel="stylesheet" type="text/css" />
<link href="front/else/core/login/css/style.css" rel="stylesheet" type="text/css" />
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
 type="text/javascript" src="front/else/core/login/js/<?php echo $_smarty_tpl->tpl_vars['strLang']->value;?>
/index.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript">
var insRoot;

	document.observe('dom:loaded', function()
	{
		insRoot = new Code_Core_Login();
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