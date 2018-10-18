{extends $layout}

{block content}

{if $posts}
	
		<article id="post-{$post->id}" class="{$post->htmlClasses}">

				<header class="entry-header">
		
					<h1 class="entry-title">
						 {__ 'Author Archives:'}
		                <span class="vcard">
		                    <a class="url fn n" href="{$author->postsUrl}" title="{$author->name}" rel="me">{$author->name}</a>
		                </span>
					</h1>
					
				</header>

				{if strlen($author->bio) !== 0}
				<div class="entry-content">
					<div id="author-info" class="clearfix">
						<div id="author-avatar" class="left">{!$author->avatar(60)}</div>
						<div id="author-description" class="left">
							<div class="author-name">{_x 'About', 'about author'} {$author->name}</div>
							<div class="bio">{$author->bio}</div>
						</div>
					</div>
				</div>
				{/if}
				
		</article><!-- /#post-{$post->id} -->	


		{include snippets/content-nav.php location => 'nav-above'}

		{include snippets/content-loop.php posts => $posts}

		{include snippets/content-nav.php location => 'nav-below'}

	{ifset $themeOptions->advertising->showBox4}
	<div id="advertising-box-4" class="advertising-box">
	    {!$themeOptions->advertising->box4Content}
	</div>
	{/ifset}

{else}

	{include snippets/nothing-found.php}

{/if}

{/block}