var alertPlaceholder = "";
var formularioValido=true;
//Se Ejecuta Despues de Descargar el DOM (HTML)
document.addEventListener("DOMContentLoaded", () => { 
 
    const inputs = document.querySelectorAll("input");
    alertPlaceholder = document.getElementById('liveAlertPlaceholder');
    var enviar = document.querySelector("#enviar");
    
    flatpickr("#fecha_inicio", {
        minDate: "today",
        dateFormat: "Y-m-d", // Formato de fecha (opcional)
        onValueUpdate: function(selectedDates, dateStr, instance) {
        
        }
    });
  
    flatpickr("#fecha_fin", {
        minDate: "today",
        dateFormat: "Y-m-d", // Formato de fecha (opcional)
        onValueUpdate: function(selectedDates, dateStr, instance) {
            console.log(dateStr);
        }
    });
    // Limpia el valor del campo de entrada
    //inputDate.value = ""; // O puedes usar inputDate.value = null;

    inputs.forEach(
        function(myinput){
            myinput.addEventListener("blur",validarInputs);
        }
    );
    if(enviar != null) {
        enviar.addEventListener("click", function(event) {
            event.preventDefault(); // Evita el envío predeterminado del formulario
            validarFormulario(event);
        });
    }
   

});

function validarFormulario(event){

    var formulario= document.querySelector("#formulario");
    if (formulario.checkValidity() && formularioValido){
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

    if(event.target.id==='titulo'){
        resultado= validarLongitudTitulo(event.target.value);
    }
    if(event.target.id==='descripcion'){
        console.log(event.target.value);
        resultado= validarDescripcion(event.target.value);
    }
    if(event.target.id==='ubicacion'){
        console.log(event.target.value);
        resultado= validarLongitudUbicacion(event.target.value);
    }
    if(event.target.id==='tiempo_minimo'){
        resultado= validarDiasDeEstadia(event.target.value);
    }
    if(event.target.id==='tiempo_maximo'){
        resultado= validarDiasDeEstadia(event.target.value);
    }
    if(event.target.id==='cupo'){
        resultado= validarCupo(event.target.value);
    }
    if(event.target.id==='costo'){
        resultado= validarCosto(event.target.value);
    }
    if(event.target.id==='fecha_inicio'){
        resultado= validarFechaInicio(event.target.value);
    }
    if(event.target.id==='fecha_fin'){
        resultado= validarFechaFin(event.target.value);
    }
    if(event.target.id==='imagenes'){
        resultado= validarImagen(event.target.value);
    }
    if(resultado){
        formularioValido=true;
        event.target.style.borderColor="#ced4da";
    }else{
        formularioValido=false;
        event.target.style.borderColor="crimson";
    }
 
}

function validarLongitudTitulo(titulo) {
    var valor = titulo.value;
    if(valor != ""){
        // Verificar la longitud del valor ingresado
        if (valor.length > 100) {
            alert("El texto es muy largo.", 'warning');
            //  truncar el texto 
            input.value = valor.substring(0, 100);
        }
        return true;
    } else {
        alert("Debe ingresar Título.", 'warning');
        return false;
    }
}

function validarDescripcion(descripcion) {
    var valor = descripcion.value;

    if(valor === ""){
        alert("Debe ingresar una descripción.", 'warning');
        return false;
    }
    return true;
}

function validarLongitudUbicacion(ubicacion) {
    var valor = ubicacion.value;

    if(valor !=""){
        // Verificar la longitud del valor ingresado
        if (valor.length > 300) {
            alert("El texto es muy largo.", 'warning');
            // Puedes truncar el texto 
            input.value = valor.substring(0, 300);            
        }
        return true;
    } else {
        alert("Debe ingresar Ubicación.", 'warning');
        return false;
    }
}

function validarFechaInicio(fechaInicio){

    var fechaIngresada = new Date(fechaInicio.value);

    // Obtener la fecha actual
    var fechaActual = new Date();

    if(fechaInicio != ""){
        // Comparar las fechas
        if (fechaIngresada < fechaActual) {
            alert("La fecha no puede ser menor a la actual.", 'warning');
            return false;
        } 
        return true;
    }
    
}

function validarImagen(imagenes){
    var archivos = imagenes.files;
    var pesoMaximo = 1024 * 1024;

    if (archivos.length != 0) {

            // Verificar tipos de archivos (opcional)
        for (var i = 0; i < archivos.length; i++) {
            var tipo = archivos[i].type.split('/')[0];
            if (tipo !== 'image') {
                alert("Por favor, selecciona solo imágenes.");
                return false;
            }
        }
    
        // Verificar el peso de cada archivo
        for (var i = 0; i < archivos.length; i++) {
            if (archivos[i].size > pesoMaximo) {
                alert("El archivo " + archivos[i].name + " excede el peso máximo permitido.", 'warning');
                return false;
            }
        }
        return true;
    }
    
}

function validarFechaFin(fechaFin){

    var fechaIngresada = new Date(fechaFin.value);

    // Obtener la fecha actual
    var fechaActual = new Date();

    if(fechaFin != ""){
        // Comparar las fechas
        if (fechaIngresada < fechaActual) {
            alert("La fecha no puede ser menor a la actual.", 'warning');
            return false;
        } 
        return true;
    } 
    
}
    

function validarCupo(cupo){

    var regex = /^(?:[1-9]|[1-9]\d|4\d\d|500)$/;
    if(cupo != ""){   
        if(regex.test(cupo)) {
            return true;
        } else {
            alert('El cupo es un valor entre 1 y 500','warning');
            return false; 
        }  
    } else {
        alert('Debe seleccionar cantidad de personas','warning');
        return false;
    }
}

function validarCosto(costo){
    regex = /[1-9]\d{4,}(\.\d+)?$/ // para permitir decimales opcionales después de los cuatro 
    if(costo != ""){   
        if(regex.test(costo)) {
            return true;
        } else {
            alert('El costo debe ser mayor a 10000','warning');
            return false; 
        }  
    } else {
        alert('Debe seleccionar un costo para la propiedad','warning');
        return false;
    }
}

function validarDiasDeEstadia(dias){

    var regex = /^([1-9]|[1-5]\d|60)$/;

    if(dias != ""){
        if(regex.test(dias)) {
            return true;
        } else {
            alert('Los días de estadia es un valor entre 1 y 60','warning');
            return false; 
        }   
    } else {
        alert('Debe seleccionar cantidad de días','warning');
        return false;
    }
}
