
////////// EDIT FIELD DIRECTIVE //////////
postworld.directive( 'editField', ['$compile', function($compile, $scope){

    return { 
        restrict: 'A',
        scope : function(){
            // Scope functions here
        },
        //template : '',
        link : function (scope, elem, attrs){

            ////////// PARSE INPUT FIELDS //////////

            // OBJECT : Define the object which has with default values
            
            if (attrs.object)
                var object = attrs.object;
            else
                var object = 'edit_fields'; // Default object name : window['edit_fields']
            
            // FIELD : Define the field which is being edited
            var field = attrs.editField;

            ////////// TEXT INPUTS //////////
            if ( isInArray('input-', attrs.input) ){
                var input_text_fields = ['text','password','hidden','url'];
                var input_extension = attrs.input.replace("input-", ""); // strip "input-"

                if ( isInArray(input_extension, input_text_fields) ){

                    ///// PLACEHOLDER /////
                    if(attrs.placeholder)
                        var placeholder = attrs.placeholder;
                    else
                        var placeholder = '';

                    // Set Original Value : oValue
                    if( attrs.value )
                        var oValue = attrs.value;
                    else if ( window[object] )
                        var oValue = window[object][field];
                    else
                        var oValue = '';

                    // Generate HTML
                    var input_html = "<input type='" + input_extension + "' name='" + attrs.editField + "' id='" + attrs.editField + "' class='" + attrs.editField + "' value='"+ oValue +"' placeholder='"+placeholder+"'>";
                    var input_element = angular.element( input_html );
                    elem.append( input_element );
                    //$compile( input_element )( scope );
                }
            }


            ////////// TEXT AREA //////////
            if ( isInArray('textarea', attrs.input) ){

                // Placeholder
                if(attrs.placeholder)
                    var placeholder = attrs.placeholder;
                else
                    var placeholder = 'Placeholder';

                // Wrap
                if(attrs.wrap)
                    var wrap = attrs.wrap;
                else
                    var wrap = '';

                // Set Original Value : oValue
                if( attrs.value )
                    var oValue = attrs.value;
                else if ( window[object] )
                    var oValue = window[object][field];
                else
                    var oValue = '';

                // Generate HTML
                var input_html = "<textarea name='" + attrs.editField + "' id='" + attrs.editField + "' class='" + attrs.editField + "' placeholder='"+placeholder+"' " + wrap + ">"+oValue+"</textarea>";
                var input_element = angular.element( input_html );
                elem.append( input_element );
                //$compile( input_element )( scope );
                
            }

            ////////// SELECT / MULTIPLE SELECT //////////
            if ( isInArray( 'select', attrs.input ) ){

                // Check for "-multiple" extension
                var input_extension = attrs.input.replace("select-", ""); // strip "input-"
                if (input_extension == 'multiple'){
                    var multiple = ' multiple ';
                    
                    // Split the value attribute into an Array
                    if ( typeof attrs.value !== 'undefined' )
                        var oValue = attrs.value.split(',');
                }
                else{
                    var multiple = '';
                    // If DATA-VALUE attribute is defined 
                    if ( !isEmpty(attrs.value) )
                        var oValue = attrs.value;
                    // Otherwise, use the value coorosponding to the key equal to the edit-field value
                    else
                        var oValue = window[object][attrs.editField];
                }
                
                // Get the size of the select area
                if ( attrs.size )
                    var size = " size='"+attrs.size+"' ";
                else
                    var size = '';

                ///// TAXONOMY OPTIONS /////
                // Process Taxonomy Edit Field
                if ( isInArray( 'taxonomy', attrs.editField ) ){
                    // Get the name of the requested taxonomy
                    var tax_name = extract_parentheses( attrs.editField );
                    var terms = window['taxonomy'][tax_name];
                    var selected = window[object].taxonomy[tax_name];
                    var select_items = parse_hierarchical_select_items( terms, selected, 'slug', 'name', 'terms', 2, '- ' ); // window[object].taxonomy[tax_name]
                }

                ///// PROVIDED OPTIONS /////
                // Use provided options
                else if(attrs.options){
                    var options = window[attrs.options];
                    var selected = oValue;
                    var select_items = parse_linear_select_items( options, selected );
                }

                ///// DEFAULT OPTIONS /////
                // Process Standard Edit Fields
                else{
                    var select_options = window[object];
                    var selected = oValue;
                    var select_items = parse_linear_select_items( select_options[field], selected );
                }                   

                // Parse the HTML
                var select_head = "<select id='"+attrs.editField+"' name='"+attrs.editField+"' " + multiple + " " + size + " >";
                var select_foot = "</select>";

                //if ( typeof select_items === 'undefined' ){
                //}

                var input_element = angular.element( select_head + select_items + select_foot );
                elem.append( input_element );
                //$compile( input_element )( scope );
                
            }

        }
    }
}]);
