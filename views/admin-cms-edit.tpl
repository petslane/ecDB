{extends file='layout.tpl'}

{block name=title}Admin - ecDB{/block}

{block name=head}
    <style>
        .cms-content-container {
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .cms-content-container textarea {
            box-sizing: border-box;
            width: 100%;
            height: 400px;
        }
        .cms-content-tab-md, .cms-content-tab-preview {
            display: inline-block;
            border: 1px solid gray;
            padding: 4px 10px;
            font-size: 14px;
            border-radius: 7px 7px 0 0;
            border-bottom: 0;
            opacity: 0.5;
        }
        .cms-content-tab-md.active, .cms-content-tab-preview.active {
            opacity: 1;
        }
        .cms-content-preview {
            border: 1px solid #ccc;
            padding: 6px;
        }
    </style>
    <script>
        $(function () {
            var $tabs = $('.cms-content-tabs > a');
            var $textarea = $('.cms-content-container > textarea');
            var $preview = $('.cms-content-container > .cms-content-preview');
            var $loading = $('.cms-content-container > .cms-content-preview-loading');
            $('.cms-content-tab-md').click(function () {
                $tabs.removeClass('active');
                $(this).addClass('active');
                $preview.hide();
                $loading.hide();
                $textarea.show();
            });
            $('.cms-content-tab-preview').click(function () {
                $tabs.removeClass('active');
                $(this).addClass('active');
                $preview.hide();
                $textarea.hide();
                $loading.show();
                $.ajax({
                    url: '{$base_url}/admin/cms/md-preview',
                    data: {
                        md: $textarea.val()
                    },
                    type: 'post'
                }).done(function (data) {
                    $preview.html(data.html);
                    $loading.hide();
                    $preview.show();
                });
            });
        });
        function deletePage() {
            if (confirm('Delete page?')) {
                var form = document.createElement('form');
                form.setAttribute('method', 'post');
                form.setAttribute('action', '{$base_url}/admin/cms/{$data.id}/delete');
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
{/block}

{block name=body}
    <h1>
        Admin area - <a href="{$base_url}/admin/cms">CMS</a> -
        {if $newPage}
            Create page
        {else}
            Edit page
        {/if}
    </h1>
    {include file="admin-submenu.tpl"}

<form method="post">
    <div>
        <label>
            Path:
            <input name="name" type="text" class="medium ac_input" value="{$data.name|escape}">
        </label>
    </div>
    <div>
        <label>
            Page title:
            <input name="title" type="text" class="medium ac_input" value="{$data.title|escape}">
        </label>
    </div>
    <div class="cms-content-container">
        <div class="cms-content-tabs">
            <a class="cms-content-tab-md active">Edit</a>
            <a class="cms-content-tab-preview">Preview</a>
        </div>
        <textarea name="content">{$data.content|escape:'html'}</textarea>
        <div style="display: none" class="cms-content-preview"></div>
        <div style="display: none" class="cms-content-preview-loading">Loading</div>
    </div>
    <div>
        <button class="button green" name="update" type="submit">
            <span class="icon medium save"></span>
            {if $newPage}
                Create page
            {else}
                Update page
            {/if}
        </button>
        {if !$newPage}
            <button class="button red" name="delete" type="button" onclick="deletePage()"><span class="icon medium trash"></span> Delete</button>
        {/if}
    </div>
</form>
{/block}