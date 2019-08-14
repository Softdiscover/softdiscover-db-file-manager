/**
 * Frontend entry point.
 *
 * src/front/front-index.js
 */
const front = require( './components/front-test' );

front.log( 'Here is a message for the frontend!' );

// Let's test a function using Lodash.
front.log( front.getLastArrayElement( [ 1, 2, 3 ] ) ); // Should log out 3.