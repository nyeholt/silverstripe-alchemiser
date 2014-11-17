<div class="alchemy-analyse">
	<p>
		Alchemy has extracted the following metadata from the document. Choose
		the metadata you wish to apply, then click "Apply" and the metadata fields
		will be updated with those values. Then save the document to save the
		applied metadata.
	</p>

	<% loop Changes %>

		<% if Type == 'array' %>
		<h3>$Title</h3>

		<div class="$Title added-removed">
			<% if $AddInfo %>
			<div class="added">
				<h5>Added $Title</h5>
				<ul>
					<% loop $AddInfo %>
						<li>
							<input id="alchemy-add-$Up.Title-$Pos" class="alchemy-add-$Up.Title" data-$Up.Title="$Name.ATT" type="checkbox" checked="checked" value="1">
							<label for="alchemy-add-$Up.Title-$Pos">$Name</label>
						</li>
					<% end_loop %>
				</ul>
			</div>
			<% end_if %>

			<% if $RemoveInfo %>
			<div class="removed">
				<h5>Removed $Title</h5>
					<ul>
						<% loop $RemoveInfo %>
							<li>
								<input id="alchemy-rm-$Up.Title-$Pos" class="alchemy-rm-$Up.Title" data-$Up.Title="$Name.ATT" type="checkbox" checked="checked" value="1">
								<label for="alchemy-rm-$Up.Title-$Pos">$Name</label>
							</li>
						<% end_loop %>
					</ul>
				
			</div>
			<% end_if %>
		</div>
		
		<% else %>
		
		<h3>$Title</h3>
		<p>
			<input id="alchemy-change-$Title" type="checkbox" checked="checked" value="1" data-$Title="$AddInfo.ATT">
			<label for="alchemy-change-$Title">Change $Title from "$RemoveInfo" to "$AddInfo".</label>
		</p>
		<% end_if %>

	<% end_loop %>
</div>