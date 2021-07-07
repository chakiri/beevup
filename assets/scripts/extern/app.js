import $ from 'jquery';
global.$ = global.jQuery = $;

//No need to import popper.js because we installed it in node_module and bootstrap automatically call it
import 'bootstrap';

//Import JS Routing
import '../routing';

import '../../styles/extern/app.css';