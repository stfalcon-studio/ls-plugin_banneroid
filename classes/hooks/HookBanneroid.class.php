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
 * Banneroid Plugin Hook class for LiveStreet
 *
 * Sets Hook for menu template and adds link into it
 */
class PluginBanneroid_HookBanneroid extends Hook
{

    /**
     * Register Hooks
     *
     * @return void
     */
    public function RegisterHook() {
        $this->AddHook('template_main_menu_item', 'InitAction', __CLASS__);
        if (isset($_SERVER['REQUEST_URI'])) {
            $this->AddHook('init_action', 'AddBannerBlock', __CLASS__, -100);
            $this->AddHook(Config::Get('plugin.banneroid.banner_content_hook'), 'AddBannersInContent', __CLASS__, 0);
            $this->AddHook('template_body_begin', 'AddBannersInHeader', __CLASS__, 0);
            $this->AddHook('template_body_end', 'AddBannersInFooter', __CLASS__, 0);
        }
    }

    /**
     * Hook Handler
     * Add a link to menu
     *
     * @return mixed
     */
    public function InitAction($aVars) {
        $oUser = $this->User_GetUserCurrent();


        // If user is admin than show link
        if ($oUser && $oUser->isAdministrator()) {
            return $this->Viewer_Fetch(
                            Plugin::GetTemplatePath(__CLASS__) . 'menu.banneroid.tpl');
        }
    }

    /**
     * Hook Handler
     * Add banners block to side bar
     *
     * @return mixed
     */
    public function AddBannerBlock($aVars) {

        if (in_array(Router::GetAction(), Config::Get('plugin.banneroid.banner_skip_actions'))) {
            return false;
        }
        $aBanners = $this->PluginBanneroid_Banner_GetSideBarBanners($_SERVER['REQUEST_URI'], true);
        if (count($aBanners)) { //Insert banner block
            $this->Viewer_AddBlock('right', 'banneroid', array(
                'plugin'   => 'banneroid',
                'aBanners' => $aBanners
            ), Config::Get('plugin.banneroid.banner_block_order'));
        }
        return true;
    }

    /**
     * Hook Handler
     * Add banners to content footer
     *
     * @return mixed
     */
    public function AddBannersInContent($aVars) {
        if (in_array(Router::GetAction(), Config::Get('plugin.banneroid.banner_skip_actions'))) {
            return false;
        }
        
        $aBanners = $this->PluginBanneroid_Banner_GetContentBanners($_SERVER['REQUEST_URI'], true);
        if (count($aBanners)) { //Insert banner block
            $this->Viewer_Assign("aBanners", $aBanners);
            $this->Viewer_Assign('sBannersPath', Config::Get("plugin.banneroid.images_dir"));
            return $this->Viewer_Fetch(
                            Plugin::GetTemplatePath(__CLASS__) . 'content.banneroid.tpl');
        }
    }

    /**
     * Hook Handler
     * Add banners to body header
     *
     * @return mixed
     */
    public function AddBannersInHeader($aVars) {
        if (in_array(Router::GetAction(), Config::Get('plugin.banneroid.banner_skip_actions'))) {
            return false;
        }
        
        $aBanners = $this->PluginBanneroid_Banner_GetHeaderBanners($_SERVER['REQUEST_URI'], true);
        if (count($aBanners)) { //Insert banner block
            $this->Viewer_Assign("aBanners", $aBanners);
            $this->Viewer_Assign('sBannersPath', Config::Get("plugin.banneroid.images_dir"));
            return $this->Viewer_Fetch(
                            Plugin::GetTemplatePath(__CLASS__) . 'header.banneroid.tpl');
        }
    }

    /**
     * Hook Handler
     * Add banners to body footer
     *
     * @return mixed
     */
    public function AddBannersInFooter($aVars) {
        if (in_array(Router::GetAction(), Config::Get('plugin.banneroid.banner_skip_actions'))) {
            return false;
        }
        
        $aBanners = $this->PluginBanneroid_Banner_GetFooterBanners($_SERVER['REQUEST_URI'], true);
        if (count($aBanners)) { //Insert banner block
            $this->Viewer_Assign("aBanners", $aBanners);
            $this->Viewer_Assign('sBannersPath', Config::Get("plugin.banneroid.images_dir"));
            return $this->Viewer_Fetch(
                            Plugin::GetTemplatePath(__CLASS__) . 'footer.banneroid.tpl');
        }
    }

}