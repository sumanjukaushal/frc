{extends $layout}

{block content}

{if $posts}

	{if $type}

		{include snippets/content-loop-dir-search.php posts => $posts}

		{willPaginate}
		<nav class="paginate-links">
			{paginateLinks true}
		</nav>
		{/willPaginate}

	{else}

		{if !$isIndexPage}
		<article id="post-{$post->id}" class="{$post->htmlClasses}">

				<header class="entry-header">

					<h1 class="entry-title">
						<a href="{$post->permalink}" title="{__ 'Permalink to'} {$post->title}" rel="bookmark">{$post->title}</a>
					</h1>

				</header>

				{if $post->thumbnailSrc}
				<a href="{!$post->thumbnailSrc}">
					<div class="entry-thumbnail"><img src="{thumbnailResize $post->thumbnailSrc, w => 140, h => 200}" alt=""></div>
				</a>
				{/if}

				<div class="entry-content">
					{!$post->content}
				</div>
		</article><!-- /#post-{$post->id} -->
		{/if}

		{include snippets/content-nav.php location => 'nav-above'}

		{include snippets/content-loop.php posts => $posts}

		{include snippets/content-nav.php location => 'nav-below'}

	{/if}

	{ifset $themeOptions->advertising->showBox4}
	<div id="advertising-box-4" class="advertising-box">
	    {!$themeOptions->advertising->box4Content}
	</div>
	{/ifset}

{else}

	{include snippets/nothing-found.php}

{/if}

{/block}