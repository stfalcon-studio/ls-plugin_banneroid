{assign var="noSidebar" value=true}
{include file='header.tpl' showWhiteBack=true menu='banneroid'}
<script type="text/javascript" src="{$sTemplateWebPathPlugin}js/banneroid.js"></script>
<div class="page people">
    <h1>{$aLang.banneroid_title}</h1>
    {if $aBannersList}
        <table class="table table-people table-talk">
            <thead>
                <tr>
                    <td>{$aLang.banneroid_banner}</td>
                    <td>{$aLang.banneroid_place}</td>
                    <td>{$aLang.banneroid_delete}</td>
                </tr>
            </thead>
            <tbody>
                {foreach from=$aBannersList item=oBanner}
                    <tr>
                        <td><a href="{router page='banneroid'}stats-banners/{$oBanner->getId()}/" class="link">{$oBanner->getName()}</a></td>
                        <td>{$oBanner->getPagesNames()}</td>
                        <td>
                            <a href="{router page='banneroid'}edit/{$oBanner->getId()}/">{$aLang.banneroid_edit}</a>
                            <a href="javascript:if(confirm('{$aLang.banneroid_delete}?'))window.location.href='{router page='banneroid'}delete/{$oBanner->getId()}/';">{$aLang.banneroid_delete}</a>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {else}
        {$aLang.banneroid_empty}
    {/if}
</div>
<input name="add_banner" type="button" value="{$aLang.banneroid_add}" onclick="window.location.href='{router page='banneroid'}add/'" />
{include file='footer.tpl'}