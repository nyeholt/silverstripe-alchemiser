<div id="$ID" data-analyse-link="$Link(analyse)">
	<% if IsReadonly %>
	<% else %>
		<h2>Extract Metadata</h2>
		<p>
			Click the link below to send the document content to Alchemy to
			extract a category, keywords and entities from the content. You can
			then select the relevant information to save as metadata.
		</p>
		<p><a href="$Link(analyse)" class="alchemy-analyse">Analyze Content</a></p>
	<% end_if %>
	<% control Children %>
		$FieldHolder
	<% end_control %>
</div>