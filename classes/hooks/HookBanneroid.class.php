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
class PluginBanneroid_HookBanneroid extends Hook {

    /**
     * Register Hooks
     *
     * @return void
     */
    public function RegisterHook() {
        $this->AddHook('template_main_menu', 'InitAction', __CLASS__);
        $this->AddHook('engine_init_complete', 'AddBannerBlock', __CLASS__, 0);
        $this->AddHook(Config::Get('plugin.banneroid.banner_content_hook'), 'AddBannersInContent', __CLASS__, 0);
        $this->AddHook('template_body_begin', 'AddBannersInHeader', __CLASS__, 0);
        $this->AddHook('template_body_end', 'AddBannersInFooter', __CLASS__, 0);
        $this->AddHook('template_html_head_end', 'InjJs', __CLASS__);
        $this->AddHook('template_topic_split', 'AddBannerInTopic', __CLASS__);
    }

    public function AddBannerInTopic($aVars) {
        if (in_array(Router::GetAction(), Config::Get('plugin.banneroid.banner_skip_actions'))){
            return false;
        }

        $aBanners = $this->PluginBanneroid_Banner_GetContentBanners($_SERVER['REQUEST_URI'], 1, true);

        if (count($aBanners)) { //Inser banner block
            if (!empty($aBanners[$aVars['cnt']])){
                //if ($bAddStats and $sType!=1) {

                    $oUser = $this->User_GetUserCurrent();
                    $this->PluginBanneroid_Banner_AddBannerStats(array
                        ('banner_id' => $aBanners[$aVars['cnt']]->getId(),
                        'user_id' => $oUser ? $oUser->getId() : '',
                        'event' => 'SHOW',
                        'show_type' => '1',
                        'banner_uri' => $this->PluginBanneroid_Banner_GetFullUrl(),
                    ));
                //}

              $this->Viewer_Assign("aBanners", array('0'=>$aBanners[$aVars['cnt']]));
              $this->Viewer_Assign('sBannersPath', Config::Get("plugin.banneroid.images_dir"));
              return $this->Viewer_Fetch(
                      Plugin::GetTemplatePath(__CLASS__) . 'content.banneroid.tpl');
            }
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
      $aCBt=array();
      $aCB=array();
        if (in_array(Router::GetAction(), Config::Get('plugin.banneroid.banner_skip_actions'))){
            return false;
        }

        if (Router::GetAction() == 'index' or Router::GetAction()=='new' or Router::GetAction()=='blog'){
          $aCB=Config::Get('block.rule_index_blog.blocks.right');
        }


        $aBanners = $this->PluginBanneroid_Banner_GetSideBarBanners($_SERVER['REQUEST_URI']);

        if (count($aBanners)) { //Inser banner block
          if (!empty($aCB)){
            $c=0;
            foreach ($aCB as $key => $val){
              if (!empty($val['priority'])){
                $aCBt[]=$val['priority'];
              } else {
                $aCBt[]=$c;
                $c++;
              }
            }
          }
        arsort($aCBt);
          $sPriority=0;
          foreach ($aBanners as $oBanners){

            if (!empty($aCBt[$oBanners->getBannerNum()])) $sPriority = $aCBt[$oBanners->getBannerNum()] + 1; else $sPriority=$sPriority-2;

            $this->Viewer_AddBlock('right', 'banneroid',
                    array('plugin' => 'banneroid', 'aBanners' => array('0'=>$oBanners)),
                    $sPriority);
          }
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
        if (in_array(Router::GetAction(), Config::Get('plugin.banneroid.banner_skip_actions'))){
            return false;
        }

        $aBanners = $this->PluginBanneroid_Banner_GetContentBanners($_SERVER['REQUEST_URI'], 1, true);
        if (count($aBanners)) { //Inser banner block
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
        if (in_array(Router::GetAction(), Config::Get('plugin.banneroid.banner_skip_actions'))){
            return false;
        }

        $aBanners = $this->PluginBanneroid_Banner_GetContentBanners($_SERVER['REQUEST_URI'], 3, true);

        if (count($aBanners)) { //Inser banner block
            $this->Viewer_Assign("aBanners", $aBanners);
            $this->Viewer_Assign('sBannersPath', Config::Get("plugin.banneroid.images_dir"));
            return $this->Viewer_Fetch(
                    Plugin::GetTemplatePath(__CLASS__) . 'content.banneroid.tpl');
        }
    }
    /**
     * Hook Handler
     * Add banners to body footer
     *
     * @return mixed
     */
    public function AddBannersInFooter($aVars) {
        if (in_array(Router::GetAction(), Config::Get('plugin.banneroid.banner_skip_actions'))){
            return false;
        }

        $aBanners = $this->PluginBanneroid_Banner_GetContentBanners($_SERVER['REQUEST_URI'], 4, true);

        if (count($aBanners)) { //Inser banner block
            $this->Viewer_Assign("aBanners", $aBanners);
            $this->Viewer_Assign('sBannersPath', Config::Get("plugin.banneroid.images_dir"));
            return $this->Viewer_Fetch(
                    Plugin::GetTemplatePath(__CLASS__) . 'content.banneroid.tpl');
        }
    }
    
    public function InjJs(){
        return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'header.js.tpl');
    }
    
}
