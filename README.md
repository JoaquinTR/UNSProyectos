# UNSProyectos

Uns Proyectos es una aplicación destinada a la administración de proyectos en general dentro del ambiente académico de la Universidad Nacional del Sur. La aplicación está desarrollada utilizando Laravel 9 y Bootstrap 5, y está construida sobre un Docker lo cual permite un grado de portabilidad excepcional.

## Objetivo primordial

La idea central del proyecto es brindar una herramienta tanto a la cátedra como a los alumnos para planificar un proyecto de software sin tener que caer en diversas herramientas que o bien no son gratuitas o poseen limitaciones de uso que impiden el flujo ágil de un proyecto de software. Por el lado de la cátedra se define una herramienta automatizada de creación de cuentas, de entrega de sprints y de devolución de manera centralizada evitando imprimir planificaciones de manera innecesaria y cualquier complicación relacionada a las tecnologías preexistentes. Del lado del alumno se desea crear una herramienta gráfica intuitiva y amistosa para planificar sprints de un proyecto de software eliminando por completo tener que buscar una herramienta que lo permita.

## Alcance

Actualmente en fase de análisis y desarrollo, está destinado a la administración del proyecto de la materia Administración de Proyectos de Software, pero está pensado para que permita adminstrar todo tipo de proyecto de manera gráfica (sutil proyección a futuro, que sea útil para la Universidad y no solo a una materia).

## Funcionamiento

Actualmente la aplicación gira en torno a dos roles principales, profesor y alumno. El profesor actúa como administrador del proyecto de la materia mientras que los alumnos como planificadores de sprints. La aplicación está pensada para que la cátedra de la materia cree un proyecto nuevo, defina la estructura del mismo en base a la cantidad de sprints que posee, sus deadlines y cuándo comienzan para luego definir cómo van a estar compuestos los grupos de trabajo. Para esto último está pensado que se importen todos los datos de alumnos anotados en la materia y los dividan en comisiones. La creación de usuarios es automática y via un mail destinado a las casillas de los alumnos. 
Una vez definidas las comisiones arranca el proyecto automáticamente y una vez alcanzado el deadline de un sprint, éste se entrega solo.
En todo momento los profesores pueden ver el estado de todo sprint de toda cátedra, además de modificar el inicio o deadline de cualquier sprint.
Una vez disparada la entrega automática de un sprint, la cátedra dispone de una herramienta símple para visualizar la planificación brindada y proceder a su corrección.
Los alumnos poseen una herramienta para ver las correcciones en tiempo real.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
