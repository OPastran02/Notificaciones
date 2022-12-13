Código php:

Para implementar la versión 1.2-RC3  se deben reemplazar/incorporar lo archivos del tag utilizando los commandos de git (git fetch --tags && git checkout <tag>).

Darle permesos a apache a la carpeta source

chown -R apache:apache source

Luego se deben dar permisos a las carpetas de cache, dentro de la carpeta donde se encuentra el código el sistema en la terminal ejecutar:

chmod -R 775 var/cache
chmod -R 775 var/logs
chmod -R 775 var/sessions

Luego se deben actualizar los paquetes del composer.lock, dentro de la carpeta donde se encuentra el código el sistema en la terminal ejecutar:

composer install --optimize-autoloader

inicializarlas las carpetas de cache ejecutando, dentro de la carpeta donde se encuentra el código el sistema en la terminal ejecutar:

php bin/console cache:clear --env=prod --no-debug

Posteriormente, en caso de que no esten, se deben incorporar las siguientes variables al archivo de configuración "source/app/config/parameters.yml" :

google_url: https://maps.google.com/maps/a....
usig_normalizar_geocodificar: https://ws.usig.buenosaires.gob.ar/rest/normalizar_y_geocodificar_direcciones
usig_convertir_coords: https://ws.usig.buenosaires.gob.ar/rest/convertir_coordenadas
usig_datos_utiles: https://ws.usig.buenosaires.gob.ar/datos_utiles
usig_reversegeocoding: https://ws.usig.buenosaires.gob.ar/geocoder/2.2/reversegeocoding
usig_smp: https://ws.usig.buenosaires.gob.ar/geocoder/2.2/smp
oauth2_url: https://oauth2-server-hml.apps.buenosaires.gob.ar

Por ultimo ejecutar el script de la nueva estructura de la base de datos, dentro de la carpeta donde está 
alojado la base de datos (database)

mysql -h [ip] -u [usuario] -p[contraseña] notificaciones < database.sql

recordar que para la contraseña no hay que dejar espacio, ej: -pPassword
