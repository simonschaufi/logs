<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'co-stack.com Logs',
    'description' => 'Log Reader with a backend module and API to read, filter and delete logs from the TYPO3 Logging API',
    'category' => 'module',
    'state' => 'stable',
    'author' => 'Oliver Eglseder',
    'author_email' => 'oliver.eglseder@co-stack.com',
    'author_company' => 'co-stack.com',
    'version' => '4.0.0',
    'constraints' => [
        'depends' => [
            'php' => '7.4.0-7.4.99',
            'typo3' => '11.0.0-11.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
