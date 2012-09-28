{assign var="bNoSidebar" value=true}
{include file='header.tpl' showWhiteBack=true menu='banneroid'}
<link rel="stylesheet" media="screen" type="text/css" href="{$sTemplateWebPathPluginBanneroid}css/datepicker_vista.css" />
<script type="text/javascript" src="{$sTemplateWebPathPluginBanneroid}js/datepicker.js"></script>
<script type="text/javascript" src="{$sTemplateWebPathPluginBanneroid}js/banneroid.js"></script>
<div class="page people">

    <h1>{if $oBanner}
        {$oBanner->getName()}
        {else}
            {$aLang.plugin.banneroid.banneroid_total}
            {/if}
                <form action="" method="post" id="statsForm">
                    <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
                    <input name='banner_start_date' type='text' value='{$_aRequest.banner_start_date}' class='date datepicker' />
                    <input name='banner_end_date' type='text' value='{$_aRequest.banner_end_date}' class='date datepicker' />
                    <input name="filter" type="submit" value="{$aLang.plugin.banneroid.banneroid_date_filter}" />
                </form>
            </h1>


            {if $aBannersStats}
                <table>
                    <thead>
                        <tr>
                            <td>{$aLang.plugin.banneroid.banneroid_name}</td>
                            <td>{$aLang.plugin.banneroid.banneroid_clics}</td>
                            <td>{$aLang.plugin.banneroid.banneroid_displays}</td>
                        </tr>
                    </thead>

                    <tbody>
                        {foreach from=$aBannersStats item=oStats}
                            <tr>
                                <td><a href="{router page='banneroid'}edit/{$oStats.banner_id}/" class="link">{$oStats.banner_name}</a></td>
                                <td>{$oStats.click_count}</td>
                                <td>{$oStats.view_count}</td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            {else}
                {$aLang.plugin.banneroid.banneroid_empty}
            {/if}
        </div>
        {include file='footer.tpl'}