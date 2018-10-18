		<footer id="colophon" role="contentinfo">

			<div id="supplementary" class="widgets defaultContentWidth">

				{isActiveSidebar footer-widgets}
				<div id="footer-widgets" class="widget-area" role="complementary">
					{dynamicSidebar footer-widgets}
				</div>
				{/isActiveSidebar}

			</div>

			{include 'snippets/branding-footer.php'}

		</footer>

	</div><!-- #page -->

	{footer}

	{googleAnalyticsCode $themeOptions->general->ga_code}

</body>
</html>