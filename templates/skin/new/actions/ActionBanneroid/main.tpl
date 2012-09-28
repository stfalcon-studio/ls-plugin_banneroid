{assign var="bNoSidebar" value=true}
{include file='header.tpl' showWhiteBack=true menu='banneroid'}
<link rel="stylesheet" media="screen" type="text/css" href="{$sTemplateWebPathPluginBanneroid}css/datepicker_vista.css" />
<script type="text/javascript" src="{$sTemplateWebPathPluginBanneroid}js/datepicker.js"></script>
<script type="text/javascript" src="{$sTemplateWebPathPluginBanneroid}js/banneroid.js"></script>
<div class="page people">
    <h1>{$aLang.plugin.banneroid.banneroid_title}</h1>
    {if $aBannersList}
        <table>
            <thead>
                <tr>
                    <td>{$aLang.plugin.banneroid.banneroid_banner}</td>
                    <td>{$aLang.plugin.banneroid.banneroid_place}</td>
                    <td>{$aLang.plugin.banneroid.banneroid_delete}</td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aBannersList item=oBanner}
                    <tr>
                        <td><a href="{router page='banneroid'}stats-banners/{$oBanner->getId()}/" class="link">{$oBanner->getName()}</a></td>
                        <td>{$oBanner->getPagesNames()}</td>
                        <td>
                            <a href="{router page='banneroid'}edit/{$oBanner->getId()}/">{$aLang.plugin.banneroid.banneroid_edit}</a>
                            <a href="javascript:if(confirm('{$aLang.plugin.banneroid.banneroid_delete}?'))window.location.href='{router page='banneroid'}delete/{$oBanner->getId()}/';">{$aLang.plugin.banneroid.banneroid_delete}</a>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {else}
        {$aLang.plugin.banneroid.banneroid_empty}
    {/if}
</div>
<input name="add_banner" type="button" value="{$aLang.plugin.banneroid.banneroid_add}" onclick="window.location.href='{router page='banneroid'}add/'" />
{include file='footer.tpl'}