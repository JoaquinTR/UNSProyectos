#!/bin/bash
#NOTA: Buscar los archivos vendor/laravel/sail/runtime/XXX/Dockerfile y pisar:
#'&& curl -sL https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - \' con:
#'&& curl -fsSL https://deb.nodesource.com/setup_$NODE_VERSION.x | bash - \'
vendor/bin/sail down --remove-orphans -v # Baja los containers y restruye los volumenes
vendor/bin/sail build --no-cache --parallel --force-rm # Toma tiempo, reconstruye las imágenes
echo "correr 'sail up' manualmente"