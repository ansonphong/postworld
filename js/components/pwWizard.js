/*_        ___                  _ 
 \ \      / (_)______ _ _ __ __| |
  \ \ /\ / /| |_  / _` | '__/ _` |
   \ V  V / | |/ / (_| | | | (_| |
    \_/\_/  |_/___\__,_|_|  \__,_|
                                  																		
//////// ---- WIZARD ---- ////////*/

postworld.directive( 'pwWizard', [ function($scope){
	return {
		restrict: 'A',
		controller: 'pwWizardCtrl',
		link: function( $scope, element, attrs ){
			
			$scope.wizardState = {};

			// WIZARD Attribute
			attrs.$observe('wizardName', function(value) {
				if (value) {
					$scope.wizardState['wizardName'] = value;
				}
			});

		}
	};
}]);

postworld.controller('pwWizardCtrl',
	['$scope', '$rootScope', '$window', '$timeout', 'ext', 'pwData', '$log','$modal','pwWizardData',
	function($scope, $rootScope, $window, $timeout, ext, $pwData, $log, $modal, $pwWizardData) {

	// Localize the current view from PW Globals
	var pwGlobals = ( !_.isUndefined( $window.pwGlobals ) ) ?
		$window.pwGlobals :
		{};

	// Setup Wizard Function
	$scope.setWizard = function(){
		// Set the main wizards data set
		$scope.wizards = $pwWizardData.wizards('en');

		// Set Default Wizard Status (while waiting for AJAX)
		$scope.wizardStatus = $pwWizardData.wizardStatusModel();
	};

	// Setup Wizard
	$scope.setWizard();

	// Update Wizard Language on Detect Language Change
	$scope.$watch('lang', function() {
		$scope.setWizard();
	});

	///// SETUP WIZARD STATE /////
	$scope.wizardState = {};
	// Initialize after 1ms timeout
	$timeout(function() {
		// If the wizard-name attribute is set
		if( !_.isUndefined($scope.wizardState.wizardName ) ){
			var wizardName = $scope.wizardState.wizardName;
			// If the specified wizard name exists as a wizard
			if( !_.isUndefined( $scope.wizards[wizardName] ) ){
				// If the selected wizard is initialized
				if( $scope.wizardState['initialized'] != true ){
					// Set initialized true avoid multiple initializations
					$scope.wizardState['initialized'] = true;
					// Continue the wizard where we left off
					$scope.initWizard( wizardName, 'continue' );
					// Detect the current stage
					var currentStage = $scope.getCurrentStage();
					// Set the current stage object
					$scope.wizardState['currentStage'] = currentStage; 

				}
			}
		}
    }, 1);

	///// WATCH WIZARD STATUS /////
	$scope.$watch('wizardStatus', function() {
		// Set a boolean if the current stage is completed
		if( !_.isUndefined( $scope.wizardState ) &&
			!_.isEmpty( $scope.wizardState.currentStage ))
			$scope.wizardState['stageComplete'] = $scope.isStageComplete($scope.wizardState.currentStage.id);
	});

	//////////// INITIALIZE WIZARD ////////////
	$scope.initWizard = function( wizardName, action ){
		
		// If requested Wizard does not exist
		if( _.isUndefined( $scope.wizards[wizardName] ) )
			return false;
	
		///// Wizard State /////
		// Set the specified wizard as the current wizard
		$scope.wizardState['currentWizard'] = $scope.wizards[wizardName];

		// Set the name of the wizard
		$scope.wizardState['wizardName'] = wizardName;

		// Set the status of the wizard
		$scope.wizardState['status'] = "loading";

		///// Wizard Status /////
		// Check the user's status of the wizard
		var vars = {
			'wizard_name' : wizardName
		};
		$pwData.get_wizard_status( vars ).then(
			// Success
			function(response) {    
				//alert( JSON.stringify( response.data ) );
				var wizardStatus = response.data;

				// If the Wizard has not been started
				if( _.isEmpty( wizardStatus )  )
					// Set the empty status object model
					wizardStatus = $scope.wizardStatusModel;

				// Set the Status
				$scope.wizardStatus = wizardStatus;

				// Set the finished loading status of the wizard
				$scope.wizardState['status'] = "done";
				$scope.wizardState['initAjax'] = true;

				// START Action
				// Send them to the next incomplete stage
				if( action == 'start' )
					$scope.runWizard( 'start' );

			},
			// Failure
			function(response) {
				$log.debug("getWizardStatus : ERROR : ", response);
			}
		);

	};


	//////////// WIZARD STAGE COMPLETE ////////////
	$scope.runWizard = function( action, wizardName ){
		// Mark the current stage of the wizard complete, if on a stage
		// And jump ahead to the next stage

		// If Wizard Name is not defined, find it
		if( _.isUndefined( wizardName ) &&
			!_.isUndefined( $scope.wizardState ) &&
			!_.isUndefined( $scope.wizardState.wizardName ) )
			var wizardName = $scope.wizardState['wizardName'];
		else
			return false;

		// Require scope.wizardStatus
		if( _.isUndefined($scope.wizardStatus) )
			return false;

		// Set the current stage complete
		if(
			action == 'next' &&
			// wizardStatus exists
			!_.isUndefined( $scope.wizardStatus ) &&
			// wizardState exists
			!_.isUndefined( $scope.wizardState ) &&
			// currentStage is defined
			!_.isEmpty( $scope.wizardState.currentStage ) &&
			// currentStage ID is not already in the array of completed stages
			!ext.isInArray( $scope.wizardState.currentStage.id, $scope.wizardStatus.completed ) ){
				// Push the stage as complete
				$scope.wizardStatus['completed'].push( $scope.wizardState.currentStage.id );
			}

		// Define local vars
		var wizardStatus = $scope.wizardStatus;
		var wizardState = $scope.wizardState;

		// Set the the Wizard status in the DB
		var vars = {
			'wizard_name' : wizardName,
			'value' : wizardStatus
		};

		// Insulate from 2-way Data Binding
		var JSON = angular.toJson(vars);
		vars = angular.fromJson(JSON);

		// Check the next stage
		var nextStage = $scope.getNextIncompleteStage( wizardState, wizardStatus );

		if( nextStage != 'complete' ){
			// Set the Wizard to Active
			vars['value'].active = true;
			// Set the Wizard to Visible
			vars['value'].visible = true;
		}

		///// SET THE STATUS IN THE DB /////
		$pwData.set_wizard_status( vars ).then(
			// Success
			function(response) {
				// Then go to the next stage
				$scope.gotoNextStage();
			
			},
			// Failure
			function(response) {
				$log.debug(response);
			}
		);


	};

	$scope.gotoNextStage = function(){

		// Define local vars
		var wizardStatus = $scope.wizardStatus;
		var wizardState = $scope.wizardState;

		///// GO TO NEXT STAGE /////
		// Get the next stage
		var nextStage = $scope.getNextIncompleteStage( wizardState, wizardStatus );
		//alert( wizardState.wizardName + " // Next Incomplete Stage : " + JSON.stringify( nextStage ) );
		
		// If complete, send to the completed URL
		if( nextStage == 'complete' ){
			// Deactivate the current Wizard
			$scope.deactivateWizard();

			// Go to the custom complete URL
			if( !_.isUndefined( wizardState.currentWizard['completed-url'] ) )
				$window.location = wizardState.currentWizard['completed-url'];
			
			return true;
		}
			

		// If the next stage has a URL, go there
		if( !_.isUndefined(nextStage) &&
			!_.isUndefined(nextStage.url) )
			$window.location = nextStage.url;

	};


	$scope.getWizardStatus = function( wizardName ){
		// Generic function used to fetch the status of a wizard

		var vars = {
			'wizard_name' : wizardName
		};
		$pwData.get_wizard_status( vars ).then(
			// Success
			function(response) {    

			},
			// Failure
			function(response) {
				$log.debug("getWizardStatus : ERROR : ", response);
			}
		);
	};

	$scope.setWizardStatus = function( wizardName, wizardStatus ){
		// This is a generic AJAX function which
		// Saves the wizard status to the database 

		var vars = {
			'wizard_name' : wizardName,
			'value' : wizardStatus
		};
		$pwData.set_wizard_status( vars ).then(
			// Success
			function(response) {    
				//alert( JSON.stringify( response.data ) );
			},
			// Failure
			function(response) {
				console.log(response);
			}
		);
	};

	$scope.getCurrentStage = function(){
		// Check which stage we're on
		// And return with the object

		// Get post_name
		if( pwGlobals != {} )
			var post_name = pwGlobals.current_view.post.post_name;
		else
			return false;

		// Cycle through all wizard stages
		var wizardName = $scope.wizardState.wizardName;
		var currentWizardStage = {};

		// Iterate through the stages
		angular.forEach( $scope.wizards[wizardName].stages,
			function( wizardStage ){
			// If the current stage matches the post_name
			if( wizardStage.post_name == post_name )
				// This is the current stage
				currentWizardStage = wizardStage;
			
		});

		return currentWizardStage;

	};

	$scope.isOnStage = function(){
		return ( !_.isEmpty( $scope.getCurrentStage() ) ) ?
			true : false ;
	}

	$scope.isOnLastStage = function(){
		// Check if the state is currently on the last stage of the wizard
		// By checking ig the current stage 'order' values is equal to the number of stages

		// Get the current stage object
		if( !_.isUndefined( $scope.wizardState ) &&
			!_.isUndefined( $scope.wizardState.currentStage ) &&
			!_.isUndefined( $scope.wizardState.currentStage.order ))
			var currentStageOrder = parseInt( $scope.wizardState.currentStage.order );
		else
			return false;

		// Get the current stage object
		if( !_.isUndefined( $scope.wizardState.currentWizard ) &&
			!_.isUndefined( $scope.wizardState.currentWizard.stages &&
			_.isArray( $scope.wizardState.currentWizard.stages ) ))
			var currentWizardLength = $scope.wizardState.currentWizard.stages.length;
		else
			return false;

		return ( currentStageOrder == currentWizardLength ) ?
			true : false ;
	};

	$scope.isOnNotLastStage = function(){
		if ( $scope.isOnLastStage() )
			return false;
		if ( $scope.isOnStage() )
			return true;
		else
			return false;

	};

	$scope.getIncompleteStages = function( wizardState, wizardStatus ){
		// Compares the current Wizard (global) with the Wizard Status (user)
		// Returns with an object of the incomplete stages
		// Which is the stages that the user has not completed

		// Local Vars
		var wizardStages = wizardState.currentWizard.stages;
		var incompleteStages = {};
		var incompleteStagesKeys = [];

		// Compare Status to State
		// Get the incomplete stages
		angular.forEach( wizardStages, function( wizardStage ){
			// If the stage has not been completed
			if(  !ext.isInArray( wizardStage.id, wizardStatus.completed )  ){
				// Add it to the object, with the order number as the key
				var order = parseInt(wizardStage.order);
				incompleteStages[ order ] = wizardStage;
				// Push the stage.id to the stages keys array
				incompleteStagesKeys.push( wizardStage.id );
			}
		});
		
		// If there are no incomplete stages, return
		if( incompleteStagesKeys.length == 0 ){
			return "complete";
		}

		// Setup the model
		var incomplete = {
			stages: incompleteStages,
			keys: incompleteStagesKeys
		}
		return incomplete;

	};

	$scope.getNextIncompleteStage = function( wizardState, wizardStatus ){
		// Gets the next incomplete stage in the wizard
		// By comparing the Wizard Status object to the current wizard
		// With respect to the 'stage.order' value
		
		var nextStage = {};

		// Call the incompleteStages function
		var incomplete = $scope.getIncompleteStages( wizardState, wizardStatus );

		// If we're complete
		if( incomplete == 'complete' )
			return 'complete';

		// Get the number of stages
		var len = wizardState.currentWizard.stages.length;

		// Get the incomplete stage with the lowest order number
		// Iterate through 0-len
		for (i = 0; i <= len; i++){
			// Starting from 0 up, see if a key with that value exists
			if( !_.isUndefined( incomplete.stages[i] ) ){
				// Get the first result
				var nextStage = incomplete.stages[i];
				// Break from the `for`
				break;
			}
		}
		//alert( JSON.stringify(nextStage) );
		// Return with the result
		return nextStage;
			
	};

	$scope.isStageComplete = function( stageId ){
		// Check if the given stage is complete
		var wizardStatus = $scope.wizardStatus;
		return ext.isInArray( stageId, wizardStatus.completed );

	};

	$scope.wizardStageClass = function( stageId, postName ){
		// Sets the CSS classes in the DOM, applied to the wizard stages
		// Used in conjuntion with ng-class inside ng-repeat
		// ie. ng-class="wizardStageClass(stage.id, stage.post_name)"

		// Setup String
		var classes = '';
		// Active (we are on the page)
		if( postName == pwGlobals.current_view.post.post_name )
			classes = classes + 'active ';
		// Complete
		if( $scope.isStageComplete( stageId ) )
			classes = classes + 'completed ';
		// Required : TODO

		// Return with the string of classes
		return classes;

	};

	$scope.wizardWrapperClasses = function(){
		// Sets the CSS classes in the DOM, applied to the wizard wrapper
		// Used in conjuntion with ng-class
		// ie. ng-class="wizardWrapperClasses()"

		// Setup String
		var wrapperClasses = '';
		// Visible
		if( $scope.wizardStatus['visible'] == true )
			wrapperClasses = wrapperClasses + 'visible ';
		// Active
		if( $scope.wizardStatus['active'] == true )
			wrapperClasses = wrapperClasses + 'active ';
		// Initialized
		if( $scope.wizardState['initAjax'] == true )
			wrapperClasses = wrapperClasses + 'initialized ';
		return wrapperClasses;

	};

	$scope.toggleWizardVisible = function(){
		// Toggles the 'visible' attribute of the wizardStatus
		// And saves the current status to the DB

		// Toggle visible
		$scope.wizardStatus['visible'] =
			( $scope.wizardStatus['visible'] == true ) ?
			false : true;

		// Save wizardStatus to the DB via AJAX
		$scope.setWizardStatus(
			$scope.wizardState.wizardName, 	// Wizard Name
			$scope.wizardStatus 			// Status Object
			);
	
	};

	$scope.deactivateWizard = function(){
		// Sets wizardStatus 'visible' and 'active' attributes to false
		// And saves the current status to the DB

		// Shut it down
		$scope.wizardStatus['visible'] = false;
		$scope.wizardStatus['active'] = false;

		// Save wizardStatus to the DB via AJAX
		$scope.setWizardStatus(
			$scope.wizardState.wizardName, 	// Wizard Name
			$scope.wizardStatus 			// Status Object
			);
	};

	$scope.isWizardVisible = function(){
		// Returns with a boolean, if the status of the wizard is visible
		return ( $scope.wizardStatus['visible'] ) ?
			true : false;
	};

}]);


///// GRAVEYARD /////
/*
$scope.continueWizard = function(){
	// Define local vars
	var wizardStatus = $scope.wizardStatus;
	var wizardState = $scope.wizardState;
}
*/

/*
// Open Wizard Modal
var modalInstance = $modal.open({
	templateUrl: $pwData.pw_get_template('panels','','modal-login'),
	backdrop:false,
	windowClass:'wizard',
	//controller: ModalInstanceCtrl,
	
	resolve: {
		items: function () {
		//	return $scope.items;
		}
	}
	
});
*/
