<?php

declare(strict_types=1);

namespace Application;

use Application\Form\Poll\CreateForm;
use Application\Model\Table\CategoriesTable;
use Application\Model\Table\OptionsTable;
use Application\Model\Table\PollsTable;
use Application\Model\Table\VotesTable;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\AdapterInterface;

class Module
{
    public function getConfig(): array
    {
        /** @var array $config */
        $config = include __DIR__ . '/../config/module.config.php';
        return $config;
    }

    public function getServiceConfig(): array
    {
        return [
            'factories' => [
                CategoriesTable::class => function ($container) {
                    $adapter = $container->get(AdapterInterface::class);
                    return new CategoriesTable($adapter);
                },
                OptionsTable::class => function ($container) {
                    $adapter = $container->get(AdapterInterface::class);
                    return new OptionsTable($adapter);
                },
                PollsTable::class => function ($container) {
                    $adapter = $container->get(Adapter::class);
                    return new PollsTable($adapter);
                },
                VotesTable::class => function ($container) {
                    $adapter = $container->get(AdapterInterface::class);
                    return new VotesTable($adapter);
                }
            ]
        ];
    }

    public function getFormElementConfig(): array
    {
        return [
            'factories' => [
                CreateForm::class => function ($container) {
                    $categoriesTable = $container->get(CategoriesTable::class);
                    return new CreateForm($categoriesTable);
                }
            ]
        ];
    }
}
