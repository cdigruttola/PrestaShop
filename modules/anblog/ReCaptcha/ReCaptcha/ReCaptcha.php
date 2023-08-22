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

require_once _PS_MODULE_DIR_.'anblog/ReCaptcha/ReCaptcha/RequestMethod/Post.php';
/**
 * reCAPTCHA client.
 */
class ReReCaptcha
{
    /**
     * Version of this client library.
     * @const string
     */
    const VERSION = 'php_1.1.3';

    /**
     * Shared secret for the site.
     * @var string
     */
    private $secret;

    /**
     * Method used to communicate with service. Defaults to POST request.
     * @var RequestMethod
     */
    private $requestMethod;

    /**
     * Create a configured instance to use the reCAPTCHA service.
     *
     * @param string $secret shared secret between site and reCAPTCHA server.
     * @param RequestMethod $requestMethod method used to send the request. Defaults to POST.
     * @throws \RuntimeException if $secret is invalid
     */
    public function __construct($secret, RequestMethod $requestMethod = null)
    {
        if (empty($secret)) {
            throw new \RuntimeException('No secret provided');
        }

        if (!is_string($secret)) {
            throw new \RuntimeException('The provided secret must be a string');
        }

        $this->secret = $secret;

        if (!is_null($requestMethod)) {
            $this->requestMethod = $requestMethod;
        } else {
            $this->requestMethod = new Post();
        }
    }
 
    /**
     * Calls the reCAPTCHA siteverify API to verify whether the user passes
     * CAPTCHA test.
     *
     * @param string $response The value of 'g-recaptcha-response' in the submitted form.
     * @param string $remoteIp The end user's IP address.
     * @return Response Response from the service.
     */
    public function verify($response, $remoteIp = null)
    {
        // Discard empty solution submissions
        if (empty($response)) {
            return new Response(false, array('missing-input-response'));
        }

        $params = new RequestParameters($this->secret, $response, $remoteIp, self::VERSION);
        $rawResponse = $this->requestMethod->submit($params);
        return Response::fromJson($rawResponse);
    }
}
