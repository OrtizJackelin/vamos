var alertPlaceholder = "";
//Se Ejecuta Despues de Descargar el DOM (HTML)
document.addEventListener("DOMContentLoaded", () => {
 
    const inputs = document.querySelectorAll("input");
    var inputDate = document.getElementById("fechaNacimiento");
    alertPlaceholder = document.getElementById('liveAlertPlaceholder');

    // Limpia el valor del campo de entrada
    inputDate.value = null; // O puedes usar inputDate.value = null;

    inputs.forEach(
        function(myinput){
            myinput.addEventListener("blur",validarInputs);
        }
    );
    
    document.querySelector("#enviar").addEventListener("click", function(event) {
        event.preventDefault(); // Evita el envío predeterminado del formulario
        validarFormulario(event);
    });

});

function validarFormulario(event){
    var formulario= document.querySelector("#formulario");
    if (formulario.checkValidity()){
        formulario.submit();
    }
    else{
        formulario.reportValidity();
    }

}

function alert(message, type) {
    var wrapper = document.createElement('div')
    wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">'+
    '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">'+
        '<path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>'+
    '</svg>'+
     message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
  
    alertPlaceholder.append(wrapper)
  }

function validarInputs(event){

    var resultado=event.target.checkValidity();
    let contrasena = "";

    if(event.target.id==='email'){
        resultado= validarEmail(event.target.value);
    }
    if(event.target.id==='fechaNacimiento'){
        console.log(event.target.value);
        resultado= validarFechaNacimiento(event.target.value);

    }
    if(event.target.id==='clave'){
        resultado= validarClave(event.target.value);
        contrasena = event.target.value;
    }
    if(event.target.id==='repetirClave'){
        resultado= validarRepetirClave(event.target.value, contrasena );
    }
    if(event.target.id==='dni'){
        resultado= validarDni(event.target.value);
    }
    if(event.target.id==='telefono'){
        resultado= validarTelefono(event.target.value);
    }
    if(resultado){
      
        event.target.style.borderColor="#ced4da";
    }else{
      
        event.target.style.borderColor="crimson";
    }

    
}

function validarEmail(email){

    var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,3})$/;
    return regex.test(email) ? true : false;
    
}

function validarClave(clave){

    var regex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[#$%^&+=!])(?!.*\s).{8,}$/;
    return regex.test(clave) ? true : false;

        /*Esta expresión regular exige lo siguiente:
        Al menos una letra mayúscula.
        Al menos una letra minúscula.
        Al menos un número.
        Al menos un carácter especial (puedes personalizar la lista de caracteres especiales).
        No contiene espacios en blanco.
        Tiene una longitud mínima de 8 caracteres (puedes ajustar este número).
        Aquí tienes una breve explicación de los componentes de la expresión regular:

        ^: Coincide con el inicio de la cadena.
        (?=.*[A-Z]): Busca al menos una letra mayúscula.
        (?=.*[a-z]): Busca al menos una letra minúscula.
        (?=.*\d): Busca al menos un número.
        (?=.*[@#$%^&+=!]): Busca al menos uno de los caracteres especiales en la lista (puedes personalizarlos).
        (?!.*\s): Asegura que no haya espacios en blanco en la cadena.
        .{8,}: Asegura que la longitud de la cadena sea al menos 8 caracteres.
        $: Coincide con el final de la cadena.*/
    
}
function validarRepetirClave(repetirClave, clave){
    if(repetirClave===clave){
        return true;
    }else{
        alert('La clave no coincide.', 'warning');
        return false;
    }
    
}

function validarFechaNacimiento(fechaNacimiento) {

    var fecha = new Date(fechaNacimiento);
    var fechaActual = new Date();
    var edadMinima = 18;
    var edad = fechaActual.getFullYear() - fecha.getFullYear();
    var mesActual = fechaActual.getMonth();
    var mesNacimiento = fecha.getMonth();
    console.log(fecha);
    console.log(fechaActual);

    console.log(fecha);
    console.log(fechaActual);

    if (isNaN(fecha.getTime())) {
        alert('La fecha ingresada no es válida.','warning');
        return false;
    }

    if (fecha > fechaActual) {
        alert('La fecha no puede ser mayor que la fecha actual.', 'warning');
        return false;
    }

    if (mesNacimiento > mesActual || (mesNacimiento === mesActual && fecha.getDate() > fechaActual.getDate())) {
        edad--;
    }

    if (edad < edadMinima) {
        //e.preventDefault(); // Evita que se envíe el formulario
        //document.getElementById('errorFechaNacimiento').textContent = 'Debes tener al menos 18 años para registrarte.'; colocar en el formulario para mostrar los mensajes de error
        alert('Debes tener al menos 18 años para registrarte.','warning');
        return false;
    }
    return true; // Permite que el formulario se envíe si la fecha es válid
}

function validarDni(dni){
    if(dni===""){
        alert('Debe ingresar dni.','warning');
        return false;
        
    } else{
        const regex = /^[1-9][0-9]{6,7}$/;
        return regex.test(dni);
    }
}


function validarTelefono(telefono){
    if(telefono ===""){
        alert('Debe ingresar un numero de telefono.','warning');
        return false;
    } else{
        const regex = /^\d{10}$/;
        return regex.test(telefono);
    }
}    

