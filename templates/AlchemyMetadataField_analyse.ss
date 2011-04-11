<div class="alchemy-analyse">
	<p>
		Alchemy has extracted the following metadata from the document. Choose
		the metadata you wish to apply, then click "Apply" and the metadata fields
		will be updated with those values. Then save the document to save the
		applied metadata.
	</p>

	<% if CategoryChanged %>
		<h3>Category</h3>
		<p>
			<input id="alchemy-change-category" type="checkbox" checked="checked" value="1" data-category="$NewCategory.ATT">
			<label for="alchemy-change-category">Change the category from "$OldCategory" to "$NewCategory".</label>
		</p>
	<% end_if %>

	<% if KeywordsChanged %>
		<h3>Keywords</h3>

		<div class="keywords added-removed">
			<div class="added">
				<h5>Added Keywords</h5>
				<% if KeywordsAdded %>
					<ul>
						<% control KeywordsAdded %>
							<li>
								<input id="alchemy-add-keyword-$Pos" class="alchemy-add-keyword" data-keyword="$Name.ATT" type="checkbox" checked="checked" value="1">
								<label for="alchemy-add-keyword-$Pos">$Name</label>
							</li>
						<% end_control %>
					</ul>
				<% else %>
					<p><em>No keywords added.</em></p>
				<% end_if %>
			</div>

			<div class="removed">
				<h5>Removed Keywords</h5>
				<% if KeywordsRemoved %>
					<ul>
						<% control KeywordsRemoved %>
							<li>
								<input id="alchemy-rm-keyword-$Pos" class="alchemy-rm-keyword" data-keyword="$Name.ATT" type="checkbox" checked="checked" value="1">
								<label for="alchemy-rm-keyword-$Pos">$Name</label>
							</li>
						<% end_control %>
					</ul>
				<% else %>
					<p><em>No keywords removed.</em></p>
				<% end_if %>
			</div>
		</div>
	<% end_if %>

	<% if EntitiesChanged %>
		<div class="entities">
			<h3>Entities</h3>

			<% control EntitiesChanged %>
				<div class="entity added-removed" data-field="$Name">
					<div class="added">
						<h5>Added $Title</h5>
						<% if Added %>
							<ul>
								<% control Added %>
									<li>
										<input id="alchemy-add-entity-$ParentPos-$Pos" class="alchemy-add-entity" data-entity="$Name" type="checkbox" checked="checked" value="1">
										<label for="alchemy-add-entity-$ParentPos-$Pos">$Name</label>
									</li>
								<% end_control %>
							</ul>
						<% else %>
							<p><em>No $Title added.</em></p>
						<% end_if %>
					</div>

					<div class="removed">
						<h5>Removed $Title</h5>
						<% if Removed %>
							<ul>
								<% control Removed %>
									<li>
										<input id="alchemy-rm-entity-$ParentPos-$Pos" class="alchemy-rm-entity" data-entity="$Name" type="checkbox" checked="checked" value="1">
										<label for="alchemy-rm-entity-$ParentPos-$Pos">$Name</label>
									</li>
								<% end_control %>
							</ul>
						<% else %>
							<p><em>No $Title removed.</em></p>
						<% end_if %>
					</div>
				</div>
			<% end_control %>
		</div>
	<% end_if %>
</div>