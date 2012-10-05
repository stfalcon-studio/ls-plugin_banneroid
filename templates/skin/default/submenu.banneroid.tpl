<ul class="menu nav nav-menu">
    <li {if $sEvent=='main' or $sEvent=='add'}class="active"{/if}>
        <div><a href="{router page='banneroid'}">{$aLang.plugin.banneroid.banneroid_title}</a></div>
    </li>
        {if $sEvent=='main' or $sEvent=='add'}
                <li {if !$sEvent || $sEvent=='main'}class="active"{/if}><div><a href="{router page='banneroid'}">{$aLang.blog_menu_all}</a></div></li>
                <li {if $sEvent=='add'}class="active"{/if}><div><a href="{router page='banneroid'}add/">{$aLang.plugin.banneroid.banneroid_add}</a></div></li>
        {/if}
    
    <li {if $sEvent=='stats' or $sEvent=='stats-urls' or $sEvent=='stats-banners'}class="active"{/if}>
        <div><a href="{router page='banneroid'}stats/">{$aLang.plugin.banneroid.banneroid_stats}</a></div>
    </li>
        {if $sEvent=='stats' or $sEvent=='stats-urls' or $sEvent=='stats-banners'}
                <li {if $sEvent=='stats'}class="active"{/if}><div><a href="{router page='banneroid'}stats/">{$aLang.plugin.banneroid.banneroid_total}</a></div></li>
                <li {if $sEvent=='stats-banners'}class="active"{/if}><div><a href="{router page='banneroid'}stats-banners/">{$aLang.plugin.banneroid.banneroid_bans_stats}</a></div></li>
        {/if}
    
</ul>