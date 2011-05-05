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

class PluginBanneroid_ModuleBanner extends Module {

    /**
     * Mapper
     * @var Mapper
     */
    protected $_oMapper;
    /**
     * Names of palaces on page
     * @var Mapper
     */
    protected $_aPlaceNames;

    /**
     * Initialization
     *
     * @return void
     */
    public function Init() {
        $this->_oMapper = Engine::GetMapper(__CLASS__);
        $this->_aPlaceNames[0] = $this->Lang_Get('banneroid_total');
        $this->_aPlaceNames[1] = $this->Lang_Get('banneroid_under_article');
        $this->_aPlaceNames[2] = $this->Lang_Get('banneroid_side_bar');
    }

    /**
     * Return list of banners from DB
     *
     * @return array
     */
    public function GetBannersList() {
        $aCollection = array();
        $aRows = $this->_oMapper->GetBannersList();
        if (is_array($aRows) && count($aRows)) {
            foreach ($aRows as $aRow) {
                $oBanner = new PluginBanneroid_ModuleBanner_EntityBanner($aRow);
                $aPages = $this->_oMapper->GetBannerPagesNames($oBanner);
                $sPages = '';
                foreach ($aPages as $aRow) {
                    $sPages.=" " . $this->Lang_Get($aRow['place_name']) . '(' .
                            $this->_aPlaceNames[$aRow['place_type']] . ')';
                }
                $oBanner->setPagesNames($sPages);
                $aCollection[] = $oBanner;
            }
            return $aCollection;
        }
    }

    /**
     * Return banner by Id from DB
     *
     * @return object|null
     */
    public function GetBannerById($sId) {
        $aRow = $this->_oMapper->GetBannerById($sId);
        if ($aRow) {
            return new PluginBanneroid_ModuleBanner_EntityBanner($aRow);
        } else
            return null;
    }

    /**
     * Upload banner image
     *
     * @return boolean
     */
    public function UploadImage($aImageFile, $oBanner) {
        $sFileTmp = $aImageFile['tmp_name'];

        if (is_uploaded_file($sFileTmp)) {

            if (strlen(@$oBanner->getBannerImage())) {//remove old image file
                @unlink(Config::Get("plugin.banneroid.upload_dir") . $oBanner->getBannerImage());
            }

            $sFileName = func_generator(); //gen new file name

            $aFileInfo = pathinfo($aImageFile['name']);
            $sFileName.="." . $aFileInfo['extension'];



            if (!@move_uploaded_file($sFileTmp,
                            Config::Get("plugin.banneroid.upload_dir") . $sFileName)) {
                return false;
            }

            $oBanner->setBannerImage($sFileName);
            return true;
        }
        return false;
    }

    /**
     * Update banner 
     *
     * @return boolean
     */
    public function UpdateBanner(PluginBanneroid_ModuleBanner_EntityBanner $oBanner) {
        if ($oBanner->getBannerId() == '0') {
            return $this->_oMapper->AddBanner($oBanner);
        } else {
            return $this->_oMapper->UpdateBanner($oBanner);
        }
    }

    /**
     * Get all available pages
     *
     * @return array
     */
    public function GetAllPages() {
        return $this->_oMapper->GetAllPages();
    }

    /**
     * Update banners pages in DB
     *
     * @return true
     */
    public function UpdateBannerPages($aPages, $oBanner) {
        $aBP = $this->_oMapper->GetBannerPages($oBanner); //Current banner pages

        $aBannerPages[1] = array();
        $aBannerPages[2] = array();

        if (is_array($aBP) && count($aBP)) {

            foreach ($aBP as $aRow) { //make arrays for compare new pages and current
                $aBannerPages[$aRow['place_type']][] = $aRow['page_id'];
            }
        }

        foreach ($aPages as $iK => $aVal) {

            $aAdd = array_diff($aVal, $aBannerPages[$iK]); //Add new pages

            foreach ($aAdd as $v) {
                $this->_oMapper->AddBannerPage($v, $iK, $oBanner);
            }

            $aDel = array_diff($aBannerPages[$iK], $aVal); //Remove not used pages

            foreach ($aDel as $v) {
                $this->_oMapper->DeleteBannerPage($v, $iK, $oBanner);
            }
        }
        return true;
    }

    /**
     * Return banner pages
     *
     * @return array
     */
    public function GetActivePages($oBanner) {
        $aBP = $this->_oMapper->GetBannerPages($oBanner); //Current banner pages
        $aPages = array();
        foreach ($aBP as $aRow) {
            $aPages[$aRow['page_id']] = 1;
        }
        return $aPages;
    }

    /**
     * Delete banner
     * @param string $sBannerId
     * @return void
     */
    public function DeleteBanner($sBannerId) {
        $oBanner = $this->GetBannerById($sBannerId);

        if (strlen(@$oBanner->getBannerImage())) {//remove image file
            @unlink(Config::Get("plugin.banneroid.upload_dir") . $oBanner->getBannerImage());
        }

        $this->_oMapper->DeleteBanner($oBanner);
    }

    /**
     * Hide banner
     * 
     * @param int $sBannerId
     * @return void
     */
    public function HideBanner($sBannerId)
    {
       $this->_oMapper->HideBanner($sBannerId);
    }

    /**
     * Return page url
     * 
     * @return string
     */
    public function GetFullUrl() {
        $sURL = 'http';
        if (isset($_SERVER["HTTPS"]) == "on") {
            $sURL.= "s";
        }
        $sURL.= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $sURL.= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $sURL.= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $sURL;
    }

    /**
     * Get side bar banners by page url
     * @param string $sUrl
     * @return array
     */
    public function GetSideBarBanners($sUrl) {
        $aBanners = $this->_oMapper->GetBannerByParams($sUrl, 2);
        $oBanner = null;
        if (is_array($aBanners) && count($aBanners)) {
            foreach ($aBanners as $aRow) {
                if ($aRow['banner_click_max'] != 0 && $aRow['click_count'] >= $aRow['banner_click_max'] ) {
                    continue;
                } elseif ($aRow['banner_view_max'] != 0 && $aRow['view_count'] >= $aRow['banner_view_max']) {
                    continue;
                } else {
                  $oBanner = new PluginBanneroid_ModuleBanner_EntityBanner($aRow);
                  break;
                }
            }
        }
        return $oBanner;
    }

    /**
     * Get content banners by page url
     * @param string $sUrl
     * @param $bAddStats boolean (add banner statistic)
     * @return array
     */
    public function GetContentBanners($sUrl, $bAddStats=false) {
        $aBanners = $this->_oMapper->GetBannerByParams($sUrl, 1);
        $aList = array();

        if (is_array($aBanners) && count($aBanners)) {
            foreach ($aBanners as $aRow) {
                $aList[] = new PluginBanneroid_ModuleBanner_EntityBanner($aRow);
                if ($bAddStats) {
                    $oUser = $this->User_GetUserCurrent();
                    $this->AddBannerStats(array
                        ('banner_id' => $aRow['banner_id'],
                        'user_id' => $oUser ? $oUser->getId() : '',
                        'event' => 'SHOW',
                        'show_type' => '1',
                        'banner_uri' => $this->GetFullUrl(),
                    ));
            }
        }
        }
        return $aList;
    }

    /**
     * Save banner
     * @param object $oBanner
     * @return boolean
     */
    function Save($oBanner) {
        if (isset($_REQUEST['submit_banner'])) { // Update or insert banner
            $iBannerId = $oBanner->getId();

            $sStartDate = $_REQUEST['banner_start_date'];
            $sEndDate = $_REQUEST['banner_end_date'];

            $bStateError = 0;
            if (!preg_match(Config::Get('plugin.banneroid.banner_date_reg'), $sStartDate)) {
                $this->Message_AddError(
                        $this->Lang_Get("banneroid_error_date_start"),
                        $this->Lang_Get('banneroid_error'));
                $bStateError = 1;
            }

            if (!preg_match(Config::Get('plugin.banneroid.banner_date_reg'), $sEndDate)) {
                $this->Message_AddError(
                        $this->Lang_Get("banneroid_error_date_end"),
                        $this->Lang_Get('banneroid_error'));
                $bStateError = 1;
            }


            if (!func_check($_REQUEST['banner_name'], 'text', 2, 3000)) {
                $this->Message_AddError(
                        $this->Lang_Get("banneroid_error_name"),
                        $this->Lang_Get('banneroid_error'));
                $bStateError = 1;
            }

            if (!preg_match(Config::Get('plugin.banneroid.banner_url_reg'), $_REQUEST['banner_url']) and !$_REQUEST['banner_html']) {
                $this->Message_AddError(
                        $this->Lang_Get("banneroid_error_url"),
                        $this->Lang_Get('banneroid_error'));
                $bStateError = 1;
            }
            if (!func_check((int)getRequest('banner_view_max', 0), 'float', 0, 65536)) {
                 $this->Message_AddError(
                        $this->Lang_Get("banneroid_error_max_view"),
                        $this->Lang_Get('banneroid_error'));
                $bStateError = 1;
            }

            if (!func_check((int)getRequest('banner_click_max', 0), 'float', 0, 65536)) {
                 $this->Message_AddError(
                        $this->Lang_Get("banneroid_error_max_click"),
                        $this->Lang_Get('banneroid_error'));
                $bStateError = 1;
            }

            if ($bStateError) {
                return false;
            }


            // Fill banner entity object
            $oBanner->setBannerHtml($_REQUEST['banner_html']);
            $oBanner->setBannerName($_REQUEST['banner_name']);
            $oBanner->setBannerUrl($_REQUEST['banner_url']);
            $oBanner->setBannerStartDate($sStartDate);
            $oBanner->setBannerEndDate($sEndDate);
            $oBanner->setBannerClickMax(getRequest('banner_click_max', 0));
            $oBanner->setBannerViewMax(getRequest('banner_view_max', 0));
            $oBanner->setBannerType($_REQUEST['banner_type']);
            $oBanner->setBannerIsActive($_REQUEST['banner_is_active']);

            $iOk = 1;

            //upload image for banner ------------
            if (isset($_FILES["banner_image"]) && $_FILES["banner_image"]["error"] == 0) {
                $aImageFile = $_FILES["banner_image"];

                $aSize = @getimagesize($aImageFile["tmp_name"]);
                if (!in_array($aSize['mime'], Config::Get('plugin.banneroid.images_mime'))) {
                    $this->Message_AddError(
                            $this->Lang_Get("banneroid_error_image_extension"),
                            $this->Lang_Get('banneroid_error'));
                    $iOk = 0;
                } else

                if (!$this->UploadImage($aImageFile, $oBanner)) {
                    $this->Message_AddError(
                            $this->Lang_Get("banneroid_error_unable_to_upload_image"),
                            $this->Lang_Get('banneroid_error'));
                    $iOk = 0;
                }
            }

            if ($iOk) { //If no errors add or update banner
                $iRes = $this->UpdateBanner($oBanner);
            }
            if ($iBannerId == 0) { // Update id for new banner
                $oBanner->setBannerId($iRes);
            }

            if (!isset($iRes)) { //show error editiding banner
                $this->Message_AddError(
                        $this->Lang_Get("banneroid_error_edit"),
                        $this->Lang_Get('banneroid_error'));
                return false;
            } else { // Add banner pages --------------
                if ($_REQUEST['banner_type'] == 1 && is_array(getRequest('banner_place')) && count(getRequest('banner_place'))) {
                    $aPages[1] = getRequest('banner_place');
                } else {
                    $aPages[1] = array();
                }

                if ($_REQUEST['banner_type'] == 2 && is_array(getRequest('banner_place')) && count(getRequest('banner_place'))) {
                    $aPages[2] = getRequest('banner_place');
                } else {
                    $aPages[2] = array();
                }

                $this->UpdateBannerPages($aPages, $oBanner);
            }
            return true;
        }
        return false;
    }

    /**
     * Add banner statistics to db
     * @param array $aParams     
     * @return void
     */
    public function AddBannerStats($aParams) {
        if ($statId = $this->_oMapper->GetStatIdBannerCurrentDay($aParams['banner_id'])){
            $this->_oMapper->UpdateBannerStats($aParams);
        } else {
            $this->_oMapper->AddBannerStats($aParams);
        }
    }

    /**
     * Get banner statistics
     * @param array $aParams
     * @return void
     */
    public function GetStatsTotal($aParams=array()) {
        $aSub = array();
        if (isset($_REQUEST['filter'])) {
            $aSub = array('stats_date_start' => $_REQUEST['banner_start_date'],
                'stats_date_end' => $_REQUEST['banner_end_date']);
        }

        $aData[$this->_aPlaceNames[0]] = $this->_oMapper->GetBannerStatsbyParams($aSub);
        $aData[$this->_aPlaceNames[1]] = $this->_oMapper->GetBannerStatsbyParams(
                        array_merge($aSub, array('banner_type' => 1))
        );

        $aData[$this->_aPlaceNames[2]] = $this->_oMapper->GetBannerStatsbyParams(
                        array_merge($aSub, array('banner_type' => 2))
        );

        return $aData;
    }

    /**
     * Get banner statistics
     * 
     * @return array
     */
    public function GetStatsBanners($bannerId = null) {

        $aDates = array();

        if (isset($_REQUEST['banner_start_date'])) {
            $aDates['stats_date_start'] = $_REQUEST['banner_start_date'];
        }

        if (isset($_REQUEST['banner_end_date'])) {
            $aDates['stats_date_end'] = $_REQUEST['banner_end_date'];
        }
        if ($bannerId) {
            $aDates['banner_id'] = $bannerId;
        }
        $aData = $this->_oMapper->GetBannerStatsListbyParams(
                        array_merge($aDates, array(
                            'stats_group_by' => 'banner_id',
                            'stats_order_by_desc' => 'banner_id',
                        )));

        return $aData;
    }

}
