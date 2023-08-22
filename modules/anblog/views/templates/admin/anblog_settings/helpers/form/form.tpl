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

{extends file="helpers/form/form.tpl"}
{block name="input"}
	{if $input.type == 'tabConfig'}
		<div class="row">
			{assign var=tabList value=$input.values}
			<ul class="nav nav-tabs" role="tablist">
			{foreach $tabList as $key => $value name="tabList"}
				<li role="presentation" class="{if $smarty.foreach.tabList.first}active{/if}"><a href="#{$key|escape:'html':'UTF-8'}" class="aptab-config" role="tab" data-toggle="tab">{$value|escape:'html':'UTF-8'}</a></li>
			{/foreach}
			</ul>
		</div>
	{elseif $input.type == 'number'}
		<input type="number"
			   id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
			   name="{$input.name}"
			   class="form-control {if isset($input.class)} {$input.class} {/if}"
			   onkeyup="return (function (el, e) {
                if (e.keyCode == 8) return true;
                jQuery(el).val((parseInt(jQuery(el).val()) || 1));
                if (jQuery(el).val() < (parseInt(jQuery(el).attr('min')) || 1)) {
                    jQuery(el).val((parseInt(jQuery(el).attr('min')) || 1));
                } else if (jQuery(el).val() > (parseInt(jQuery(el).attr('max')) || 99999)) {
                    jQuery(el).val((parseInt(jQuery(el).attr('max')) || 99999));
                }
            })(this, event);"
			   value="{$fields_value[$input.name]|escape:'html':'UTF-8'}"
				{if isset($input.size)} size="{$input.size}"{/if}
				{if isset($input.maxchar) && $input.maxchar} data-maxchar="{$input.maxchar|intval}"{/if}
				{if isset($input.maxlength) && $input.maxlength} maxlength="{$input.maxlength|intval}"{/if}
				{if isset($input.readonly) && $input.readonly} readonly="readonly"{/if}
				{if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}
				{if isset($input.autocomplete) && !$input.autocomplete} autocomplete="off"{/if}
				{if isset($input.required) && $input.required} required="required" {/if}
				{if isset($input.max)} max="{$input.max|intval}"{/if}
				{if isset($input.min)} min="{$input.min|intval}"{/if}
				{if isset($input.placeholder) && $input.placeholder} placeholder="{$input.placeholder}"{/if} />
		{if isset($input.suffix)}
			<span class="input-group-addon">
                {$input.suffix}
            </span>
		{/if}
	{else}
		{$smarty.block.parent}
	{/if}
	
{/block}