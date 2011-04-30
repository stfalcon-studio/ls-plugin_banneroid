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
class PluginBanneroid_ActionBanneroid extends ActionPlugin
{

    /**
     * Action initialization
     *
     * @return void
     */
    public function Init()
    {
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
    protected function CheckUserRights()
    {
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
    protected function RegisterEvent()
    {
        $this->AddEvent('main', 'EventBannersList');
        $this->AddEvent('redirect', 'EventBannerRedirect');
        $this->AddEvent('stats', 'EventBannerStatistics');
        $this->AddEvent('stats-banners', 'EventBannerStatsBans');
        $this->AddEvent('add', 'EventBannerAdd');
        $this->AddEvent('edit', 'EventBannerEdit');
        $this->AddEvent('delete', 'EventBannerDelete');
    }

    /**
     * Display statistics total
     *
     * @return void
     */
    protected function EventBannerStatistics()
    {
        $this->Viewer_Assign('aBannersStats', $this->PluginBanneroid_Banner_GetStatsTotal());
    }

    /**
     * Display statistics by banners
     *
     * @return void
     */
    protected function EventBannerStatsBans()
    {
        if ($sBannerId = (int)$this->GetParam(0)) {
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
    protected function EventBannerRedirect()
    {
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
    protected function EventBannerAdd()
    {
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
        $_REQUEST['banner_type'] = 1;
        $this->SetTemplateAction('edit');
    }

    /**
     * Show banners list
     *
     * @return void
     */
    protected function EventBannersList()
    {
        $this->Viewer_Assign('aBannersList', $this->PluginBanneroid_Banner_GetBannersList());
    }

    /**
     * Add/Update and show banner
     *
     * @return void
     */
    protected function EventBannerEdit()
    {
        $sBannerId = (int)$this->GetParam(0); // Id of current banner

        $oBanner = $this->PluginBanneroid_Banner_GetBannerById($sBannerId);

        if (!$oBanner) {
            return Router::Action('error');
        }

        if (getRequest('submit_banner')) {
            $this->PluginBanneroid_Banner_Save($oBanner);
            func_header_location(Config::Get("path.root.web").'/banneroid/');
        }


        // Setting banner page vars

        $this->Viewer_Assign('oBanner', $oBanner);
        $this->Viewer_Assign('aPages', $this->PluginBanneroid_Banner_GetActivePages($oBanner));

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
    protected function EventBannerDelete()
    {
        $sBannerId = $this->GetParam(0);

        $this->PluginBanneroid_Banner_HideBanner($sBannerId);

        func_header_location(Config::Get("path.root.web").'/banneroid/');
    }

}
