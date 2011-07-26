<div class="block banner">
    <div class="tl"><div class="tc"></div><div class="tr"></div></div>
    <div class="cl"><div class="cr">
            <div id="banners_container">
                <div id="banner_container">
                    <div id="banners">
                        {if !$oBanner->getBannerHtml()}<a style="font-size:15px;display:block;" href="{router page='banneroid'}redirect/{$oBanner->getBannerId()}/2/"><img src="{$sBannersPath}{$oBanner->getBannerImage()}" /></a>
                        {else}
                          <div class="baneroid" title="{$oBanner->getId()}">{$oBanner->getBannerHtml()}</div>
                        {/if }
                    </div>
                </div>
            </div>
        </div></div>
    <div class="bl"><div class="bc"></div><div class="br"></div></div>
</div>
