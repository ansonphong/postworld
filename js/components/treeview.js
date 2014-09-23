'use strict';

postworld.directive('yaTree', function () {
  return {
    restrict: 'A',
    transclude: 'element',
    priority: 1000,
    terminal: true,
    compile: function (tElement, tAttrs, transclude) {
      var repeatExpr, childExpr, rootExpr, childrenExpr,branchExpr;

      repeatExpr = tAttrs.yaTree.match(/^(.*) in ((?:.*\.)?(.*)) at (.*)$/);
      childExpr = repeatExpr[1]; 	// child
      rootExpr = repeatExpr[2];		// data.children [data4 or whatever the scope variable holding the data is]
      childrenExpr = repeatExpr[3];	// children, the name of the children attribute
      branchExpr = repeatExpr[4];	// ol or ul - the name of the element holding the tree 

      return function link (scope, element, attrs) {

        var rootElement = element[0].parentNode,	// this is the actual root <ol or ul>
            cache = [];

        // Reverse lookup object to avoid re-rendering elements
        function lookup (child) {
          var i = cache.length;
          while (i--) {
            if (cache[i].scope[childExpr] === child) {
              return cache.splice(i, 1)[0];
            }
          }
        };
        
        function treeChange(root) {
		    // console.log('inserting...');
        	if (!scope.commentsLoaded) return;
        	root = scope.treedata.children;
          var currentCache = [];
	      // console.log('rootExpr changed...', rootExpr, root.length);

          // Recurse the data structure
          (function walk (children, parentNode, parentScope, depth) {
          		// ^^ root, rootElement, scope, 0
          		// ^^ root is the value of data.children.
          		// ^^ rootElement is the OL/UL element
          		// ^^ scope is the scope, and depth starts with zero for recursion

            var i = 0,
                n = children.length,
                last = n - 1,
                cursor,
                child,
                cached,
                childScope,
                grandchildren;

            // Iterate the children at the current level
            for (; i < n; ++i) {

              // We will compare the cached element to the element in 
              // at the destination index. If it does not match, then 
              // the cached element is being moved into this position.
              cursor = parentNode.childNodes[i]; // cursor looks at each element childn LI of the parentNode, starting with rootElement

              child = children[i]; // child looks at the structure tree for child equivalent to the cursor [in the scope.data tree structure]

              // See if this child has been previously rendered
              // using a reverse lookup by object reference
              cached = lookup(child); // get the elemen associated with this child, and at the same time, if it is found, then remove it from cache, only the elements that will be deleted will remain in cache
              
              // If the parentScope no longer matches, we've moved.
              // We'll have to transclude again so that scopes 
              // and controllers are properly inherited
              if (cached && cached.parentScope !== parentScope) { // if it exists and exists in a different scope, then it must have moved somewhere else
                cache.push(cached); // add it to cache array, coz the cache array will have all of its elements deleted, it should have been named DeleteCache
                cached = null;
              }
              
              // If it has not, render a new element and prepare its scope
              // We also cache a reference to its branch node which will
              // be used as the parentNode in the next level of recursion
              if (!cached) {
                transclude(parentScope.$new(), function (clone, childScope) { // this will create the actual element of child

                  childScope[childExpr] = child; // set the scope equal to child
                  
                  // create a cached object
                  cached = {
                    scope: childScope,
                    parentScope: parentScope,
                    element: clone[0],
                    branch: clone.find(branchExpr)[0]
                  };

                  // This had to happen during transclusion so inherited 
                  // controllers, among other things, work properly
                  parentNode.insertBefore(cached.element, cursor);
                  // console.log('adding element');
                });
              } else if (cached.element !== cursor) {
                parentNode.insertBefore(cached.element, cursor);
              }

              // Lets's set some scope values
              childScope = cached.scope;

              // Store the current depth on the scope in case you want 
              // to use it (for good or evil, no judgment).
              childScope.$depth = depth;
              
              // Emulate some ng-repeat values
              childScope.$index = i;
              childScope.$first = (i === 0);
              childScope.$last = (i === last);
              childScope.$middle = !(childScope.$first || childScope.$last);

              // Push the object onto the new cache which will replace
              // the old cache at the end of the walk.
              currentCache.push(cached); // this will be the new cache

              // If the child has children of its own, recurse 'em.             
              grandchildren = child[childrenExpr];
              if (grandchildren && grandchildren.length) {
                walk(grandchildren, cached.branch, childScope, depth + 1);
              }
            }
             // var endTime =  new Date().getTime();
		     // scope.endTime = endTime;

          })(root, rootElement, scope, 0);

			var i;
          // Cleanup objects which have been removed.
          // Remove DOM elements and destroy scopes to prevent memory leaks.
          i = cache.length; // this is the old cache that should be cleaned up

			
          while (i--) {
            var cached = cache[i];
            if (cached.scope) {
              cached.scope.$destroy();
            }
            if (cached.element) {
              cached.element.parentNode.removeChild(cached.element);
            } 
          }

          // Replace previous cache.
          cache = currentCache; // create the new cache
		  // console.log('end of inserting...');
		  // scope.$apply();
        };
        // scope.$watch(rootExpr, treeChange, true);
        scope.$watch('treeUpdated', treeChange, true);
      };
    }
  };
});
