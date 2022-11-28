{include file='header' pageTitle='wcf.acp.dataMigration'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}wcf.acp.dataMigration{/lang}</h1>
        {if !$sourceSystem|empty}
            <p class="contentHeaderDescription">{lang}wcf.acp.dataMigration.source.{@$sourceSystem}{/lang}</p>
        {/if}
    </div>

    {hascontent}
        <nav class="contentHeaderNavigation">
            <ul>
                {content}{event name='contentHeaderNavigation'}{/content}
            </ul>
        </nav>
    {/hascontent}
</header>

{@$form->getHtml()}

{include file='footer'}
