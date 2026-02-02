/**
 * ============================================================
 * CENTRO EDUCATIVO - JAVASCRIPT
 * Proyecto IAW - CFGS ASIR
 * ============================================================
 * 
 * Este archivo contiene todas las funciones JavaScript
 * para validaciones de formularios y interacciones de usuario.
 */

// ============================================================
// VALIDACIÓN DE FORMULARIO DE ESTUDIANTES
// ============================================================
/**
 * Valida el formulario de estudiantes antes de enviarlo
 * Comprueba que los campos obligatorios estén completos
 * 
 * @returns {boolean} - true si el formulario es válido, false en caso contrario
 */
function validarFormularioEstudiante() {
    // Obtener valores de los campos
    var nombre = document.getElementById('nombre').value.trim();
    var apellidos = document.getElementById('apellidos').value.trim();
    var email = document.getElementById('email').value.trim();
    var fechaMatricula = document.getElementById('fecha_matricula').value;
    
    // Array para almacenar errores
    var errores = [];
    
    // Validar nombre
    if (nombre === '') {
        errores.push('El nombre es obligatorio');
    } else if (nombre.length < 2) {
        errores.push('El nombre debe tener al menos 2 caracteres');
    }
    
    // Validar apellidos
    if (apellidos === '') {
        errores.push('Los apellidos son obligatorios');
    } else if (apellidos.length < 2) {
        errores.push('Los apellidos deben tener al menos 2 caracteres');
    }
    
    // Validar email
    if (email === '') {
        errores.push('El email es obligatorio');
    } else if (!validarEmail(email)) {
        errores.push('El formato del email no es válido');
    }
    
    // Validar fecha de matrícula
    if (fechaMatricula === '') {
        errores.push('La fecha de matrícula es obligatoria');
    }
    
    // Si hay errores, mostrarlos y devolver false
    if (errores.length > 0) {
        alert('Por favor, corrija los siguientes errores:\n\n• ' + errores.join('\n• '));
        return false;
    }
    
    return true;
}

// ============================================================
// VALIDACIÓN DE FORMULARIO DE PROFESORES
// ============================================================
/**
 * Valida el formulario de profesores antes de enviarlo
 * 
 * @returns {boolean} - true si el formulario es válido, false en caso contrario
 */
function validarFormularioProfesor() {
    // Obtener valores de los campos
    var nombre = document.getElementById('nombre').value.trim();
    var apellidos = document.getElementById('apellidos').value.trim();
    var email = document.getElementById('email').value.trim();
    var especialidad = document.getElementById('especialidad').value.trim();
    var fechaAlta = document.getElementById('fecha_alta').value;
    
    // Array para almacenar errores
    var errores = [];
    
    // Validar nombre
    if (nombre === '') {
        errores.push('El nombre es obligatorio');
    }
    
    // Validar apellidos
    if (apellidos === '') {
        errores.push('Los apellidos son obligatorios');
    }
    
    // Validar email
    if (email === '') {
        errores.push('El email es obligatorio');
    } else if (!validarEmail(email)) {
        errores.push('El formato del email no es válido');
    }
    
    // Validar especialidad
    if (especialidad === '') {
        errores.push('La especialidad es obligatoria');
    }
    
    // Validar fecha de alta
    if (fechaAlta === '') {
        errores.push('La fecha de alta es obligatoria');
    }
    
    // Si hay errores, mostrarlos y devolver false
    if (errores.length > 0) {
        alert('Por favor, corrija los siguientes errores:\n\n• ' + errores.join('\n• '));
        return false;
    }
    
    return true;
}

// ============================================================
// VALIDACIÓN DE FORMULARIO DE CURSOS
// ============================================================
/**
 * Valida el formulario de cursos antes de enviarlo
 * 
 * @returns {boolean} - true si el formulario es válido, false en caso contrario
 */
function validarFormularioCurso() {
    // Obtener valores de los campos
    var nombre = document.getElementById('nombre').value.trim();
    var horas = document.getElementById('horas').value;
    var fechaInicio = document.getElementById('fecha_inicio').value;
    
    // Array para almacenar errores
    var errores = [];
    
    // Validar nombre del curso
    if (nombre === '') {
        errores.push('El nombre del curso es obligatorio');
    }
    
    // Validar horas
    if (horas === '' || horas <= 0) {
        errores.push('Las horas deben ser un número mayor que 0');
    }
    
    // Validar fecha de inicio
    if (fechaInicio === '') {
        errores.push('La fecha de inicio es obligatoria');
    }
    
    // Si hay errores, mostrarlos y devolver false
    if (errores.length > 0) {
        alert('Por favor, corrija los siguientes errores:\n\n• ' + errores.join('\n• '));
        return false;
    }
    
    return true;
}

// ============================================================
// CONFIRMACIÓN DE ELIMINACIÓN
// ============================================================
/**
 * Muestra un diálogo de confirmación antes de eliminar un registro
 * 
 * @param {string} tipo - El tipo de elemento a eliminar (estudiante, profesor, curso)
 * @param {string} nombre - El nombre del elemento a eliminar
 * @returns {boolean} - true si el usuario confirma, false en caso contrario
 */
function confirmarEliminacion(tipo, nombre) {
    var mensaje = '¿Está seguro de que desea eliminar ';
    
    switch(tipo.toLowerCase()) {
        case 'estudiante':
            mensaje += 'al estudiante "' + nombre + '"?';
            break;
        case 'profesor':
            mensaje += 'al profesor "' + nombre + '"?';
            break;
        case 'curso':
            mensaje += 'el curso "' + nombre + '"?';
            break;
        default:
            mensaje += '"' + nombre + '"?';
    }
    
    mensaje += '\n\nEsta acción no se puede deshacer.';
    
    return confirm(mensaje);
}

// ============================================================
// VALIDACIÓN DE EMAIL
// ============================================================
/**
 * Valida el formato de un email usando expresión regular
 * 
 * @param {string} email - El email a validar
 * @returns {boolean} - true si el formato es válido
 */
function validarEmail(email) {
    // Expresión regular para validar email
    var patron = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return patron.test(email);
}

// ============================================================
// FILTRAR TABLA (BÚSQUEDA EN TIEMPO REAL)
// ============================================================
/**
 * Filtra las filas de una tabla según el texto de búsqueda
 * 
 * @param {string} inputId - ID del campo de búsqueda
 * @param {string} tablaId - ID de la tabla a filtrar
 */
function filtrarTabla(inputId, tablaId) {
    // Obtener el valor de búsqueda
    var input = document.getElementById(inputId);
    var filtro = input.value.toLowerCase();
    
    // Obtener la tabla y sus filas
    var tabla = document.getElementById(tablaId);
    var filas = tabla.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    // Recorrer cada fila de la tabla
    for (var i = 0; i < filas.length; i++) {
        var fila = filas[i];
        var celdas = fila.getElementsByTagName('td');
        var encontrado = false;
        
        // Buscar en cada celda de la fila
        for (var j = 0; j < celdas.length; j++) {
            var celda = celdas[j];
            if (celda) {
                var texto = celda.textContent || celda.innerText;
                if (texto.toLowerCase().indexOf(filtro) > -1) {
                    encontrado = true;
                    break;
                }
            }
        }
        
        // Mostrar u ocultar la fila según el resultado
        if (encontrado) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    }
}

// ============================================================
// LIMPIAR FORMULARIO
// ============================================================
/**
 * Limpia todos los campos de un formulario
 * 
 * @param {string} formId - ID del formulario a limpiar
 */
function limpiarFormulario(formId) {
    var formulario = document.getElementById(formId);
    if (formulario) {
        formulario.reset();
    }
}

// ============================================================
// VALIDACIÓN EN TIEMPO REAL (opcional - mejora de UX)
// ============================================================
/**
 * Añade validación visual en tiempo real a los campos
 */
document.addEventListener('DOMContentLoaded', function() {
    // Obtener todos los campos requeridos
    var camposRequeridos = document.querySelectorAll('input[required], select[required]');
    
    camposRequeridos.forEach(function(campo) {
        // Validar al perder el foco
        campo.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.style.borderColor = '#dc3545';
            } else {
                this.style.borderColor = '#28a745';
            }
        });
        
        // Restaurar estilo al escribir
        campo.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.style.borderColor = '#28a745';
            }
        });
    });
});

// ============================================================
// MENSAJE DE CONSOLA (para depuración)
// ============================================================
console.log('✅ Scripts de Centro Educativo cargados correctamente');
