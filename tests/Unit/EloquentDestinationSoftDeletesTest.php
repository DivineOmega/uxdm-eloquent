<?php

namespace DivineOmega\uxdm\Objects\Tests;

use Carbon\Carbon;
use DivineOmega\uxdm\Objects\DataItem;
use DivineOmega\uxdm\Objects\DataRow;
use DivineOmega\uxdm\Objects\Destinations\EloquentDestination;
use DivineOmega\uxdm\TestClasses\Eloquent\SoftDeletableUser;
use PHPUnit\Framework\TestCase;

final class EloquentDestinationSoftDeletesTest extends TestCase
{
    private $pdo = null;

    private function getDestination()
    {
        $this->pdo = new \PDO('sqlite:'.__DIR__.'/Data/destination.sqlite');

        $sql = 'DROP TABLE IF EXISTS soft_deletable_users';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $sql = 'CREATE TABLE IF NOT EXISTS soft_deletable_users (id integer primary key autoincrement, name TEXT, value INTEGER, deleted_at datetime)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        require_once 'includes/BootDestinationEloquentDatabase.php';

        return new EloquentDestination(SoftDeletableUser::class);
    }

    private function createDataRows()
    {
        $faker = \Faker\Factory::create();

        $dataRows = [];

        $dataRow = new DataRow();
        $dataRow->addDataItem(new DataItem('name', $faker->word, true));
        $dataRow->addDataItem(new DataItem('value', $faker->randomNumber));
        $dataRow->addDataItem(new DataItem('deleted_at', Carbon::parse($faker->dateTime)->format('Y-m-d H:i:s')));
        $dataRows[] = $dataRow;

        $dataRow = new DataRow();
        $dataRow->addDataItem(new DataItem('name', $faker->word, true));
        $dataRow->addDataItem(new DataItem('value', $faker->randomNumber));
        $dataRow->addDataItem(new DataItem('deleted_at', Carbon::parse($faker->dateTime)->format('Y-m-d H:i:s')));
        $dataRows[] = $dataRow;

        return $dataRows;
    }

    private function createEmptyDataRows()
    {
        $dataRows = [];

        $dataRow = new DataRow();
        $dataRow->addDataItem(new DataItem('name'));
        $dataRow->addDataItem(new DataItem('value'));
        $dataRow->addDataItem(new DataItem('deleted_at'));
        $dataRows[] = $dataRow;

        return $dataRows;
    }

    private function alterDataRows(array $dataRows)
    {
        $faker = \Faker\Factory::create();

        foreach ($dataRows as $dataRow) {
            $dataItem = $dataRow->getDataItemByFieldName('value');
            $dataItem->value = $faker->randomNumber;
        }

        return $dataRows;
    }

    private function getActualArray()
    {
        $sql = 'SELECT name, value, deleted_at FROM soft_deletable_users';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $rows;
    }

    private function getActualEmptyArray()
    {
        return [
            [
                'name' => '',
                'value' => '',
                'deleted_at' => null,
            ],
        ];
    }

    private function getActualValueArray(array $dataRows)
    {
        $expectedArray = [];

        foreach ($dataRows as $dataRow) {
            $expectedArrayRow = [];
            foreach ($dataRow->getDataItems() as $dataItem) {
                $expectedArrayRow[$dataItem->fieldName] = $dataItem->value;
            }
            $expectedArray[] = $expectedArrayRow;
        }

        return $expectedArray;
    }

    private function getExpectedArray(array $dataRows)
    {
        $expectedArray = [];

        foreach ($dataRows as $dataRow) {
            $expectedArrayRow = [];
            foreach ($dataRow->getDataItems() as $dataItem) {
                $expectedArrayRow[$dataItem->fieldName] = $dataItem->value;
            }
            $expectedArray[] = $expectedArrayRow;
        }

        return $expectedArray;
    }

    public function testPutDataRows()
    {
        $dataRows = $this->createDataRows();

        $destination = $this->getDestination();

        $destination->putDataRows($dataRows);

        $this->assertEquals($this->getExpectedArray($dataRows), $this->getActualArray());

        $dataRows = $this->alterDataRows($dataRows);

        $destination->putDataRows($dataRows);

        $this->assertEquals($this->getExpectedArray($dataRows), $this->getActualArray());

        $dataRows = $this->createEmptyDataRows();

        $destination->putDataRows($dataRows);

        $this->assertEquals($this->getExpectedArray($dataRows), $this->getActualEmptyArray());

        $dataRows = $this->alterDataRows($dataRows);

        $destination->putDataRows($dataRows);

        $this->assertEquals($this->getExpectedArray($dataRows), $this->getActualValueArray($dataRows));

        $destination->finishMigration();
    }
}
