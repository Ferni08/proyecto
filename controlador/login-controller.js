angular
  .module('login-module') // Se manda a llamar el modulo para agregarle un controlador
  .controller('login-controller', Login); // Declaración del controlador
  // controller('nombre', funcion a usar);
  // .controller('routecontroller',routing); // Declaracion del controlador route
function Login($scope, $http, $location) {
  alert('holis controlador');
  $scope.boton = Llamada;
  function Llamada() {
    // alert('Vamos a enviar usuasrio: ' + $scope.usuario + ' y contraseña: ' + $scope.contra);
    $http({
      method: 'POST',
      url: 'modelo/login.php',
      data: {
        user: $scope.usuario,
        psw: $scope.contra
      }
    }).then(function(response){
      if (response['data'] == 0) {
        alert('Los datos ingresados no son correctos');
      } else {
        alert('Los datos son correctos');
          location.href=("pag.html");
      }
    });
  }
}
