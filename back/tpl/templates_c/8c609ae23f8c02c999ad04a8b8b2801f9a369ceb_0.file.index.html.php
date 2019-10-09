<?php /* Smarty version 3.1.24, created on 2019-06-16 09:02:14
         compiled from "/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/html/index.html" */ ?>
<?php
/*%%SmartyHeaderCode:10439125015d0605960d84a7_71594034%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8c609ae23f8c02c999ad04a8b8b2801f9a369ceb' => 
    array (
      0 => '/storage/emulated/0/htdocs/rucaro/back/tpl/templates/else/core/base/html/index.html',
      1 => 1560675138,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '10439125015d0605960d84a7_71594034',
  'variables' => 
  array (
    'strLang' => 0,
    'headTitle' => 0,
    'numVersion' => 0,
    'jsonStatus' => 0,
    'caution' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.24',
  'unifunc' => 'content_5d060596186e56_20408133',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_5d060596186e56_20408133')) {
function content_5d060596186e56_20408133 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '10439125015d0605960d84a7_71594034';
?>
<!DOCTYPE html>
<html lang="<?php echo $_smarty_tpl->tpl_vars['strLang']->value;?>
">
<head>
<meta charset="utf-8"/>
<title><?php echo $_smarty_tpl->tpl_vars['headTitle']->value;?>
</title>
<link href="front/else/lib/css/style.css?v=<?php echo $_smarty_tpl->tpl_vars['numVersion']->value;?>
" rel="stylesheet" type="text/css" />
<link href="front/else/core/base/css/style.css?v=<?php echo $_smarty_tpl->tpl_vars['numVersion']->value;?>
" rel="stylesheet" type="text/css" />
<link rel="shortcut icon" href="front/else/core/base/img/favicon.ico?v=<?php echo $_smarty_tpl->tpl_vars['numVersion']->value;?>
">
<?php echo '<script'; ?>
 type="text/javascript" src="front/else/lib/ext/prototype.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="front/else/lib/ext/protochart.js?v=<?php echo $_smarty_tpl->tpl_vars['numVersion']->value;?>
"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="front/else/lib/ext/scriptaculous.js?load=effects,dragdrop"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="front/else/lib/js/<?php echo $_smarty_tpl->tpl_vars['strLang']->value;?>
/code.js?v=<?php echo $_smarty_tpl->tpl_vars['numVersion']->value;?>
"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript" src="front/else/core/base/js/<?php echo $_smarty_tpl->tpl_vars['strLang']->value;?>
/root.js?v=<?php echo $_smarty_tpl->tpl_vars['numVersion']->value;?>
"><?php echo '</script'; ?>
>
<!--[if IE]><?php echo '<script'; ?>
 type="text/javascript" src="front/else/lib/ext/excanvas.js"><?php echo '</script'; ?>
><![endif]-->
<?php echo '<script'; ?>
 type="text/javascript">

var varsStatus = <?php echo $_smarty_tpl->tpl_vars['jsonStatus']->value;?>
;
var insRoot;


document.observe('dom:loaded',function(){
	insRoot = new Code_Core_Base_Root({varsStatus: varsStatus});
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