#### RF1. Registro de usuario
Permitir a un nuevo usuario registrarse en el sistema de manera segura.

Feature: Registro de usuario
  Scenario: Registro exitoso
    Given que un usuario accede al formulario de registro
    When introduce datos válidos y envía el formulario
    Then se crea un nuevo usuario en la base de datos con la contraseña encriptada
    And el sistema muestra un mensaje de confirmación o redirige al login

####  RF2. Inicio de sesión

Permitir que los usuarios registrados accedan con sus credenciales.

Feature: Inicio de sesión
  Scenario: Inicio de sesión correcto
    Given que el usuario está registrado
    When introduce su correo y contraseña válidos
    Then el sistema valida las credenciales
    And crea una sesión activa para el usuario

#### RF3. Cierre de sesión

Finalizar sesión de manera segura.

Feature: Cierre de sesión
  Scenario: Cierre de sesión exitoso
    Given que el usuario tiene una sesión activa
    When hace clic en “Cerrar sesión”
    Then la sesión se destruye
    And el usuario es redirigido a la página de login

#### RF4. Crear proyecto

Feature: Creación de proyectos
  Scenario: Proyecto creado correctamente
    Given que el usuario está autenticado
    When completa el formulario de nuevo proyecto con datos válidos
    Then el sistema guarda el proyecto en la base de datos
    And el nuevo proyecto aparece en el listado del usuario

#### RF5. Editar proyecto

#### Feature: Edición de proyectos
  Scenario: Actualización exitosa
    Given que existe un proyecto del usuario
    When el usuario modifica su nombre o descripción y guarda
    Then los cambios se actualizan en la base de datos

#### RF6. Eliminar proyecto

Feature: Eliminación de proyectos
  Scenario: Proyecto eliminado
    Given que el usuario tiene un proyecto existente
    When confirma la eliminación
    Then el sistema elimina el proyecto y sus tareas asociadas

#### RF7. Crear tarea

Feature: Creación de tareas
  Scenario: Tarea creada correctamente
    Given que el usuario tiene un proyecto seleccionado
    When introduce los datos de una nueva tarea válidos
    Then el sistema guarda la tarea en la base de datos
    And la tarea aparece en la lista de tareas del proyecto

#### RF8. Editar tarea

Feature: Edición de tareas
  Scenario: Edición exitosa
    Given que existe una tarea en un proyecto
    When el usuario edita su descripción o estado y guarda
    Then el sistema actualiza la información en la base de datos

#### RF9. Cambio de estado (AJAX)

Feature: Cambio de estado AJAX
  Scenario: Cambio de estado sin recarga
    Given que el usuario visualiza una tarea en la lista
    When cambia su estado a “completada” desde la interfaz
    Then el estado se actualiza en la base de datos
    And la página no se recarga

#### RF10. Eliminar tarea

Feature: Eliminación de tareas
  Scenario: Eliminación exitosa
    Given que el usuario tiene una tarea creada
    When confirma su eliminación
    Then la tarea se elimina de la base de datos

#### RF11. Estadísticas globales

Feature: Panel de control
  Scenario: Visualización de estadísticas
    Given que el usuario tiene tareas con distintos estados
    When accede al panel de control
    Then el sistema muestra el número de tareas por estado
    And calcula el porcentaje de progreso global

#### RF12. Diseño responsive

Feature: Diseño responsive
  Scenario: Visualización en distintos dispositivos
    Given que el usuario accede desde cualquier dispositivo
    When la resolución de pantalla cambia
    Then la interfaz se adapta correctamente sin pérdida de funcionalidad

#### RF13. Formularios dinámicos

Feature: Formularios dinámicos
  Scenario: Creación o edición sin recargar
    Given que el usuario está en la vista principal
    When abre un modal para crear o editar un elemento
    Then puede enviar el formulario mediante AJAX
    And la información se actualiza en pantalla sin recarga

#### RF14. Sanitización y consultas preparadas

Feature: Seguridad de entrada de datos
  Scenario: Prevención de inyección SQL o XSS
    Given que el usuario envía datos desde un formulario
    When el backend procesa la solicitud
    Then los datos se validan y sanitizan correctamente
    And las consultas se ejecutan mediante PDO con parámetros preparados

