<ul class="menu">
    <li {if !$sEvent or $sEvent=='main'}class="active"{/if}>
        <div><a href="{router page='banneroid'}">{$aLang.blog_menu_all}</a></div>
    </li>

    <li {if $sEvent=='add'}class="active"{/if}>
        <div><a href="{router page='banneroid'}add/">{$aLang.banneroid_add}</a></div>
    </li>


    <li {if $sEvent=='stats' or $sEvent=='stats-urls' or $sEvent=='stats-banners'}class="active"{/if}>
        <div><a href="{router page='banneroid'}stats/">{$aLang.banneroid_stats}</a></div>
    {if $sEvent=='stats' or $sEvent=='stats-urls' or $sEvent=='stats-banners'}
        <ul class="sub-menu">
            <li {if $sEvent=='stats'}class="active"{/if}><div><a href="{router page='banneroid'}stats/">{$aLang.banneroid_total}</a></div></li>
            <li {if $sEvent=='stats-banners'}class="active"{/if}><div><a href="{router page='banneroid'}stats-banners/">{$aLang.banneroid_bans_stats}</a></div></li>
        </ul>
	{/if}
    </li>
</ul>