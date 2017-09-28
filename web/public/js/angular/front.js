var app = angular.module("frontApp", []);

app.controller("frontendController", function($scope, $http){

	$scope.productos = [];


  // Este método es ejecutado desde el Frontend
  $scope.userNew = function() {
    var json = new Object();
    json.nombre = $scope.nombre;
    json.apellido = $scope.apellido;
    json.username = $scope.username;
    json.password = $scope.password;
    json.email = $scope.email;

    var user = new Object();
    user.json = json;

    $scope.rqt = user;

    $http({ 
      method: 'POST',
      data: $scope.rqt, // Acá van los datos recibidos del formulario, que serán enviados a la ruta /registrar.
      url: '/user/new',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (success){
      // Cuando termina de ejecutarse la llamada al backend hacemos algo..
      $scope.rtaUser = success.data;
      debugger
    } ,function (error){
      // Si falla hacemos algo
    });
  }

  // Creamos la funciona allEmpresas, la cual va a recibir por GET el json de la ruta /empresa.
  $scope.allEmpresas = function() {
    $http({
      method: 'GET',
      url: '/empresa',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function (success){
      // Cuando termina de ejecutarse la llamada al backend hacemos algo..
      $scope.empresas = success.data;
    } ,function (error){
      // Si falla hacemos algo
    });
  }

  // Este metodo se ejecuta cuando se carga el controlador. 
  // Por lo que el frontend, ya va a tener a todas las empresas dentro de la variable $scope.empresas.
  $scope.allEmpresas();

});