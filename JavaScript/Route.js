angular
  .module('login-module')
  .config(function($routeProvider) {
    $routeProvider
      .when('/', {
        templateURL: '../html/pagina.html',
        // controller: 'login-controller',
      });
  });
