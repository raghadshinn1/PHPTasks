<?php


declare(strict_types=1);


function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

function formatJOD(float $amount): string
{
    return number_format($amount, 3) . " JOD";
}

const ACADEMY_NAME = "Orange Coding Academy";
