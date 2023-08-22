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
{if isset($anblogtags) AND !empty($anblogtags)}
    <section id="tags_blog_block_left" class="block an-blog-tags block-categories">
        <h4 class='title_block'><a href="">{l s='Tags Post' mod='anblog'}</a></h4>
        <div class="block_content clearfix">
            {foreach from=$anblogtags item="tag"}
                <a href="{$tag.link|escape:'html':'url'}">{$tag.name|escape:'html':'UTF-8'}</a>
            {/foreach}
        </div>
    </section>
{/if}