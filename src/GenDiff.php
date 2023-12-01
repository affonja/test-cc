<?php

namespace Differ\GenDiff;

use function Functional\flatten;

define("ROOT_DIR", $_SERVER["PWD"]);
define("FIXTURES_DIR", ROOT_DIR . '/tests/fixtures/');

function genDiff(string $path1, string $path2): string
{
    $is_absolute_path = getTypePath($path1);
    $path1 = getFullPath($path1, $is_absolute_path);
    $is_absolute_path = getTypePath($path2);
    $path2 = getFullPath($path2, $is_absolute_path);

    if (!file_exists($path1) && (!file_exists($path2))) {
        throw new \Exception('File not exist');
    }
    $file1 = (array)json_decode(file_get_contents($path1));
    $file2 = (array)json_decode(file_get_contents($path2));

    $keys = array_merge(array_keys($file1), array_keys($file2));
    sort($keys);
    $keys = array_unique($keys);


    $diff = array_map(
        function ($key) use ($file2, $file1) {
            if (array_key_exists($key, $file1) && !array_key_exists($key, $file2)) {
                $result[] = "- $key: " . boolToString($file1[$key]);
            } elseif (!array_key_exists($key, $file1) && array_key_exists($key, $file2)) {
                $result[] = "+ $key: " . boolToString($file2[$key]);
            } elseif (array_key_exists($key, $file1) && array_key_exists($key, $file2)) {
                if ($file1[$key] === $file2[$key]) {
                    $result[] = "  $key: " . boolToString($file1[$key]);
                } else {
                    $result[] = "- $key: " . boolToString($file1[$key]);
                    $result[] = "+ $key: " . boolToString($file2[$key]);
                }
            }
            return $result ?? [];
        },
        $keys
    );
    $diff = flatten($diff);

    return implode(PHP_EOL, $diff);
}

function getFullPath(string $path, bool $type): string
{
    if ($type) {
        if (stripos(pathinfo($path)['dirname'], realpath(ROOT_DIR)) === false) {
            if ($path[0] === '/' || $path[0] === '\\') {
                $path = ROOT_DIR . $path;
            }
        }
    } else {
        if (pathinfo($path)['dirname'] === '.') {
            $path = FIXTURES_DIR . $path;
        } else {
            $path = ROOT_DIR . '/' . $path;
        }
    }

    return realpath($path) ?? '';
}

function getTypePath(string $path): bool
{
    $a = strspn($path, '/\\', 0, 1);
    $b = strlen($path) > 3 && ctype_alpha($path[0]);
    $c = substr($path, 1, 1) === ':';
    $d = strspn($path, '/\\', 2, 1);

    return $a || ($b && $c && $d);
}

function boolToString($val): string|int
{
    return is_bool($val) ? ($val === true ? 'true' : 'false') : $val;
}
