<?php


namespace App\Concerns;

use Illuminate\Support\Collection;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

trait HasEnvFile
{
    /**
     * @return Collection
     */
    public function getAllEnv() : Collection
    {
        $envDetails = DotenvEditor::getKeys();
        $envDetails = new Collection($envDetails);
        return $envDetails->map(function ($item, $key) {
            return [
                'key' => $key,
                'data' => $item,
            ];
        })->groupBy(function ($item, $key) {
            $key = explode('_', $key);
            return $key[0];
        });
    }

    /**
     * Get the specified env data.
     * @param  array  $env
     * @return Collection
     */
    public function getEnv(array $env) : Collection
    {
        $envDetails = DotenvEditor::getKeys($env);
        $envDetails = new Collection($envDetails);
        return $envDetails->map(function ($item, $key) {
            return [
                'key' => $key,
                'data' => $item,
            ];
        })->groupBy(function ($item, $key) {
            $key = explode('_', $key);
            return $key[0];
        });
    }

    /**
     * @param  arrray $inputs
     * @return Collection
     */
    public function updateEnv($inputs) : void
    {
        $keys = DotenvEditor::getKeys($inputs->keys());

        array_walk($keys, function ($data, $key) use ($inputs) {
            if ($inputs->input($key) != $data['value']) {
                DotenvEditor::setKey($key, $inputs->input($key));
            }
        });

        DotenvEditor::save();
    }
}
