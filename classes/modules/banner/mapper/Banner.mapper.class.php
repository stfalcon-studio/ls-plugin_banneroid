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

class PluginBanneroid_ModuleBanner_MapperBanner extends Mapper {

    /**
     * Select list of banners
     *
     * @return array
     */
    function GetBannersList() {
        $sql = 'SELECT
                        *
                FROM
                        ' . Config::Get('db.table.banneroid.banner') . '
                WHERE
                 bannes_is_show = 1
                ';
        return $this->oDb->select($sql);
    }

    /**
     * Select banner by its Id
     *
     * @return array
     */
    public function GetBannerById($sId) {
        $sql = 'SELECT
			*
	        FROM
			' . Config::Get('db.table.banneroid.banner') . '
		WHERE
			banner_id = ?d';
        return $this->oDb->selectRow($sql, $sId);
    }

    /**
     * Get active banners filtered by type
     *
     * @param string $sUrl
     * @param int $sType
     * @return  array
     */
    public function GetBannerByParams($sUrl, $sType) {
        $sql = 'SELECT
                    banner.*

                FROM
                ' . Config::Get('db.table.banneroid.banner') . ' banner
                    LEFT JOIN
                            ' . Config::Get('db.table.banneroid.places_holders') . ' pholder
                         ON banner.banner_id = pholder.banner_id
                    LEFT JOIN
                            ' . Config::Get('db.table.banneroid.places') . ' places
                         ON pholder.page_id = places.place_id
                    LEFT JOIN
                            ' . Config::Get('db.table.banneroid.stats') . ' stats
                         ON (banner.banner_id = stats.banner_id
                             AND
                            stats.stat_date = CURDATE())
                WHERE
                        ? LIKE places.place_url
                    AND
                        pholder.place_type = ?d
                    AND
                        banner_is_active=1
                    AND
                        bannes_is_show=1
                    AND
                        banner_start_date<=CURDATE()
                    AND
                        (banner_end_date>=CURDATE() OR banner_end_date="0000-00-00")


                GROUP BY
                        banner.banner_id
                ORDER BY
                        stats.view_count
                LIMIT
                        1';

        return $this->oDb->select($sql, $sUrl, $sType);
    }

    /**
     * Update banner in DB
     *
     * @param PluginBanneroid_ModuleBanner_EntityBanner $oBanner
     * @return boolean
     */
    public function UpdateBanner(PluginBanneroid_ModuleBanner_EntityBanner $oBanner) {
        $sql = "UPDATE " . Config::Get('db.table.banneroid.banner') . "
			SET
				banner_name = ?,
				banner_html = ?,
				banner_url = ?,
				banner_image = ?,
				banner_start_date = ?,
				banner_end_date = ?,
				banner_type = ?d,
				banner_is_active = ?,
				banner_edit_date = ?
			WHERE
				banner_id = ?d
		";


        if ($this->oDb->query($sql,
                        $oBanner->getName(),
                        $oBanner->getBannerHtml(),
                        $oBanner->getBannerUrl(),
                        $oBanner->getBannerImage(),
                        $oBanner->getBannerStartDate(),
                        $oBanner->getBannerEndDate(),
                        $oBanner->getBannerType(),
                        $oBanner->getBannerIsActive(),
                        date("Y-m-d H:i:s"),
                        $oBanner->getId())) {

            return true;
        }
        return false;
    }

    /**
     * Adds banner into DB
     *
     * @param PluginBanneroid_ModuleBanner_EntityBanner $oBanner
     * @return boolean
     */
    public function AddBanner(PluginBanneroid_ModuleBanner_EntityBanner $oBanner) {
        $sql = "INSERT INTO " . Config::Get('db.table.banneroid.banner') . "
                            (?#)
		VALUES
                            (?a)
		";
        $aData['banner_name'] = $oBanner->getName();
        $aData['banner_html'] = $oBanner->getBannerHtml();
        $aData['banner_url'] = $oBanner->getBannerUrl();
        $aData['banner_image'] = $oBanner->getBannerImage();
        $aData['banner_start_date'] = $oBanner->getBannerStartDate();
        $aData['banner_end_date'] = $oBanner->getBannerEndDate();
        $aData['banner_is_active'] = $oBanner->getBannerIsActive();
        $aData['banner_type'] = $oBanner->getBannerType();
        $aData['banner_add_date'] = date("Y-m-d H:i:s");
        $aData['banner_edit_date'] = date("Y-m-d H:i:s");

        return $this->oDb->query($sql, array_keys($aData), array_values($aData));
    }

    /**
     * Select all pages from DB
     *
     * @return array
     */
    public function GetAllPages() {
        $sql = 'SELECT
                        *
                FROM
                        ' . Config::Get('db.table.banneroid.places') . '
                ';
        return $this->oDb->select($sql);
    }

    /**
     * Select pages linked with banner
     *
     * @param PluginBanneroid_ModuleBanner_EntityBanner $oBanner
     * @return array
     */
    public function GetBannerPages($oBanner) {
        $sql = 'SELECT
                        *
                FROM
                        ' . Config::Get('db.table.banneroid.places_holders') . '
                WHERE banner_id = ?d ';

        return $this->oDb->select($sql, $oBanner->getId());
    }

    /**
     * Select pages names linked with banner
     *
     * @param PluginBanneroid_ModuleBanner_EntityBanner $oBanner
     * @return array
     */
    public function GetBannerPagesNames($oBanner) {
        $sql = 'SELECT
                        PLS.place_name,PLSH.place_type
                FROM
                        ' . Config::Get('db.table.banneroid.places_holders') . ' PLSH
                LEFT JOIN
                        ' . Config::Get('db.table.banneroid.places') . ' PLS ON (PLS.place_id=PLSH.page_id)
                WHERE banner_id = ?d ';

        return $this->oDb->select($sql, $oBanner->getId());
    }

    /**
     * Delete banner page from DB
     *
     * @param int $sPageId
     * @param int $sPageType
     * @param PluginBanneroid_ModuleBanner_EntityBanner $oBanner
     * @return <type>
     */
    public function DeleteBannerPage($sPageId, $sPageType, $oBanner) {
        $sql = 'DELETE FROM
                        ' . Config::Get('db.table.banneroid.places_holders') . '
                WHERE banner_id = ?d AND page_id = ?d AND place_type = ?d';

        return $this->oDb->query($sql, $oBanner->getId(), $sPageId, $sPageType);
    }

    /**
     * Delete banner from DB
     *
     * @param PluginBanneroid_ModuleBanner_EntityBanner $oBanner
     * @return void
     */
    public function DeleteBanner($oBanner) {

        $sql = 'DELETE FROM
                        ' . Config::Get('db.table.banneroid.banner') . '
                WHERE banner_id = ?d ';

        $this->oDb->query($sql, $oBanner->getId());
        // delete banner pages
        $sql = 'DELETE FROM
                        ' . Config::Get('db.table.banneroid.places_holders') . '
                WHERE banner_id = ?d ';

        $this->oDb->query($sql, $oBanner->getId());
    }

    /**
     * Add link to page on banner
     *
     * @param int $sPageId
     * @param int $sPageType
     * @param PluginBanneroid_ModuleBanner_EntityBanner $oBanner
     * @return boolean
     */
    public function AddBannerPage($sPageId, $sPageType, $oBanner) {
        $aData = array(
            'banner_id' => $oBanner->getId(),
            'page_id' => $sPageId,
            'place_type' => $sPageType);

        $sql = 'INSERT INTO
                        ' . Config::Get('db.table.banneroid.places_holders') . '
                        (?#)
                VALUES
                        (?a)';

        return $this->oDb->query($sql, array_keys($aData), array_values($aData));
    }

    /**
     * Add banner stats
     *
     * @param array $aParams
     * @return void
     */
    public function AddBannerStats($aParams)
    {
        $sql = 'INSERT INTO
                        ' . Config::Get('db.table.banneroid.stats') . '
                SET
                    banner_id = ?d,';
        if ($aParams['event'] == 'SHOW') {
            $sql .= 'view_count = DEFAULT(view_count) + 1,';
        } else {
            $sql .= 'click_count = DEFAULT(click_count) + 1,';
        }
        $sql .= 'stat_date = DATE(NOW())';

        $this->oDb->query($sql, $aParams['banner_id']);
    }

    public function UpdateBannerStats($aParams)
    {
        $sql = 'UPDATE
                    ' . Config::Get('db.table.banneroid.stats');
        if ($aParams['event'] == 'SHOW') {
            $sql .= ' SET view_count = view_count + 1';
        } else {
            $sql .= ' SET click_count = click_count + 1';
        }
        $sql .= ' WHERE
                        banner_id = ?d
                  AND
                        stat_date = DATE(NOW())';

        $this->oDb->query($sql, $aParams['banner_id']);
    }

    public function GetStatIdBannerCurrentDay($bannerId)
    {
        $sql = 'SELECT
                    bs.stats_id
                FROM
                        ' . Config::Get('db.table.banneroid.stats') . ' bs
                WHERE
                    bs.banner_id = ?d
                AND
                    bs.stat_date = DATE(NOW())';
        if ($aRow = $this->oDb->selectRow($sql, $bannerId)) {
            return $aRow['stats_id'];
        }

        return false;
    }

    /**
     * Get banner stats items
     *
     * @param array $aParams
     * @return integer
     */

    public function GetBannerStatsbyParams($aParams=array()) {


        $sql = 'SELECT
                    SUM(bs.view_count) as view_count,
                    SUM(bs.click_count) as click_count
                FROM
                        ' . Config::Get('db.table.banneroid.stats') . ' bs
                LEFT JOIN
                        ' . Config::Get('db.table.banneroid.banner') . ' AS banner ON banner.banner_id=bs.banner_id
                WHERE   1
                        { AND banner.banner_type = ?}
                        { AND bs.stat_date >= ? }
                        { AND bs.stat_date <= ? }

                ';
        return $this->oDb->selectRow($sql,
                (empty($aParams['banner_type']) ? DBSIMPLE_SKIP : $aParams['banner_type']),
                (empty($aParams['stats_date_start']) ? DBSIMPLE_SKIP : $aParams['stats_date_start']),
                (empty($aParams['stats_date_end']) ? DBSIMPLE_SKIP : $aParams['stats_date_end'])
        );
    }


    /**
     * Get banner stats list
     *
     * @param array $aParams
     * @return array
     */

    public function GetBannerStatsListbyParams($aParams=array()) {
        $sql = 'SET @clik:=0,@pred_uri=\'\' ';
        $this->oDb->query($sql);

        $sql = 'SELECT bs.banner_id,
                       SUM(bs.view_count) AS view_count,
                       SUM(bs.click_count) AS click_count,
                       banner.banner_name
                FROM
                        ' . Config::Get('db.table.banneroid.stats') . ' bs
                LEFT JOIN
                        ' . Config::Get('db.table.banneroid.banner') . ' AS banner ON banner.banner_id=bs.banner_id
                WHERE   1
                        { AND bs.banner_id = ? }
                        { AND bs.stat_date >= ? }
                        { AND bs.stat_date <= ? }
                        { GROUP BY ?# }
                        { ORDER BY ?# DESC}
                        { ORDER BY ?# ASC}

                ';
        return $this->oDb->select($sql,
                (empty($aParams['banner_id']) ? DBSIMPLE_SKIP : $aParams['banner_id']),
                (empty($aParams['stats_date_start']) ? DBSIMPLE_SKIP : $aParams['stats_date_start']),
                (empty($aParams['stats_date_end']) ? DBSIMPLE_SKIP : $aParams['stats_date_end']),
                (empty($aParams['stats_group_by']) ? DBSIMPLE_SKIP : $aParams['stats_group_by']),
                (empty($aParams['stats_order_by_desc']) ? DBSIMPLE_SKIP : $aParams['stats_order_by_desc']),
                (empty($aParams['stats_order_by_asc']) ? DBSIMPLE_SKIP : $aParams['stats_order_by_asc'])
        );
    }

    public function HideBanner($sBannerId)
    {
         $sql = "UPDATE " . Config::Get('db.table.banneroid.banner') . "
			SET
				bannes_is_show = 0
            WHERE
				banner_id = ?d
		";


        if ($this->oDb->query($sql, $sBannerId)) {
            return true;
        }
        return false;
    }

}