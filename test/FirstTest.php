<?php

include 'functions.php';

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FirstTest extends TestCase
{
    public function testCheckTestWork()
    {
        $this->assertTrue(true);
    }

    public function testSubstract()
    {
        //Arrange
        $value1 = 8;
        $value2 = 3;
        $expectedResult = 5;

        //Act
        $result = substract($value1, $value2);

        //Assert
        $this->assertEquals($expectedResult, $result);
    }

    public function testAbsSum()
    {
        //Arrange
        $value1 = 10;
        $value2 = -30;
        $expectedResult = 20;

        //Act
        $result = absoluteSum($value1, $value2);

        //Assert
        $this->assertEquals($expectedResult, $result);
    }

    public function testCountdown()
    {
        $value = 8;
        $expectedResult = [8, 7, 6, 5, 4, 3, 2, 1, 0];

        $result = countdown($value);

        $this->assertEquals($expectedResult, $result);
        $this->assertContains(4, $result);
    }

    #[DataProvider('GetValuesForSum')]
    public function testSum(array $values, int $expectedResult)
    {
        //Act
        $result = sum(...$values);

        //Assert
        $this->assertEquals($expectedResult, $result);
    }

    public static function GetValuesForSum() : array
    {
        return [
            [[1, 2], 3],
            [[4, 5], 9],
            [[3, 5, 2], 10],
            [[1, 1, 1, 1, 1], 5],
            [[2], 2],
        ];
    }


    public function testDivide()
    {
        //Arrange
        $value1 = 10;
        $value2 = 5;
        $expectedResult = 2;

        //Act
        $result = divide($value1, $value2);

        //Assert
        $this->assertEquals($expectedResult, $result);
    }

    public function testDivideByZero()
    {
        //Arrange
        $value1 = 10;
        $value2 = 0;

        $this->expectException(InvalidArgumentException::class);
        //Act
        $result = divide($value1, $value2);
    }

    #[DataProvider('GetSentences')]
    public function testHowManyLetters($value, $expectedResult)
    {
        $result = howManyLetters($value);

        $this->assertEquals($expectedResult, $result);
    }

    public static function GetSentences() : array
    {
        return [
            ['thomas', 6],
            ['thomas est formateur', 18],
            ['          thomas est formateur', 18],
            ['          thomas est formateur         ', 18],
            ['              thomas est formateur         ', 18],
        ];
    }
}