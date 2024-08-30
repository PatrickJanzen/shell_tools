<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Host;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Host::class)]
class HostTest extends TestCase
{
    public function testHost(): void
    {
        $h = new Host();
        $h->setName('Test');
        $h->addOption('Hallo', 'Welt');
        $h->fillOptions(['fill'=>'me']);

        $h2 = new Host();
        $h2->setName('Test2');
        $h2->addOption('Welt', 'Hallo');
        $h->fillBase($h2);
        $h->fillOptions(['fill'=>'it']);

        $fields = $h->fields();
        $this->assertCount(4, $fields);
        $this->assertSame('alias', $fields[0]);
        $this->assertSame('hallo', $fields[1]);
        $this->assertSame('fill', $fields[2]);
        $this->assertSame('welt', $fields[3]);
        $this->assertSame('Test', $h->getName());

        $res = $h->toTableRow($fields);
        $this->assertSame(['alias' => 'Test', 'hallo' => 'Welt', 'fill' => '', 'welt' => 'Hallo'], $res);

        $this->assertTrue($h->match('T'));
        $this->assertFalse($h->match('F'));
    }
}
