<?php

require __DIR__.'/vendor/autoload.php';

return (new Jubeki\LaravelCodeStyle\Config())
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in('src')
            ->in('tests')
    )
    ->setRules([
        '@Laravel' => true,
        // '@Laravel:risky' => true,
    ])
    // ->setRiskyAllowed(true)
;
