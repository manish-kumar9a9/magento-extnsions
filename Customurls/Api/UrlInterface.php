<?php
/**
 * Contributor company: iPragmatech solution Pvt Ltd.
 * Contributor Author : Manish Kumar
 * Date: 22/09/16
 * Time: 06:17 PM
 */
namespace Ipragmatech\Customurls\Api;

/**
 * Interface UrlInterface
 * @package Ipragmatech\Customurls\Api
 * @api
 */
interface UrlInterface
{

    /**
     * Return about us url.
     * @return string
     */
    public function getAboutusUrl();

    /**
     * Return contact us url.
     * @return string
     */
    public function getContactUrl();

    /**
     * Return customer service url.
     * @return string
     */
    public function getCustomerServiceUrl();

    /**
     * Return policy url.
     * @return string
     */
    public function getPolicyUrl();


}