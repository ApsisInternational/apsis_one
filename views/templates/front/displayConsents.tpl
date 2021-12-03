{extends file='page.tpl'}

{block name='page_header_container'}
{/block}

{block name='page_content_container'}
    <div>{$pageContent}</div>
{/block}

{block name='page_footer_container'}
    <div class="apsis-consents-footer-links">
        <a href="{$link->getPageLink('my-account', true)|escape:'html'}">
            <i class="material-icons">chevron_left</i>
            Return to your account
        </a>
        <a href="{$urls.base_url}">
            <i class="material-icons">home</i>
            Home
        </a>
    </div>
{/block}