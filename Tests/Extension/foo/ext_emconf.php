<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'co-stack.com Foo',
    'description' => 'Test extension to create log entries in the development environment',
    'category' => 'misc',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'Oliver Eglseder',
    'author_email' => 'oliver.eglseder@co-stack.com',
    'author_company' => 'co-stack.com',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
