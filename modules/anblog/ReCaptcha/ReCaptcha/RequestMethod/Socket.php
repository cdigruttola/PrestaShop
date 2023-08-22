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

use Tools;

/**
 * Convenience wrapper around native socket and file functions to allow for
 * mocking.
 */
class Socket
{
    private $handle = null;

    /**
     * fsockopen
     *
     * @see http://php.net/fsockopen
     * @param string $hostname
     * @param int $port
     * @param int $errno
     * @param string $errstr
     * @param float $timeout
     * @return resource
     */
    public function fsockopen($hostname, $port = -1, &$errno = 0, &$errstr = '', $timeout = null)
    {
        $this->handle = fsockopen($hostname, $port, $errno, $errstr, (is_null($timeout) ? ini_get("default_socket_timeout") : $timeout));

        if (!!$this->handle && $errno === 0 && $errstr === '') {
            return $this->handle;
        }
        return false;
    }

    /**
     * fwrite
     *
     * @see http://php.net/fwrite
     * @param string $string
     * @param int $length
     * @return int | bool
     */
    public function fwrite($string, $length = null)
    {
        return fwrite($this->handle, $string, (is_null($length) ? Tools::strlen($string) : $length));
    }

    /**
     * fgets
     *
     * @see http://php.net/fgets
     * @param int $length
     * @return string
     */
    public function fgets($length = null)
    {
        return fgets($this->handle, $length);
    }

    /**
     * feof
     *
     * @see http://php.net/feof
     * @return bool
     */
    public function feof()
    {
        return feof($this->handle);
    }

    /**
     * fclose
     *
     * @see http://php.net/fclose
     * @return bool
     */
    public function fclose()
    {
        return fclose($this->handle);
    }
}
