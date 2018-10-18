{extends $layout}

{block content}

{if $posts}

	{if $type}

		<article>

				<header class="entry-header">

					<h1 class="entry-title">
						<span>{__ 'Search result'}</span>
					</h1>

				</header>

		</article>

		{include snippets/content-loop-dir-search.php posts => $posts}

		{willPaginate}
		<nav class="paginate-links">
			{paginateLinks true}
		</nav>
		{/willPaginate}

	{else}

		<article id="post-{$post->id}" class="{$post->htmlClasses}">

				<header class="entry-header">

					<h1 class="entry-title">
						<span>
						{if $archive->isDay}
							{__ 'Daily Archives:'} <span>{date $posts[0]->date}</span>
						{elseif $archive->isMonth}
							{__ 'Monthly Archives:'} <span>{$posts[0]->date|date:'F Y'}</span>
						{elseif $archive->isYear}
							{__ 'Yearly Archives:'} <span>{$posts[0]->date|date:'Y'}</span>
						{else}
							{__ 'Blog Archives'}
						{/if}
						</span>
					</h1>

				</header>

		</article><!-- /#post-{$post->id} -->


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