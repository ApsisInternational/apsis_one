<div style="padding-bottom: 12px;">
    {if empty($formFields)}
        You do not have any subscriptions.
    {else}
        {foreach from=$formFields item="field"}
            <div class="form-group row">
                <div class="col-md-6">
                    <span class="custom-checkbox">
                        <label>
                            <input name="{$field.name}" type="checkbox" value="1" checked="checked" disabled="disabled">
                            <span><i class="material-icons rtl-no-flip checkbox-checked">&#xE5CA;</i></span>
                            {$field.label nofilter}
                        </label>
                    </span>
                </div>
            </div>
        {/foreach}
    {/if}
</div>