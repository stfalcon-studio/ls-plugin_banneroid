<?php

$sDirRoot = dirname(realpath((dirname(__DIR__)) . "/../../../"));
set_include_path(get_include_path().PATH_SEPARATOR.$sDirRoot);

require_once($sDirRoot."/tests/AbstractFixtures.php");


class BanneroidFixtures extends AbstractFixtures
{


    public static function getOrder()
    {
        return 0;
    }

    public function load()
    {
        $sDateBefore = date("Y-m-d", time()-86400);
        $sDateAfter = date("Y-m-d", time()+86400);
        //Add banner HTML
        $oBannerStfalcon_1 = $this->_createBanner('(1)H->stfalcon - examples of works:Place->under Article', 'http://stfalcon.com/',
            '<h1>Web development</h1>', null, $sDateBefore, $sDateAfter,'1',array('1','2'),'kind_html');
        $this->addReference('banner-stfalcon-under-article', $oBannerStfalcon_1);

        $oBannerStfalcon_2 = $this->_createBanner('(2)H->stfalcon - examples of works:Place->sidebar', 'http://stfalcon.com/',
            '<h1>Web development Stfalcon</h1>', null,  $sDateBefore, $sDateAfter,'2',array('2'),'kind_html');
        $this->addReference('banner-stfalcon-under-article', $oBannerStfalcon_2);

        $oBannerStfalcon_3 = $this->_createBanner('(3)H->stfalcon - examples of works:Place->header', 'http://stfalcon.com/',
            '<h1>Web development Header Banner</h1>', null,  $sDateBefore, $sDateAfter,'3',array('2'),'kind_html');
        $this->addReference('banner-stfalcon-header', $oBannerStfalcon_3);

        $oBannerStfalcon_4 = $this->_createBanner('(4)H->stfalcon - examples of works:Place->footer', 'http://stfalcon.com/',
            '<h1 align="center">Development Footer Banner for Example Stfalcon</h1>', null,  $sDateBefore, $sDateAfter,'4',array('1'),'kind_html');
        $this->addReference('banner-stfalcon-footer', $oBannerStfalcon_4);

        //Add banner Images

        $oBannerLs = $this->_createBanner('(5)Livestreet CMS system', 'http://livestreet.ru/',
          '','livestreet_logo.jpeg' ,  $sDateBefore, $sDateAfter,'2',array('1','2'),'kind_image');
        $this->addReference('livestreet-cms-system', $oBannerLs);

        $oBannerZF2 = $this->_createBanner('(6)Zend Framework 2', 'http://framework.zend.com/',
           '','ZF2.jpeg' , $sDateBefore, $sDateAfter,'1',array('2'),'kind_image');
        $this->addReference('zend-framework-2', $oBannerZF2);

        $oBannerJquery = $this->_createBanner('(7)jQuery Framework', 'http://jquery.com/',
          '','jquery.jpeg' , $sDateBefore, $sDateAfter,'3',array('1'),'kind_image');
        $this->addReference('jquery-framework', $oBannerJquery);

        $oBannerStfalconBannerImg= $this->_createBanner('(8)Stfalcon Image Banner', 'http://stfalcon.com/',
           '','stfalcon_logo_2.jpg' , $sDateBefore, $sDateAfter,'4',array('2'),'kind_image');
        $this->addReference('banner-stfalcon-image-1', $oBannerStfalconBannerImg);


    }


    private function _createBanner($sNameBanner, $sBannerUrl, $sHtmlBanner,$sImageBanner, $sStartDate, $sEndDate,$iBannerType,$oPlaces, $sBannerKind){

        $sFileUploadBanneroid = Config::Get('plugin.banneroid.upload_dir');
        $sDirImg = dirname(realpath((dirname(__DIR__)) . "/../../../"));
        $sFile = $sDirImg."/plugins/banneroid/tests/behat/fixtures/image/";

        $this->aActivePlugins = $this->oEngine->Plugin_GetActivePlugins();

        $oBanner = Engine::GetEntity('PluginBanneroid_Banner');

        /** @var $oBanner PluginBanneroid_ModuleBanner_EntityBanner */

        $oBanner->setBannerId('0');
        $oBanner->setBannerName($sNameBanner);

        $oBanner->setBannerHtml($sHtmlBanner);

//        if ($sImageBanner != null ){
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

        $this->oEngine->PluginBanneroid_Banner_AddBanner($oBanner);

        return $oBanner;
    }
}