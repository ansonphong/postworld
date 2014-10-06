<div id="infinite_admin" ng-app="infinite" class="sidebars">
	<h1>
		<i class="icon-th-large"></i>
		<?php echo $theme_admin['sidebars']['page_title']; ?>
	</h1>


	<script>
		var sidebarsDataCtrl = function( $scope ){
			$scope.sidebars = <?php
				$i_sidebars = get_option('i-sidebars');
				if( empty($i_sidebars) )
					$i_sidebars = json_encode(array());
				echo $i_sidebars;
				?>;
		}
	</script>

	<div
		i-admin-sidebars
		ng-controller="sidebarsDataCtrl"
		ng-cloak>

		<!-- SAVE BUTTON -->
		<button i-save-option ng-click="saveOption('i-sidebars','sidebars')" class="button button-primary">
			<span ng-show="status != 'saving'"><i class="icon-save"></i> Save</span>
			<span ng-show="status == 'saving'"><i class="icon-spinner icon-spin"></i> Save</span>
		</button>
		<hr>

		<button ng-click="newSidebar()" class="button button-secondary"> + <?php echo $i_language['sidebars']['add_new']; ?> </button>

		<div class="sidebars form-wrap">
			
			<div
				style="border-top:2px solid #ccc; margin:10px; padding:10px;"
				ng-repeat = "sidebar in sidebars track by $index"
				class = "sidebar" >

				<h3>{{sidebar.name}}</h3>
				<button ng-click="removeSidebar(sidebar)" class="button button-secondary"> - Remove Sidebar </button>


				<div class="form-field">
					<label for="tag-id"><b><?php echo $i_language['sidebars']['name']; ?></b></label>
					<p><?php echo $i_language['sidebars']['name_info']; ?></p>
					<input ng-model="sidebar.name">
				</div>

				<div class="form-field">
					<label for="tag-id"><b><?php echo $i_language['sidebars']['id']; ?></b></label>
					<p><?php echo $i_language['sidebars']['id_info']; ?></p>
					<input ng-model="sidebar.id">
				</div>

				<div class="form-field">
					<label for="tag-id"><b><?php echo $i_language['sidebars']['description']; ?></b></label>
					<p><?php echo $i_language['sidebars']['description_info']; ?></p>
					<input ng-model="sidebar.description">
				</div>

				<div class="form-field">
					<label for="tag-id"><b><?php echo $i_language['sidebars']['class']; ?></b></label>
					<p><?php echo $i_language['sidebars']['class_info']; ?></p>
					<input ng-model="sidebar.class">
				</div>

				<div class="form-field">
					<label for="tag-id"><b><?php echo $i_language['sidebars']['before_widget']; ?></b></label>
					<p><?php echo $i_language['sidebars']['before_widget_info']; ?></p>
					<textarea ng-model="sidebar.before_widget"></textarea>
				</div>

				<div class="form-field">
					<label for="tag-id"><b><?php echo $i_language['sidebars']['after_widget']; ?></b></label>
					<p><?php echo $i_language['sidebars']['after_widget_info']; ?></p>
					<textarea ng-model="sidebar.after_widget"></textarea>
				</div>

				<div class="form-field">
					<label for="tag-id"><b><?php echo $i_language['sidebars']['before_title']; ?></b></label>
					<p><?php echo $i_language['sidebars']['before_title_info']; ?></p>
					<textarea ng-model="sidebar.before_title"></textarea>
				</div>

				<div class="form-field">
					<label for="tag-id"><b><?php echo $i_language['sidebars']['after_title']; ?></b></label>
					<p><?php echo $i_language['sidebars']['after_title_info']; ?></p>
					<textarea ng-model="sidebar.after_title"></textarea>
				</div>

			</div>

		</div>


		<hr>
		<pre>{{ sidebars | json }}</pre>

	</div>
</div>