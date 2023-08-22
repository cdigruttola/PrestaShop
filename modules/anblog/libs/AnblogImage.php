<?php
require_once _PS_MODULE_DIR_.'anblog/loader.php';

class AnblogImage
{
    public $id;
    public $uplname = '';
    public $main = '';
    public $mainurl = '';
    public $error = '';
    public $thumbs = array();
    public $thumbsurls = array();
    public $exists = false;

    public function __construct($post)
    {
        if (is_array($post) && array_key_exists('id_anblog_blog', $post)) {
            $this->id = $post['id_anblog_blog'];
            $postimg =  $post['image'];
        } elseif (is_array($post) && array_key_exists('id', $post)) {
            $this->id = $post['id'];
            $postimg =  $post['image'];
        } elseif (is_object($post) && get_class($post) == 'AnblogBlog' && isset($post->id)) {
            $this->id = $post->id;
            $postimg =  $post->image;
        } else {
            return false;
        }
		
        if ($postimg != '') {
            $this->main = _ANBLOG_BLOG_IMG_DIR_.'b/'.$postimg;
			
            if (!$this->baseImgExists()) {
                return false;
            }

            $url = _ANBLOG_BLOG_IMG_URI_;
            $this->mainurl = $url.'b/'.$postimg;
            $this->checkAndCreateThumbs();
            $this->exists = true;
        }
    }

    public function baseImgExists()
    {
		return file_exists($this->main);
    }

    public function uploadNew($id = 0)
    {
        $image = $_FILES['image_link'];
        $res = true;
        if ($id) {
            $this->id = $id;
        }

        $res &= is_array($image);
        if (ImageManager::validateUpload($image) != false) {
            $this->error = ImageManager::validateUpload($image);
            return false;
        }
        $tmp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
        $res &= move_uploaded_file($image['tmp_name'], $tmp_name);

        if ($res) {
            $type = Tools::strtolower(Tools::substr(strrchr($image['name'], '.'), 1));

            Configuration::set('PS_IMAGE_QUALITY', $type);
            $img_name = 'b-'. uniqid() . '-' .'anblog_original.'.$type;
            if (ImageManager::resize(
                $tmp_name,
                _ANBLOG_BLOG_IMG_DIR_.'b/'.$img_name
            )
                && chmod(_ANBLOG_BLOG_IMG_DIR_.'b/'.$img_name, 0666)
            ) {
                $res = true;
                $this->main = _ANBLOG_BLOG_IMG_DIR_.'b/'.$img_name;
                $this->uplname = $img_name;
            } else {
                return false;
            }
        }

        $res &= $this->checkAndCreateThumbs(true);

        if (!$res || !isset($img_name)) {
            return false;
        }

        $this->exists = true;
        return $img_name;
    }

    public function checkAndCreateThumbs($new = false)
    {

        if ($this->main == '') {
            return false;
        }
        $res = true;

        $image_types = Db::getInstance()->executeS('
                SELECT *
                FROM `'._DB_PREFIX_.'image_type`
                WHERE `name`LIKE \'anblog_%\'');

        $type = Tools::strtolower(Tools::substr(strrchr($this->main, '.'), 1));

        Configuration::set('PS_IMAGE_QUALITY', $type);
        foreach ($image_types as $imageType) {
            $img_path = str_replace('anblog_original', $imageType['name'], $this->main);
            if ($new || !file_exists($img_path)) {
                if (ImageManager::resize(
                    $this->main,
                    $img_path,
                    (int)$imageType['width'],
                    (int)$imageType['height']
                )
                    && chmod($img_path, 0666)
                ) {
                    $res &= true;
                } else {
                    $res &= false;
                }
            }

            $this->thumbs[$imageType['name']] = $img_path;
            $this->thumbsurls[$imageType['name']] = str_replace('anblog_original', $imageType['name'], $this->mainurl);
        }
        return $res;
    }

    public function getImageTypeByName($name)
    {
        return Db::getInstance()->getRow('
                SELECT *
                FROM `'._DB_PREFIX_.'image_type`
                WHERE `name`=\''. $name .'\'');
    }

    public function delete($originalSave = false)
    {
		if ($this->baseImgExists()) {

            $image_types = Db::getInstance()->executeS('
                SELECT *
                FROM `'._DB_PREFIX_.'image_type`
                WHERE `name`LIKE \'anblog_%\'');
            foreach ($image_types as $imageType) {
			
            //    if (!$thumbsOnly || $imageType['name'] != 'anblog_default') {
                    @unlink(str_replace('anblog_original', $imageType['name'], $this->main));
             //   }
            }
			
			if (!$originalSave){
				return @unlink($this->main);
			}
			return true;
        } else {
            return false;
        }
    }
}
