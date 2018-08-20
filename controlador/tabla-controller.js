angular
  .module('login-module') // Se manda a llamar el modulo para agregarle un controlador
  .controller('tabla-controller', tabla); // Declaración del controlador
  // controller('nombre', funcion a usar);

function tabla($scope, $http, $location) {
  $scope.importar = function rcaja() {
    $http.get('modelo/tabla.php').then(function(datos){ //Pedir la info. de la consulta
      $scope.equipo = datos;  //Guardar los datos en una nueva vriable
    });
  }
  $scope.importar();
  }
