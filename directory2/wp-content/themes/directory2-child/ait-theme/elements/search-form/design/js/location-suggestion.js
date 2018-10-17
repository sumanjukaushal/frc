var prefetchURL = LocAjax.prefurl + "?action=prefetch_locations&loc-sugg="+LocAjax.locSuggestion;
console.log("prefetchURL"+prefetchURL);
 var locations = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.whitespace,
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  prefetch: prefetchURL
 
});
jQuery('#prefetch .typeahead').typeahead(null, {
  name: 'locations',
  source: locations,
  minlength:3,
  limit:10
 });
/*
var product_dataset = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: {
                        url: prefetchURL,
                        cache:false,
                        ttl: 10 // in milliseconds
                        cacheKey: "change here!"
        }
        
});
*/
/*var loc_dataset = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.whitespace,
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  prefetch: 'https://freerangecamping.co.nz/plus/wp-content/locations/json_locations_flat.php'
 
});
loc_dataset.clearPrefetchCache();
loc_dataset.initialize();
//loc_dataset.removeItem("YOUR CACHEKEY");
//https://github.com/twitter/typeahead.js/issues/1475 <==
*/
/*
var loc_dataset = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		prefetch: {
                        url: prefetchURL,
                        cache:false,
                        ttl: 10 // in milliseconds
        }
});
loc_dataset.clearPrefetchCache();
loc_dataset.initialize();
jQuery('#prefetch .typeahead').typeahead(null, {
  name: 's',
  source: loc_dataset,
  minlength:3,
  limit:10
 });
*/
/*
jQuery('#prefetch1 .typeahead1').typeahead({minLength: 3,highlight: true}, {
        name: 's',
        display: 'location',
        source: loc_dataset,
        limit:120,
	
        classNames: {
          input: 'Typeahead-input',
          hint: 'Typeahead-hint',
          selectable: 'Typeahead-selectable'
        },
        hint:true,
        templates: {
                empty: ["<div style='background-color:lightgrey;width:400px;z-index:-5000'><b>",'unable to find matching location','</b></div>'].join('\n'),
                suggestion: Handlebars.compile('<div style="background-color:lightgrey;width:400px;z-index:-5000" class="row_hover"><strong style="width:400px;color:black"><a class="row_hover" href="#-1">{{location}}</a></strong></div>'),
                header: Handlebars.compile("<div style='background-color:lightgrey;width:400px;z-index:-5000'><b>Search results for {{query}}:'</b></div>"),
                footer: Handlebars.compile("<div style='background-color:lightgrey;width:400px;z-index:-5000'><b>---------freerangecamping.com.au---------</b></div>"),
        }
 });
*/
//prefetch: "https://freerangecamping.co.nz/plus/wp-content/locations/json_locations_flat.php"
//prefetch: AitSettings.home.url + "/wp-content/locations/json_locations_flat.php"
//prefetch: LocAjax.prefurl + "?action=prefetch_locations&loc-sugg="+LocAjax.locSuggestion
/*
var product_dataset = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
		queryTokenizer: Bloodhound.tokenizers.whitespace,
		remote: {
		  url: LocAjax.prefurl+"?action=loc_suggestions&s=%QUERY",
						  replace: function (url,query) {
						   return url.replace('%QUERY', query);
						  },
						wildcard: "%QUERY"
		},
		transform: function(response) {
                console.log('transform', response);
                return response.items;
        }
});
*/
//https://twitter.github.io/typeahead.js/examples/
//https://github.com/twitter/typeahead.js/blob/master/doc/jquery_typeahead.md#datasets
/*
jQuery('#prefetch .typeahead').typeahead({minLength: 3,highlight: true},{
	name: 's',
	display: 'location',
	source: product_dataset,
	limit:120,
	
	classNames: {
	  input: 'Typeahead-input',
	  hint: 'Typeahead-hint',
	  selectable: 'Typeahead-selectable'
	},
	highlight: true,
	hint:true,
	templates: {
		suggestion: Handlebars.compile('<div style="background-color:lightgrey;width:400px;z-index:-5000" class="row_hover"><strong style="width:400px;color:black"><a class="row_hover" href="#-1">{{location}}</a></strong></div>'),
		header: Handlebars.compile("<div style='background-color:lightgrey;width:400px;z-index:-5000'><b>Search results for {{query}}:'</b></div>"),
		footer: Handlebars.compile("<div style='background-color:lightgrey;width:400px;z-index:-5000'><b>---------freerangecamping.com.au---------</b></div>"),
	}
});
*/