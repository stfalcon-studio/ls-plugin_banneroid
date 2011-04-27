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

// Set action event on page mailing
Config::Set('router.page.banneroid', 'PluginBanneroid_ActionBanneroid');
Config::Set('db.table.banneroid.banner', '___db.table.prefix___banner');
Config::Set('db.table.banneroid.places', '___db.table.prefix___banner_pages');
Config::Set('db.table.banneroid.places_holders', '___db.table.prefix___banner_place_holders');
Config::Set('db.table.banneroid.stats', '___db.table.prefix___banner_stats');

Config::Set('plugin.banneroid.upload_dir', Config::Get('path.root.server') . Config::Get('path.uploads.root') . '/banneroid/');
Config::Set('plugin.banneroid.images_dir', Config::Get('path.root.web') . Config::Get('path.uploads.root') . '/banneroid/');
Config::Set('plugin.banneroid.max_year', 5);
Config::Set('plugin.banneroid.images_mime', array('image/jpeg', 'image/gif', 'image/png'));
//Config::Set('plugin.banneroid.hook_tyoes', array('', 'topic_show_end', '__SIDE_BAR__'));
Config::Set('plugin.banneroid.banner_block_order', 900);
Config::Set('plugin.banneroid.banner_content_hook', 'template_topic_show_end');
Config::Set('plugin.banneroid.banner_skip_actions', array('error','banneroid'));

Config::Set('plugin.banneroid.banner_url_reg',
                '#((https?|ftp|gopher|telnet|file|notes|ms-help):((//)|(\\\\))+[\w\d:\#@%/;$()~_?\+-=\\\.&]*)#iu');
Config::Set('plugin.banneroid.banner_date_reg', '/^[0-9]{4}\-[0-9]{2}-[0-9]{2}$/iu');

$config = array ();
return $config;
