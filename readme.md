# Vamos - Proyecto final de programación III - Técnicatura web UNSL

Vamos es un proyecto personal desarrollado para la materia de programación III, que trata de un sitio web para alquileres de inmuebles por día, desarrollado bajo el siguiente enunciado:

> Vamos es una aplicación web 2.0 que ofrece lugares para alquilar por día. El
portal permite que los usuarios ofrezcan casas, departamentos, habitaciones, etc.
y también alquilarlas. El lugar ofrecido puede ser público o privado.
La aplicación permite que los usuarios se registren y tengan un perfil que incluye
los datos personales, intereses, una foto de rostro, una certificación (☑) en caso de
ser una cuenta verificada y una pequeña bio. El usuario puede cambiar los datos
cuando lo desee, aunque deberá repetir el proceso de verificación.
Existe una distinción de usuarios de cuentas verificadas. Los usuarios con
cuentas verificadas no tienen plazos de espera cuando publican nuevas ofertas o
cuando alquilan, entre otras ventajas. Para verificar una cuenta se puede enviar un
mensaje con documentación adjunta al administrador para que sea revisada. El
administrador puede activar la verificación a los usuarios, con una fecha de
vencimiento.
La aplicación permite que el usuario pueda crear distintas ofertas de alquiler,
donde cada alquiler debe contener un título, una descripción, una ubicación que
facilite su búsqueda, etiquetas, una galería de fotos, un listado de servicios (de una
lista estandarizada), un costo de alquiler por día, un tiempo mínimo y máximo de
permanencia y un cupo. Opcionalmente pueden especificarse una fecha de inicio y
de fin, donde la oferta de alquiler estará activa. Un usuario regular puede tener
solamente una oferta de alquiler activa y debe esperar entre 3 (tres) y 5 (cinco)
días hábiles para que se revise su oferta, restricciones inexistentes para los
usuarios verificados.
Un usuario del portal, también puede tomar las ofertas de alquiler, claramente
siempre y cuando no sea el creador de dicho alquiler, las fechas de alquiler estén
dentro de lo publicado y ese periodo no colisione con otro alquiler. Los usuarios
regulares deben esperar a que el dueño del alquiler acepte su oferta, que es
automáticamente rechazada si pasan al menos 72 hs. sin que el dueño tome una
decisión. Además solo pueden aplicar a un alquiler a la vez. Los usuarios
verificados son aceptados automáticamente.
Una vez concluido el alquiler, los usuarios verificados pueden reseñar el
hospedaje. Las reseñas son públicas en la oferta de alquiler. El dueño del alquiler
puede contestar las reseñas, pero esa es toda la interacción de las mismas, es
decir, el texto original con su puntaje y una respuesta opcional.
Los usuarios del portal podrán buscar alquileres por texto libre (título más
descripción), etiquetas o ubicación. También podrán ver alquileres recomendados
de acuerdo a un pareo entre sus intereses y las etiquetas de los alquileres. Los
alquileres de usuarios certificados deben aparecer en forma destacada.
Las reglas de negocio no especificadas podrán ser consensuadas con el profesor
de la materia.de la materia.

## Configuración

Aplicación desarrollada en php y MariaDB.

### Requerimientos
- PHP Version 8.2.4
- MariaDB 10.4.28

###Instalación

1.  php y MariaDb se encuentran disponibles en el paquete [xampp](https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.4/xampp-windows-x64-8.2.4-0-VS16-installer.exe "xampp") 
2. Una vez completada la instalación, copiar el contenido del repositorio dentro de la carpeta htdocs de xampp
3. Crear una base detos con el nombre 'vamos' y ejecutar el script `BD/vamos.sql`
4. Utilizar el navegador preferido para navegar el sitio:  `http://localhost/{carpeta_repo}`



### Usuarios De Ejemplo
|Nombre |Email|  Clave |es_verifcado|es_administrador|
| ------------ | ------------ | ------------ | ------------ | ------------ |
|   Carlos Martinez|  martinezc@gmail.com |   conMucho100%|   0|    0|
|  Karla Ramirez |  karlak@hotmail.com |   miKarla#45|  1 |   0|
|  Maria DB |  maria789@gmail.com |   Loschamos$29|  0 |   0|
|  Guillermo Zapata |   guillermo54@gmail.com|  Pasta$29 |  1 |   0|
|  Miguel |   miguel@gmail.com|  M$59lindo |  0 |   1|
|   Jose Ortiz|  jose@gmail.com |   Papa$2903|  0|   0|

####Usuarios Versus Id asociados en user. id
|Nombre |id|
| ------------ | ------------ |
|   Carlos Martinez|23 |
|  Karla Ramirez | 24|
|  Maria DB | 20|
|  Guillermo Zapata | 7|
|  Miguel |21|
|   Jose Ortiz|22|

####Usuarios  Id Versus id publicaciones asociadas.   publicacion. id
|Nombre Usuario|Id Usuario |Id Publicación|
| ------------ | ------------ |------------ |
|   Carlos Martinez|23 | 29|
|  Karla Ramirez | 24|34, 35, 36, 37, 38|
|  Maria DB | 20|28|
|  Guillermo Zapata | 7|20, 21,  22,  23, 30, 31, 32, 33|
|  Miguel |21| |
|  Jose Ortiz|22| |



### Convenciones
#### Estatus de cuentas de usuario - campo user. es_verificado
|  valor |  significado  |
| ------------ | ------------ |
| 0  | Usuario regular   |
| 1  |Usuario Verificado   |

#### Estatus de cuentas de usuario - campo user. es_administrador
|  valor |  significado  |
| ------------ | ------------ |
| 0  | Usuario    |
| 1  |Administrador |

#### Estatus de  solicitud de verificación de cuenta de usuario - campo verificacion_cuenta. estado
|  valor |  significado  |
| ------------ | ------------ |
| 0  | Solicitud en proceso    |
| 1  |Solicitud aprobada |
| 2  |Solicitud rechazada |

#### Estatus solicitud de publicación de alquiler - campo publicacion. estado
|  valor |  significado  |
| ------------ | ------------ |
| 0  | Solicitud en proceso    |
| 1  |Solicitud aprobada |
| 2  |Solicitud rechazada |

#### Estatus de solicitud de alquiler - campo alquiler. aprobado
|  valor |  significado  |
| ------------ | ------------ |
| 0  | Solicitud en proceso    |
| 1  |Solicitud aprobada |
| 2  |Solicitud rechazada |


