<ul class="nav nav-menu">
    <li {if $sEvent=='main' or $sEvent=='add'}class="active"{/if}>
        <div><a href="{router page='banneroid'}">{$aLang.plugin.banneroid.banneroid_title}</a></div>
    </li>
    <li {if $sEvent=='stats' or $sEvent=='stats-urls' or $sEvent=='stats-banners'}class="active"{/if}>
        <div><a href="{router page='banneroid'}stats/">{$aLang.plugin.banneroid.banneroid_stats}</a></div>
    </li>
</ul>