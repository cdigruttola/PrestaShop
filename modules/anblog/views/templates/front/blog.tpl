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

{extends file="page.tpl"}

	{block name="left_column"}
		{if $show_in_blog}
		<div class="row">
		<div id="left-column" class="col-xs-12 col-sm-4 col-md-3">
			<div class="anblog_left_mobile-cover"></div>
			<div class="anblog_left_mobile-modal">
				<div id="anblog_left_wrapper">
						<div class="mobile-menu-header">
						<div class="anblog_left_mobile-btn-close">
							<svg
							xmlns="http://www.w3.org/2000/svg"
							xmlns:xlink="http://www.w3.org/1999/xlink"
							width="16px" height="16px">
							<path fill-rule="evenodd"  fill="rgb(0, 0, 0)"
							d="M16.002,0.726 L15.274,-0.002 L8.000,7.273 L0.725,-0.002 L-0.002,0.726 L7.273,8.000 L-0.002,15.274 L0.725,16.002 L8.000,8.727 L15.274,16.002 L16.002,15.274 L8.727,8.000 L16.002,0.726 Z"/>
							</svg>
						</div>
					</div>
					{Module::getInstanceByName('anblog')->hookDisplayLeftColumn(array()) nofilter}
				</div>
			</div>
		</div>
		{/if}
	{/block}

	{block name="content_wrapper"}
		{if !$show_in_blog}
		<div class="row">
		{/if}
		<div id="content-wrapper" class="left-column right-column {if $show_in_blog}col-sm-12 col-md-12 col-lg-9{else}col-sm-12 col-md-12{/if}">
		    <div class="blog-content-wrapper">
		
				{if $show_in_blog}
				<div class="hidden-lg-up">
					<button id="anblog_left_toggler" class="btn btn-secondary">
						<svg 
						xmlns="http://www.w3.org/2000/svg"
						xmlns:xlink="http://www.w3.org/1999/xlink"
						width="16px" height="4px">
						<image  x="0px" y="0px" width="16px" height="4px"  xlink:href="data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAECAMAAACwak/eAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAAS1BMVEUAAAAmIyQmIyQmIyQmIyQmJCUmIyQmJCQmIyQmJCQmIyQmJCQmIyQlIyQmJCUmIyQmJCUmJCQmJCUlIyQmIyQmJCQmJCUlIyT///8VIQx0AAAAFHRSTlMAX/PXIT7oPu+SwcGSktc+6NfzX4D2ZO4AAAABYktHRBibaYUeAAAAB3RJTUUH4wsSETMJQZd5WgAAAD9JREFUCNcVy8ERgCAMAMFLwIiIggrYf6fG784soiHCYrZCDCqk3jfIY+5QxpscZnHoM8Pxg+jppVqrXq77+QA/HgImmGTStAAAAABJRU5ErkJggg==" />
						</svg>
						{l s='Show sidebar' mod='anblog'}
					</button>
				</div>
				{/if}
				{if isset($no_follow) AND $no_follow}
					{assign var='no_follow_text' value='rel="nofollow"'}
				{else}
					{assign var='no_follow_text' value=''}
				{/if}

				{************************************************}
				{if isset($filter.type)}
					{if $filter.type=='tag'}
						<h1>{l s='Filter Blogs By Tag' mod='anblog'} : <span>{$filter.tag|escape:'html':'UTF-8'}</span></h1>
					{elseif $filter.type=='author'}
						{if isset($filter.id_employee)}
							<h1>{l s='Filter Blogs By Blogger' mod='anblog'} : <span>{$filter.employee->firstname|escape:'html':'UTF-8'} {$filter.employee->lastname|escape:'html':'UTF-8'}</span></h1>
						{else}
							<h1>{l s='Filter Blogs By Blogger' mod='anblog'} : <span>{$filter.author_name|escape:'html':'UTF-8'}</span></h1>
						{/if}
					{/if}
				{/if}

				{************************************************}
				{if isset($category) && $category->id_anblogcat && $category->active}

					<h1>{$category->title|escape:'html':'UTF-8'}</h1>

					{if $config->get('listing_show_categoryinfo',1)}
						<div class="panel panel-default">
							<div class="panel-body">
								{if $category->image}
									<div class="row">
										<div class="category-image col-xs-12 col-sm-12 col-lg-4 col-md-6 text-center">
											<img src="{$category->image|escape:'html':'UTF-8'}" class="img-fluid" alt="" />
										</div>
										<div class="col-xs-12 col-sm-12 col-lg-8 col-md-6 category-info caption">
											{$category->content_text nofilter}{* HTML form , no escape necessary *}
										</div>
									</div>
								{else}
									<div class="category-info caption">
										{$category->content_text nofilter}{* HTML form , no escape necessary *}
									</div>
								{/if}
							</div>
						</div>
					{/if}
				{/if}

				{************************************************}
				{if count($leading_blogs)>0}	
					<div class="row blog-item-list">
					{foreach from=$leading_blogs item=blog name=leading_blog}
					{if $post_type == 'Type 1'}
						<div class="col-sm-12">
							{include file="module:anblog/views/templates/front/miniature-post-type1.tpl"}
						</div>
					{elseif  $post_type == 'Type 2'}
						<div class="{if $show_in_blog}col-sm-6{else}col-sm-4{/if}">
							{include file="module:anblog/views/templates/front/miniature-post-type2.tpl"}
						</div>
					{else}
						<div class="col-sm-12">
							{include file="module:anblog/views/templates/front/miniature-post-type3.tpl"}
						</div>
					{/if}
						
					{/foreach}
					</div>
					<div class="{if $config->get('item_posts_type') == 'Type 2'}type-2-pagination {/if}row top-pagination-content clearfix bottom-line">
						{include file="module:anblog/views/templates/front/_pagination.tpl"}
					</div>
				{else}
					<div class="alert alert-warning">{l s='Sorry, no posts has been posted in the blog yet, but it will be done soon.' mod='anblog'}</div>
				{/if}

		    </div>
		</div>	
	</div>
</div>
	{/block}