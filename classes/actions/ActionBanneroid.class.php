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

/**
 * Banneroid Plugin Action class for LiveStreet
 *
 * Show form for banners settings
 */
class PluginBanneroid_ActionBanneroid extends ActionPlugin {

    protected $oUserCurrent = null;

    /**
     * Action initialization
     *
     * @return void
     */
    public function Init() {
        $this->oUserCurrent = $this->User_GetUserCurrent();
        if (!$this->CheckUserRights()) {
            return Router::Action('error');
        }
        $this->Viewer_AddHtmlTitle($this->Lang_Get('banneroid_title'));
        $this->SetDefaultEvent('main');
    }

    /**
     * Check user rights
     *
     * @return booblean
     */
    protected function CheckUserRights() {
        $oUser = $this->User_GetUserCurrent(); //Current user
        // если необходимо - можно будет вынести в конфиг список Events, доступ к которым необходим всем
        if (Router::GetActionEvent() == 'redirect') {
            return true;
        }
        if (!$oUser || !$oUser->isAdministrator()) { //Test user rigts
            return false;
        }
        return true;
    }

    /**
     * Registration events
     *
     * @return void
     */
    protected function RegisterEvent() {
        $this->AddEventPreg('/^ajaxaddplace$/i', 'AjaxAddPlace');
        $this->AddEventPreg('/^ajaxdellplace$/i', 'AjaxDellPlace');
        $this->AddEvent('restore', 'EventBannersRestore');
        $this->AddEvent('ajaxclick', 'AjaxClick');

        $this->AddEvent('main', 'EventBannersList');
        $this->AddEvent('redirect', 'EventBannerRedirect');
        $this->AddEvent('stats', 'EventBannerStatistics');
        $this->AddEvent('stats-banners', 'EventBannerStatsBans');
        $this->AddEvent('add', 'EventBannerAdd');
        $this->AddEvent('edit', 'EventBannerEdit');
        $this->AddEvent('delete', 'EventBannerDelete');
    }

    protected function AjaxClick() {
        $this->Viewer_SetResponseAjax();
        $sId=getRequest('sId');;
        $this->PluginBanneroid_Banner_AddBannerStats(array
            ('banner_id' => $sId,
            'event' => 'CLICK',
        ));
    }

    protected function EventBannersRestore() {
        $sBannerId = (int) $this->GetParam(0); // Id of current banner

        $oBanner = $this->PluginBanneroid_Banner_GetBannerById($sBannerId);

        if (!$oBanner) {
            return Router::Action('error');
        }
        $this->PluginBanneroid_Banner_RestoreBanner($oBanner);
        Router::Location(Router::getPath("banneroid") . 'edit/' . $oBanner->getId() . '/');
    }

    protected function AjaxDellPlace() {

        $this->Viewer_SetResponseAjax();

        if (!$this->oUserCurrent or !$this->oUserCurrent->isAdministrator()) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
            return Router::Action('error');
        }

        if ($this->PluginBanneroid_Banner_DellPagePlace(getRequest('sId'))) {
            $this->Message_AddNoticeSingle($this->Lang_Get('banneroid_page_ok_dell'), $this->Lang_Get('attention'));
            return;
        }
        $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
        return;
    }

    protected function AjaxAddPlace() {

        $this->Viewer_SetResponseAjax();

        if (!$this->oUserCurrent or !$this->oUserCurrent->isAdministrator()) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
            return Router::Action('error');
        }

        $aPagePlace = array(
            'place_name' => getRequest('place_name'),
            'place_title' => getRequest('place_title'),
            'place_url' => getRequest('place_url')
        );

        $oPagePlace = Engine::GetEntity('PluginBanneroid_Banner', $aPagePlace);

        if ($sId = $this->PluginBanneroid_Banner_AddPagePlace($aPagePlace)) {
            $sHtml = '<td>' . $aPagePlace['place_title'] . '</td><td>' . $aPagePlace['place_url'] . '</td>
        <td><input name="banner_place[]" type="checkbox" value="' . $sId . '" /><a href="#" onclick="DellPlace(' . $sId . '); return false;" style="color: red;">&#215;</a></td>';
            $this->Viewer_AssignAjax('sHtml', $sHtml);
            $this->Viewer_AssignAjax('sId', $sId);
            $this->Message_AddNoticeSingle($this->Lang_Get('banneroid_page_ok_creat'), $this->Lang_Get('attention'));
            return;
        }
        $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
        return;
    }

    /**
     * Display statistics total
     *
     * @return void
     */
    protected function EventBannerStatistics() {
        $this->Viewer_Assign('aBannersStats', $this->PluginBanneroid_Banner_GetStatsTotal());
    }

    /**
     * Display statistics by banners
     *
     * @return void
     */
    protected function EventBannerStatsBans() 
    {
        if ($sBannerId = (int) $this->GetParam(0)) {
            $oBanner = $this->PluginBanneroid_Banner_GetBannerById($sBannerId);
            if (!$oBanner) {
                return Router::Action('error');
            }
            $this->Viewer_Assign('oBanner', $oBanner);
            $this->Viewer_Assign('aBannersStats', $this->PluginBanneroid_Banner_GetStatsBanners($sBannerId));
        } else {
            $this->Viewer_Assign('aBannersStats', $this->PluginBanneroid_Banner_GetStatsBanners());
        }
    }

    /**
     * Redirect link from banner to banner url
     *
     * @return void
     */
    protected function EventBannerRedirect() {
        $sBannerId = $this->GetParam(0); // Id of current banner
        $oBanner = $this->PluginBanneroid_Banner_GetBannerById($sBannerId);
        if (!$oBanner) {
            return Router::Action('error');
        }
        //@todo вынести в конфиг id?
        if ($oUser = $this->User_GetUserCurrent()) {
            $userId = $oUser->getId();
        } else {
            $userId = 1;
        }

        // add sttatistic of clicks

        $this->PluginBanneroid_Banner_AddBannerStats(array
            ('banner_id' => $oBanner->getBannerId(),
            'event' => 'CLICK',
        ));
        func_header_location($oBanner->getBannerUrl());
    }

    /**
     * Show banner add
     *
     * @return void
     */
    protected function EventBannerAdd() {
        $oBanner = new PluginBanneroid_ModuleBanner_EntityBanner();
        $oBanner->setBannerStartDate();
        $oBanner->setBannerId(0);
        $this->Viewer_Assign('add_banner', 1);

        if (getRequest('submit_banner')) {
            if ($this->PluginBanneroid_Banner_Save($oBanner)) {
                func_header_location('../edit/' . $oBanner->getId());
            }
        }

        $this->Viewer_Assign('oBanner', $oBanner);
        $_REQUEST['banner_places'] = $this->PluginBanneroid_Banner_GetAllPages();
        $_REQUEST['banner_start_date'] = date('Y-m-d');
        $_REQUEST['banner_end_date'] = '0000-00-00';
        $_REQUEST['banner_is_image'] = true;
        $_REQUEST['banner_type'] = 1;
        $this->SetTemplateAction('edit');
    }

    /**
     * Show banners list
     *
     * @return void
     */
    protected function EventBannersList() {
        $this->Viewer_Assign('aBannersList', $this->PluginBanneroid_Banner_GetBannersList());
    }

    /**
     * Add/Update and show banner
     *
     * @return void
     */
    protected function EventBannerEdit() {
        $sBannerId = (int) $this->GetParam(0); // Id of current banner

        $oBanner = $this->PluginBanneroid_Banner_GetBannerById($sBannerId);

        if (!$oBanner) {
            return Router::Action('error');
        }

        if (getRequest('submit_banner')) {
            $this->PluginBanneroid_Banner_Save($oBanner);
            func_header_location(Config::Get("path.root.web") . '/banneroid/');
        }


        // Setting banner page vars

        $this->Viewer_Assign('oBanner', $oBanner);
        $this->Viewer_Assign('aPages', $this->PluginBanneroid_Banner_GetActivePages($oBanner));

        $_REQUEST['banner_num'] = $oBanner->getBannerNum();
        $_REQUEST['banner_name'] = $oBanner->getBannerName();
        $_REQUEST['banner_html'] = $oBanner->getBannerHtml();
        $_REQUEST['banner_url'] = $oBanner->getBannerUrl();
        $_REQUEST['banner_start_date'] = $oBanner->getBannerStartDate();
        $_REQUEST['banner_end_date'] = $oBanner->getBannerEndDate();
        $_REQUEST['banner_is_active'] = $oBanner->getBannerIsActive();
        $_REQUEST['banner_places'] = $this->PluginBanneroid_Banner_GetAllPages();
        $_REQUEST['banner_type'] = $oBanner->getBannerType();

        if (strlen(@$oBanner->getBannerImage())) {
            $_REQUEST['banner_image'] = Config::Get("plugin.banneroid.images_dir") .
                    $oBanner->getBannerImage();
            $_REQUEST['banner_is_image'] = true;
        }
    }

    /**
     * Delete banner
     *
     * @return void
     */
    protected function EventBannerDelete() {
        $sBannerId = $this->GetParam(0);

        $this->PluginBanneroid_Banner_HideBanner($sBannerId);

        func_header_location(Config::Get("path.root.web") . '/banneroid/');
    }

}
