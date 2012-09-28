<ul class="menu">
    <li {if $sEvent=='main' or $sEvent=='add'}class="active"{/if}>
        <div><a href="{router page='banneroid'}">{$aLang.plugin.banneroid.banneroid_title}</a></div>
        {if $sEvent=='main' or $sEvent=='add'}
            <ul class="sub-menu">
                <li {if !$sEvent || $sEvent=='main'}class="active"{/if}><div><a href="{router page='banneroid'}">{$aLang.plugin.banneroid.blog_menu_all}</a></div></li>
                <li {if $sEvent=='add'}class="active"{/if}><div><a href="{router page='banneroid'}add/">{$aLang.plugin.banneroid.banneroid_add}</a></div></li>
            </ul>
        {/if}
    </li>
    <li {if $sEvent=='stats' or $sEvent=='stats-urls' or $sEvent=='stats-banners'}class="active"{/if}>
        <div><a href="{router page='banneroid'}stats/">{$aLang.plugin.banneroid.banneroid_stats}</a></div>
        {if $sEvent=='stats' or $sEvent=='stats-urls' or $sEvent=='stats-banners'}
            <ul class="sub-menu">
                <li {if $sEvent=='stats'}class="active"{/if}><div><a href="{router page='banneroid'}stats/">{$aLang.plugin.banneroid.banneroid_total}</a></div></li>
                <li {if $sEvent=='stats-banners'}class="active"{/if}><div><a href="{router page='banneroid'}stats-banners/">{$aLang.plugin.banneroid.banneroid_bans_stats}</a></div></li>
            </ul>
        {/if}
    </li>
</ul>