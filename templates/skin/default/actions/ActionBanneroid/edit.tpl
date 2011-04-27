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
    <form method="POST" action="" enctype="multipart/form-data" id="fmBanneroid">
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
        <h1><span>{if $add_banner}{$aLang.banneroid_add}{else}{$aLang.banneroid_edit}{/if}</span></h1>

        <label>
			{$aLang.banneroid_name}<br/>
            <input class="w40p text" type="text" id="banner_name" name="banner_name" value="{$_aRequest.banner_name}"  />
        </label>
        <br/>
        <label>
			{$aLang.banneroid_url}<br/>
            <input class="w100p text" type="text" id="banner_url" name="banner_url" value="{$_aRequest.banner_url}"  />
        </label>
        <br/>
        <label>
			{$aLang.banneroid_picture}<br/>
          <input class="w40p text" type="file" id="banner_image" name="banner_image"   /><br />
			{if $_aRequest.banner_is_image}<img src="{$_aRequest.banner_image}" />{/if}
        </label>
        <br/>
        <label>
          <b>Код банера</b><br />
          <textarea id="banner_html" name="banner_html">{$_aRequest.banner_html}</textarea>
        </label>
        <br/>


        <label>{$aLang.banneroid_start_date}<br/>
            <input name='banner_start_date' type='text' value='{$_aRequest.banner_start_date}' class='date demo_vista' />
        </label>

        <br />
        <label>{$aLang.banneroid_end_date}<br/>
            <input name="banner_end_date" type='text' value='{$_aRequest.banner_end_date}' class='date demo_vista'>
        </label>

        <br />
        <br />
        <strong>{$aLang.banneroid_place_zone}</strong>
        <br />

        <label>{$aLang.banneroid_under_article} 
            <input name="banner_type" type="radio" value="1" {if $_aRequest.banner_type==1}checked{/if} /></label>
        <label>{$aLang.banneroid_side_bar}
            <input name="banner_type" type="radio" value="2" {if $_aRequest.banner_type==2}checked{/if} /></label>



        <br/>
        <br />

        <table style="width:500px" border="0">
            <tr>
                <th>{$aLang.banneroid_page}</th>
                <th class="side_bar">&nbsp;</th>
            </tr>
			{foreach from=$_aRequest.banner_places item=ban_place}

            <tr>
                <td>{$aLang[$ban_place.place_name]}</td>
                <td class="side_bar" ><input name="banner_place[]" type="checkbox" value="{$ban_place.place_id}"
	{if $aPages[$ban_place.place_id]}checked="checked"{/if} class="side_bar" /></td>
            </tr>
  {/foreach}
        </table>

        <br />
        <label>
			{$aLang.banneroid_active}
            <input name="banner_is_active" type="hidden" value="0" />
            <input name="banner_is_active" type="checkbox" value="1" {if $_aRequest.banner_is_active}checked="checked"{/if}/>
        </label>
        <br/>
        <br/><br/>

        <input type="submit" name="submit_banner" value="{$aLang.banneroid_save}" />
        <input type="submit" name="cancel" value="{$aLang.banneroid_cancel}"/>

    </form>
</div>
{include file='footer.tpl'}
