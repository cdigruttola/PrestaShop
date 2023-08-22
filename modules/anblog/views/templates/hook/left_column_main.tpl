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
{if isset($an_left_category) AND !empty($an_left_category)}
{$an_left_category nofilter}
{/if}
{if isset($an_left_tag) AND !empty($an_left_tag)}
{$an_left_tag nofilter}
{/if}
{if isset($an_left_recent) AND !empty($an_left_recent)}
{$an_left_recent nofilter}
{/if}

{if isset($url_rss) && $url_rss != ''}
<a href="{$url_rss}" class="btn btn-primary ">{$config->get('rss_title_item')}</a>
{/if}