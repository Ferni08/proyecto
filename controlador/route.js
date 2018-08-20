angular
  .module('login-module')
  .config(routes);

  function routes($routeProvider) {
    $routeProvider
    .when("/", {
      templateUrl : "vista/login-view.html",
      controller: "login-controller"
    })
    .when("/tabla", {
      templateUrl : "vista/tabla-view.html",
      controller: "tabla-controller"
    })
  }
