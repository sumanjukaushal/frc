{extends $layout}

{block content}

{if $posts}

	<header class="entry-header">
		<h1 class="entry-title"><span>{__ 'Search Results for:'} {!$site->searchQuery}</span></h1>
	</header>

	{if $type}

		{include snippets/content-loop-dir-search.php posts => $posts}

	{else}

		{include snippets/content-loop.php posts => $posts}

	{/if}

	{willPaginate}
	<nav class="paginate-links">
		{paginateLinks true}
	</nav>
	{/willPaginate}

	{ifset $themeOptions->advertising->showBox4}
	<div id="advertising-box-4" class="advertising-box">
	    {!$themeOptions->advertising->box4Content}
	</div>
	{/ifset}

{else}

	{include snippets/nothing-found.php}

{/if}

{/block}