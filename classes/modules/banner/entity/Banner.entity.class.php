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
    public function getName() {
        return $this->_aData['banner_name'];
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

}