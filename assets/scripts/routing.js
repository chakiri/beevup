const routes = require("../../public/js/fos_js_routes");
const Routing = require("../../public/bundles/fosjsrouting/js/router"); // do not forget to dump your assets `symfony console assets:install --symlink public`

Routing.setRoutingData(routes);

// Setting Routing as global variable
global.Routing = Routing;