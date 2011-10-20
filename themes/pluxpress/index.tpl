<div class="informer">

	<div class="container">
		{{site_description}}
		{{filteredtagtitle}} <strong>{{filtered_tag}}</strong>
	</div>
	
</div>
	
<div class="container">

	<div class="articleslist">
		{{# articles_list }}
			<div class="article">
				<h3><a href="{{article_link}}">{{article_title}}</a></h3>
				<p class="info">{{publishedat}}: {{article_date}}, {{publishedby}} {{article_author}}</p>
				<p class="text">{{{article_pretext}}} <a href="{{article_link}}">{{more}}</a></p>
				<p class="tags">{{tags}}: {{{article_tags}}}</p>
			</div>
		{{/ articles_list }}

		<div class="pagination">
			{{{pagination}}}
		</div>
	</div>

	<div class="toptags">
		<h4>{{toptagstitle}}:</h4>
		{{{toptags}}}
	</div>
	
</div>