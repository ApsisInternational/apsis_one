{extends file='page.tpl'}

{block name='page_title'}
    {$pageTitle}
{/block}

{block name='page_content'}
    {render file=$formTemplate ui=$consentsForm}
{/block}

{block name='page_footer_container'}
    <div style="margin-bottom: 50px">
        <a style="font-size: 14px;" href="{$link->getPageLink('my-account', true)|escape:'html'}">
            <i class="material-icons">chevron_left</i>
            Return to your account
        </a>
        <a style="font-size: 14px; margin-left: 20px;" href="{$urls.base_url}">
            <i class="material-icons">home</i>
            Home
        </a>
    </div>
{/block}