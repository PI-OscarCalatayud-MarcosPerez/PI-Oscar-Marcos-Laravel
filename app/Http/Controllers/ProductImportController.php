<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

/**
 * Controlador de importación masiva de productos.
 * Gestiona la lectura de archivos CSV, validación de datos y
 * persistencia en la base de datos mediante Eloquent.
 */
class ProductImportController extends Controller
{
    /** Cabeceras esperadas en el archivo CSV */
    private const CSV_HEADERS = ['id', 'nombre', 'descripcion', 'precio', 'img', 'estoc', 'categoria'];

    /**
     * Muestra el formulario de importación (vista Blade).
     */
    public function show()
    {
        return view('pages.formulario');
    }

    /**
     * Procesa la subida del CSV y devuelve una vista Blade con los resultados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'arxiuCsv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $resultado = $this->procesarCsv($request->file('arxiuCsv'));

        return view('pages.formulario', [
            'messages' => $resultado['messages'],
            'csvErrors' => $resultado['csvErrors'],
            'rowErrors' => $resultado['rowErrors'],
            'importedCount' => $resultado['importedCount'],
        ]);
    }

    /**
     * Procesa el CSV y devuelve respuesta JSON (usado por la SPA y herramientas externas).
     */
    public function importApi(Request $request)
    {
        $request->validate([
            'arxiuCsv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $resultado = $this->procesarCsv($request->file('arxiuCsv'));

        return response()->json([
            'success' => true,
            'imported_count' => $resultado['importedCount'],
            'messages' => $resultado['messages'],
            'errors' => $resultado['csvErrors'],
            'row_warnings' => $resultado['rowErrors'],
        ]);
    }

    /**
     * Lógica compartida de procesamiento del archivo CSV.
     * Lee el fichero fila por fila, valida los datos y los inserta en la BD.
     *
     * @param  \Illuminate\Http\UploadedFile  $file  Archivo CSV subido
     * @return array  Resultado con contadores y mensajes
     */
    private function procesarCsv($file): array
    {
        $path = $file->getRealPath();
        $messages = [];
        $csvErrors = [];
        $rowErrors = [];
        $importedCount = 0;

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return $this->resultado(0, [], ['No se pudo abrir el archivo CSV.'], []);
        }

        $headerRow = fgetcsv($handle, 1000, ',');
        if (!$headerRow) {
            fclose($handle);
            return $this->resultado(0, [], ['El archivo CSV está vacío o no se puede leer.'], []);
        }

        // Limpiar BOM y comillas del header
        $headerRow[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headerRow[0]);
        if (count($headerRow) === 1) {
            $headerRow = str_getcsv(trim($headerRow[0], '"'), ',');
        }

        $headers = array_map('strtolower', array_map('trim', $headerRow));
        $headerMap = array_flip($headers);

        // Validar que las columnas obligatorias están presentes
        $missing = array_diff(self::CSV_HEADERS, $headers);
        if (!empty($missing)) {
            fclose($handle);
            return $this->resultado(0, [], ['Faltan columnas: ' . implode(', ', $missing)], []);
        }

        // Procesar cada fila del CSV
        $rowNum = 1;
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNum++;

            // Corregir filas con formato incorrecto (todo en una sola columna)
            if (count($row) === 1) {
                $row = str_getcsv(trim($row[0], '"'), ',');
                $row = array_map(fn($v) => str_replace('""', '"', $v), $row);
            }

            if (implode('', $row) === '')
                continue;

            if (count($row) !== count($headers)) {
                $rowErrors[] = "Fila $rowNum ignorada: Columnas insuficientes.";
                continue;
            }

            // Mapear datos del CSV a variables
            $id = (int) $row[$headerMap['id']];
            $nombre = trim($row[$headerMap['nombre']]);
            $precio = (float) str_replace(',', '.', $row[$headerMap['precio']]);
            $stock = (int) $row[$headerMap['estoc']];
            $categoria = trim($row[$headerMap['categoria']]);
            $imagenUrl = trim($row[$headerMap['img']]);
            $descripcion = trim($row[$headerMap['descripcion']]);

            // Verificar duplicados por ID
            if (Product::find($id)) {
                $rowErrors[] = "Fila $rowNum ignorada: El ID $id ya existe.";
                continue;
            }

            // Generar SKU y verificar duplicados
            $sku = 'JUEGO-' . $id;
            if (Product::where('sku', $sku)->exists()) {
                $rowErrors[] = "Fila $rowNum ignorada: El SKU $sku ya existe.";
                continue;
            }

            // Validar datos obligatorios
            if (empty($nombre) || $precio <= 0 || $stock < 0) {
                $rowErrors[] = "Fila $rowNum ignorada: Datos inválidos ($nombre).";
                continue;
            }

            // Insertar producto en la base de datos
            try {
                $product = new Product();
                $product->id = $id;
                $product->sku = $sku;
                $product->nombre = $nombre;
                $product->descripcion = $descripcion;
                $product->precio = $precio;
                $product->stock = $stock;
                $product->imagen_url = $imagenUrl;
                $product->categoria = $categoria;
                $product->save();

                $importedCount++;
            } catch (\Exception $e) {
                $rowErrors[] = "Fila $rowNum error al guardar: " . $e->getMessage();
            }
        }

        fclose($handle);

        if ($importedCount > 0) {
            $totalCount = Product::count();
            $messages[] = "Proceso completado. $importedCount productos añadidos. Total: $totalCount.";
        }

        return $this->resultado($importedCount, $messages, $csvErrors, $rowErrors);
    }

    /**
     * Genera el array de resultado estandarizado.
     */
    private function resultado(int $count, array $messages, array $csvErrors, array $rowErrors): array
    {
        return [
            'importedCount' => $count,
            'messages' => $messages,
            'csvErrors' => $csvErrors,
            'rowErrors' => $rowErrors,
        ];
    }
}
