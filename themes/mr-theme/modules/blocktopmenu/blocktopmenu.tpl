{if $MENU != ''}
	<!-- Menu -->
	<div id="block_top_menu" class="sf-contener clearfix col-lg-12">
		<div class="cat-title">{l s="Categories" mod="blocktopmenu"}</div>
		<ul class="sf-menu clearfix menu-content">
			<li>
				<a id="logo_link" href="/">
					<img id="menu_logo" src="/img/logo.png" alt="Mr Logo">
				</a>
			</li>
			{$MENU}
			{*
			<li>
				<a href="/shop" id="start_here">
				{if $lang_iso == en}
					Start here
				{else}
					Start her
				{/if}
				</a>
			</li>
			*}
			{if $MENU_SEARCH}
				<li class="sf-search noBack" style="float:right">
					<form id="searchbox" action="{$link->getPageLink('search')|escape:'html':'UTF-8'}" method="get">
						<p>
							<input type="hidden" name="controller" value="search" />
							<input type="hidden" value="position" name="orderby"/>
							<input type="hidden" value="desc" name="orderway"/>
							<input type="text" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|escape:'html':'UTF-8'}{/if}" />
						</p>
					</form>
				</li>
			{/if}
		</ul>
	</div>
	<!--/ Menu -->
{/if}