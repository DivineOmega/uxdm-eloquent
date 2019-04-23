<?php

use DivineOmega\uxdm\Objects\Sources\EloquentSource;
use DivineOmega\uxdm\TestClasses\Eloquent\User;
use PHPUnit\Framework\TestCase;

final class EloquentSourceTest extends TestCase
{
    private function createSource()
    {
        require_once 'includes/BootSourceEloquentDatabase.php';

        return new EloquentSource(User::class);
    }

    private function createSourceWithCallback()
    {
        require_once 'includes/BootSourceEloquentDatabase.php';

        return new EloquentSource(User::class, function ($query) {
            $query->where('name', 'Bear');
        });
    }

    public function testGetFields()
    {
        $source = $this->createSource();

        $this->assertEquals(['id', 'name', 'email'], $source->getFields());
    }

    public function testGetDataRows()
    {
        $source = $this->createSource();

        $dataRows = $source->getDataRows(1, ['name', 'email']);

        $this->assertCount(2, $dataRows);

        $dataItems = $dataRows[0]->getDataItems();

        $this->assertCount(2, $dataItems);

        $this->assertEquals('name', $dataItems[0]->fieldName);
        $this->assertEquals('Tim', $dataItems[0]->value);

        $this->assertEquals('email', $dataItems[1]->fieldName);
        $this->assertEquals('tim@example.com', $dataItems[1]->value);

        $dataItems = $dataRows[1]->getDataItems();

        $this->assertCount(2, $dataItems);

        $this->assertEquals('name', $dataItems[0]->fieldName);
        $this->assertEquals('Bear', $dataItems[0]->value);

        $this->assertEquals('email', $dataItems[1]->fieldName);
        $this->assertEquals('bear@example.com', $dataItems[1]->value);

        $dataRows = $source->getDataRows(2, ['name', 'email']);

        $this->assertCount(0, $dataRows);
    }

    public function testGetDataRowsOnlyOneField()
    {
        $source = $this->createSource();

        $dataRows = $source->getDataRows(1, ['name']);

        $this->assertCount(2, $dataRows);

        $dataItems = $dataRows[0]->getDataItems();

        $this->assertCount(1, $dataItems);

        $this->assertEquals('name', $dataItems[0]->fieldName);
        $this->assertEquals('Tim', $dataItems[0]->value);

        $dataItems = $dataRows[1]->getDataItems();

        $this->assertCount(1, $dataItems);

        $this->assertEquals('name', $dataItems[0]->fieldName);
        $this->assertEquals('Bear', $dataItems[0]->value);

        $dataRows = $source->getDataRows(2, ['name']);

        $this->assertCount(0, $dataRows);
    }

    public function testGetDataRowsWithQueryCallback()
    {
        $source = $this->createSourceWithCallback();

        $dataRows = $source->getDataRows(1, ['name', 'email']);

        $this->assertCount(1, $dataRows);

        $dataItems = $dataRows[0]->getDataItems();

        $this->assertCount(2, $dataItems);

        $this->assertEquals('name', $dataItems[0]->fieldName);
        $this->assertEquals('Bear', $dataItems[0]->value);

        $this->assertEquals('email', $dataItems[1]->fieldName);
        $this->assertEquals('bear@example.com', $dataItems[1]->value);

        $dataRows = $source->getDataRows(2, ['name', 'email']);

        $this->assertCount(0, $dataRows);
    }

    public function testCountDataRows()
    {
        $source = $this->createSource();

        $this->assertEquals(2, $source->countDataRows());
    }

    public function testCountPages()
    {
        $source = $this->createSource();

        $this->assertEquals(1, $source->countPages());
    }
}
