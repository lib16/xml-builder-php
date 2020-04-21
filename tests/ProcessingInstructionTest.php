<?php
namespace Lib16\XML\Tests;

use Lib16\XML\ProcessingInstruction;
use PHPUnit\Framework\TestCase;

class ProcessingInstructionTest extends TestCase
{

    public function provider(): array
    {
        return [
            [
                ProcessingInstruction::create('target', 'content'),
                '<?target content ?>'
            ],
            [
                ProcessingInstruction::create('target')
                    ->attrib('attrib1', 'value1')
                    ->attrib('attrib2', 'value2'),
                '<?target attrib1="value1" attrib2="value2" ?>'
            ]
        ];
    }

    /**
     * @dataProvider provider
     */
    public function test(ProcessingInstruction $actual, string $expected)
    {
        $this->assertEquals($expected, $actual->__toString());
    }
}
