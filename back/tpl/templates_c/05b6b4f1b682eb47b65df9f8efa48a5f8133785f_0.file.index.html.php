<?php /* Smarty version 3.1.24, created on 2022-08-13 00:23:56
         compiled from "/var/www/html/accounting/back/tpl/templates/else/core/base/html/index.html" */ ?>
<?php
/*%%SmartyHeaderCode:44810875962f6ef1c255777_87657383%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '05b6b4f1b682eb47b65df9f8efa48a5f8133785f' => 
    array (
      0 => '/var/www/html/accounting/back/tpl/templates/else/core/base/html/index.html',
      1 => 1422415614,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '44810875962f6ef1c255777_87657383',
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
  'unifunc' => 'content_62f6ef1c283669_96634969',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_62f6ef1c283669_96634969')) {
function content_62f6ef1c283669_96634969 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '44810875962f6ef1c255777_87657383';
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