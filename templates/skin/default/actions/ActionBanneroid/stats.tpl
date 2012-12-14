{assign var="noSidebar" value=true}
{include file='header.tpl' showWhiteBack=true menu='banneroid'}
<script type="text/javascript" src="{$sTemplateWebPathPluginBanneroid}js/banneroid.js"></script>
<ul class="nav nav-pills">
    <li {if $sEvent=='stats'}class="active"{/if}><div><a href="{router page='banneroid'}stats/">{$aLang.plugin.banneroid.banneroid_total}</a></div></li>
    <li {if $sEvent=='stats-banners'}class="active"{/if}><div><a href="{router page='banneroid'}stats-banners/">{$aLang.plugin.banneroid.banneroid_bans_stats}</a></div></li>
</ul>
<div class="page people">
    <h1>{$aLang.plugin.banneroid.banneroid_total}</h1>
    <div style="margin-bottom: 15px;">
        <form action="" method="post" id="statsForm">
            <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
            <input name='banner_start_date' type='text' value='{$_aRequest.banner_start_date}' class='date datepicker' />
            <input name='banner_end_date' type='text' value='{$_aRequest.banner_end_date}' class='date datepicker' />
            <input name="filter" type="submit" value="{$aLang.plugin.banneroid.banneroid_date_filter}" />
        </form>
    </div>
    {if $aBannersStats}
        <table class="table table-people table-talk">
            <thead>
                <tr>
                    <td>{$aLang.plugin.banneroid.banneroid_place_zone}</td>
                    <td>{$aLang.plugin.banneroid.banneroid_clics}</td>
                    <td>{$aLang.plugin.banneroid.banneroid_displays}</td>
                </tr>
            </thead>

            <tbody>
                {foreach from=$aBannersStats key=place item=stats}
                    <tr>
                        <td>{$place}</td>
                        <td>{$stats.click_count}</td>
                        <td>{$stats.view_count}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {else}
        {$aLang.plugin.banneroid.banneroid_empty}
    {/if}
</div>
{include file='footer.tpl'}