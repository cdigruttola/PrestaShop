<?php
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

require_once _PS_MODULE_DIR_.'anblog/ReCaptcha/ReCaptcha/RequestMethod.php';

/**
 * Convenience wrapper around the cURL functions to allow mocking.
 */
class Curl
{

    /**
     * @see http://php.net/curl_init
     * @param string $url
     * @return resource cURL handle
     */
    public function init($url = null)
    {
        return curl_init($url);
    }

    /**
     * @see http://php.net/curl_setopt_array
     * @param resource $ch
     * @param array $options
     * @return bool
     */
    public function setoptArray($ch, array $options)
    {
        return curl_setopt_array($ch, $options);
    }

    /**
     * @see http://php.net/curl_exec
     * @param resource $ch
     * @return mixed
     */
    public function exec($ch)
    {
        return curl_exec($ch);
    }

    /**
     * @see http://php.net/curl_close
     * @param resource $ch
     */
    public function close($ch)
    {
        curl_close($ch);
    }
}
