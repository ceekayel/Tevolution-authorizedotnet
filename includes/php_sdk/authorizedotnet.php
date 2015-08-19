<?php
/**
 * The AuthorizeNet PHP SDK. Include this file in your project.
 *
 * @package AuthorizeNet
 */

require TEMPLATIC_AUTHORIZE_DIR . '/php_sdk/lib/shared/AuthorizeNetRequest.php';
require TEMPLATIC_AUTHORIZE_DIR . '/php_sdk/lib/shared/AuthorizeNetTypes.php';
require TEMPLATIC_AUTHORIZE_DIR . '/php_sdk/lib/shared/AuthorizeNetXMLResponse.php';
require TEMPLATIC_AUTHORIZE_DIR . '/php_sdk/lib/shared/AuthorizeNetResponse.php';
require TEMPLATIC_AUTHORIZE_DIR . '/php_sdk/lib/AuthorizeNetAIM.php';
require TEMPLATIC_AUTHORIZE_DIR . '/php_sdk/lib/AuthorizeNetARB.php';
require TEMPLATIC_AUTHORIZE_DIR . '/php_sdk/lib/AuthorizeNetCIM.php';
require TEMPLATIC_AUTHORIZE_DIR . '/php_sdk/lib/AuthorizeNetSIM.php';
require TEMPLATIC_AUTHORIZE_DIR . '/php_sdk/lib/AuthorizeNetDPM.php';
require TEMPLATIC_AUTHORIZE_DIR . '/php_sdk/lib/AuthorizeNetTD.php';
require TEMPLATIC_AUTHORIZE_DIR . '/php_sdk/lib/AuthorizeNetCP.php';

if (class_exists("SoapClient")) {
    require TEMPLATIC_AUTHORIZE_DIR . '/php_sdk/lib/AuthorizeNetSOAP.php';
}
/**
 * Exception class for AuthorizeNet PHP SDK.
 *
 * @package AuthorizeNet
 */
class AuthorizeNetException extends Exception
{
}