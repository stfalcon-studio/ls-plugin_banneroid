<div style="width:100%;border-bottom:3px solid silver;padding-top:3px;text-align:center;">
{foreach from=$aBanners item=oBanner}
  {if !$oBanner->getBannerHtml()}
  <a style="font-size:15px;display:block;" href="{router page='banneroid'}redirect/{$oBanner->getBannerId()}/3/"><img src="{$sBannersPath}{$oBanner->getBannerImage()}" /></a>
  {else}
    {$oBanner->getBannerHtml()}
  {/if }

{/foreach}
</div>
