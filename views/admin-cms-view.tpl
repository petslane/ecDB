{extends file='layout.tpl'}

{block name=title}{$data.title} - ecDB{/block}

{block name=head}{/block}

{block name=body}
    <h1>{$data.title}</h1>

    {$content}
{/block}