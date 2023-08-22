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

/**
 * Stores and formats the parameters for the request to the reCAPTCHA service.
 */
class RequestParameters
{
    /**
     * Site secret.
     * @var string
     */
    private $secret;

    /**
     * Form response.
     * @var string
     */
    private $response;

    /**
     * Remote user's IP address.
     * @var string
     */
    private $remoteIp;

    /**
     * Client version.
     * @var string
     */
    private $version;

    /**
     * Initialise parameters.
     *
     * @param string $secret Site secret.
     * @param string $response Value from g-captcha-response form field.
     * @param string $remoteIp User's IP address.
     * @param string $version Version of this client library.
     */
    public function __construct($secret, $response, $remoteIp = null, $version = null)
    {
        $this->secret = $secret;
        $this->response = $response;
        $this->remoteIp = $remoteIp;
        $this->version = $version;
    }

    /**
     * Array representation.
     *
     * @return array Array formatted parameters.
     */
    public function toArray()
    {
        $params = array('secret' => $this->secret, 'response' => $this->response);

        if (!is_null($this->remoteIp)) {
            $params['remoteip'] = $this->remoteIp;
        }

        if (!is_null($this->version)) {
            $params['version'] = $this->version;
        }

        return $params;
    }

    /**
     * Query string representation for HTTP request.
     *
     * @return string Query string formatted parameters.
     */
    public function toQueryString()
    {
        return http_build_query($this->toArray(), '', '&');
    }
}
