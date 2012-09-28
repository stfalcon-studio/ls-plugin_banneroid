{foreach from=$aBanners item=oBanner}
    {if !$oBanner->getBannerHtml()}
        <a style="font-size:15px;display:block;" href="{router page='banneroid'}redirect/{$oBanner->getBannerId()}/1/"><img src="{$sBannersPath}{$oBanner->getBannerImage()}" /></a>
        {else}
            {$oBanner->getBannerHtml()}
        {/if }
{/foreach}
