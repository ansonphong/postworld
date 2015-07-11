/*      _             _     
  _ __ | |__  _ __   (_)___ 
 | '_ \| '_ \| '_ \  | / __|
 | |_) | | | | |_) | | \__ \
 | .__/|_| |_| .__(_)/ |___/
 |_|         |_|   |__/     

///// SELECT FUNCTIONS FROM PHP.JS /////*/
// http://phpjs.org/

function array_replace(arr) {
	//  discuss at: http://phpjs.org/functions/array_replace/
	// original by: Brett Zamir (http://brett-zamir.me)
	//   example 1: array_replace(["orange", "banana", "apple", "raspberry"], {0 : "pineapple", 4 : "cherry"}, {0:"grape"});
	//   returns 1: {0: 'grape', 1: 'banana', 2: 'apple', 3: 'raspberry', 4: 'cherry'}

	var retObj = {},
		i = 0,
		p = '',
		argl = arguments.length;

	if (argl < 2) {
		throw new Error('There should be at least 2 arguments passed to array_replace()');
	}

	// Although docs state that the arguments are passed in by reference, it seems they are not altered, but rather the copy that is returned (just guessing), so we make a copy here, instead of acting on arr itself
	for (p in arr) {
		retObj[p] = arr[p];
	}

	for (i = 1; i < argl; i++) {
		for (p in arguments[i]) {
			retObj[p] = arguments[i][p];
		}
	}
	return retObj;
}

function array_replace_recursive(arr) {
	//  discuss at: http://phpjs.org/functions/array_replace_recursive/
	// 	original by: Brett Zamir (http://brett-zamir.me)
	//  example 1: array_replace_recursive({'citrus' : ["orange"], 'berries' : ["blackberry", "raspberry"]}, {'citrus' : ['pineapple'], 'berries' : ['blueberry']});
	//  returns 1: {citrus : ['pineapple'], berries : ['blueberry', 'raspberry']}

	var retObj = {},
		i = 0,
		p = '',
		argl = arguments.length;

	if (argl < 2) {
		throw new Error('There should be at least 2 arguments passed to array_replace_recursive()');
	}

	// Although docs state that the arguments are passed in by reference, it seems they are not altered, but rather the copy that is returned (just guessing), so we make a copy here, instead of acting on arr itself
	for (p in arr) {
		retObj[p] = arr[p];
	}

	for (i = 1; i < argl; i++) {
		for (p in arguments[i]) {
			if ( retObj[p] && typeof retObj[p] === 'object' ) {
				retObj[p] = array_replace_recursive(retObj[p], arguments[i][p]);
			} else {
				retObj[p] = arguments[i][p];
			}
		}
	}
	return retObj;
}

function is_numeric(mixed_var) {
  //  discuss at: http://phpjs.org/functions/is_numeric/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: David
  // improved by: taith
  // bugfixed by: Tim de Koning
  // bugfixed by: WebDevHobo (http://webdevhobo.blogspot.com/)
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  // bugfixed by: Denis Chenu (http://shnoulle.net)
  //   example 1: is_numeric(186.31);
  //   returns 1: true
  //   example 2: is_numeric('Kevin van Zonneveld');
  //   returns 2: false
  //   example 3: is_numeric(' +186.31e2');
  //   returns 3: true
  //   example 4: is_numeric('');
  //   returns 4: false
  //   example 5: is_numeric([]);
  //   returns 5: false
  //   example 6: is_numeric('1 ');
  //   returns 6: false

  var whitespace =
    " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
  return (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -
    1)) && mixed_var !== '' && !isNaN(mixed_var);
}