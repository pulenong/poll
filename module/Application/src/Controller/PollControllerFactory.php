<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Form\Poll\CreateForm;
use Application\Model\Table\OptionsTable;
use Application\Model\Table\PollsTable;
use Application\Model\Table\VotesTable;
use Laminas\Form\FormElementManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class PollControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): PollController
    {
        $formManager = $container->get(FormElementManager::class);
        return new PollController(
            $formManager->get(CreateForm::class),
            $container->get(OptionsTable::class),
            $container->get(PollsTable::class),
            $container->get(VotesTable::class)
        );
    }
}