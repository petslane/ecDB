{extends file='layout.tpl'}

{block name=title}Admin - ecDB{/block}

{block name=head}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-tokeninput/1.7.0/jquery.tokeninput.js"></script>
    <style>
        .icon-preview {
            display: inline-block;
            border: 1px solid #b4b4b4;
            margin: 1px;
            vertical-align: bottom;
            padding: 5px;
            min-height: 16px;
            min-width: 16px;
        }
        .icon-preview.icon-preview-2 .material-icons {
            font-size: 16px;
            vertical-align: bottom;
            height: 16px;
            width: 16px;
        }
    </style>
    <script>
        function deleteMenu() {
            if (confirm('Delete menu?')) {
                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', '{$base_url}/admin/menu/{$data.id}/delete');
                document.body.appendChild(form);
                form.submit();
            }
        }
        $(function () {
            $('.use-custom-color input')
                .on('change', function () {
                    $('.custom-color').toggle($(this).prop('checked'));
                })
                .change();

            $('.icon-type-select')
                .on('change', function () {
                    var v = $(this).val();

                    $('.container-icon-type-old').toggle(v === 'old');
                    $('.container-icon-type-md').toggle(v === 'md');
                })
                .change();

            $('.icon-select')
                .on('change', function () {
                    var v = $(this).val();

                    var preview = '<span class="icon medium ' + v.substr(4) + '"></span>';

                    $('.icon-preview-1')
                        .empty()
                        .html(preview);
                })
                .change();

            $('.input-md-name')
                .on('keyup', function () {
                    var v = $(this).val();

                    v = v.split(' ').join('_');

                    var preview = '<i class="material-icons">' + v + '</i>';

                    $('.icon-preview-2')
                        .empty()
                        .html(preview);
                })
                .keyup();

            var cms_pages = {$cms_pages|@json_encode};
            var routes = [];
            {assign var="select_route_names" value=","|@explode:$data.select_route_names}
            var select_route_names = {$select_route_names|@json_encode};
            var selectedRoutes = [];
            {foreach from=$routes item=r}
                routes.push({
                    id: {$r|@json_encode},
                    name: {$r|@json_encode}
                });
                {if $r|@in_array:$select_route_names}
                    selectedRoutes.push({
                        id: {$r|@json_encode},
                        name: {$r|@json_encode}
                    });
                {/if}
            {/foreach}

            var cms_pages_2 = cms_pages.map(function (row) {
                var id = 'cms:' + row.id;
                if (!!~select_route_names.indexOf(id)) {
                    selectedRoutes.push({
                        id: id,
                        name: row.name
                    });
                }
                return {
                    id: id,
                    name: row.name
                };
            });

            $("#input-selected-routes").tokenInput(routes.concat(cms_pages_2), {
                preventDuplicates: true,
                prePopulate: selectedRoutes
            });
            $('[name="menu[type]"]')
                .on('change', function () {
                    $('.container-link-internal').toggle($(this).val() == 1);
                    $('.container-link-cms').toggle($(this).val() == 2);
                    $('.container-link-url').toggle($(this).val() == 3);
                })
                .change();

            var val = $('[name="menu[link_internal]"').val();
            selectedRoutes = routes.filter(function (data) {
                return val && data.id == val;
            });
            $('[name="menu[link_internal]"').tokenInput(routes, {
                preventDuplicates: true,
                prePopulate: selectedRoutes,
                tokenLimit:1
            });

            var val = $('[name="menu[link_cms]"').val();
            var selectedCmsPage = cms_pages.filter(function (cms_page) {
                return val && cms_page.id == val;
            });
            $('[name="menu[link_cms]"').tokenInput(cms_pages, {
                preventDuplicates: true,
                prePopulate: selectedCmsPage,
                tokenLimit:1
            });
        });
    </script>
{/block}

{block name=body}
    <h1>
        Admin area - <a href="{$base_url}/admin/menu">Menu</a> -
        {if $newPage}
            Create menu item
        {else}
            Edit menu item
        {/if}
    </h1>
    {include file="admin-submenu.tpl"}

<form method="post" class="form-style-1">
    <div>
        <label>
            <span>Title:</span>
            <input name="menu[title]" type="text" class="medium" value="{$data.title|escape}">
        </label>
    </div>
    <div class="use-custom-color">
        <label>
            <span>Use custom color:</span>
            {if !$smarty.post.menu}
                {$data.use_base_color=$data.base_color}
            {/if}
            <input name="menu[use_base_color]" type="checkbox" value="1" {if $data.use_base_color}checked{/if}>
        </label>
    </div>
    <div class="custom-color" {if !$data.base_color}style="display: none"{/if}>
        <label>
            <span>Base color:</span>
            <input name="menu[base_color]" type="color" class="medium" value="{$data.base_color|escape}">
        </label>
    </div>

    <div>
        <label>
            <span>Icon:</span>
            {if !isset($data.icon_type)}
                {if !$data.icon}
                    {$data.icon_type=""}
                {elseif $data.icon|@substr:0:4 != 'old-'}
                    {$data.icon_type="md"}
                {elseif $data.icon|@substr:0:4 == 'old-'}
                    {$data.icon_type="old"}
                {/if}
            {/if}
            <select name="menu[icon_type]" class="icon-type-select">
                <option {if !$data.icon_type}selected{/if}>no icon</option>
                <option {if $data.icon_type == 'md'}selected{/if} value="md">Material design icon</option>
                <option {if $data.icon_type == 'old'}selected{/if} value="old">Old built-in icons</option>
            </select>
        </label>
    </div>
    <div class="container-icon-type-old">
        <label>
            <span></span>
            {if !isset($data.icon_old) && $data.icon|@substr:0:4 == 'old-'}
                {$data.icon_old=$data.icon}
            {/if}
            <select name="menu[icon_old]" class="icon-select">
                {foreach from=$icons item=icon}
                    <option value="{$icon}" {if $data.icon_old == $icon}selected{/if}>{$icon}</option>
                {/foreach}
            </select>
            <div class="icon-preview icon-preview-1"></div>
        </label>
    </div>
    <div class="container-icon-type-md">
        <label>
            <span>MD icon name:</span>
            {if !isset($data.icon_md) && $data.icon_type == 'md'}
                {$data.icon_md=$data.icon}
            {/if}
            <input name="menu[icon_md]" type="text" class="medium input-md-name" value="{$data.icon_md|escape}">
            <div class="icon-preview icon-preview-2"></div>
            <a href="https://material.io/icons/" target="_blank">Find icon name from material.io</a>
        </label>
    </div>
    <div>
        <label>
            <span>Link type:</span>
            <select name="menu[type]">
                <option value="1" {if $data.type == 1}selected{/if}>internal page</option>
                <option value="2" {if $data.type == 2}selected{/if}>cms page</option>
                <option value="3" {if $data.type == 3}selected{/if}>external url</option>
            </select>
        </label>
    </div>
    <div class="container-link-internal" style="display: none">
        <label>
            <span>Internal page:</span>
            {if !isset($data.link_internal) && $data.type == 1}
                {$data.link_internal=$data.link}
            {/if}
            <input name="menu[link_internal]" type="text" class="medium" value="{$data.link_internal|escape}">
        </label>
    </div>
    <div class="container-link-cms" style="display: none">
        <label>
            <span>CMS page:</span>
            {if !isset($data.link_cms) && $data.type == 2}
                {$data.link_cms=$data.link}
            {/if}
            <input name="menu[link_cms]" type="text" class="medium" value="{$data.link_cms|escape}">
        </label>
    </div>
    <div class="container-link-url" style="display: none">
        <label>
            <span>External URL:</span>
            {if !isset($data.link_url) && $data.type == 3}
                {$data.link_url=$data.link}
            {/if}
            <input name="menu[link_url]" type="text" class="medium" value="{$data.link_url|escape}">
        </label>
    </div>
    <div>
        <label>
            <span>Visibility:</span>
            <select name="menu[visibility]">
                <option value="0" {if $data.visibility == 0}selected{/if}>hidden</option>
                <option value="1" {if $data.visibility == 1}selected{/if}>anonymous</option>
                <option value="2" {if $data.visibility == 2}selected{/if}>members</option>
                <option value="3" {if $data.visibility == 3}selected{/if}>admin</option>
            </select>
        </label>
    </div>
    <div>
        <label>
            <span>Selected routes:</span>
            <input name="menu[select_route_names]" type="text" class="medium" id="input-selected-routes" value="{$data.select_route_names|escape}">
        </label>
    </div>

    <div>
        <button class="button green" name="update" type="submit">
            <span class="icon medium save"></span>
            {if $newPage}
                Create menu item
            {else}
                Update menu item
            {/if}
        </button>
        {if !$newPage}
            <button class="button red" name="delete" type="button" onclick="deleteMenu()"><span class="icon medium trash"></span> Delete</button>
        {/if}
    </div>
</form>
{/block}