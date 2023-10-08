//Se Ejecuta Despues de Descargar el DOM (HTML)
document.addEventListener("DOMContentLoaded", () => {
 
    const inputs = document.querySelectorAll("input");
    var inputDate = document.getElementById("fechaNacimiento");

    // Limpia el valor del campo de entrada
    inputDate.value = null; // O puedes usar inputDate.value = null;

    inputs.forEach(
        function(myinput){
            myinput.addEventListener("blur",validarInputs);
        }
    );
    document.querySelector("#enviar").addEventListener("click",listar);

});
function listar(event){
    var formulario= document.querySelector("#formulario");
    if (formulario.checkValidity()){
        formulario.submit();
    }
    else{
        formulario.reportValidity();
    }

}

function validarInputs(event){

    var resultado=event.target.checkValidity();
    let contrasena = "";

    if(event.target.id==='email'){
        resultado= validarEmail(event.target.value);
    }
    if(event.target.id==='fechaNacimiento'){
        resultado= validarFechaNacimiento(event.target.value);
    }
    if(event.target.id==='clave'){
        resultado= validarClave(event.target.value);
        contrasena = event.target.value;
    }
    if(event.target.id==='repetirClave'){
        resultado= validarRepetirClave(event.target.value, contrasena );
    }
    if(event.target.id==='fechaNacimiento'){
        resultado= validarFechaNacimiento(event.target.value);
    }
    if(event.target.id==='codPais'){
        resultado= validarCodigoPais(event.target.value);
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
        return false;
    }
    
}

function validarFechaNacimiento(fechaNacimiento) {

    var fecha = new Date(fechaNacimiento);
    var fechaActual = new Date();
    var edadMinima = 18;
    var edad = fechaActual.getFullYear() - fechaNacimiento.getFullYear();
    var mesActual = fechaActual.getMonth();
    var mesNacimiento = fechaNacimiento.getMonth();

    if (isNaN(fecha.getTime())) {
        alert("La fecha ingresada no es válida.");
        return false;
    }

    if (fecha > fechaActual) {
        alert("La fecha no puede ser mayor que la fecha actual.");
        return false;
    }

    if (mesNacimiento > mesActual || (mesNacimiento === mesActual && fechaNacimiento.getDate() > fechaActual.getDate())) {
        edad--;
    }

    if (edad < edadMinima) {
        //e.preventDefault(); // Evita que se envíe el formulario
        //document.getElementById('errorFechaNacimiento').textContent = 'Debes tener al menos 18 años para registrarte.'; colocar en el formulario para mostrar los mensajes de error
        alert("Debes tener al menos 18 años para registrarte.");
        return false;
    }
    return true; // Permite que el formulario se envíe si la fecha es válid
}

function validarCodigoPais(codPais){
    if(codPais===""){
        alert("Debe seleccionar un codigo.");
        return false;
        
    } else{
        return true;
    }
}


function validarTelefono(telefono){
    if(telefono ===""){
        alert("Debe ingresar un numero de telefono.");
        return false;
    } else{
        var regexTelefono = /^(\d{10})$/;
        return regex.test(telefono);
    }
}    

