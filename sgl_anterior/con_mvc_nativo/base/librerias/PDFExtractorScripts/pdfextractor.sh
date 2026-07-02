#!/bin/bash

## ############################################################################
## PDFExtractor
##
## Herramienta de desacople de archivos PDF embebidos.
##
## Carlos XXXX
## 2023-01-17
## ############################################################################

# Configuración ###############################################################
APP_ID="PDFExtractor v0.1 beta"
APP_DATETIME="2023-01-17"

# Paths
SCRIPT_PATH="$(dirname "$0")"

# Herramientas necesarias
CMD_PDFDETACH=$( command -v pdfdetach )

# Defaults
INPUT_FILE=""
OUTPUT_PATH=""

# Funciones ###################################################################
function log_ts
{
	echo "$( date "+%Y-%m-%d %H:%M:%S" )"
}

function xecho ()
{
	echo -e "[$( log_ts )] $1"
}

function header
{
	xecho "----------------------------------------"
	xecho "$APP_ID"
	xecho "$APP_DATETIME"
	xecho "----------------------------------------"
	xecho ""
}

function ayuda
{
	xecho "Uso:"
	xecho "    $0 -i input.pdf -o /output/directory"
	xecho ""
	xecho "Ayuda:"
	xecho "    -i, --input ARCHIVO      Archivo PDF de entrada, el cual contiene los PDF"
	xecho "                             embebidos."
	xecho "    -o, --output DIRECTORIO  Directorio de salida el cual contendrá los archivos"
	xecho "                             embebidos que han sido extraídos."
	xecho "    -h, --help               Ayuda sobre la utilización de la herramienta."
	xecho ""
}

###############################################################################
# Entry point #################################################################
###############################################################################
header

# Control de parametros #######################################################
while [ "$1" != "" ]; do
	case $1 in
		-i | --input )	shift
						INPUT_FILE=$1
						;;
		-o | --output )	shift
						OUTPUT_PATH=$1
						;;
		-h | --help )	ayuda
						exit
						;;
		* )				ayuda
						exit 1
	esac
	shift
done

[[ "$CMD_PDFDETACH" == "" ]] && { ayuda; xecho "ERROR: herramienta 'pdfdetach' no econtrada. Abortando..."; exit 1; }
[[ "$INPUT_FILE" == "" ]] && { ayuda; xecho "ERROR: debe especificar el archivo de entrada. Abortando..."; exit 1; }
[[ "$OUTPUT_PATH" == "" ]] && { ayuda; xecho "ERROR: debe especificar el directorio de salida. Abortando..."; exit 1; }
[[ ! -f $INPUT_FILE ]] && { ayuda; xecho "No se encuentra el archivo '$INPUT_FILE'. Abortando..."; exit 1; }
[[ ! -d $OUTPUT_PATH ]] && { ayuda; xecho "No se encuentra el directorio '$OUTPUT_PATH'. Abortando..."; exit 1; }

# Quito la barra final a la ruta
[[ "${OUTPUT_PATH}" == */ ]] && OUTPUT_PATH="${OUTPUT_PATH: : -1}"

xecho "Parámetros:"
xecho "    INPUT_FILE  = $INPUT_FILE "
xecho "    OUTPUT_PATH = $OUTPUT_PATH "
xecho ""

# Procesamiento ###############################################################

embebidos=$( $CMD_PDFDETACH -list $INPUT_FILE \
	| grep -Pi '^[0-9]+: (.*)(\.pdf)$' \
	| sed -r 's|^([0-9]+): (.*)(\.pdf)$|\1/\2\3|gi' \
)

cant_embebidos=$( $CMD_PDFDETACH -list $INPUT_FILE \
	| grep -Pi '^[0-9]+: (.*)(\.pdf)$' \
	| wc -l
)

xecho "Extrayendo embebidos:"
[[ "$cant_embebidos" == "0" ]] && { xecho "    No hay archivos .pdf embebidos en el documento."; exit 0; }

OLDIFS=$IFS
IFS=$'\n'
for emb in $embebidos ; do
	id=$( echo $emb | cut -d'/' -f1 )
	archivo=$( echo $emb | cut -d'/' -f2 )
	$CMD_PDFDETACH $INPUT_FILE -save $id -o $OUTPUT_PATH/$archivo

	# Verifico existencia del archivo
	if [ -f $OUTPUT_PATH/$archivo ] ; then
		# Verifico el mime-type del archivo
		if [ "$( file --mime $archivo | grep -o ': application/pdf' )" == ": application/pdf" ] ; then
			xecho "    $id -> $archivo"
		else
			rm $archivo
			xecho "    ERROR AL EXTRAER: $archivo no es un .pdf válido."
		fi
	else
		xecho "    ERROR AL EXTRAER: $id -> $archivo"
	fi
done
IFS=$OLDIFS

xecho ""
xecho "Proceso finalizado."
