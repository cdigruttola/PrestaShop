/**
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
 */
$(document).ready(function () {
            /*  */
            // open modal
    search_btn = $('#anblog_left_toggler'),
    search_modal = $('.anblog_left_mobile-cover, .anblog_left_mobile-modal, #anblog_left_wrapper');
    search_btn.off();
    search_btn.on('click', function (event) {
        $('html').addClass('anblog_left_mobile-open');
        search_modal.fadeIn();
    });

    // close modal
    $('.anblog_left_mobile-modal').click(function () {
        var select = $('#anblog_left_wrapper');
        if ($(event.target).closest(select).length) {
            return;
        }
        search_modal.fadeOut(function () {
            $('html').removeClass('anblog_left_mobile-open');

        });
    });
    $('.anblog_left_mobile-btn-close').on('click', function (event) {
        search_modal.fadeOut(function () {
            $('html').removeClass('anblog_left_mobile-open');

        });
    });
            $("#comment-form").submit(
                function () {
                    var action = $(this).attr('action');
                    var data = $('#comment-form').serialize();

                    if ($("#comment-form").parent().find('.comment-message').length<=0 ) {
                        var msg = $('<div class="comment-message"></div>');
                        $("#comment-form").before(msg);
                    } else {
                        var msg = $("#comment-form").parent().find(".comment-message");
                    }
                    // $('.btn-submit-comment').addClass('disable');
                    $('.btn-submit-comment').css({'display':'none'});
                    $('.btn-submit-comment-wrapper').addClass('bt-active');
                    $('.anblog-cssload-container').css({'display':'block'});
                    $.ajax(
                        {
                            url:action,
                            data: data+"&submitcomment="+Math.random(),
                            type:'POST',
                            dataType: 'json',
                            success:function ( ct ) {
                                if (!ct.error ) {
                                    $(msg).html('<div class="alert alert-info">'+ct.message+'</div>');
                                    $('input[type=text], textarea', '#comment-form').each(
                                        function () {
                                            $(this).val('');
                                            if (typeof grecaptcha !== 'undefined') {
                                                grecaptcha.reset();
                                            }
                                        }
                                    );
                                } else {
                                    if (typeof grecaptcha !== 'undefined') {
                                        grecaptcha.reset();
                                    }
                                    $(msg).html('<div class="alert alert-warning">'+ct.message+'</div>');
                                }
                                // $('.btn-submit-comment').removeClass('disable');
                                $('.btn-submit-comment').css({'display':'block'});
                                $('.btn-submit-comment-wrapper').removeClass('bt-active');
                                $('.anblog-cssload-container').css({'display':'none'});
                            }
                        }
                    );
                    return false;
                }
            );

            $('.top-pagination-content a.disabled').click(
                function () {
                    return false;
                }
            )

            //DONGND:: update link in language block
            var current_lang = prestashop.language.iso_code;
    if (typeof array_list_rewrite != 'undefined') {
        var current_list_rewrite = array_list_rewrite[current_lang];
        var current_blog_rewrite = array_blog_rewrite[current_lang];
        var current_category_rewrite = array_category_rewrite[current_lang];
        var current_config_blog_rewrite = array_config_blog_rewrite[current_lang];
        var current_config_category_rewrite = array_config_category_rewrite[current_lang];

        $.each(
            array_list_rewrite,
            function (iso_code, list_rewrite) {
                if (iso_code != current_lang) {
                      var url_search = prestashop.urls.base_url + iso_code;
                      //DONGND:: update for module blockgrouptop and default
                    if ($('#an_block_top').length) {
                        var parent_o = $('#an_block_top .language-selector');
                    } else {
                        var parent_o = $('.language-selector-wrapper');
                    }

                    parent_o.find('li a').each(
                        function () {

                            var lang_href = $(this).attr('href');

                            if (lang_href.indexOf(url_search) > -1 ) {
                                if ($('body#module-anblog-list').length) {
                                    var url_change = lang_href.replace('/'+current_list_rewrite, '/'+list_rewrite);
                                } else {
                                    var url_change = lang_href.replace('/'+current_list_rewrite+'/', '/'+list_rewrite+'/');
                                }

                                if ($('body#module-anblog-blog').length) {
                                    if (config_url_use_id == 1) {
                                        url_change = url_change.replace('/'+current_config_blog_rewrite+'/', '/'+array_config_blog_rewrite[iso_code]+'/');
                                    }
                                    url_change = url_change.replace('/'+current_blog_rewrite, '/'+array_blog_rewrite[iso_code]);
                                }

                                if ($('body#module-anblog-category').length) {
                                    if (config_url_use_id == 1) {
                                        url_change = url_change.replace('/'+current_config_category_rewrite+'/', '/'+array_config_category_rewrite[iso_code]+'/');
                                    }
                                    url_change = url_change.replace('/'+current_category_rewrite, '/'+array_category_rewrite[iso_code]);
                                }

                                $(this).attr('href', url_change);
                            }
                        }
                    );
                }
            }
        )
    }
});

