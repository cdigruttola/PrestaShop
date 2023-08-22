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

{if isset($no_follow) AND $no_follow}
	{assign var='no_follow_text' value='rel="nofollow"'}
{else}
	{assign var='no_follow_text' value=''}
{/if}

{if isset($p) AND $p}	
	{if ($n*$p) < $nb_items }
		{assign var='blogShowing' value=$n*$p}
	{else}
		{assign var='blogShowing' value=($n*$p-$nb_items-$n*$p)*-1}
	{/if}
	{if $p==1}
		{assign var='blogShowingStart' value=1}
	{else}
		{assign var='blogShowingStart' value=$n*$p-$n+1}
	{/if}
        
	<nav class="pagination">
		{if $start!=$stop}
		<div class="col-xs-12 col-md-6 col-lg-6">		
			{if $nb_items > 1}
				{l s='Showing' mod='anblog'} {$blogShowingStart} - {$blogShowing} {l s='of' mod='anblog'} {$nb_items} {l s='items' mod='anblog'}
			{else}
				{l s='Showing' mod='anblog'} {$blogShowingStart} - {$blogShowing} {l s='of' mod='anblog'} 1 {l s='item' mod='anblog'}
			{/if}
		</div>
		{/if}
		{if $start!=$stop}
			<div id="pagination{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}" class="col-xs-12 col-md-6 col-lg-6">			
				<ul class="page-list clearfix text-sm-center">
					{if $p != 1}
						{assign var='p_previous' value=$p-1}
						<li id="pagination_previous{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}">							
							<a {$no_follow_text|escape:'html':'UTF-8'} class="previous" rel="prev" href="{$link->goPage($requestPage, $p_previous)|escape:'html':'UTF-8'}">
								<svg
                                 xmlns="http://www.w3.org/2000/svg"
                                 xmlns:xlink="http://www.w3.org/1999/xlink"
                                 width="16px" height="7px">
                                <path fill-rule="evenodd"  fill="rgb(153, 153, 153)"
                                 d="M0.218,3.005 L3.193,0.205 C3.484,-0.069 3.955,-0.069 4.246,0.205 C4.536,0.479 4.536,0.922 4.246,1.196 L2.540,2.800 L15.256,2.800 C15.667,2.800 16.000,3.113 16.000,3.500 C16.000,3.887 15.667,4.200 15.256,4.200 L2.540,4.200 L4.246,5.804 C4.536,6.078 4.536,6.521 4.246,6.795 C4.100,6.932 3.910,7.000 3.719,7.000 C3.529,7.000 3.339,6.932 3.193,6.795 L0.218,3.995 C-0.073,3.722 -0.073,3.278 0.218,3.005 Z"/>
                                </svg>
								<span>{l s='Previous' mod='anblog'}</span>
							</a>
						</li>
					{else}
						<li id="pagination_previous{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}">							
							<a class="previous disabled" rel="prev" href="#">
								<svg
                                 xmlns="http://www.w3.org/2000/svg"
                                 xmlns:xlink="http://www.w3.org/1999/xlink"
                                 width="16px" height="7px">
                                <path fill-rule="evenodd"  fill="rgb(153, 153, 153)"
                                 d="M0.218,3.005 L3.193,0.205 C3.484,-0.069 3.955,-0.069 4.246,0.205 C4.536,0.479 4.536,0.922 4.246,1.196 L2.540,2.800 L15.256,2.800 C15.667,2.800 16.000,3.113 16.000,3.500 C16.000,3.887 15.667,4.200 15.256,4.200 L2.540,4.200 L4.246,5.804 C4.536,6.078 4.536,6.521 4.246,6.795 C4.100,6.932 3.910,7.000 3.719,7.000 C3.529,7.000 3.339,6.932 3.193,6.795 L0.218,3.995 C-0.073,3.722 -0.073,3.278 0.218,3.005 Z"/>
                                </svg>
								<span>{l s='Previous' mod='anblog'}</span>
							</a>
						</li>
					{/if}
					{if $start==3}
						<li><a {$no_follow_text|escape:'html':'UTF-8'}  href="{$link->goPage($requestPage, 1)|escape:'html':'UTF-8'}">1</a></li>
						<li><a {$no_follow_text|escape:'html':'UTF-8'}  href="{$link->goPage($requestPage, 2)|escape:'html':'UTF-8'}">2</a></li>
					{/if}
					{if $start==2}
						<li><a {$no_follow_text|escape:'html':'UTF-8'}  href="{$link->goPage($requestPage, 1)|escape:'html':'UTF-8'}">1</a></li>
					{/if}
					{if $start>3}
						<li><a {$no_follow_text|escape:'html':'UTF-8'}  href="{$link->goPage($requestPage, 1)|escape:'html':'UTF-8'}">1</a></li>
						<li class="truncate">...</li>
					{/if}
					{section name=pagination start=$start loop=$stop+1 step=1}
						{if $p == $smarty.section.pagination.index}
							<li class="current">
								<a {$no_follow_text|escape:'html':'UTF-8'} href="{$link->goPage($requestPage, $smarty.section.pagination.index)|escape:'html':'UTF-8'}" class="disabled">
									{$p|escape:'html':'UTF-8'}
								</a>
							</li>
						{else}
							<li>
								<a {$no_follow_text|escape:'html':'UTF-8'} href="{$link->goPage($requestPage, $smarty.section.pagination.index)|escape:'html':'UTF-8'}">
									{$smarty.section.pagination.index|escape:'html':'UTF-8'}
								</a>
							</li>
						{/if}
					{/section}
					{if $pages_nb>$stop+2}
						<li class="truncate">...</li>
						<li>
							<a href="{$link->goPage($requestPage, $pages_nb)|escape:'html':'UTF-8'}">
								{$pages_nb|intval}
							</a>
						</li>
					{/if}
					{if $pages_nb==$stop+1}
						<li>
							<a href="{$link->goPage($requestPage, $pages_nb)|escape:'html':'UTF-8'}">
								{$pages_nb|intval}
							</a>
						</li>
					{/if}
					{if $pages_nb==$stop+2}
						<li>
							<a href="{$link->goPage($requestPage, $pages_nb-1)|escape:'html':'UTF-8'}">
								{$pages_nb-1|intval}
							</a>
						</li>
						<li>
							<a href="{$link->goPage($requestPage, $pages_nb)|escape:'html':'UTF-8'}">
								{$pages_nb|intval}
							</a>
						</li>
					{/if}
					{if $pages_nb > 1 AND $p != $pages_nb}
						{assign var='p_next' value=$p+1}
						<li id="pagination_next{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}">						
							<a {$no_follow_text|escape:'html':'UTF-8'} class="next" rel="next" href="{$link->goPage($requestPage, $p_next)|escape:'html':'UTF-8'}">							
								<span>{l s='Next' mod='anblog'}</span>
								<svg
                                 xmlns="http://www.w3.org/2000/svg"
                                 xmlns:xlink="http://www.w3.org/1999/xlink"
                                 width="16px" height="8px">
                                <path fill-rule="evenodd"  fill="rgb(153, 153, 153)"
                                 d="M15.782,3.223 L12.807,0.424 C12.516,0.150 12.045,0.150 11.754,0.424 C11.464,0.697 11.464,1.141 11.754,1.414 L13.460,3.018 L0.744,3.018 C0.333,3.018 -0.000,3.332 -0.000,3.719 C-0.000,4.105 0.333,4.419 0.744,4.419 L13.460,4.419 L11.755,6.023 C11.464,6.297 11.464,6.740 11.755,7.014 C11.900,7.150 12.090,7.219 12.281,7.219 C12.471,7.219 12.661,7.150 12.807,7.014 L15.782,4.214 C16.073,3.940 16.073,3.497 15.782,3.223 Z"/>
                                </svg>
							</a>
						</li>
					{else}
						<li id="pagination_next{if isset($paginationId)}_{$paginationId|escape:'html':'UTF-8'}{/if}">						
							<a class="next disabled" rel="next" href="#">	
								<span>{l s='Next' mod='anblog'}</span>
								<svg
                                 xmlns="http://www.w3.org/2000/svg"
                                 xmlns:xlink="http://www.w3.org/1999/xlink"
                                 width="16px" height="8px">
                                <path fill-rule="evenodd"  fill="rgb(153, 153, 153)"
                                 d="M15.782,3.223 L12.807,0.424 C12.516,0.150 12.045,0.150 11.754,0.424 C11.464,0.697 11.464,1.141 11.754,1.414 L13.460,3.018 L0.744,3.018 C0.333,3.018 -0.000,3.332 -0.000,3.719 C-0.000,4.105 0.333,4.419 0.744,4.419 L13.460,4.419 L11.755,6.023 C11.464,6.297 11.464,6.740 11.755,7.014 C11.900,7.150 12.090,7.219 12.281,7.219 C12.471,7.219 12.661,7.150 12.807,7.014 L15.782,4.214 C16.073,3.940 16.073,3.497 15.782,3.223 Z"/>
                                </svg>
							</a>
						</li>
					{/if}
				</ul>			
			</div>
		{/if}
	</nav>	
{/if}