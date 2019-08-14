/**
 * Test admin component.
 *
 * src/admin/components/admin-test.js
 */

// Required in our shared function.
const { upper } = require( '../../../utils/utils-index' );

const admin = {
	log( message ) {
		console.log( upper( message ) );
	}
};

module.exports = admin;
