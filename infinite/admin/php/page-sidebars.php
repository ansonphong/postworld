<?php
	$iSidebars = i_get_option( array( 'option_name' => 'i-sidebars' ) );
?>
<script>
	infinite.controller( 'pwSidebarsDataCtrl', [ '$scope', function( $scope ){
		$scope.iSidebars = <?php echo json_encode( $iSidebars ); ?>;
	}]);
</script>

<div id="infinite_admin" ng-app="infinite" class="postworld sidebars wrap">
	<div
		i-admin
		i-admin-sidebars
		ng-controller="pwSidebarsDataCtrl"
		ng-cloak>

		<h2>
			<i class="icon-map"></i>
			Sidebars
			<button class="add-new-h2" ng-click="newSidebar()">Add New Sidebar</button>
		</h2>
		
		<hr class="thick">

		<div class="pw-row">

			<!-- ///// ITEMS MENU ///// -->
			<div class="pw-col-3">
				<ul class="list-menu">
					<li
						ng-repeat="item in iSidebars"
						ng-click="selectItem(item)"
						ng-class="menuClass(item)">
						{{ item.name }}
					</li>
				</ul>
				<div class="space-6"></div>
			</div>


			<!-- ///// EDIT SETTINGS ///// -->
			<div class="pw-col-9">
				<div ng-show="showView('editItem')">

					<h3><i class="icon-gear"></i> Sidebar Settings</h3>

					<div class="pw-row">
						<div class="pw-col-6">
							<label
								for="item-name"
								class="inner"
								tooltip="<?php echo $i_language['sidebars']['name_info']; ?>"
								tooltip-popup-delay="333">
								<?php echo $i_language['sidebars']['name']; ?>
								<i class="icon-info-circle"></i>
							</label>
							<input
								id="item-name"
								class="labeled"
								type="text"
								ng-model="selectedItem.name">
						</div>
						<div class="pw-col-6">
							<label
								for="item-id"
								class="inner"
								tooltip="<?php echo $i_language['sidebars']['id_info']; ?>"
								tooltip-popup-delay="333">
								<?php echo $i_language['sidebars']['id']; ?>
								<i class="icon-info-circle"></i>
							</label>
							<button
								class="inner inner-bottom-right inner-controls"
								ng-click="enableInput('#item-id');focusInput('#item-id')"
								tooltip="Editing the ID may cause instances of the feed to disappear"
								tooltip-placement="left"
								tooltip-popup-delay="333">
								<i class="icon-edit"></i>
							</button>
							<input
								id="item-id"
								class="labeled"
								type="text"
								ng-model="selectedItem.id"
								disabled
								ng-blur="disableInput('#item-id')">
						</div>
					</div>






					<div
						style="border-top:2px solid #ccc; margin:10px; padding:10px;"
						class="sidebar" >

						<!-- DELETE BUTTON -->
						<button
							class="button deletion"
							ng-click="deleteItem(selectedItem,'iSidebars')">
							<i class="icon-close"></i>
							Delete Sidebar
						</button>



						<div class="form-field">
							<label for="tag-id"><b><?php echo $i_language['sidebars']['description']; ?></b></label>
							<p><?php echo $i_language['sidebars']['description_info']; ?></p>
							<input ng-model="selectedItem.description">
						</div>

						<div class="form-field">
							<label for="tag-id"><b><?php echo $i_language['sidebars']['class']; ?></b></label>
							<p><?php echo $i_language['sidebars']['class_info']; ?></p>
							<input ng-model="selectedItem.class">
						</div>

						<div class="form-field">
							<label for="tag-id"><b><?php echo $i_language['sidebars']['before_widget']; ?></b></label>
							<p><?php echo $i_language['sidebars']['before_widget_info']; ?></p>
							<textarea ng-model="selectedItem.before_widget"></textarea>
						</div>

						<div class="form-field">
							<label for="tag-id"><b><?php echo $i_language['sidebars']['after_widget']; ?></b></label>
							<p><?php echo $i_language['sidebars']['after_widget_info']; ?></p>
							<textarea ng-model="selectedItem.after_widget"></textarea>
						</div>

						<div class="form-field">
							<label for="tag-id"><b><?php echo $i_language['sidebars']['before_title']; ?></b></label>
							<p><?php echo $i_language['sidebars']['before_title_info']; ?></p>
							<textarea ng-model="selectedItem.before_title"></textarea>
						</div>

						<div class="form-field">
							<label for="tag-id"><b><?php echo $i_language['sidebars']['after_title']; ?></b></label>
							<p><?php echo $i_language['sidebars']['after_title_info']; ?></p>
							<textarea ng-model="selectedItem.after_title"></textarea>
						</div>

					</div>









				</div>
			</div>

			<hr class="thick">






		</div>




		<hr class="thick">


		<div class="sidebars form-wrap">
			
			

		</div>


		<hr>
		<pre>{{ iSidebars | json }}</pre>

	</div>
</div>