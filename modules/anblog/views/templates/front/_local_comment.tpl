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

<div id="blog-localengine">
	<h4 class="comment-list-title">{l s='Comments' mod='anblog'}</h4>
		{if $comments}
		{if $config->get('item_show_listcomment','1') == 1}
			

			<div class="comments clearfix">
			    <div class="comments-list">
                    {foreach from=$comments item=comment name=comment} {$default=''}
                    <div class="comment-item" id="comment{$comment.id_anblog_comment|escape:'html':'UTF-8'}">
                        <div class="comment-wrap">
                             <div class="comment-content">
                                {$comment.comment|nl2br nofilter}{* HTML form , no escape necessary *}
                            </div>
                            <div class="comment-meta">
                                <span class="comment-infor">
                                    <span class="comment-created">
										<i class="material-icons">&#xE192;</i>
                                      <span>{l s='Created On' mod='anblog'} {strtotime($comment.date_add)|date_format:"%A, %e/%m/%Y"|escape:'html':'UTF-8'}</span>
                                    </span>
                                    <span class="comment-postedby"><i class="material-icons">person</i><span>{l s='Posted By' mod='anblog'}: {$comment.user|escape:'html':'UTF-8'}</span></span>
                                </span>


                            </div>


                        </div>
                    </div>
                    {/foreach}
                </div>
				{if $blog_count_comment}
				<div class="top-pagination-content clearfix bottom-line">
					{include file="module:anblog/views/templates/front/_pagination.tpl"}
				</div>
				{/if}
			</div>
		{/if}
	{/if}	
		
		{if $config->get('item_show_formcomment','1') == 1}
			<h4>{l s='Leave your comment' mod='anblog'}</h4>
			<form class="form-horizontal clearfix" method="post" id="comment-form" action="{$blog_link|escape:'html':'UTF-8'}" onsubmit="return false;">
				<div class="form-group row">
					<div  class="col-lg-3">
						<label class="control-label" for="inputFullName">{l s='Full Name' mod='anblog'}</label>
					</div>
					<div class="col-lg-9">
						<input type="text" name="user" placeholder="{l s='Enter your full name' mod='anblog'}" id="inputFullName" class="form-control">
					</div>
				</div>

				<div class="form-group row">
					<div class="col-lg-3">
						<label class="control-label" for="inputEmail">{l s='Email' mod='anblog'}</label>
					</div>
					<div class="col-lg-9">
						<input type="text" name="email"  placeholder="{l  s='Enter your email' mod='anblog'}" id="inputEmail" class="form-control">
					</div>
				</div>

				<div class="form-group row">
					<div class="col-lg-3">
						<label class="control-label" for="inputComment">{l  s='Comment' mod='anblog'}</label>
					</div>
					<div class="col-lg-9">
						<textarea type="text" name="comment" rows="6"  placeholder="{l  s='Enter your comment' mod='anblog'}" id="inputComment" class="form-control"></textarea>
					</div>
				</div>

				{if $config->get('google_captcha_status')}
					<div class="form-group row">
						<div class="col-lg-3">
							<label class="control-label" for="inputEmail">{l s='Captcha' mod='anblog'}</label>
						</div>
						<div class="col-lg-8 col-md-8 ipts-captcha">
								<div class="g-recaptcha" data-sitekey="{$config->get('google_captcha_site_key')}"></div>

						</div>
					</div>
				{/if}

				<input type="hidden" name="id_anblog_blog" value="{$id_anblog_blog|intval}">
				<div class="form-group row">
				    <div class="col-lg-3">
                    </div>
					<div class="col-lg-9">
						<button class="btn btn-primary btn-submit-comment-wrapper" name="submitcomment" type="submit">
							<span class="btn-submit-comment">{l s='Submit' mod='anblog'}</span>
							<span class="anblog-cssload-container cssload-speeding-wheel"></span>
						</button>
					</div>
				</div>
			</form>
		{/if}
</div>