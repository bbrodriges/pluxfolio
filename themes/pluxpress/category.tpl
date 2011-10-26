<div class="informer">
	<div class="container">
		{{category_title}}
	</div>
</div>

<div class="container">
<div class="category">

	<p>{{category_description}}</p>
	
	{{# galleries_list }}
		<p>
			{{{gallery_thumb}}}<br>
			<a href="{{gallery_link}}">{{gallery_name}}</a><br>
			<small>{{gallery_description}}</small>
		</p>
	{{/ galleries_list }}

</div>