{extends file='layout.tpl'}

{block name=title}{$data.title}{/block}

{block name=head}{/block}

{block name=body}
    <!-- Main content -->
    <div class="loginWrapper">
        {if $data.title}
            <h1>{$data.title}</h1>
        {/if}

        {$content}
    </div>
    <!-- END -->
{/block}