{*
* 2020 Anvanto
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author Anvanto (anvantoco@gmail.com)
*  @copyright  2020 anvanto.com

*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<script type="text/javascript">
	var PS_ALLOW_ACCENTED_CHARS_URL = {$PS_ALLOW_ACCENTED_CHARS_URL|escape:'html':'UTF-8'};
	var anblog_del_img_txt = "{$anblog_del_img_txt|escape:'html':'UTF-8'}";
	var anblog_del_img_mess =  "{$anblog_del_img_mess|escape:'html':'UTF-8'}";
</script>
<div class="" id="megamenu">

<div class="col-md-4"><div class="panel panel-default"><h3 class="panel-title">{l s='Tree Blog Categories Management' mod='anblog'} </h3>
			<div class="panel-content">{l s='Drap and drop categories to change their positions or update parent-child connections.' mod='anblog'}
				<hr><p><input type="button" value="{l s='New Category' mod='anblog'}" id="addcategory" data-loading-text="{l s='Processing' mod='anblog'}" class="btn btn-success" name="addcategory"></p><hr>{$tree}</div></div></div>
	<div class="col-md-8">{$form}</div>
<script type="text/javascript"> var action="{$action}"; var addnew ="{$addnew}"; {literal}$("#content").PavMegaMenuList({action:action,addnew:addnew});{/literal}</script>
</div>