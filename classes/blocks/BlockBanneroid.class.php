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
 * Add banners block in to side bar
 * Add a link to menu
 *
 * @return mixed
 */
class PluginBanneroid_BlockBanneroid extends Block
{

    public function Exec()
    {
        if ($oBanner = $this->GetParam('oBanner')) {
            $this->PluginBanneroid_ModuleBanner_AddBannerStats(array(
                'banner_id' => $oBanner->getId(),
                'event' => 'SHOW',
            ));

            $this->Viewer_Assign("oBanner", $oBanner);
        }
        $this->Viewer_Assign('sBannersPath', Config::Get("plugin.banneroid.images_dir"));
    }

}