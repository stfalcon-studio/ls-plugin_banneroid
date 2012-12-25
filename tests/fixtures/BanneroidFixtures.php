<?php

$sDirRoot = dirname(realpath((dirname(__DIR__)) . "/../../"));
set_include_path(get_include_path().PATH_SEPARATOR.$sDirRoot);

require_once($sDirRoot . "/tests/AbstractFixtures.php");

class BanneroidFixtures extends AbstractFixtures
{
    public static function getOrder()
    {
        return 0;
    }

    public function load()
    {
        $result = $this->oEngine->Database_ExportSQL(dirname(__FILE__) . '/page.sql');

        $sDateBefore = date("Y-m-d", time()-86400);
        $sDateAfter = date("Y-m-d", time()+86400);
        //Add banner HTML
        $oBanner = $this->_createBanner('Banner #1', 'http://stfalcon.com/',
            '<h1>Web development</h1>', null, $sDateBefore, $sDateAfter,'1',array('1','2'),'kind_html');
        $this->addReference('banner1', $oBanner);


        $oBanner = $this->_createBanner('Banner #2', 'http://stfalcon.com/',
            '<h1>Web development Stfalcon</h1>', null,  $sDateBefore, $sDateAfter,'2',array('2'),'kind_html');
        $this->addReference('banner2', $oBanner);

        $oBanner = $this->_createBanner('Banner #3', 'http://stfalcon.com/',
            '<h1>Web development Header Banner</h1>', null,  $sDateBefore, $sDateAfter,'3',array('2'),'kind_html');
        $this->addReference('banner3', $oBanner);

        $oBanner = $this->_createBanner('Banner #4', 'http://stfalcon.com/',
            '<h1 align="center">Development Footer Banner for Example Stfalcon</h1>', null,  $sDateBefore, $sDateAfter,'4',array('1'),'kind_html');
        $this->addReference('banner4', $oBanner);

        //Add banner Images
        $oBanner = $this->_createBanner('Banner #5', 'http://livestreet.ru/',
          '','livestreet_logo.jpeg' ,  $sDateBefore, $sDateAfter,'2',array('1','2'),'kind_image');
        $this->addReference('banner5', $oBanner);
        $this->oEngine->PluginBanneroid_Banner_AddBannerStats(
            array(
                'banner_id'  => $oBanner->getId(),
                'user_id'    => '',
                'event'      => 'SHOW',
                'show_type'  => '1',
                'banner_uri' => '/',
            )
        );

        $oBanner = $this->_createBanner('Banner #6', 'http://framework.zend.com/',
           '','ZF2.jpeg' , $sDateBefore, $sDateAfter,'1',array('2'),'kind_image');
        $this->addReference('banner6', $oBanner);
        $this->oEngine->PluginBanneroid_Banner_AddBannerStats(
            array(
                'banner_id'  => $oBanner->getId(),
                'user_id'    => '',
                'event'      => 'SHOW',
                'show_type'  => '1',
                'banner_uri' => '/',
            )
        );

        $oBanner = $this->_createBanner('Banner #7', 'http://jquery.com/',
          '','jquery.jpeg' , $sDateBefore, $sDateAfter,'3',array('1'),'kind_image');
        $this->addReference('banner7', $oBanner);
        $this->oEngine->PluginBanneroid_Banner_AddBannerStats(
            array(
                'banner_id'  => $oBanner->getId(),
                'user_id'    => '',
                'event'      => 'SHOW',
                'show_type'  => '1',
                'banner_uri' => '/',
            )
        );

        $oBanner= $this->_createBanner('Banner #8', 'http://stfalcon.com/',
           '','stfalcon_logo_2.jpg' , $sDateBefore, $sDateAfter,'4',array('2'),'kind_image');
        $this->addReference('banner8', $oBanner );

        $this->oEngine->PluginBanneroid_Banner_AddBannerStats(
            array(
                'banner_id'  => $oBanner->getId(),
                'user_id'    => '',
                'event'      => 'SHOW',
                'show_type'  => '1',
                'banner_uri' => '/',
            )
        );
    }


    private function _createBanner($sNameBanner, $sBannerUrl, $sHtmlBanner,$sImageBanner, $sStartDate, $sEndDate,$iBannerType,$oPlaces, $sBannerKind)
    {

        $sFileUploadBanneroid = Config::Get('plugin.banneroid.upload_dir');
        $sDirImg = dirname(realpath((dirname(__DIR__)) . "/../../"));
        $sFile = $sDirImg."/plugins/banneroid/tests/fixtures/image/";

        $this->aActivePlugins = $this->oEngine->Plugin_GetActivePlugins();

        $oBanner = Engine::GetEntity('PluginBanneroid_Banner');

        /** @var $oBanner PluginBanneroid_ModuleBanner_EntityBanner */

        $oBanner->setBannerId('0');
        $oBanner->setBannerName($sNameBanner);

        $oBanner->setBannerHtml($sHtmlBanner);

        if ($sImageBanner != null && $sBannerKind == "kind_image"){
            $sOldFileImg = $sFile.$sImageBanner;
            $sNewFileImg = $sFileUploadBanneroid.$sImageBanner;

            if (copy($sOldFileImg, $sNewFileImg)) {
                $oBanner->setBannerImage($sImageBanner);
            } else {
                throw new Exception("File Images \" $sImageBanner \" not copy");
            }
        }
        $oBanner->setBannerUrl($sBannerUrl);
        if (in_array('l10n', $this->aActivePlugins)) {
            $oBanner->setBannerLang(Config::Get('lang.current'));
        }else{
            $oBanner->setBannerLang(Config::Get('lang.default'));
        }
        $oBanner->setBannerStartDate($sStartDate);
        $oBanner->setBannerEndDate($sEndDate);

        $oBanner->setBannerType($iBannerType);
        $oBanner->setBannerPlaces($oPlaces);
        $oBanner->setBannerShow(true);
        $oBanner->setBannerIsActive(true);
        $oBanner->setDateAdd();

        $bannerId = $this->oEngine->PluginBanneroid_Banner_UpdateBanner($oBanner);
        $oBanner->setBannerId($bannerId);

        $pagesList = array($iBannerType => $oPlaces);

        $this->oEngine->PluginBanneroid_Banner_UpdateBannerPages($pagesList, $oBanner);

        return $oBanner;
    }
}