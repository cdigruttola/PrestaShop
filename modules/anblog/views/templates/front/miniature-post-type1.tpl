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


<article class="blog-item blog-item-type-1">
	<div class="blog-image-container">
		{if $config->get('listing_show_title','1')}
			<h4 class="title">
				<a href="{$blog.link|escape:'html':'UTF-8'}" title="{$blog.title|escape:'html':'UTF-8'}">{$blog.title|escape:'html':'UTF-8'}</a>
			</h4>
		{/if}
		<div class="blog-meta">
			{if $config->get('listing_show_hit','1')}
                <span class="blog-hit">
                    <i class="material-icons">visibility</i> <span> {$blog.hits|intval} {l s='Views' mod='anblog'}</span>

                </span>
            {/if}

             {if $config->get('listing_show_created','1')}
                <span class="blog-created">
                    <i class="material-icons">&#xE192;</i>
                    <time class="date" datetime="{strtotime($blog.date_add)|date_format:"%Y"|escape:'html':'UTF-8'}">
                        {assign var='blog_day' value=strtotime($blog.date_add)|date_format:"%e"}{l s=$blog_day mod='anblog'}/{assign var='blog_month' value=strtotime($blog.date_add)|date_format:"%m"}{l s=$blog_month mod='anblog'}/{assign var='blog_year' value=strtotime($blog.date_add)|date_format:"%Y"}{l s=$blog_year mod='anblog'}
                    </time>
                </span>
            {/if}
            {if isset($blog.comment_count)&&$config->get('listing_show_counter','1')}
                <span class="blog-ctncomment">
                    <i class="material-icons">comment</i> <span>{$blog.comment_count|intval} {l s='comments' mod='anblog'}</span>

                </span>
            {/if}
            {if $config->get('listing_show_author','1')&&!empty($blog.author)}
                <span class="blog-author">
                    <i class="material-icons">person</i> <span>{l s='Posted By' mod='anblog'}:</span>
                    <a href="{$blog.author_link|escape:'html':'UTF-8'}" title="{$blog.author|escape:'html':'UTF-8'}">{$blog.author|escape:'html':'UTF-8'}</a>
                </span>
            {/if}

            {if $config->get('listing_show_category','1')}
                <span class="blog-cat">
                    <i class="material-icons">list</i> <span>{l s='In' mod='anblog'}:</span>
                    <a href="{$blog.category_link|escape:'html':'UTF-8'}" title="{$blog.category_title|escape:'html':'UTF-8'}">{$blog.category_title|escape:'html':'UTF-8'}</a>
                </span>
            {/if}
		</div>
		{if $blog.preview_url && $config->get('listing_show_image',1)}
			<div class="blog-image">
				<a href="{$blog.link|escape:'html':'UTF-8'}" title="{$blog.title|escape:'html':'UTF-8'}"><img src="{$blog.preview_url|escape:'html':'UTF-8'}" title="{$blog.title|escape:'html':'UTF-8'}" alt="" class="img-fluid" /></a>
			</div>
		{elseif $blog.thumb_url && $config->get('listing_show_image',1)}
			<div class="blog-image">
				<img src="{$blog.thumb_url|escape:'html':'UTF-8'}" title="{$blog.title|escape:'html':'UTF-8'}" alt="" class="img-fluid" />
			</div>
		{/if}
	</div>
	<div class="blog-info">
		{if $config->get('listing_show_description','1')}
			<div class="blog-shortinfo">
				{$blog.description|strip_tags:'UTF-8'|truncate:160:'...' nofilter}{* HTML form , no escape necessary *}
			</div>
		{/if}
		{if $config->get('listing_show_readmore',1)}
			<p>
				<a href="{$blog.link|escape:'html':'UTF-8'}" title="{$blog.title|escape:'html':'UTF-8'}" class="more btn btn-primary">{l s='Read more' mod='anblog'}</a>
			</p>
		{/if}
	</div>

</article>
