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

(function ($) {
    $.fn.PavMegaMenuList = function (opts) {
        // default configuration
        var config = $.extend(
            {},
            {
                action:null,
                addnew : null,
                confirm_del:'Are you sure delete this?'
            },
            opts
        );

        function checkInputHanlder()
        {
            var _updateMenuType = function () {
                $(".menu-type-group").parent().parent().hide();
                $("[for^=content_text_]").parent().hide();

                if ($("#menu_type").val() =='html' ) {
                    $("[for^=content_text_]").parent().show();
                } else {
                    $("#"+$("#menu_type").val()+"_type").parent().parent().show();
                }
            };
            _updateMenuType();
            $("#menu_type").change(_updateMenuType);

            var _updateSubmenuType = function () {
                if ($("#type_submenu").val() =='html' ) {
                    $("[for^=submenu_content_text_]").parent().show();
                } else {
                    $("[for^=submenu_content_text_]").parent().hide();
                }
            };
            _updateSubmenuType();
            $("#type_submenu").change(_updateSubmenuType);

        }

        function manageTreeMenu()
        {
            if ($('ol').hasClass("sortable")) {
                $('ol.sortable').nestedSortable(
                    {
                        forcePlaceholderSize: true,
                        handle: 'div',
                        helper:    'clone',
                        items: 'li',
                        opacity: .6,
                        placeholder: 'placeholder',
                        revert: 250,
                        tabSize: 25,
                        tolerance: 'pointer',
                        toleranceElement: '> div',
                        maxLevels: 4,

                        isTree: true,
                        expandOnHover: 700,
                        startCollapsed: true,
                        
                        stop: function () {
                            var serialized = $(this).nestedSortable('serialize');
                        
                            $.ajax(
                                {
                                    type: 'POST',
                                    url: config.action.replace(/&amp;/g, '&') +"&doupdatepos=1&rand="+Math.random(),
                                    data : serialized+'&updatePosition=1'
                                }
                            ).done(
                                function (msg) {
                                    showSuccessMessage(msg);
                                }
                            );
                        }
                    }
                );
                
                $('#addcategory').click(
                    function () {
                        location.href=config.addnew;
                    }
                );
            }
        }
        /**
          * initialize every element
          */
        this.each(
            function () {
                $(".quickedit",this).click(
                    function () {
                        location.href=config.action+"&id_anblogcat="+$(this).attr('rel').replace("id_","");
                    }
                );

                $(".quickdel",this).click(
                    function () {
                        if (confirm(config.confirm_del) ) {
                            location.href=config.action+"&dodel=1&id_anblogcat="+$(this).attr('rel').replace("id_","");
                        }
                 
                    }
                );
                manageTreeMenu();
            }
        );

        return this;
    };
    
})(jQuery);


jQuery(document).ready(
    function () {
        $(".an-modal").fancybox(
            {
                'type':'iframe',
                'width':980,
                'height':500,
                afterLoad:function (   ) {
                    if ($('body',$('.fancybox-iframe').contents()).find("#main").length  ) {
                        $('body',$('.fancybox-iframe').contents()).find("#header").hide();
                        $('body',$('.fancybox-iframe').contents()).find("#footer").hide();
                    }
                }
            }
        );
     
        $("#widgetds a.btn").fancybox({'type':'iframe'});

        $(".an-modal-action, #widgets a.btn").fancybox(
            {
                'type':'iframe',
                'width':950,
                'height':500,
                afterLoad:function (   ) {
                    if ($('body',$('.fancybox-iframe').contents()).find("#main").length  ) {
                        $('body',$('.fancybox-iframe').contents()).find("#header").hide();
                        $('body',$('.fancybox-iframe').contents()).find("#footer").hide();
                    }
                },
                afterClose: function (event, ui) {
                    //    location.reload();
                },
            }
        );

        //DONGND:: delete image uploaded
        if ($('#image_link-images-thumbnails').length > 0) {
            anblog_del_img($('#image_link-images-thumbnails'), 'image');
        }
    
        if ($('#thumb_link-images-thumbnails').length > 0) {
            anblog_del_img($('#thumb_link-images-thumbnails'), 'thumb');
        }
    
        $('.anblog-del-img-bt').click(
            function () {
                if (confirm(anblog_del_img_mess) ) {
                    var id_parent = $(this).data('id');
                    $('#'+id_parent).parent().fadeOut(
                        function () {
                            $(this).remove();
                        }
                    );
                    var id_element = $(this).data('element');
                    $('#'+id_element).val('');
                }
                return false;
            }
        )
    }
);

//DONGND;; function delete image uploaded

function anblog_del_img(img_id_element, img_name_e)
{
    img_id_element.append('<a class="btn btn-default anblog-del-img-bt" href="#" data-element="'+img_name_e+'" data-id="'+img_id_element.attr('id')+'"><i class="icon-trash"></i>'+anblog_del_img_txt+'</a>');
}

 
jQuery(document).ready(
    function () {
        var id_panel = $("#bloggeneralsetting .anblog-globalconfig li.active a").attr("href");
        $(id_panel).addClass('active').show();
        $('.anblog-globalconfig li').click(
            function () {
                if (!$(this).hasClass('active')) {
                    var default_tab = $(this).find('a').attr("href");
                    $('input[name="ANBLOG_DASHBOARD_DEFAULTTAB"]').val(default_tab);
                }
            }
        );
        $('.engine_select').change(function () {

            $('.comment_item').parents('.form-group').hide();
            $('.' + $(this).val()).parents('.form-group').show();
            if ($(this).val() == 'local') {
                $('[name="google_captcha_status"]').parents('.form-group').show();
            } else {
                $('[name="google_captcha_status"]').parents('.form-group').hide();
            }
        });
        $('.engine_select').change();
    }
);

/*
 * SHOW HIDE - URL include ID
 */
$(document).ready(
    function () {
        $('.form-action').change(
            function () {
                var elementName = $(this).attr('name');
                $('.'+elementName+'_sub').hide(300);
                $('.'+elementName+'-'+$(this).val()).show(500);
            }
        );
        $('.form-action').trigger("change");
    }
);

