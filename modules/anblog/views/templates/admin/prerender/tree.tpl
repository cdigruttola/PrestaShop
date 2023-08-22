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
	<ol class="level{$level|escape:'htmlall'} {if $level == 1}sortable{/if}" >
		{foreach $items as $item}
			<li id="list_{$item['id_anblogcat']|escape:'htmlall'}"  class="{if $item['id_anblogcat'] == $selected}selected{/if}">
				<div>
					<span class="disclose">
						<span>
						</span>
					</span>
					{$item['title']|escape:'htmlall'}
					(ID:{$item['id_anblogcat']|escape:'htmlall'})
					<span class="quickedit" rel="id_{$item['id_anblogcat']|escape:'htmlall'}">E</span>
					<span class="quickdel" rel="id_{$item['id_anblogcat']|escape:'htmlall'}">D</span>
				</div>
				{if isset($item['children'])}
					{assign var=level value=$level+1}
					{call name=printList level=$level items=$item['children']}
				{else}
					{assign var=level value=$level-1}
				{/if}
			</li>
		{/foreach}
	</ol>
{/function}

{call name=printList level=1 items=$tree}


