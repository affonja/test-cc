<?php

namespace Differ\Tests\JsonTest;

use PHPUnit\Framework\TestCase;

use function Differ\GenDiff\genDiff;

class JsonTest extends TestCase
{
    public function testGenDiff(): void
    {
        $file1 = 'file1.json';
        $file2 = 'file2.json';
        $str = "- follow: false
  host: hexlet.io
- proxy: 123.234.53.22
- timeout: 50
+ timeout: 20
+ verbose: true";

        $this->assertEquals($str, genDiff($file1, $file2));
    }
}
