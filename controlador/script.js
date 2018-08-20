angular
.module('login-module')
.directive("acTitulo",[function() {

  var directiveDefinitionObject ={
    restrict:"E",
    replace:true,
    templateUrl:"vista/menu-view.html"
  }

  return directiveDefinitionObject;
}]);
