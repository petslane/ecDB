<!DOCTYPE HTML>
<html>
<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{$base_url}/css/style.css" media="screen"/>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
    <meta name="description" content="Viwe all your added components."/>
    <meta name="keywords" content="electronics, components, database, project, inventory"/>
    <link rel="shortcut icon" href="{$base_url}/favicon.ico"/>
    <link rel="apple-touch-icon" href="{$base_url}/img/apple.png"/>
    <title>{block name=title}Home - ecDB{/block}</title>
    {if $ga.account}
        <script type="text/javascript">
            <!--
            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', '{$ga.account}']);
            _gaq.push(['_setDomainName', '{$ga.site}']);
            _gaq.push(['_trackPageview']);
            _gaq.push(['_trackPageLoadTime']);

            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();
            -->
        </script>
    {/if}
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    {block name=head}{/block}
</head>
<body>
<div id="wrapper">
    <!-- Header -->
    <div id="header">
        <div class="logoWrapper">
            <a href ="."><span class="logoImage"></span></a>
        </div>

            {if $smarty.session.SESS_MEMBER_ID}
                <span class="userInfo">
                    Logged in as
                    <a href="{$base_url}/my">
                        {$smarty.session.SESS_FIRST_NAME} {$smarty.session.SESS_LAST_NAME}
                    </a>
                    -
                    <a href="{$base_url}/logout">Sign out</a>
	            </span>
                <div class="searchContent">
                    <form class="search" action="{$base_url}/components/search" method="get">
                        <input type="text" name="q" autofocus/>
                    </form>
                </div>
            {/if}
    </div>

    <div id="menu">
        <ul>
            {foreach $menu_items as $menu_item}
                {if $menu_item.visibility == "1" && !$smarty.session.SESS_MEMBER_ID
                 || $menu_item.visibility == "2" && $smarty.session.SESS_MEMBER_ID
                 || $menu_item.visibility == "3" && $smarty.session.SESS_IS_ADMIN}
                    {assign var="select_route_names" value=','|@explode:$menu_item.select_route_names}
                    {if $menu_item.base_color}
                        <style>
                            div#menu ul li.menu-custom-color-{$menu_item.id} a {
                                border-bottom: 1px solid {$menu_item.base_color};
                                background-color: {$menu_item.base_color|menuColorBackground};
                                background-image: -moz-linear-gradient(100% 100% 90deg, {$menu_item.base_color|menuColorBackground}, #f8f8f8);
                                background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#f8f8f8), to({$menu_item.base_color|menuColorBackground}));
                            }
                            div#menu ul li.menu-custom-color-{$menu_item.id} a:hover {
                                background-color: {$menu_item.base_color|menuColorBackground};
                                background-image: none;
                            }
                            div#menu ul li.menu-custom-color-{$menu_item.id} a.selected {
                                background-image: none;
                                background-color: #ffffff;
                                border-bottom: 1px solid #ffffff;
                            }
                        </style>

                    {/if}
                    <li class="menu-custom-color-{$menu_item.id}">
                        {assign var="class" value=""}
                        {if $menu_route_name|@in_array:$select_route_names}
                            {assign var="class" value="selected"}
                        {/if}
                        <a
                                {if $menu_item.type == 1}
                                    href="{pathFor name=$menu_item.link}"
                                {elseif $menu_item.type == 2}
                                    href="{pathFor name="cms" page=$menu_item.cms_link}"
                                {else}
                                    href="{$menu_item.link}"
                                {/if}
                                class="{$class}">
                            {if $menu_item.icon|@substr:0:4 == "old-"}
                                <span class="icon medium {$menu_item.icon|@substr:4}"></span>
                            {elseif $menu_item.icon}
                                {mdIcon name=$menu_item.icon size=16}
                            {/if}
                            {$menu_item.title}
                        </a>
                    </li>
                {/if}
            {/foreach}
        </ul>
    </div>

    <!-- END -->
    <div id="content">
        <div>
            {if !empty($errors)}
                {foreach from=$errors item=$msg}
                    <div class="message red">
                        <ul class="error"><li>{$msg}</li></ul>
                    </div>
                {/foreach}
            {/if}
            {if !empty($messages)}
                {foreach from=$messages item=$msg}
                    <div class="message green">
                        {$msg}
                    </div>
                {/foreach}
            {/if}
            {if !empty($info)}
                {foreach from=$info item=$msg}
                    <div class="message orange">
                        {$msg}
                    </div>
                {/foreach}
            {/if}
        </div>
        {block name=body}{/block}
    </div>
    <!-- Text outside the main content -->
    <div id="copyText">
        <div class="leftBox">
            <div>
                © 2010 - {'Y'|@date} ecDB - Created by <a href="http://nilsf.se">Nils Fredriksson</a>
                - <a href="{$base_url}/contact">Contact us</a>
                - <a href="{$base_url}/terms">Terms & Privacy</a>
                - <a href="{$base_url}/about">About</a>
            </div>

            <div class="stats">

                {$STATS.members}
                <span class="boldText">members</span>,

                {$STATS.components}
                <span class="boldText">components </span>and

                {$STATS.projects}
                <span class="boldText">projects</span>.
            </div>
        </div>
        <div class="rightBox">
            Design by <a href="http://www.buildlog.eu"><span class="blIcon"></span></a>
        </div>
    </div>
    <!-- END -->
</div>
</body>
</html>
