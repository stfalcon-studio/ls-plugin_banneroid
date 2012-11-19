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

    protected $aActivePlugins = array();
    protected $sLang = null;
    protected $sFileUpload = null;

    /**
     * Action initialization
     *
     * @return void
     */
    public function Init() {
        if (!$this->CheckUserRights()) {
            return Router::Action('error');
        }

        $this->aActivePlugins = $this->Plugin_GetActivePlugins();
        $this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.banneroid.banneroid_title'));
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
    protected function EventBannerStatistics() {
        $this->Viewer_Assign('aBannersStats', $this->PluginBanneroid_Banner_GetStatsTotal());
    }

    /**
     * Display statistics by banners
     *
     * @return void
     */
    protected function EventBannerStatsBans() {
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
        Router::Location($oBanner->getBannerUrl());
    }

    /**
     * check Banneroid fields
     * @return boolean
     */
    protected function checkBannerFields() {
        $this->Security_ValidateSendForm();
        $bStateError = false;

        if (!preg_match(Config::Get('plugin.banneroid.banner_date_reg'), getRequest('banner_start_date'))) {
            $this->Message_AddError(
                $this->Lang_Get("plugin.banneroid.banneroid_error_date_start"), $this->Lang_Get('plugin.banneroid.banneroid_error'));
            $bStateError = true;
        }

        if (!preg_match(Config::Get('plugin.banneroid.banner_date_reg'), getRequest('banner_end_date'))) {
            $this->Message_AddError(
                $this->Lang_Get("plugin.banneroid.banneroid_error_date_end"), $this->Lang_Get('plugin.banneroid.banneroid_error'));
            $bStateError = true;
        }


        if (!func_check(getRequest('banner_name'), 'text', 2, 3000)) {
            $this->Message_AddError(
                $this->Lang_Get("plugin.banneroid.banneroid_error_name"), $this->Lang_Get('plugin.banneroid.banneroid_error'));
            $bStateError = true;
        }

        if (!preg_match(Config::Get('plugin.banneroid.banner_url_reg'), getRequest('banner_url')) || getRequest('banner_html')) {
            $this->Message_AddError(
                $this->Lang_Get("plugin.banneroid.banneroid_error_url"), $this->Lang_Get('plugin.banneroid.banneroid_error'));
            $bStateError = true;
        }


        if (in_array('l10n', $this->aActivePlugins)) {
            $this->sLang = getRequest('banner_lang');
            if ($this->sLang === '0') {

                $this->sLang = null;
            } else {
                $aLangs = $this->PluginL10n_L10n_GetAllowedLangs();
                if (!in_array($this->sLang, $aLangs)) {
                    $this->Message_AddError(
                        $this->Lang_Get("plugin.banneroid.banneroid_error_lang"), $this->Lang_Get('plugin.banneroid.banneroid_error'));
                    $bStateError = true;
                }
            }
        } else {
            $this->sLang = null;
        }

           if (isset($_FILES["banner_image"]) && $_FILES["banner_image"]["error"] == 0) {
                $aImageFile = $_FILES["banner_image"];

                $aSize = @getimagesize($aImageFile["tmp_name"]);
                if (!in_array($aSize['mime'], Config::Get('plugin.banneroid.images_mime'))) {
                    $this->Message_AddError(
                            $this->Lang_Get("banneroid_error_image_extension"),
                            $this->Lang_Get('banneroid_error'));
                   $bStateError = true;
                }
            }


        if (!is_array(getRequest('banner_place')) && count(getRequest('banner_place')) == 0) {

                $this->Message_AddError(
                    $this->Lang_Get("plugin.banneroid.banneroid_error_place"), $this->Lang_Get('banneroid_error'));
                $bStateError = true;
        }

        /**
         * Выполнение хуков
         */
        $this->Hook_Run('check_banner_fields', array('bStateError' => &$bStateError));
        return $bStateError;
    }



    /**
     * Show banner add
     *
     * @return void
     */
    protected function EventBannerAdd() {
        $this->Viewer_Assign('add_banner', 1);

        $this->SetTemplateAction('edit');

        /*
         * проверка на активность плагина l10n
         */
        if (in_array('l10n', $this->aActivePlugins)) {
            $aLangs = $this->PluginL10n_L10n_GetAllowedLangsToViewer();
            $this->Viewer_Assign('aLangs', $aLangs);
        }

        //Передача всех страниц на которых баннер может размещаться
        $this->Viewer_Assign('aPlaces', $this->PluginBanneroid_Banner_GetAllPages());


        /*
         * Добавления банера
         */
        if (getRequest('submit_banner')) {

            /**
             * Запускаем проверку корректности ввода полей при добавлении баннера
             */
            if ($this->checkBannerFields()) {
                return;
            }


            $oBanner = new PluginBanneroid_ModuleBanner_EntityBanner();
            // Fill banner entity object
            $oBanner->setBannerId(0);
            $oBanner->setBannerName(getRequest('banner_name'));
            $oBanner->setBannerHtml(getRequest('banneroid_html'));
            $oBanner->setBannerUrl(getRequest('banner_url'));
            $oBanner->setBannerLang($this->sLang);
            $oBanner->setBannerStartDate(getRequest('banner_start_date'));
            $oBanner->setBannerEndDate(getRequest('banner_end_date'));
            $oBanner->setBannerIsActive(getRequest('banner_is_active'));
            $oBanner->setBannerType(getRequest('banner_type'));
            $oBanner->setBannerPlaces(getRequest('banner_place'));
            //Загрузка фото
            $aImageFile = $_FILES["banner_image"];
            $this->sFileUpload = $this->PluginBanneroid_Banner_UploadImage($aImageFile, $oBanner);


            /*
             * Сохранения банера в БД
             */
            if ($this->PluginBanneroid_ModuleBanner_AddBanner($oBanner)){
                $this->Message_AddNotice($this->Lang_Get('plugin.banneroid.banneroid_ok_add'), $this->Lang_Get('attention'), true);
                Router::Location(Config::Get("path.root.web") . '/banneroid/');
            }

        } else {
            $_REQUEST['banner_start_date'] = date('Y-m-d');
            $_REQUEST['banner_end_date'] = '0000-00-00';
            $_REQUEST['banner_is_image'] = true;
            $_REQUEST['banner_type'] = 1;
        }

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
     * Update and show banner
     *
     * @return void
     */
    protected function EventBannerEdit() {
        //Получаэм ИД текущего баннера
        $sBannerId = (int) $this->GetParam('banner_id');
        //устанавливаем шаблон
        $this->SetTemplateAction('edit');
        //Передача всех страниц на которых баннер может размещаться
        $this->Viewer_Assign('aPlaces', $this->PluginBanneroid_Banner_GetAllPages());

        $oBanner = $this->PluginBanneroid_Banner_GetBannerById($sBannerId);
        if ($_REQUEST) {
            $_REQUEST['banner_image'] = Config::Get("plugin.banneroid.images_dir") . $oBanner->getBannerImage();
            $_REQUEST['banner_is_image'] = true;
        }

        /*
         * проверка на активность плагина l10n
         */
        if (in_array('l10n', $this->aActivePlugins)) {
            $aLangs = $this->PluginL10n_L10n_GetAllowedLangsToViewer();
            $this->Viewer_Assign('aLangs', $aLangs);
        }
        /*
         * проверка на существования банера
         */
        if (!$oBanner) {
            return Router::Action('error');
        }

        if (getRequest('submit_banner')) {

            $iBannerId = $oBanner->getId();

            /**
             * Запускаем проверку корректности ввода полей при добавлении баннера
             */
            if ($this->checkBannerFields()) {
                return;
            }

            // Fill banner entity object
            $oBanner->setBannerName(getRequest('banner_name'));
            //перезагрузка фото баннера
            if (isset($_FILES["banner_image"]) && (getRequest('banner_kind') == 'kind_image')) {
                //загрузка фото
                $aImageFile = $_FILES["banner_image"];
                $this->PluginBanneroid_Banner_UploadImage($aImageFile, $oBanner);
            } else {
                $oBanner->setBannerHtml(getRequest('banneroid_html'));
            }
            $oBanner->setBannerUrl(getRequest('banner_url'));
            $oBanner->setBannerLang($this->sLang);
            $oBanner->setBannerStartDate(getRequest('banner_start_date'));
            $oBanner->setBannerEndDate(getRequest('banner_end_date'));
            $oBanner->setBannerIsActive(getRequest('banner_is_active'));
            $oBanner->setBannerType(getRequest('banner_type'));
            $oBanner->setBannerPlaces(getRequest('banner_place'));

            if ($this->PluginBanneroid_ModuleBanner_UpdateBanner($oBanner)) {
                $this->Message_AddNotice($this->Lang_Get('plugin.banneroid.banneroid_ok_edit'), $this->Lang_Get('attention'), true);
                Router::Location(Config::Get("path.root.web") . '/banneroid/');
            }
        }

        $_REQUEST['banner_id'] = $oBanner->getId();
        $_REQUEST['banner_name'] = $oBanner->getBannerName();
        $_REQUEST['banneroid_html'] = $oBanner->getBannerHtml();
        $_REQUEST['banner_url'] = $oBanner->getBannerUrl();
        $_REQUEST['banner_lang'] = $oBanner->getBannerLang();
        $_REQUEST['banner_start_date'] = $oBanner->getBannerStartDate();
        $_REQUEST['banner_end_date'] = $oBanner->getBannerEndDate();
        $_REQUEST['banner_is_active'] = $oBanner->getBannerIsActive();
        $_REQUEST['banner_places'] = $oBanner->PluginBanneroid_Banner_GetActivePages($oBanner);
        $_REQUEST['banner_type'] = $oBanner->getBannerType();
        $_REQUEST['banner_image'] = Config::Get("plugin.banneroid.images_dir") . $oBanner->getBannerImage();
        $_REQUEST['banner_is_image'] = true;
        $this->Viewer_Assign('oBanner', $oBanner);
    }

    /**
     * Delete banner
     *
     * @return void
     */
    protected function EventBannerDelete() {
        $sBannerId = $this->GetParam(0);

        $this->PluginBanneroid_Banner_HideBanner($sBannerId);
        $this->Message_AddNotice($this->Lang_Get('plugin.banneroid.banneroid_ok_delete'), $this->Lang_Get('attention'), true);

        Router::Location(Config::Get("path.root.web") . '/banneroid/');
    }

}
