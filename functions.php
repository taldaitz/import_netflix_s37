<?php

function sum(int ...$values) : int  // Parametre variatic
{
    $result = 0;

    foreach($values as $value) {
        $result += $value;
    }
    return $result;
}

function absoluteSum(int $firstValue, int $secondValue) : int
{
    return abs(sum($firstValue, $secondValue));
}

function substract(int $firstValue, int $secondValue) : int
{
    return sum($firstValue, $secondValue * -1);
}

function countdown(int $number) : array
{
    $result = [];

    for($i = $number; $i >= 0; $i--)
    {
        $result[] = $i;
    }

    return $result;
} 

function divide(int $number, int $divisor) : float
{
    if($divisor === 0)  throw new InvalidArgumentException();
    
    return $number / $divisor;
}


function howManyLetters(string $sentence) : int 
{
    return strlen(str_replace(' ', '', trim($sentence)));
}