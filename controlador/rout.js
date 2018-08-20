angular
  .module('tabla-module')
  .config(routes);

  function routes($routeProvider) {
    $routeProvider
    .when("/tabla", {
      templateUrl : "vista/tabla-view.html",
      controller: "tabla-controller"
    })
  }
