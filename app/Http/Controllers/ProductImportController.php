<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductImportController extends Controller
{
    /**
     * SPRINT 3 - REQUISITO C4: Importación masiva de productos.
     * Este controlador gestiona la lógica para leer archivos CSV/Excel,
     * validar los datos y persistirlos en MySQL mediante Eloquent (v2).
     *
     * Muestra el formulario de importación.
     */
    public function show()
    {
        return view('pages.formulario');
    }

    /**
     * Procesa la subida del archivo CSV y actualiza la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validar subida
        $request->validate([
            'arxiuCsv' => 'required|file|mimes:csv,txt|max:2048', // 2MB máximo
        ]);

        $file = $request->file('arxiuCsv');
        $path = $file->getRealPath();

        $messages = [];
        $csvErrors = [];
        $rowErrors = [];
        $importedCount = 0;
        $nuevosAgregados = 0;

        $expectedHeaders = ['id', 'nombre', 'descripcion', 'precio', 'img', 'estoc', 'categoria'];

        // 2. Abrir fichero
        if (($handle = fopen($path, "r")) !== FALSE) {
            $headerRow = fgetcsv($handle, 1000, ",");

            if ($headerRow) {
                // Limpieza BOM y comillas
                $headerRow[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headerRow[0]);

                // Fix filas con formato incorrecto (todo en una columna)
                if (count($headerRow) === 1)
                    $headerRow = str_getcsv(trim($headerRow[0], '"'), ",");

                $headers = array_map('strtolower', array_map('trim', $headerRow));
                $headerMap = array_flip($headers);

                // Validar columnas
                $missing = array_diff($expectedHeaders, $headers);
                if (!empty($missing)) {
                    $csvErrors[] = "Faltan columnas: " . implode(', ', $missing);
                } else {
                    $rowNum = 1;

                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $rowNum++;

                        // Fix filas con formato incorrecto
                        if (count($row) === 1) {
                            $row = str_getcsv(trim($row[0], '"'), ",");
                            foreach ($row as $k => $v)
                                $row[$k] = str_replace('""', '"', $v);
                        }

                        if (implode('', $row) == '')
                            continue; // Saltar vacías

                        if (count($row) != count($headers)) {
                            $rowErrors[] = "Fila $rowNum ignorada: Columnas insuficientes.";
                            continue;
                        }

                        // Mapear datos
                        $id = (int) $row[$headerMap['id']];
                        $nom = trim($row[$headerMap['nombre']]);
                        $preu = (float) str_replace(',', '.', $row[$headerMap['precio']]);
                        $estoc = (int) $row[$headerMap['estoc']];
                        $categoria = trim($row[$headerMap['categoria']]);
                        $img = trim($row[$headerMap['img']]);
                        $descripcio = trim($row[$headerMap['descripcion']]);

                        // B) VALIDAR DUPLICADOS EN BD
                        // Verificamos si existe por ID o SKU
                        if (Product::find($id)) {
                            $rowErrors[] = "Fila $rowNum ignorada: El ID $id ya existe en la base de datos.";
                            continue;
                        }

                        // Generar SKU si no viene en CSV (el CSV del usuario no trae SKU explícito como columna requerida, lo construimos)
                        $sku = 'JUEGO-' . $id;
                        if (Product::where('sku', $sku)->exists()) {
                            $rowErrors[] = "Fila $rowNum ignorada: El SKU $sku ya existe en la base de datos.";
                            continue;
                        }

                        // Validaciones de datos
                        if (empty($nom) || $preu <= 0 || $estoc < 0) {
                            $rowErrors[] = "Fila $rowNum ignorada: Datos inválidos ($nom).";
                            continue;
                        }

                        // Agregar producto a BD
                        try {
                            // Usamos una instancia nueva para asignar el ID explícitamente (ya que no es fillable por defecto)
                            $product = new Product();
                            $product->id = $id;
                            $product->sku = $sku;
                            $product->nombre = $nom;
                            $product->descripcion = $descripcio;
                            $product->precio = $preu;
                            $product->stock = $estoc;
                            $product->imagen_url = $img;
                            $product->categoria = $categoria;
                            $product->save();

                            $nuevosAgregados++;
                        } catch (\Exception $e) {
                            $rowErrors[] = "Fila $rowNum error al guardar: " . $e->getMessage();
                        }
                    }
                }
            } else {
                $csvErrors[] = "El archivo CSV está vacío o no se puede leer.";
            }
            fclose($handle);
        } else {
            $csvErrors[] = "No se pudo abrir el archivo CSV.";
        }

        if ($nuevosAgregados > 0) {
            $importedCount = $nuevosAgregados;
            $totalCount = Product::count();
            $messages[] = "✅ ¡Proceso completado! Nuevos añadidos: $importedCount. Total en catálogo: $totalCount.";
        }

        return view('pages.formulario', compact('messages', 'csvErrors', 'rowErrors', 'importedCount'));
    }

    /**
     * SPRINT 3 - REQUISITO C5: API de Importación.
     * Versión API que procesa el archivo y devuelve una respuesta estructurada en JSON.
     * Útil para integrar con el frontend SPA o herramientas externas.
     */
    public function importApi(Request $request)
    {
        // 1. Validar subida
        $request->validate([
            'arxiuCsv' => 'required|file|mimes:csv,txt|max:2048', // 2MB máximo
        ]);

        $file = $request->file('arxiuCsv');
        $path = $file->getRealPath();

        $messages = [];
        $csvErrors = [];
        $rowErrors = [];
        $importedCount = 0;
        $nuevosAgregados = 0;

        $expectedHeaders = ['id', 'nombre', 'descripcion', 'precio', 'img', 'estoc', 'categoria'];

        // 2. Abrir fichero
        if (($handle = fopen($path, "r")) !== FALSE) {
            $headerRow = fgetcsv($handle, 1000, ",");

            if ($headerRow) {
                // Limpieza BOM y comillas
                $headerRow[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headerRow[0]);

                if (count($headerRow) === 1)
                    $headerRow = str_getcsv(trim($headerRow[0], '"'), ",");

                $headers = array_map('strtolower', array_map('trim', $headerRow));
                $headerMap = array_flip($headers);

                // Validar columnas
                $missing = array_diff($expectedHeaders, $headers);
                if (!empty($missing)) {
                    $csvErrors[] = "Faltan columnas: " . implode(', ', $missing);
                } else {
                    $rowNum = 1;

                    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $rowNum++;

                        if (count($row) === 1) {
                            $row = str_getcsv(trim($row[0], '"'), ",");
                            foreach ($row as $k => $v)
                                $row[$k] = str_replace('""', '"', $v);
                        }

                        if (implode('', $row) == '')
                            continue;

                        if (count($row) != count($headers)) {
                            $rowErrors[] = "Fila $rowNum ignorada: Columnas insuficientes.";
                            continue;
                        }

                        // Mapear datos
                        $id = (int) $row[$headerMap['id']];
                        $nom = trim($row[$headerMap['nombre']]);
                        $preu = (float) str_replace(',', '.', $row[$headerMap['precio']]);
                        $estoc = (int) $row[$headerMap['estoc']];
                        $categoria = trim($row[$headerMap['categoria']]);
                        $img = trim($row[$headerMap['img']]);
                        $descripcio = trim($row[$headerMap['descripcion']]);

                        if (Product::find($id)) {
                            $rowErrors[] = "Fila $rowNum ignorada: El ID $id ya existe.";
                            continue;
                        }

                        $sku = 'JUEGO-' . $id;
                        if (Product::where('sku', $sku)->exists()) {
                            $rowErrors[] = "Fila $rowNum ignorada: El SKU $sku ya existe.";
                            continue;
                        }

                        if (empty($nom) || $preu <= 0 || $estoc < 0) {
                            $rowErrors[] = "Fila $rowNum ignorada: Datos inválidos ($nom).";
                            continue;
                        }

                        try {
                            $product = new Product();
                            $product->id = $id;
                            $product->sku = $sku;
                            $product->nombre = $nom;
                            $product->descripcion = $descripcio;
                            $product->precio = $preu;
                            $product->stock = $estoc;
                            $product->imagen_url = $img;
                            $product->categoria = $categoria;
                            $product->save();

                            $nuevosAgregados++;
                        } catch (\Exception $e) {
                            $rowErrors[] = "Fila $rowNum error al guardar: " . $e->getMessage();
                        }
                    }
                }
            } else {
                $csvErrors[] = "El archivo CSV está vacío o no se puede leer.";
            }
            fclose($handle);
        } else {
            $csvErrors[] = "No se pudo abrir el archivo CSV.";
        }

        if ($nuevosAgregados > 0) {
            $importedCount = $nuevosAgregados;
            $totalCount = Product::count();
            $messages[] = "Proceso completado. $importedCount productos añadidos. Total: $totalCount.";
        }

        return response()->json([
            'success' => true,
            'imported_count' => $importedCount,
            'messages' => $messages,
            'errors' => $csvErrors,
            'row_warnings' => $rowErrors
        ]);
    }
}
