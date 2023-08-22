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

{function name=printList}
	<ul {if $parent != 1}id="sub_{$parent|escape:'htmlall'}"{/if} class="level{$level|escape:'htmlall'} category-sub-menu{if $level == 1} tree dhtml {else} collapse {/if}">
		{foreach $items as $item}
			<li id="list_{$item['id_anblogcat']|escape:'htmlall'}" class=" {if isset($item['menu_class'])} {$item['menu_class']}{/if} {if $item['id_anblogcat'] == $selected}selected{/if}">
				<a href="{$item['category_link']|escape:'htmlall'}" class="category-sub-link" title="{$item['title']|escape:'htmlall'}">
					{if ($item['icon_class'])}
						<i class="fa fa-{$item['icon_class']}"></i>
					{/if}
					<span>{$item['title']|escape:'htmlall'}</span>
				</a>
				{if isset($item['children'])}
					<div class="navbar-toggler collapse-icons"
						 data-toggle="collapse" data-target="#sub_{$item['id_anblogcat']|escape:'htmlall'}">
						<i class="material-icons add">add</i>
						<i class="material-icons remove">remove</i>
					</div>
					{assign var=level value=$level+1}
					{call name=printList level=$level parent=$item['id_anblogcat'] items=$item['children']}
				{else}
					{assign var=level value=$level-1}
				{/if}
			</li>
		{/foreach}
	</ul>
{/function}

{call name=printList level=1 parent=1 items=$tree}

