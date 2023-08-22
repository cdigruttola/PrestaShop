<?php
/**
 * 2021 Anvanto
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 wesite only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses. 
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 *  @author Anvanto <anvantoco@gmail.com>
 *  @copyright  2021 Anvanto
 *  @license    Valid for 1 website (or project) for each purchase of license
 *  International Registered Trademark & Property of Anvanto
 */

$langs = Language::getLanguages(false);
$res = (bool)Db::getInstance()->execute(' TRUNCATE TABLE `'._DB_PREFIX_.'anblogcat`  ');
$res = (bool)Db::getInstance()->execute(
    'INSERT INTO `'._DB_PREFIX_."anblogcat`  (`id_anblogcat`, `id_parent`, `item`, `level_depth`, `active`, `show_title`, `position`, `submenu_content`, `privacy`, `position_type`, `menu_class`, `content`, `icon_class`, `level`, `left`, `right`, `date_add`, `date_upd`,                `template`, `randkey`,                       `groups` ) VALUES
                                             (1,  0,           NULL,   0,             0,       1,            0,          '',                NULL,      NULL,            NULL,         NULL,      NULL,         0,       0,      0,       NULL,           NULL,                  '',        'ad30975ab88db9db59a40e3edfca0ab0',''),
                                             (3,    1,           '',     1,             1,       0,            0,          '',                0,         '',              '',           '',        '',           0,       0,      0,'2020-06-01 07:06:52', '2020-06-01 11:07:22', 'default', '5577844800e55bda2c0540af22ba96ce',''),
                                             (4,  3,           '',     2,             1,       1,            0,          '',                0,         '',              '',           '',        '',           0,       0,      0,'2020-06-02 07:07:34', '2020-06-02 12:07:50', 'default', '0abc8c406b64fa2f13f5a7cbecbfb67f',''),
                                             (5, 3,           NULL,   2,             1,       0,            1,          '',                0,         NULL,            '',           NULL,      '',           0,       0,      0,'2020-06-03 08:44:07', '2020-06-03 09:05:46', 'default', '1dcae6f22c5962b687451c98c27946f0','');
"
);

$res = (bool)Db::getInstance()->execute(' TRUNCATE TABLE `'._DB_PREFIX_.'anblogcat_lang`  ');

foreach ($langs as $l) {
    $sql = 'INSERT INTO `'._DB_PREFIX_."anblogcat_lang` (`id_anblogcat`, `id_lang`, `title`,           `meta_title`,      `content_text`,                                                                                                                                                                                              `description`, `meta_keywords`,                        `meta_description`, `link_rewrite`) VALUES
		                                                (1,              LANGUAGEID, 'Root',            'Root',           NULL,                                                                                                                                                                                                        '',            '',                                     '',                 ''),
		                                                (3,              LANGUAGEID, 'Category 1',      'Category 1',     '<p>Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue</p>', '',            '',                                     '\r\n',             'category-1'),
		                                                (4,              LANGUAGEID, 'Sub Category 1',  'SubCategory 1',  '<p>Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue</p>', '',            'fashion,prestashop,antheme,pavothemes', '',                 'sub-category-1'),
		                                                (5,              LANGUAGEID, 'Sub Category 2',  'SubCategory 2',  '<p>Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue</p>', '',            'haha,fashion,charme',                  'gogogoel',         'sub-category-2');
		";
    $sql = str_replace('LANGUAGEID', $l['id_lang'], $sql);
    $res = (bool)Db::getInstance()->execute($sql);
}

$res = (bool)Db::getInstance()->execute(' TRUNCATE TABLE `'._DB_PREFIX_.'anblogcat_shop`  ');
$res = (bool)Db::getInstance()->execute(
    'INSERT INTO `'._DB_PREFIX_.'anblogcat_shop` (`id_anblogcat`, `id_shop`) VALUES
    (1, 1),
    (3, 1),
    (4, 1),
    (5, 1); '
);

$res = (bool)Db::getInstance()->execute(' TRUNCATE TABLE `'._DB_PREFIX_.'anblog_blog`  ');
$res = (bool)Db::getInstance()->execute(
    'INSERT INTO `'._DB_PREFIX_."anblog_blog` (`id_anblog_blog`,   `id_anblogcat`,   `position`, `date_add`,     `active`,   `user_id`,   `hits`, `image`,                        `thumb`, `date_upd`,            `video_code`, `params`, `featured`, `indexation`, `id_employee`, `product_ids`, `products`) VALUES
                                              (3,                  4,                0,          '2020-06-01',   1,          0,           40,     'b-blog-1_anblog_original.jpg', '',      '2020-06-01 09:55:38', '',           '',       0,          1,            1,             '',            ''        ),
                                              (4,                  4,                2,          '2020-06-02',   1,          0,           105,    'b-blog-2_anblog_original.jpg', '',      '2020-06-02 08:31:14', '',           '',       0,          1,            1,             '',            ''        ),
                                              (5,                  4,                3,          '2020-06-03',   1,          0,           9,      'b-blog-3_anblog_original.jpg', '',      '2020-06-03 01:20:28', '',           '',       0,          0,            1,             '',            ''        ),
                                              (6,                  4,                4,          '2020-06-04',   1,          0,           121,    'b-blog-4_anblog_original.jpg', '',      '2020-06-04 09:54:03', '',           '',       0,          0,            1,             '',            ''        ),
                                              (7,                  4,                5,          '2020-06-05',   1,          0,           71,     'b-blog-5_anblog_original.jpg', '',      '2020-06-05 10:14:46', '',           '',       0,          0,            1,             '',            ''        ),
                                              (8,                  4,                1,          '2020-06-06',   1,          0,           3,      'b-blog-6_anblog_original.jpg', '',      '2020-06-06 22:55:10', '',           '',       0,          0,            1,             '',            ''        ),
                                              (9,                  4,                6,          '2020-06-07',   1,          0,           0,      'b-blog-7_anblog_original.jpg', '',      '2020-06-07 11:32:42', '',           '',       0,          1,            1,             '',            ''        );
"
);
$images = array('b-blog-1_anblog_original.jpg','b-blog-2_anblog_original.jpg','b-blog-3_anblog_original.jpg','b-blog-4_anblog_original.jpg','b-blog-5_anblog_original.jpg','b-blog-6_anblog_original.jpg','b-blog-7_anblog_original.jpg');
foreach ($images as $image) {
    ImageManager::resize(
        _PS_MODULE_DIR_ . 'anblog/views/img/b/' . $image,
        _ANBLOG_BLOG_IMG_DIR_ . 'b/' .$image
    );
    chmod(_ANBLOG_BLOG_IMG_DIR_ . 'b/' . $image, 0666);
}

$res = (bool)Db::getInstance()->execute(' TRUNCATE TABLE `'._DB_PREFIX_.'anblog_blog_lang`  ');

foreach ($langs as $l) {
    $sql = 'INSERT INTO `'._DB_PREFIX_."anblog_blog_lang`  (`id_anblog_blog`, `id_lang`, `meta_description`, `meta_keywords`, `meta_title`, `link_rewrite`, `content`, `description`, `tags`) VALUES
	(3, LANGUAGEID, '', '', 'At risus pretium urna tortor metus fringilla', 'at-risus-pretium-urna-tortor-metus-fringilla', '<p>Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue</p>\r\n<p> </p>\r\n<p>Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue</p>\r\n<p> </p>\r\n<p>Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue</p>\r\n<p> </p>\r\n<p>Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue</p>', '<p>Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue</p>', 'fashion,cosmetics'),
	(4, LANGUAGEID, '', '', 'Ipsum cursus vestibulum at interdum Vivamus', 'ipsum-cursus-vestibulum-at-interdum-vivamus', '<p>Donec tellus Nulla lorem Nullam elit id ut elit feugiat lacus. Congue eget dapibus congue tincidunt senectus nibh risus Phasellus tristique justo. Justo Pellentesque Donec lobortis faucibus Vestibulum Praesent mauris volutpat vitae metus. Ipsum cursus vestibulum at interdum Vivamus nunc fringilla Curabitur ac quis. Nam lacinia wisi tortor orci quis vitae. Donec tellus Nulla lorem Nullam elit id ut elit feugiat lacus. Congue eget dapibus congue tincidunt senectus nibh risus Phasellus tristique justo. Justo Pellentesque Donec lobortis faucibus Vestibulum Praesent mauris volutpat vitae metus. Ipsum cursus vestibulum at interdum Vivamus nunc fringilla Curabitur ac quis. Nam lacinia wisi tortor orci quis vitae. Donec tellus Nulla lorem Nullam elit id ut elit feugiat lacus. Congue eget dapibus congue tincidunt senectus nibh risus Phasellus tristique justo. Justo Pellentesque Donec lobortis faucibus Vestibulum Praesent mauris volutpat vitae metus. Ipsum cursus vestibulum at interdum Vivamus nunc fringilla Curabitur ac quis. Nam lacinia wisi tortor orci quis vitae.</p>\r\n<p>Donec tellus Nulla lorem Nullam elit id ut elit feugiat lacus. Congue eget dapibus congue tincidunt senectus nibh risus Phasellus tristique justo. Justo Pellentesque Donec lobortis faucibus Vestibulum Praesent mauris volutpat vitae metus. Ipsum cursus vestibulum at interdum Vivamus nunc fringilla Curabitur ac quis. Nam lacinia wisi tortor orci quis vitae.Donec tellus Nulla lorem Nullam elit id ut elit feugiat lacus. Congue eget dapibus congue tincidunt senectus nibh risus Phasellus tristique justo. Justo Pellentesque Donec lobortis faucibus Vestibulum Praesent mauris volutpat vitae metus. Ipsum cursus vestibulum at interdum Vivamus nunc fringilla Curabitur ac quis. Nam lacinia wisi tortor orci quis vitae.Donec tellus Nulla lorem Nullam elit id ut elit feugiat lacus. Congue eget dapibus congue tincidunt senectus nibh risus Phasellus tristique justo. Justo Pellentesque Donec lobortis faucibus Vestibulum Praesent mauris volutpat vitae metus. Ipsum cursus vestibulum at interdum Vivamus nunc fringilla Curabitur ac quis. Nam lacinia wisi tortor orci quis vitae.</p>', '<p>Donec tellus Nulla lorem Nullam elit id ut elit feugiat lacus. Congue eget dapibus congue tincidunt senectus nibh risus Phasellus tristique justo. Justo Pellentesque Donec lobortis faucibus</p>', 'fashion,prestashop,antheme'),
	(5, LANGUAGEID, '', 'fashion,prestashop,antheme,prestashop theme', 'Urna pretium elit mauris cursus Curabitur at elit Vestibulum', 'urna-pretium-elit-mauris-cursus-curabitur-at-elit-vestibulum', '<p>Mi vitae magnis Fusce laoreet nibh felis porttitor laoreet Vestibulum faucibus. At Nulla id tincidunt ut sed semper vel Lorem condimentum ornare. Laoreet Vestibulum lacinia massa a commodo habitasse velit Vestibulum tincidunt In. Turpis at eleifend an mi elit Aenean porta ac sed faucibus. Nunc urna Morbi fringilla vitae orci convallis condimentum auctor sit dui. Urna pretium elit mauris cursus Curabitur at elit Vestibulum.</p>', '<p>Mi vitae magnis Fusce laoreet nibh felis porttitor laoreet Vestibulum faucibus. At Nulla id tincidunt ut sed semper vel Lorem condimentum ornare.</p>', 'fashion'),
	(6, LANGUAGEID, '', '', 'Urna pretium elit mauris cursus Curabitur at elit Vestibulum', 'urna-pretium-elit-mauris-cursus-curabitur-at-elit-vestibulum', '<p>Mi vitae magnis Fusce laoreet nibh felis porttitor laoreet Vestibulum faucibus. At Nulla id tincidunt ut sed semper vel Lorem condimentum ornare. Laoreet Vestibulum lacinia massa a commodo habitasse velit Vestibulum tincidunt In. Turpis at eleifend an mi elit Aenean porta ac sed faucibus. Nunc urna Morbi fringilla vitae orci convallis condimentum auctor sit dui. Urna pretium elit mauris cursus Curabitur at elit Vestibulum. Mi vitae magnis Fusce laoreet nibh felis porttitor laoreet Vestibulum faucibus. At Nulla id tincidunt ut sed semper vel Lorem condimentum ornare. Laoreet Vestibulum lacinia massa a commodo habitasse velit Vestibulum tincidunt In. Turpis at eleifend an mi elit Aenean porta ac sed faucibus. Nunc urna Morbi fringilla vitae orci convallis condimentum auctor sit dui. Urna pretium elit mauris cursus Curabitur at elit Vestibulum.</p>', '<p>Mi vitae magnis Fusce laoreet nibh felis porttitor laoreet Vestibulum faucibus. At Nulla id tincidunt ut sed semper vel Lorem condimentum ornare.</p>', 'antheme,prestashop,theme'),
	(7, LANGUAGEID, '', '', 'Morbi condimentum molestie Nam enim odio sodales', 'morbi-condimentum-molestie-nam-enim-odio-sodales', '<p>Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue.</p><p>Commodo laoreet semper tincidunt lorem Vestibulum nunc at In Curabitur magna. Euismod euismod Suspendisse tortor ante adipiscing risus Aenean Lorem vitae id. Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor Aenean nulla lacinia Nullam elit vel vel. At risus pretium urna tortor metus fringilla interdum mauris tempor congue.</p><p>Donec tellus Nulla lorem Nullam elit id ut elit feugiat lacus. Congue eget dapibus congue tincidunt senectus nibh risus Phasellus tristique justo. Justo Pellentesque Donec lobortis faucibus Vestibulum Praesent mauris volutpat vitae metus. Ipsum cursus vestibulum at interdum Vivamus nunc fringilla Curabitur ac quis. Nam lacinia wisi tortor orci quis vitae.</p><p>Sed mauris Pellentesque elit Aliquam at lacus interdum nascetur elit ipsum. Enim ipsum hendrerit Suspendisse turpis laoreet fames tempus ligula pede ac. Et Lorem penatibus orci eu ultrices egestas Nam quam Vivamus nibh. Morbi condimentum molestie Nam enim odio sodales pretium eros sem pellentesque. Sit tellus Integer elit egestas lacus turpis id auctor nascetur ut. Ac elit vitae.</p><p>Mi vitae magnis Fusce laoreet nibh felis porttitor laoreet Vestibulum faucibus. At Nulla id tincidunt ut sed semper vel Lorem condimentum ornare. Laoreet Vestibulum lacinia massa a commodo habitasse velit Vestibulum tincidunt In. Turpis at eleifend an mi elit Aenean porta ac sed faucibus. Nunc urna Morbi fringilla vitae orci convallis condimentum auctor sit dui. Urna pretium elit mauris cursus Curabitur at elit Vestibulum.</p>', '<p>Sed mauris Pellentesque elit Aliquam at lacus interdum nascetur elit ipsum. Enim ipsum hendrerit Suspendisse turpis laoreet fames tempus ligula pede ac. Et Lorem penatibus orci eu ultrices egestas Nam quam Vivamus nibh.</p>', 'antheme,prestashop,charme,food'),
	(8, LANGUAGEID, '', '', 'Turpis at eleifend an mi elit Aenean porta ac sed faucibus', 'turpis-at-eleifend-an-mi-elit-aenean-porta-ac-sed-faucibus', '<p>Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor. At risus pretium urna tortor metus fringilla interdum mauris tempor congue.</p><p>Commodo laoreet semper tincidunt lorem Vestibulum nunc at In Curabitur magna. Euismod euismod Suspendisse tortor ante adipiscing risus Aenean Lorem vitae id. Odio ut pretium ligula quam Vestibulum consequat convallis fringilla Vestibulum nulla. Accumsan morbi tristique auctor Aenean nulla lacinia Nullam elit vel vel. At risus pretium urna tortor metus fringilla interdum mauris tempor congue.</p>\r\n<p>Donec tellus Nulla lorem Nullam elit id ut elit feugiat lacus. Congue eget dapibus congue tincidunt senectus nibh risus Phasellus tristique justo. Justo Pellentesque Donec lobortis faucibus Vestibulum Praesent mauris volutpat vitae metus. Ipsum cursus vestibulum at interdum Vivamus nunc fringilla Curabitur ac quis. Nam lacinia wisi tortor orci quis vitae.</p>\r\n<p>Sed mauris Pellentesque elit Aliquam at lacus interdum nascetur elit ipsum. Enim ipsum hendrerit Suspendisse turpis laoreet fames tempus ligula pede ac. Et Lorem penatibus orci eu ultrices egestas Nam quam Vivamus nibh. Morbi condimentum molestie Nam enim odio sodales pretium eros sem pellentesque. Sit tellus Integer elit egestas lacus turpis id auctor nascetur ut. Ac elit vitae.</p>\r\n<p>Mi vitae magnis Fusce laoreet nibh felis porttitor laoreet Vestibulum faucibus. At Nulla id tincidunt ut sed semper vel Lorem condimentum ornare. Laoreet Vestibulum lacinia massa a commodo habitasse velit Vestibulum tincidunt In. Turpis at eleifend an mi elit Aenean porta ac sed faucibus. Nunc urna Morbi fringilla vitae orci convallis condimentum auctor sit dui. Urna pretium elit mauris cursus Curabitur at elit Vestibulum.</p>', '<p>Turpis at eleifend an mi elit Aenean porta ac sed faucibus. Nunc urna Morbi fringilla vitae orci convallis condimentum auctor sit dui. Urna pretium elit mauris cursus Curabitur at elit Vestibulum</p>', 'charme,food,template'),
	(9, LANGUAGEID, '', '', 'Nullam ullamcorper nisl quis ornare molestie', 'nullam-ullamcorper-nisl-quis-ornare-molestie', '<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quas.</p><p>Suspendisse posuere, diam in bibendum lobortis, turpis ipsum aliquam risus, sit amet dictum ligula lorem non nisl. Ut vitae nibh id massa vulputate euismod ut quis justo. Ut bibendum sem at massa lacinia, eget elementum ante consectetur. Nulla id pharetra dui, at rhoncus urna. Maecenas non porttitor purus. Nullam ullamcorper nisl quis ornare molestie.</p>\r\n<p>Etiam eget erat est. Phasellus elit justo, mattis non lorem non, aliquam aliquam an. Sed fermentum consectetur magna, eget semper ante. Aliquam scelerisque justo velit. Fusce cursus blandit dolor, in sodales urna vulputate lobortis. Nulla ut tellus turpis. Nullam lacus sem, volutpat id odio sed, cursus tristique eros. Duis at pellentesque magna. Donec magna nisi, vulputate ac nulla eu, ultricies tincidunt tellus. Nunc tincidunt sem urna, nec venenatis libero vehicula ut.</p>\r\n<p>Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Curabitur faucibus aliquam pulvinar. Vivamus mattis volutpat erat, et congue nisi semper quis. Cras vehicula dignissim libero in elementum. Mauris sit amet dolor justo. Morbi consequat velit vel est fermentum euismod. Curabitur in magna augue.</p>', '<p>Suspendisse posuere, diam in bibendum lobortis, turpis ipsum aliquam risus, sit amet dictum ligula lorem non nisl Urna pretium elit mauris cursus Curabitur at elit Vestibulum</p>', 'food,theme');
	";
    $sql = str_replace('LANGUAGEID', $l['id_lang'], $sql);
    $res = (bool)Db::getInstance()->execute($sql);
}

$res = (bool)Db::getInstance()->execute(' TRUNCATE TABLE `'._DB_PREFIX_.'anblog_blog_shop`  ');
$res = (bool)Db::getInstance()->execute(
    'INSERT INTO `'._DB_PREFIX_.'anblog_blog_shop` (`id_anblog_blog`, `id_shop`) VALUES
    (3, 1),
    (4, 1),
    (5, 1),
    (6, 1),
    (7, 1),
    (8, 1),
    (9, 1); '
);
$res = (bool)Db::getInstance()->execute(
    'INSERT INTO `'._DB_PREFIX_.'anblog_blog_categories` (`id_anblog_blog`, `id_anblogcat`, `position`) VALUES
    (3, 4, 0),
    (4, 4, 2),
    (5, 4, 3),
    (6, 4, 4),
    (7, 4, 5),
    (8, 4, 1),
    (8, 3, 1),
    (9, 4, 6); '
);

$res = (bool)Db::getInstance()->execute(' TRUNCATE TABLE `'._DB_PREFIX_.'anblog_comment`  ');
