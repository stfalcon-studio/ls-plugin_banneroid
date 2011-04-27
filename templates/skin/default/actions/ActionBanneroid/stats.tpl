{assign var="bNoSidebar" value=true}
{include file='header.tpl' menu='banneroid'}

<link rel="stylesheet" media="screen" type="text/css" href="{$sTemplateWebPathPlugin}css/datepicker_vista.css" />

<script type="text/javascript" src="{$sTemplateWebPathPlugin}js/datepicker.js"></script>

<script>
{literal}
window.addEvent('load', function() {
         new DatePicker('.demo_vista', { pickerClass: 'datepicker_vista', format: 'Y-m-d', minDate: '',inputOutputFormat: 'Y-m-d' });
	
});
{/literal}
</script>

<div class="page people">

    <h1>{$aLang.banneroid_total}
	 <form action="" method="post" id="statsForm">
      <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
      <input name='banner_start_date' type='text' value='{$_aRequest.banner_start_date}' class='date demo_vista' />
	  <input name='banner_end_date' type='text' value='{$_aRequest.banner_end_date}' class='date demo_vista' />
	  <input name="filter" type="submit" value="{$aLang.banneroid_date_filter}" />
	  </form>
    </h1>
	

	{if $aBannersStats}
    <table>
        <thead>
            <tr>
                <td class="user">{$aLang.banneroid_place_zone}</td>
                <td class="user">{$aLang.banneroid_clics}</td>
                <td class="user">{$aLang.banneroid_displays}</td>
            </tr>
        </thead>

        <tbody>
			{foreach from=$aBannersStats key=place item=stats}
            <tr>
                <td class="user">{$place}</td>
                <td class="user">{$stats.click_count}</td>
                <td class="user">{$stats.view_count}</td>
            </tr>
            {/foreach}
        </tbody>
    </table>
	{else}
        {$aLang.banneroid_empty}
	{/if}
</div>
 {include file='footer.tpl'}