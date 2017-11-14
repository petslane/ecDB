{extends file='layout.tpl'}

{block name=title}Admin - ecDB{/block}

{block name=head}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(function () {
            $("#sortable").sortable({
                placeholder: "ui-state-highlight",
                update: function( event, ui ) {
                    var order = [];
                    $(this).find('> tr').each(function () {
                        order.push($(this).data('id'));
                    });
                    $.ajax({
                        url: '{pathFor name="ajax_admin_sort"}',
                        data: {
                            order: order
                        },
                        type: 'post'
                    }).done(function (data) {
                        var rows = $('#sortable > tr').detach();
                        var rowIds = {};
                        rows.each(function (index, row) {
                            rowIds[row.getAttribute('data-id')] = row;
                        });
                        data.order.forEach(function (id) {
                            $('#sortable').append(rowIds[id]);
                        });
                    });
                }
            });
        });
    </script>
    <style>
        .ui-sortable-helper {
            border: 1px solid #b4b4b4;
            background-color: #ececec;
            box-shadow: 3px 3px 4px #00000026;
        }
        .ui-sortable-placeholder {
            background-color: #f9e898;
        }
    </style>
{/block}

{block name=body}
    <h1>Admin area - Menu</h1>
    {include file="admin-submenu.tpl"}

    <table class="globalTables" cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th>id</th>
            <th>icon</th>
            <th>color</th>
            <th style="text-align: left">title</th>
            <th>type</th>
            <th>link</th>
            <th>visibility</th>
        </tr>
        </thead>
        <tbody id="sortable">
        {foreach from=$data item=d}
            <tr data-id="{$d.id}">
                <td style="width: 10%" class="edit">
                    <a href="{$base_url}/admin/menu/{$d.id}/edit"><span class="icon medium pencil"></span></a>
                    {$d.id}
                </td>
                <td style="width: 7%">
                    {if $d.icon|@substr:0:4 == "old-"}
                        <span class="icon medium {$d.icon|@substr:4}"></span>
                    {elseif $d.icon}
                        {mdIcon name=$d.icon size=16}
                    {/if}
                </td>
                <td style="width: 8%">
                    {if $d.base_color}
                        <div style="display: inline-block; height: 16px; width: 16px; border: 1px solid {$d.base_color}; background-color: {$d.base_color|menuColorBackground};"></div>
                    {/if}
                </td>
                <td style="width: 30%; text-align: left">{$d.title}</td>
                <td style="width: 15%">
                    {if $d.type == 1}
                        internal page
                    {elseif $d.type == 2}
                        cms page
                    {elseif $d.type == 3}
                        external url
                    {else}
                        ?
                    {/if}
                </td>
                <td style="width: 15%">{$d.link}</td>
                <td style="width: 15%">
                    {if $d.visibility == 0}
                        hidden
                    {elseif $d.visibility == 1}
                        anonymous
                    {elseif $d.visibility == 2}
                        members
                    {elseif $d.visibility == 3}
                        admin
                    {else}
                        ?
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>

    <div>
        <button class="button green" name="update" type="button" onclick="window.location='{$base_url}/admin/menu/new'"><span class="icon medium sqPlus"></span> Create new menu item</button>
    </div>
{/block}