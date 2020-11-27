/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

import $ from 'jquery';
global.$ = global.jQuery = $;

//No need to import popper.js because we installed it in node_module and bootstrap automatically call it
import 'bootstrap';

//Import libraries
import 'slick-carousel';

//Import app JS files
import './scripts/script';
import './scripts/theme';


console.log('Hello Webpack Encore! Edit me in assets/app.js !');
