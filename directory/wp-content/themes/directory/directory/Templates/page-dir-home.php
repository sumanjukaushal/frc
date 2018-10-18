{extends $layout}

{block content}

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

	{ifset $themeOptions->directory->showTopCategories}
		{if !empty($themeOptions->directory->topCategoriesTitle)}
		<h2 class="subcategories-title">{$themeOptions->directory->topCategoriesTitle}</h2>
		{/if}
		<div class="category-subcategories clearfix">
			{foreach $subcategories as $category}
			{first}<ul class="subcategories">{/first}
				<li class="category">
					<div class="category-wrap-table">
						<div class="category-wrap-row">
							<div class="icon" style="background: url('{thumbnailResize $category->icon, w => 35, h => 35 }') no-repeat center top;"></div>
							<div class="description">
								<h3><a href="{!$category->link}">{!$category->name}</a></h3>
								{!$category->excerpt}
							</div>
						</div>
					</div>
				</li>
			{last}</ul>{/last}
			{/foreach}
		</div>
	{/ifset}

	{ifset $themeOptions->directory->showTopLocations}
		{if !empty($themeOptions->directory->topLocationsTitle)}
		<h2 class="subcategories-title">{$themeOptions->directory->topLocationsTitle}</h2>
		{/if}
		<div class="category-subcategories clearfix">
			{foreach $sublocations as $location}
			{first}<ul class="subcategories">{/first}
				<li class="category">
					<div class="category-wrap-table">
						<div class="category-wrap-row">
							<div class="icon" style="background: url('{thumbnailResize $location->icon, w => 35, h => 35 }') no-repeat center top;"></div>
							<div class="description">
								<h3><a href="{!$location->link}">{!$location->name}</a></h3>
								{!$location->excerpt}
							</div>
						</div>
					</div>
				</li>
			{last}</ul>{/last}
			{/foreach}
		</div>
	{/ifset}

	{if $themeOptions->directory->dirHomepageAltContent}
	<div class="alternative-content">
		{!$themeOptions->directory->dirHomepageAltContent}
	</div>
	{/if}

</article><!-- /#post-{$post->id} -->

{ifset $themeOptions->advertising->showBox4}
<div id="advertising-box-4" class="advertising-box">
    {!$themeOptions->advertising->box4Content}
</div>
{/ifset}

{/block}