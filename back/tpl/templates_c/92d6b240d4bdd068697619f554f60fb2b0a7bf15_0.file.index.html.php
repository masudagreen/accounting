<?php /* Smarty version 3.1.24, created on 2019-10-06 06:33:52
         compiled from "/app/rucaro/back/tpl/templates/else/core/login/html/index.html" */ ?>
<?php
/*%%SmartyHeaderCode:6377298205d998ad09ea209_87933407%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '92d6b240d4bdd068697619f554f60fb2b0a7bf15' => 
    array (
      0 => '/app/rucaro/back/tpl/templates/else/core/login/html/index.html',
      1 => 1570328742,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6377298205d998ad09ea209_87933407',
  'variables' => 
  array (
    'strLang' => 0,
    'headTitle' => 0,
    'caution' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d998ad0e05899_36700789',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d998ad0e05899_36700789')) {
function content_5d998ad0e05899_36700789 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '6377298205d998ad09ea209_87933407';
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