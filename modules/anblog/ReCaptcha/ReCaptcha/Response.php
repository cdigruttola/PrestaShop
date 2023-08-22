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
 * The response returned from the service.
 */
class Response
{
    /**
     * Success or failure.
     * @var boolean
     */
    private $success = false;

    /**
     * Error code strings.
     * @var array
     */
    private $errorCodes = array();

    /**
     * The hostname of the site where the reCAPTCHA was solved.
     * @var string
     */
    private $hostname;

    /**
     * Build the response from the expected JSON returned by the service.
     *
     * @param string $json
     * @return \ReCaptcha\Response
     */
    public static function fromJson($json)
    {
        $responseData = json_decode($json, true);

        if (!$responseData) {
            return new Response(false, array('invalid-json'));
        }

        $hostname = isset($responseData['hostname']) ? $responseData['hostname'] : null;

        if (isset($responseData['success']) && (bool)$responseData['success']) {
            return new Response(true, array(), $hostname);
        }

        if (isset($responseData['error-codes']) && is_array($responseData['error-codes'])) {
            return new Response(false, $responseData['error-codes'], $hostname);
        }

        return new Response(false, array(), $hostname);
    }

    /**
     * Constructor.
     *
     * @param boolean $success
     * @param array $errorCodes
     * @param string $hostname
     */
    public function __construct($success, array $errorCodes = array(), $hostname = null)
    {
        $this->success = $success;
        $this->errorCodes = $errorCodes;
        $this->hostname = $hostname;
    }

    /**
     * Is success?
     *
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * Get error codes.
     *
     * @return array
     */
    public function getErrorCodes()
    {
        return $this->errorCodes;
    }

    /**
     * Get hostname.
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }
}
