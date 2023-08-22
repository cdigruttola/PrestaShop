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

    {block name='hook_extra'}
    <meta property="og:title" content="{$blog->meta_title|escape:'html':'UTF-8'}" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="https://{$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}" />
    <meta property="og:image" content="https://{$smarty.server.HTTP_HOST}{$blog->preview_url|escape:'html':'UTF-8'}" />
    <meta property="og:image:width" content="400" />

    <meta property="og:description" content="{$blog->description nofilter}" />
    <meta property="article:published_time" content="{strtotime($blog->date_add)|date_format:"%A, %B %e, %Y"|escape:'html':'UTF-8'}" />
    <meta property="article:author" content="{$blog->author|escape:'html':'UTF-8'}" />
    <meta property="article:section" content="{$blog->category_title|escape:'html':'UTF-8'}" />
    <meta property="article:tag" content="{foreach from=$tags item=tag name=tag} {$tag.tag|escape:'html':'UTF-8'}{/foreach}" />
    {/block}

	{block name="left_column"}
		{if $show_in_post}
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
		{if !$show_in_post}
		<div class="row">
		{/if}
		<div id="content-wrapper" class="left-column right-column {if $show_in_post}col-sm-12 col-md-12 col-lg-9 {else}col-sm-12 col-md-12{/if}">
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

		<section id="main">
			{if isset($error)}
					<div id="blogpage">
						<div class="blog-detail">
							<div class="alert alert-warning">{l s='Sorry, no posts has been posted in the blog yet, but it will be done soon' mod='anblog'}</div>
						</div>
					</div>
				{else}
				<div id="blogpage">
					<article class="blog-detail">
						{if $is_active}
							<h1 class="blog-title">{$blog->meta_title|escape:'html':'UTF-8'}</h1>
							<div class="blog-meta">
							    {if isset($blog->hits)&&$config->get('item_show_hit','1')}
                                <span class="blog-hit">
                                    <i class="material-icons">visibility</i> <span>{$blog->hits|intval} {l s='Views' mod='anblog'}</span>

                                </span>
                                {/if}

                                {if $config->get('item_show_created','1')}
                                <span class="blog-created">
                                    <i class="material-icons">&#xE192;</i> <span></span>
                                    <time class="date" datetime="{strtotime($blog->date_add)|date_format:"%Y"|escape:'html':'UTF-8'}">
                                        {assign var='blog_day' value=strtotime($blog->date_add)|date_format:"%e"}{l s=$blog_day mod='anblog'}/{assign var='blog_month' value=strtotime($blog->date_add)|date_format:"%m"}{l s=$blog_month mod='anblog'}/{assign var='blog_year' value=strtotime($blog->date_add)|date_format:"%Y"}{l s=$blog_year mod='anblog'}	<!-- year -->
                                    </time>
                                </span>
                                {/if}

                                {if isset($blog_count_comment)&&$config->get('item_show_counter','1')}
                                <span class="blog-ctncomment">
                                    <i class="material-icons">comment</i> <span>{$blog_count_comment|intval} {l s='comments' mod='anblog'}</span>

                                </span>
                                {/if}

								{if $config->get('item_show_author','1')}
								<span class="blog-author">
									<i class="material-icons">person</i> <span>{l s='Posted By' mod='anblog'}: </span>
									<a href="{$blog->author_link|escape:'html':'UTF-8'}" title="{$blog->author|escape:'html':'UTF-8'}">{$blog->author|escape:'html':'UTF-8'}</a>
								</span>
								{/if}

								{if $config->get('item_show_category','1')}
								<span class="blog-cat">
									<i class="material-icons">list</i> <span>{l s='In' mod='anblog'}: </span>
									<a href="{$blog->category_link|escape:'html':'UTF-8'}" title="{$blog->category_title|escape:'html':'UTF-8'}">{$blog->category_title|escape:'html':'UTF-8'}</a>
								</span>
								{/if}

							</div>

							{if $blog->preview_url && $config->get('item_show_image','1')}
								<div class="blog-image">
									<img src="{$blog->preview_url|escape:'html':'UTF-8'}" title="{$blog->meta_title|escape:'html':'UTF-8'}" class="img-fluid" />
								</div>
							{/if}

							<div class="blog-description">
								{if $config->get('item_show_description',1)}
									{$blog->description nofilter}{* HTML form , no escape necessary *}
								{/if}
								{$blog->content nofilter}{* HTML form , no escape necessary *}
							</div>
							
							

							{if trim($blog->video_code)}
							<div class="blog-video-code">
								<div class="inner ">
									{$blog->video_code nofilter}{* HTML form , no escape necessary *}
								</div>
							</div>
							{/if}

                        {if $blog->products}
							<div class="products-grid col-xs-12 col-sm-12 col-md-12">
                                {foreach from=$blog->products item=product name=products}
									<div class="product-item col-xs-12 col-sm-4 col-md-3">
										<div class="product-thumbnail"><a href="{$product.link}" title="{$product.name}"><img class="img-fluid" src="{if isset($product.cover.bySize.menu_default)}{$product.cover.bySize.menu_default.url}{else} {$product.cover.bySize.home_default.url}{/if}" alt="{$product.cover.legend}" /></a></div>
										<div class="product-information-dropdown">
											<h5 class="product-name"><a href="{$product.link}" title="{$product.name}">{$product.name}</a></h5>
                                            {if $product.show_price}
												<div class="product-price-and-shipping"><span class="price product-price">{$product.price}</span>
                                                    {if $product.has_discount}<span class="regular-price">{$product.regular_price}</span>{/if}</div>
                                            {/if}
										</div>
									</div>
                                {/foreach}
							</div>
                        {/if}

                            {if $tags}
                            <div class="blog-tags">
                                <span>{l s='Tags:' mod='anblog'}</span>

                                {foreach from=$tags item=tag name=tag}
                                    <a href="{$tag.link|escape:'html':'UTF-8'}" title="{$tag.tag|escape:'html':'UTF-8'}"><span>{$tag.tag|escape:'html':'UTF-8'}</span></a>
                                {/foreach}

                            </div>
                            {/if}

							<div class="social-share">
								{include file="module:anblog/views/templates/front/_social.tpl" social_code=$config->get('social_code','')}
							</div>



							
							{if $config->get('item_show_listcomment','1') == 1}
							<div class="blog-comment-block clearfix">
								
								{if $config->get('item_comment_engine','local')=='facebook'}
									{include file="module:anblog/views/templates/front/_facebook_comment.tpl"}
								{elseif $config->get('item_comment_engine','local')=='diquis'}
									{include file="module:anblog/views/templates/front/_diquis_comment.tpl"}
								{elseif ($config->get('google_captcha_site_key') && $config->get('google_captcha_site_key')) || !$config->get('google_captcha_status')}
									{include file="module:anblog/views/templates/front/_local_comment.tpl"}
								{/if}
							{elseif $config->get('item_show_listcomment','1') == 0 && $config->get('item_show_formcomment','1') == 1 && (($config->get('google_captcha_site_key') && $config->get('google_captcha_site_key')) || !$config->get('google_captcha_status') ) }
								<div class="blog-comment-block clearfix">
									{include file="module:anblog/views/templates/front/_local_comment.tpl"}
								</div>
							{/if}
						{else}
							<div class="alert alert-warning">{l s='Sorry, This blog is not avariable. May be this was unpublished or deleted.' mod='anblog'}</div>
						{/if}

					</article>
				</div>
				
				<div class="hidden-xl-down hidden-xl-up datetime-translate">
					{l s='Sunday' mod='anblog'}
					{l s='Monday' mod='anblog'}
					{l s='Tuesday' mod='anblog'}
					{l s='Wednesday' mod='anblog'}
					{l s='Thursday' mod='anblog'}
					{l s='Friday' mod='anblog'}
					{l s='Saturday' mod='anblog'}
					
					{l s='January' mod='anblog'}
					{l s='February' mod='anblog'}
					{l s='March' mod='anblog'}
					{l s='April' mod='anblog'}
					{l s='May' mod='anblog'}
					{l s='June' mod='anblog'}
					{l s='July' mod='anblog'}
					{l s='August' mod='anblog'}
					{l s='September' mod='anblog'}
					{l s='October' mod='anblog'}
					{l s='November' mod='anblog'}
					{l s='December' mod='anblog'}
				</div>
			{/if}
		</section>
	
	    </div>
{/block}