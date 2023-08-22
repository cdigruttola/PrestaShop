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
		<form class="form-horizontal" method="post" id="comment-form" action="{$blog_link|escape:'html':'UTF-8'}" onsubmit="return false;">
			
			<div class="form-group">
				<label class="col-lg-3 control-label" for="inputFullName">{l s='Full Name' mod='anblog'}</label>
				<div class="col-lg-9">
					<input type="text" name="fullname" placeholder="{l s='Enter your full name' mod='anblog'}" id="inputFullName" class="form-control">
				</div>
			</div>

			<div class="form-group">
				<label class="col-lg-3 control-label" for="inputEmail">{l s='Email' mod='anblog'}</label>
				<div class="col-lg-9">
					<input type="text" name="email" placeholder="{l s='Enter your email' mod='anblog'}" id="inputEmail" class="form-control">
				</div>
			</div>
			
			<div class="form-group">
				<label class="col-lg-3 control-label" for="inputComment">{l s='Comments' mod='anblog'}</label>
				<div class="col-lg-9">
					<textarea type="text" name="comment" rows="6" placeholder="{l s='Enter your comment' mod='anblog'}" id="inputComment" class="form-control"></textarea>
				</div>
			</div>
			<div class="g-recaptcha" data-sitekey="{$config->get('google_captha_site_key')}"></div>
			 <input type="hidden" name="id_anblog_blog" value="{$id_anblog_blog|intval}">
			<div class="form-group">
				<div class="col-lg-9 col-lg-offset-3">
					<button class="btn btn-default btn-submit-comment-wrapper" name="submitcomment" type="submit">
						<span class="btn-submit-comment">{l s='Submit' mod='anblog'}</span>
						<span class="anblog-cssload-container cssload-speeding-wheel"></span>
					</button>
				</div>				
			</div>
		</form>
</div>