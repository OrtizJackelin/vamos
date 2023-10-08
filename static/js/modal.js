document.addEventListener("DOMContentLoaded", () => {
    fetchData();

    document.querySelector("#inputdistancia").addEventListener("change", opcionCambiada);
    document.querySelector("#inputAge").addEventListener("blur", llenarComboCategoria);
    const inputs = document.querySelectorAll("input");
    inputs.forEach(
        function (myinput) {
            myinput.addEventListener("blur", validarInputs);
        }
    );

    //Botones
    document.querySelector("#btn_submit_form_evento").addEventListener("click", generarPreinscripcion);
    document.querySelector("#btn_imprimir_preinscripcion").addEventListener("click", imprimirPreinscripcion);
    document.querySelector("#btn_descargar_preinscripcion").addEventListener("click", descargarPreinscripcion);
    document.querySelector("#btn_descargar_cerrar").addEventListener("click", cerrarModal);
});

function generarPreinscripcion(event) {

    var formulario = document.querySelector("#formulario")
    if (formulario.checkValidity()) {

        // generamos un numero aleatorio
        var numeroAleatorio = Math.floor(Math.random() * 100) + 1;

        //carga de datos en los p de la ventana modal
        document.querySelector("#pi_inputName").textContent = "Participante: " + document.querySelector("#inputName").value
            + " " + document.querySelector("#inputLastName").value;

        document.querySelector("#pi_inputDni").textContent = "DNI: " + document.querySelector("#inputDni").value;

        document.querySelector("#pi_monto").innerHTML = "Realizar una transferencia por<b> " + document.querySelector("#inputPrecio").
            value + "</b> pesos, a la caja de ahorros CBU 203876523e472384234 a nombre de 'Agrupación Los Linces'.";

        document.querySelector("#pi_numero").textContent = "Pre-inscripción número: " + numeroAleatorio;

        document.querySelector("#pi_numero2").textContent = numeroAleatorio;

        const myModal2 = new bootstrap.Modal('#preinscripcion_modal', {
            keyboard: false
        })

        myModal2.show();

    } else {
        formulario.reportValidity();
    }
}

function cerrarModal() {
    var formulario = document.querySelector("#formulario");
    formulario.submit();

    setTimeout(function () {
        formulario.reset();
    }, 2000);

    location.replace("eventos.html");

}

//Preinscripciones
function generarPreinscripcion(event) {

    var formulario = document.querySelector("#formulario")
    if (formulario.checkValidity()) {

        // generamos un numero aleatorio
        var numeroAleatorio = Math.floor(Math.random() * 100) + 1;

        //carga de datos en los p de la ventana modal
        document.querySelector("#pi_inputName").textContent = "Participante: " + document.querySelector("#inputName").value
            + " " + document.querySelector("#inputLastName").value;

        document.querySelector("#pi_inputDni").textContent = "DNI: " + document.querySelector("#inputDni").value;

        document.querySelector("#pi_monto").innerHTML = "Realizar una transferencia por<b> " + document.querySelector("#inputPrecio").
            value + "</b> pesos, a la caja de ahorros CBU 203876523e472384234 a nombre de 'Agrupación Los Linces'.";

        document.querySelector("#pi_numero").textContent = "Pre-inscripción número: " + numeroAleatorio;

        document.querySelector("#pi_numero2").textContent = numeroAleatorio;

        const myModal2 = new bootstrap.Modal('#preinscripcion_modal', {
            keyboard: false
        })

        myModal2.show();

    } else {
        formulario.reportValidity();
    }
}

function imprimirPreinscripcion() {
    var contenidoDiv = document.getElementById("pre_inscripcion_imprimible").innerHTML;
    var ventanaImpresion = window.open('', '_blank', 'width=500,height=500');
    ventanaImpresion.document.write('<html><head><title>Imprimir Preinscripción</title></head><body>' + contenidoDiv + '</body></html>');
    ventanaImpresion.document.close();
    ventanaImpresion.print();
}
function descargarPreinscripcion() {
    var doc = new jsPDF();
    var elementHTML = document.querySelector("#pre_inscripcion_imprimible");
    doc.fromHTML(elementHTML, 15, 15, { 'width': 170 });
    doc.save('Constancia-Preinscripcion.pdf');
}