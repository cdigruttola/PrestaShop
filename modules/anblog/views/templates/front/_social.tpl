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

		<div class="social-wrap">
                <div class="social-heading">{l s='To share with friends' mod='anblog'}:</div>

                {if $config->get('social_code','')}
                    {html_entity_decode($config->get('social_code','')) nofilter}
                {else}
                <div class="blog-social-list">
                    <!-- Facebook Button -->
                    <div class="itemSocialButton itemFacebookButton">
                            <a href="http://www.facebook.com/sharer.php?u={$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}" class="facebook-share-button" data-count="horizontal" >

                            </a>
                        </div>
                    <!-- Twitter Button -->
                    <div class="itemSocialButton itemTwitterButton">
                        <a href="https://twitter.com/intent/tweet?text={$blog->meta_title|escape:'html':'UTF-8'} {$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}" class="twitter-share-button" data-count="horizontal" >

                        </a>
                    </div>
                     <!-- Linkedin Button -->
                    <div class="itemSocialButton itemLinkedinButton">
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}&title={$blog->meta_title|escape:'html':'UTF-8'}&source=LinkedIn" class="linkedin-share-button" data-count="horizontal" >

                        </a>
                    </div>


                    <!-- Tumblr Button -->
                    <div class="itemSocialButton itemTumblrButton">
                        <a href="http://www.tumblr.com/share/link?url={$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}" class="tumblr-share-button" data-count="horizontal" >

                        </a>
                    </div>
                      <!-- Pinterest Button -->
                    <div class="itemSocialButton itemPinterestButton">
                        <a href="http://www.pinterest.com/pin/create/button/?media={if $blog->preview_url && $config->get('item_show_image','1')}{$smarty.server.HTTP_HOST}{$blog->preview_url|escape:'html':'UTF-8'}{/if}&url={$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}" class="pinterest-share-button" data-count="horizontal" >

                        </a>
                    </div>
                {/if}
                </div>
            </div>




