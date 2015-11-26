<?php
	$instance = pw_random_string();
	$proportions = apply_filters( 'pw_select_proportion', array(
			array(
				'value' => false,
				'name' => 'Flexible',
				),
			array(
				'value' => 2,
				'name' => '2 : 1',
				),
			array(
				'value' => 2.5,
				'name' => '2.5 : 1',
				),
			array(
				'value' => 3,
				'name' => '3 : 1',
				),
			array(
				'value' => 3.5,
				'name' => '3.5 : 1',
				),
			array(
				'value' => 4,
				'name' => '4 : 1',
				),
			) );
?>
<script>
	postworld.controller('<?php echo $instance ?>', function($scope){
		$scope.options<?php echo $instance ?> = <?php echo json_encode($proportions) ?>;
	});
</script>
<div ng-controller="<?php echo $instance ?>">
	<label class="inner" for="select-proportion">
		<i class="pwi-square-thin"></i>
		Proportion
	</label>
	<select
		class="labeled"
		id="select-proportion"
		ng-model="<?php echo $vars['ng_model']; ?>"
		ng-options="option.value as option.name for option in options<?php echo $instance ?>">
	</select>
</div>