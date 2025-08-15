<?php

namespace App\Concerns;

/**
 * Trait para manejar columnas vector en PostgreSQL con pgvector
 * Uso: use HasVectorColumns; en tu modelo
 */
trait HasVectorColumns
{
    /**
     * Convierte array a formato vector de PostgreSQL
     */
    public function arrayToVector(array $vector): string
    {
        return '[' . implode(',', $vector) . ']';
    }

    /**
     * Convierte string vector de PostgreSQL a array
     */
    public function vectorToArray(string $vector): array
    {
        $vector = trim($vector, '[]');
        return array_map('floatval', explode(',', $vector));
    }

    /**
     * Calcular distancia L2 entre dos vectores
     */
    public function l2Distance(array $vector1, array $vector2): float
    {
        $sum = 0;
        for ($i = 0; $i < count($vector1); $i++) {
            $sum += pow($vector1[$i] - $vector2[$i], 2);
        }
        return sqrt($sum);
    }

    /**
     * Calcular similitud coseno entre dos vectores
     */
    public function cosineSimilarity(array $vector1, array $vector2): float
    {
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        for ($i = 0; $i < count($vector1); $i++) {
            $dotProduct += $vector1[$i] * $vector2[$i];
            $magnitude1 += pow($vector1[$i], 2);
            $magnitude2 += pow($vector2[$i], 2);
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0;
        }

        return $dotProduct / ($magnitude1 * $magnitude2);
    }
}
