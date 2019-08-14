/**
 * Test frontend component.
 *
 * src/front/components/front-test.js
 */

// Required in our shared function.
const { upper, isString } = require( '../../utils/utils-index' );

// Require in the last function from Lodash.
const { last } = require('lodash');

const front = {
	log( message ) {
		if ( isString( message ) ) {
			console.log( upper( message ) );
		} else {
			console.log( message );
		}
	},
	getLastArrayElement( arr ) {
		return last(arr);
	}
};

module.exports = front;
