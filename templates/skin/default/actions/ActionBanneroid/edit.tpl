{assign var="noSidebar" value=true}
{include file='header.tpl' showWhiteBack=true menu='banneroid'}
<script type="text/javascript" src="{$sTemplateWebPathPluginBanneroid}js/banneroid.js"></script>
<div class="page people">

    <form method="post" action="" enctype="multipart/form-data" id="fmBanneroid">
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
        {if $_aRequest.banner_id}<input type="hidden" name="banner_id" value="{$_aRequest.banner_id}" />{/if}
        <h1><span>{if $add_banner}{$aLang.plugin.banneroid.banneroid_add}{else}{$aLang.plugin.banneroid.banneroid_edit}{/if}</span></h1>

        <label>
            {$aLang.plugin.banneroid.banneroid_name}<br/>
            <input class="input-wide" type="text" id="banner_name" name="banner_name" value="{$_aRequest.banner_name}"  />
        </label>
        <br/>
        <label>
            {$aLang.plugin.banneroid.banneroid_url}<br/>
            <input class="input-wide" type="text" id="banner_url" name="banner_url" value="{$_aRequest.banner_url}"  />
        </label>
        <br />
        <br />
        <fieldset id="kinds" style="width:500px;">
            <legend><strong>{$aLang.plugin.banneroid.banneroid_kind}</strong><br /></legend>
            <label><input name="banner_kind" type="radio" value="kind_image" {if $_aRequest.banner_is_image || $_aRequest.banner_kind=='kind_image'}checked="checked"{/if} />{$aLang.plugin.banneroid.banneroid_kind_image}</label>
            <label><input name="banner_kind" type="radio" value="kind_html" {if not $_aRequest.banner_is_image || $_aRequest.banneroid_html != ''}checked="checked"{/if} />{$aLang.plugin.banneroid.banneroid_kind_html}</label><br />
        </fieldset>
        <br />
        <div id="kind_image" {if $_aRequest.banner_kind == 'kind_html' || $_aRequest.banneroid_html != ''}style="display:none"{/if}>
            <label><strong>{$aLang.plugin.banneroid.banneroid_kind_image}</strong><br/>
                <input class="w40p text" type="file" id="banner_image" name="banner_image" value="{$_aRequest.banner_image_tmp}"/></br>
                {if $_aRequest.banner_is_image}<img src="{$_aRequest.banner_image}" />{/if}
            </label>
        <br/>
    </div>
    <div id="kind_html"  {if  $_aRequest.banneroid_html == ''}style="display:none"{/if}>
        <label>
            <strong>{$aLang.plugin.banneroid.banneroid_kind_html}</strong><br />
            <textarea id="banneroid_html" name="banneroid_html" cols="40" rows="20" class="input-wide">{$_aRequest.banneroid_html}</textarea>
        </label>
        <br/>
    </div>
    <label>{$aLang.plugin.banneroid.banneroid_start_date}<br/>
        <input name='banner_start_date' type='text' value='{$_aRequest.banner_start_date}' class='date datepicker' />
    </label>

    <br />
    <label>{$aLang.plugin.banneroid.banneroid_end_date}<br/>
        <input name="banner_end_date" type='text' value='{$_aRequest.banner_end_date}' class='date datepicker'>
    </label>

    <br />
    <br />
    <fieldset style="width:500px;">
        <legend>&nbsp;<strong>{$aLang.plugin.banneroid.banneroid_place_zone}</strong></legend>
        <label><input name="banner_type" type="radio" value="1" {if $_aRequest.banner_type==1}checked="checked"{/if} />{$aLang.plugin.banneroid.banneroid_under_article}</label><br />
        <label><input name="banner_type" type="radio" value="2" {if $_aRequest.banner_type==2}checked="checked"{/if} />{$aLang.plugin.banneroid.banneroid_side_bar}</label><br />
        <label><input name="banner_type" type="radio" value="3" {if $_aRequest.banner_type==3}checked="checked"{/if} />{$aLang.plugin.banneroid.banneroid_body_begin}</label><br />
        <label><input name="banner_type" type="radio" value="4" {if $_aRequest.banner_type==4}checked="checked"{/if} />{$aLang.plugin.banneroid.banneroid_body_end}</label>
    </fieldset>


    <br/>
    <br />

    <table class="table table-people table-talk" style="width: 500px;">
        <thead>
            <tr>
                <td>{$aLang.plugin.banneroid.banneroid_page}</td>
                <td style="width:20px;">&nbsp;</td>
            </tr>
        </thead>
        {foreach from=$aPlaces item=ban_place}
            <tr>
                <td>{$aLang.plugin.banneroid[$ban_place.place_name]}</td>
                <td ><input name="banner_place[]" type="checkbox" value="{$ban_place.place_id}"{foreach from=$_aRequest.banner_places key=k item=place}{if $k == $ban_place.place_id }checked="checked"{/if}{/foreach} class="side_bar" /></td>
            </tr>
        {/foreach}
</table>

<br />
<label>
    {$aLang.plugin.banneroid.banneroid_active}
    <input name="banner_is_active" type="hidden" value="0" />
    <input name="banner_is_active" type="checkbox" value="1" {if $_aRequest.banner_is_active}checked="checked"{/if}/>
</label>
<br/>
<br/><br/>
{if count($aLangs)}
    <p>
        <label>{$aLang.plugin.banneroid.banneroid_select_lang}</label>
        <select id="banner_lang" class="w100" name="banner_lang">
            <option value="0"></option>
            {foreach from=$aLangs key=sLangKey item=sLangText}
                <option {if $_aRequest.banner_lang == $sLangKey}selected="selected"{/if}value="{$sLangKey}">{$sLangKey}</option>
            {/foreach}
        </select>
    </p>
{/if}
<input type="submit" name="submit_banner" value="{$aLang.plugin.banneroid.banneroid_save}" />
<input type="submit" name="cancel" value="{$aLang.plugin.banneroid.banneroid_cancel}"/>

</form>
</div>
{include file='footer.tpl'}
