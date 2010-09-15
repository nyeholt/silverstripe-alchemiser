<?php

// Add the alchemisable extension
// DataObject::add_extension('Page', 'Alchemisable');

// and add the alchemiser key!
// AlchemyService::set_api_key('');


/*
If using in conjunction with Solr, use the following in your page to have
facets displayed

.facetList { width: 210px; float: left; border: 1px solid #ccc; padding: 10px; margin: 4px; }
.facetList ul { list-style-type: none; }
.facetList ul li { padding: 0.5em; }

.facetCrumbs { list-style-type: none; }
.facetCrumbs li { float: left; }
.facetCrumbs li a { padding: 5px; padding-right: 24px; min-height: 20px; display: block; border: 1px solid #ccc; background: #fefefe url(../../../cms/images/delete.gif) no-repeat center right }


	   <div id="Facets">
			<div class="facetList">
				<h3>Keywords</h3>
				<ul>
				<% control Facets(AlcKeywords_ms) %>
				<li><a href="$SearchLink">$Name</a> ($Count)</li>
				<% end_control %>
				</ul>
			</div>
			<div class="facetList">
				<h3>People</h3>
				<ul>
				<% control Facets(AlcPerson_ms) %>
				<li><a href="$SearchLink">$Name</a> ($Count)</li>
				<% end_control %>
				</ul>
			</div>
			<div class="facetList">
				<h3>Companies</h3>
				<ul>
				<% control Facets(AlcCompany_ms) %>
				<li><a href="$SearchLink">$Name</a> ($Count)</li>
				<% end_control %>
				</ul>
			</div>
			<div class="facetList">
				<h3>Organisations</h3>
				<ul>
				<% control Facets(AlcOrganization_ms) %>
				<li><a href="$SearchLink">$Name</a> ($Count)</li>
				<% end_control %>
				</ul>
			</div>
		   <div class="clear"><!-- --></div>
		</div>


 */