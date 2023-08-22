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

<div id="blog-dashboard">

    <div class="row">
        <div class="col-md-12">
            <a href="{$preview.href|escape:'html':'UTF-8'}" target="{$preview.target|escape:'html':'UTF-8'}"
               class="btn btn-success"
               style="width: 100%;
               margin-bottom: 10px;">
                {$preview.title|escape:'html':'UTF-8'}
            </a>
        </div>
        {*
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">{l s='Global Config' mod='anblog'}</div>
                <div class="panel-content" id="bloggeneralsetting">
                    <ul class="nav nav-tabs anblog-globalconfig" role="tablist">
                        <li class="nav-item{if $default_tab == '#fieldset_0'} active{/if}">
                            <a class="nav-link" href="#fieldset_0" role="tab" data-toggle="tab">{l s='General' mod='anblog'}</a>
                        </li>
                        <li class="nav-item{if $default_tab == '#fieldset_1_1'} active{/if}">
                            <a class="nav-link" href="#fieldset_1_1" role="tab" data-toggle="tab">{l s='Blog' mod='anblog'}</a>
                        </li>
                        <li class="nav-item{if $default_tab == '#fieldset_2_2'} active{/if}">
                            <a class="nav-link" href="#fieldset_2_2" role="tab" data-toggle="tab">{l s='Post' mod='anblog'}</a>
                        </li>
                        <li class="nav-item{if $default_tab == '#fieldset_3_3'} active{/if}">
                            <a class="nav-link" href="#fieldset_3_3" role="tab" data-toggle="tab">{l s='Left column' mod='anblog'}</a>
                        </li>
                        <li class="nav-item{if $default_tab == '#fieldset_4_4'} active{/if}">
                            <a class="nav-link" href="#fieldset_4_4" role="tab" data-toggle="tab">{l s='Widget' mod='anblog'}</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        {$globalform}
                    </div>
                </div>
            </div>
        </div>
        *}
        {$globalform}
    </div>
</div>