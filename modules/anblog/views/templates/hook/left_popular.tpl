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

{if isset($leading_blogs) AND !empty($leading_blogs)}
    <section id="blogPopularBlog" class="block an-block-sidebar hidden-sm-down block-categories">
        <h4 class='title_block'><a href="">{l s='Popular Articles' mod='anblog'}</a></h4>
            <div class="block_content products-block">
                <ul class="lists">
                    {foreach from=$leading_blogs item="blog" name=leading_blog}
                        <li class="list-item clearfix{if $smarty.foreach.leading_blog.last} last_item{elseif $smarty.foreach.leading_blog.first} first_item{else}{/if}">
                            <div class="blog-image">
                                <a class="products-block-image" title="{$blog.title|escape:'html':'UTF-8'}" href="{$blog.link|escape:'html':'UTF-8'}">
                                    <img alt="{$blog.title|escape:'html':'UTF-8'}" src="{$blog.preview_url|escape:'html':'UTF-8'}" class="img-fluid">
                                </a>
                            </div>
                            <div class="blog-content">
                            	<h3 class="post-name"><a title="{$blog.title|escape:'html':'UTF-8'}" href="{$blog.link|escape:'html':'UTF-8'}">{$blog.title|escape:'html':'UTF-8'}</a></h3>
                            	<span class="info">{$blog.date_add|date_format:"%b %d, %Y"}</span>
                            </div>
                        </li> 
                    {/foreach}
                </ul>
            </div>
    </section>
{/if}