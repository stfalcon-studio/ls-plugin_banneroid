<?php

/* ---------------------------------------------------------------------------
 * @Plugin Name: Banneroid
 * @Plugin Id: Banneroid
 * @Plugin URI:
 * @Description: Banner rotator for LS
 * @Author: stfalcon-studio
 * @Author URI: http://stfalcon.com
 * @LiveStreet Version: 0.4.2
 * @License: GNU GPL v2, http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * ----------------------------------------------------------------------------
 */

class PluginBanneroid_ModuleBanner_EntityBanner extends Entity {

    /**
     * Return banner Id
     *
     * @return integer
     */
    public function getId() {
        return $this->_aData['banner_id'];
    }

    /**
     * Return banner name
     *
     * @return string
     */
    public function getBannerName() {
        return $this->_aData['banner_name'];
    }

    /**
     * Return Banner html
     *
     * @return string
     */
    public function getBannerHtml(){
        return $this->_aData['banner_html'];
    }

    /**
     * Return Banner url
     *
     * @return string
     */
    public function getBannerUrl(){
        return $this->_aData['banner_url'];
    }

    /**
     * Return Banner lang
     *
     * @return string
     */
    public function getBannerLang(){
        return $this->_aData['banner_lang'];
    }


    /**
     * Return Banner Start Date
     *
     * @return string
     */
    public function getBannerStartDate(){
        return $this->_aData['banner_start_date'];
    }

    /**
     * Return Banner End Date
     *
     * @return string
     */
    public function getBannerEndDate(){
        return $this->_aData['banner_end_date'];
    }

    /**
     * Return Banner is Active
     *
     * @return bool
     */
    public function getBannerIsActive(){
        return $this->_aData['banner_is_active'];
    }

    /**
     * Return Banner is Active
     *
     * @return bool
     */
    public function getBannerType(){
        return $this->_aData['banner_type'];
    }

    public function getBannerPlaces(){
        return $this->_aData['banner_places'];
    }



    /**
     * Return current date
     *
     * @return date
     */
    public function getNowDate() {
        return date('Y-m-d');
    }

    /**
     * Return max available year for banner settings
     *
     * @return integer
     */
    public function getMaxYear() {
        return date('Y') + Config::Get('plugin.banneroid.max_year');
    }

//    public function setBannerLang($data)
//    {
//        $this->_aData['banner_lang'] = $data;
//    }

    public function setBannerId($data)
    {
        $this->_aData['banner_id'] = $data;
    }
    public function setBannerName($data)
    {
        $this->_aData['banner_name'] = $data;
    }

    public function setBannerHtml($data)
    {
        $this->_aData['banner_html'] = $data;
    }

    public function setBannerUrl($data)
    {
        $this->_aData['banner_url'] = $data;
    }
    public function setBannerStartDate($data)
    {
        $this->_aData['banner_start_date'] = $data;
    }
    public function setBannerEndDate($data)
    {
        $this->_aData['banner_end_date'] = $data;
    }

    public function setBannerType($data)
    {
        $this->_aData['banner_type'] = $data;
    }

    public function setBannerPlaces($data)
    {
        $this->_aData['banner_places'] = $data;
    }

    public function setBannerIsActive($data)
    {
        $this->_aData['banner_is_active'] = $data;
    }

    public function setDateAdd($data = null)
    {
        if (is_null($data)) {
            $data = date('Y-m-d H::i:s');
        }
        $this->_aData['banner_add_date'] = $data;
    }

    public function setDateEdit($data = null)
    {
        if (is_null($data)) {
            $data = date('Y-m-d H::i:s');
        }
        $this->_aData['banner_edit_date'] = $data;
    }
}