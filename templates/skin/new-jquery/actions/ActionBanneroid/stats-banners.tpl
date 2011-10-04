{assign var="noSidebar" value=true}
{include file='header.tpl' showWhiteBack=true menu='banneroid'}
<script type="text/javascript" src="{$sTemplateWebPathPlugin}js/banneroid.js"></script>
<div class="page people">

    <h1>{if $oBanner}{$oBanner->getName()}{else}{$aLang.banneroid_total}{/if}</h1>
    <div style="margin-bottom: 15px;">
        <form action="" method="post" id="statsForm">
            <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
            <input name='banner_start_date' type='text' value='{$_aRequest.banner_start_date}' class='date datepicker' />
            <input name='banner_end_date' type='text' value='{$_aRequest.banner_end_date}' class='date datepicker' />
            <input name="filter" type="submit" value="{$aLang.banneroid_date_filter}" />
        </form>
    </div>

    {if $aBannersStats}
        <table class="table table-people table-talk">
            <thead>
                <tr>
                    <td>{$aLang.banneroid_name}</td>
                    <td>{$aLang.banneroid_clics}</td>
                    <td>{$aLang.banneroid_displays}</td>
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
        {$aLang.banneroid_empty}
    {/if}
</div>
{include file='footer.tpl'}