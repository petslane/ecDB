{extends file='layout.tpl'}

{block name=title}Admin - ecDB{/block}

{block name=head}{/block}

{block name=body}
    <h1>Admin area - CMS</h1>
    {include file="admin-submenu.tpl"}

    <table class="globalTables" cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th style="width: 75px">id</th>
            <th>title</th>
            <th style="text-align: left">path</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$data item=d}
            <tr>
                <td class="edit">
                    <a href="{$base_url}/admin/cms/{$d.id}/edit"><span class="icon medium pencil"></span></a>
                    {$d.id}
                </td>
                <td style="text-align: left"><a href="{$base_url}/admin/cms/{$d.id}">{if $d.title}{$d.title}{else}<i>(no title)</i>{/if}</a></td>
                <td style="text-align: left">{$d.name}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>

    <div>
        <button class="button green" name="update" type="button" onclick="window.location='{$base_url}/admin/cms/new'"><span class="icon medium sqPlus"></span> Create new page</button>
    </div>
{/block}