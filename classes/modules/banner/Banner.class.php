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
    
    protected $aActivePlugins = array();

    /**
     * Initialization
     *
     * @return void
     */
    public function Init() {
        $this->_oMapper = Engine::GetMapper(__CLASS__);
        $this->_aPlaceNames[0] = $this->Lang_Get('plugin.banneroid.banneroid_total');
        $this->_aPlaceNames[1] = $this->Lang_Get('plugin.banneroid.banneroid_under_article');
        $this->_aPlaceNames[2] = $this->Lang_Get('plugin.banneroid.banneroid_side_bar');
        $this->_aPlaceNames[3] = $this->Lang_Get('plugin.banneroid.banneroid_body_begin');
        $this->_aPlaceNames[4] = $this->Lang_Get('plugin.banneroid.banneroid_body_end');
        $this->aActivePlugins = $this->Plugin_GetActivePlugins();
    }

    /**
     * Return list of banners from DB
     *
     * @param array $params
     *
     * @return array
     */
    public function GetBannersList($params=array()) {
        $aCollection = array();
        $aRows = $this->_oMapper->GetBannersList($params);
        if (is_array($aRows) && count($aRows)) {
            foreach ($aRows as $aRow) {
                $oBanner = new PluginBanneroid_ModuleBanner_EntityBanner($aRow);
                $aPages = $this->_oMapper->GetBannerPagesNames($oBanner);
                $sPages = '';
                foreach ($aPages as $aRow) {
                    $sPages.=" " . $this->Lang_Get('plugin.banneroid.'.$aRow['place_name']) . '(' .
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
     * @param $sId
     *
     * @return null|object
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
     * @param $aImageFile
     * @param $oBanner
     *
     * @return bool
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
     * @param PluginBanneroid_ModuleBanner_EntityBanner $oBanner
     *
     * @return bool
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
     * @param $aPages
     * @param $oBanner
     *
     * @return bool
     */
    public function UpdateBannerPages($aPages, $oBanner) {
        $aBP = $this->_oMapper->GetBannerPages($oBanner); //Current banner pages

        $aBannerPages = array_fill(1, 4, array());

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
     * @param $oBanner
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
     * Get footer banners by page url
     *
     * @param string $sUrl
     * @param bool   $bAddStats (add banner statistic)
     *
     * @return array
     */
    public function GetFooterBanners($sUrl, $bAddStats=false) {
        
        return $this->GetBannersByPosition($sUrl, $bAddStats, 4);
    }

    /**
     * Get header banners by page url
     *
     * @param string $sUrl
     * @param bool   $bAddStats (add banner statistic)
     *
     * @return array
     */
    public function GetHeaderBanners($sUrl, $bAddStats=false) {

        return $this->GetBannersByPosition($sUrl, $bAddStats, 3);
    }

    /**
     * Get sidebar banners by page url
     *
     * @param string $sUrl
     * @param bool   $bAddStats (add banner statistic)
     *
     * @return array
     */
    public function GetSideBarBanners($sUrl, $bAddStats = false) {

        return $this->GetBannersByPosition($sUrl, $bAddStats, 2);
    }

    /**
     * Get content banners by page url
     *
     * @param string $sUrl
     * @param bool   $bAddStats (add banner statistic)
     *
     * @return array
     */
    public function GetContentBanners($sUrl, $bAddStats=false) {

        return $this->GetBannersByPosition($sUrl, $bAddStats, 1);
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
                        $this->Lang_Get("plugin.banneroid.banneroid_error_date_start"),
                        $this->Lang_Get('plugin.banneroid.banneroid_error'));
                $bStateError = 1;
            }

            if (!preg_match(Config::Get('plugin.banneroid.banner_date_reg'), $sEndDate)) {
                $this->Message_AddError(
                        $this->Lang_Get("plugin.banneroid.banneroid_error_date_end"),
                        $this->Lang_Get('plugin.banneroid.banneroid_error'));
                $bStateError = 1;
            }


            if (!func_check($_REQUEST['banner_name'], 'text', 2, 3000)) {
                $this->Message_AddError(
                        $this->Lang_Get("plugin.banneroid.banneroid_error_name"),
                        $this->Lang_Get('plugin.banneroid.banneroid_error'));
                $bStateError = 1;
            }

            if (!preg_match(Config::Get('plugin.banneroid.banner_url_reg'), $_REQUEST['banner_url']) and !$_REQUEST['banner_html']) {
                $this->Message_AddError(
                        $this->Lang_Get("plugin.banneroid.banneroid_error_url"),
                        $this->Lang_Get('plugin.banneroid.banneroid_error'));
                $bStateError = 1;
            }
            
            
            if (in_array('l10n', $this->aActivePlugins)) {
                $sLang = getRequest('banneroid_lang');
                if ($sLang === '0') {
                    $sLang = null;
                } else {
                    $aLangs = $this->PluginL10n_L10n_GetAllowedLangs();
                    if (!in_array($sLang, $aLangs)) {
                        $this->Message_AddError(
                            $this->Lang_Get("plugin.banneroid.banneroid_error_lang"),
                            $this->Lang_Get('plugin.banneroid.banneroid_error'));
                        $bStateError = true;
                    }
                }
            } else {
                $sLang = null;
            }

            if ($bStateError) {
                return false;
            }


            // Fill banner entity object
            $oBanner->setBannerHtml($_REQUEST['banner_html']);
            $oBanner->setBannerName($_REQUEST['banner_name']);
            $oBanner->setBannerUrl($_REQUEST['banner_url']);
            $oBanner->setBannerLang($sLang);
            $oBanner->setBannerStartDate($sStartDate);
            $oBanner->setBannerEndDate($sEndDate);
            $oBanner->setBannerType($_REQUEST['banner_type']);
            $oBanner->setBannerIsActive($_REQUEST['banner_is_active']);

            $iOk = 1;

            //upload image for banner ------------
            if (isset($_FILES["banner_image"]) && $_FILES["banner_image"]["error"] == 0) {
                $aImageFile = $_FILES["banner_image"];

                $aSize = @getimagesize($aImageFile["tmp_name"]);
                if (!in_array($aSize['mime'], Config::Get('plugin.banneroid.images_mime'))) {
                    $this->Message_AddError(
                            $this->Lang_Get("plugin.banneroid.banneroid_error_image_extension"),
                            $this->Lang_Get('plugin.banneroid.banneroid_error'));
                    $iOk = 0;
                } else

                if (!$this->UploadImage($aImageFile, $oBanner)) {
                    $this->Message_AddError(
                            $this->Lang_Get("plugin.banneroid.banneroid_error_unable_to_upload_image"),
                            $this->Lang_Get('plugin.banneroid.banneroid_error'));
                    $iOk = 0;
                }
            }

            if ($iOk) { //If no errors add or update banner
                $iRes = $this->UpdateBanner($oBanner);
            }

            if ($iBannerId == 0 && isset($iRes)) { // Update id for new banner
                $oBanner->setBannerId($iRes);
            }

            if (!isset($iRes)) { //show error editiding banner
                $this->Message_AddError(
                        $this->Lang_Get("plugin.banneroid.banneroid_error_edit"),
                        $this->Lang_Get('plugin.banneroid.banneroid_error'));
                return false;
            } elseif(is_array(getRequest('banner_place')) && count(getRequest('banner_place'))) {
                // Add banner pages --------------
                $aPages = array_fill(1, 4, array());
                $iBannerType = (int) getRequest('banner_type');
                switch ($iBannerType){
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                        $aPages[$iBannerType] = getRequest('banner_place', array());
                        break;
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
     *
     * @param array $aParams
     *
     * @return void
     */
    public function GetStatsTotal($aParams=array()) {
        $aSub = array();
        if (isset($_REQUEST['filter'])) {
            $aSub = array(
                'stats_date_start' => $_REQUEST['banner_start_date'],
                'stats_date_end'   => $_REQUEST['banner_end_date']);
        }

        $aData[$this->_aPlaceNames[0]] = $this->_oMapper->GetBannerStatsbyParams($aSub);

        for ($i = 1; $i <= 4; $i++) {
            $aData[$this->_aPlaceNames[$i]] = $this->_oMapper->GetBannerStatsbyParams(
                array_merge($aSub, array('banner_type' => $i))
            );
        }

        return $aData;
    }

    /**
     * Get banner statistics
     *
     * @param int|null $bannerId
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
        $aData = $this->_oMapper->GetBannerStatsListbyParams(array_merge($aDates, array(
            'stats_group_by'      => 'banner_id',
            'stats_order_by_desc' => 'banner_id',
        )));

        return $aData;
    }

    /**
     * Обновляет статистику баннера
     *
     * @param $bannerId
     */
    protected function UpdateBannerStats($bannerId)
    {
        $oUser = $this->User_GetUserCurrent();
        $this->AddBannerStats(array(
            'banner_id'  => $bannerId,
            'user_id'    => $oUser ? $oUser->getId() : '',
            'event'      => 'SHOW',
            'show_type'  => '1',
            'banner_uri' => $this->GetFullUrl(),
        ));
    }

    /**
     * @param $sUrl
     * @param $bAddStats
     * @param $iPosition
     *
     * @return array
     */
    protected function GetBannersByPosition($sUrl, $bAddStats, $iPosition)
    {
        if (in_array('l10n', $this->aActivePlugins)) {
                $sLang = Config::Get('lang.current');
            } else {
                $sLang = null;
            }
            $aBanners = $this->_oMapper->GetBannerByParams($sUrl, $iPosition, $sLang);
            $aList = array();

            if (is_array($aBanners) && count($aBanners)) {
                foreach ($aBanners as $aRow) {
                    $aList[] = new PluginBanneroid_ModuleBanner_EntityBanner($aRow);
                    if ($bAddStats) {
                        $this->UpdateBannerStats($aRow['banner_id']);
                    }
                }
            }
            return $aList;
    }

}