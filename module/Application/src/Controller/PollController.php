<?php

declare(strict_types=1);

namespace Application\Controller;

use Application\Form\Poll\CreateForm;
use Application\Form\Poll\VoteForm;
use Application\Model\Table\OptionsTable;
use Application\Model\Table\PollsTable;
use Application\Model\Table\VotesTable;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use RuntimeException;
use User\Service\UrlService;

class PollController extends AbstractActionController
{
    public function __construct(
        private readonly CreateForm $createForm,
        private readonly OptionsTable $optionsTable,
        private readonly PollsTable $pollsTable,
        private readonly VotesTable $votesTable
    )
    {
    }

    public function createAction(): Response|ViewModel
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login', ['returnUrl' => UrlService::encode($this->getRequest()->getRequestUri())]);
        }

        $pollForm = $this->createForm;
        $request = $this->getRequest();

        if ($request->isPost()) {
            $pollForm->setInputFilter($this->pollsTable->getCreateFormSanitizer());
            $pollForm->setData($request->getPost()->toArray());

            if ($pollForm->isValid()) {
                $data = $pollForm->getData();

                try {

                    $info = $this->pollsTable->insertPoll($data);
                    $pollId = $info->getGeneratedValue();

                    // filter and validate options input field data
                    $options = (array) $this->params()->fromPost('options');
                    $options = array_filter(array_map('strip_tags', $options));
                    $options = array_filter(array_map('trim', $options));
                    $options = array_slice($options, 0, 4); // allow only 4 input options

                    foreach ($options as $index => $option) {
                        if (mb_strlen($option) > 100) {
                            $option[$index] = mb_substr($option, 0, 100);
                        }

                        $this->optionsTable->insertOption($option, (int) $pollId);
                    }

                    $this->flashMessenger()->addSuccessMessage('Poll successfully posted.');
                    return $this->redirect()->toRoute('poll', ['action' => 'vote', 'id' => $pollId]); 

                } catch (RuntimeException $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }


        return new ViewModel(['form' => $pollForm]);
    }

    public function indexAction(): ViewModel
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login', ['returnUrl' => UrlService::encode($this->getRequest()->getRequestUri())]); 
        }

        return new ViewModel(['polls' => $this->pollsTable->findByOwnerId((int) $this->accountPlugin()->getUserId())]);
    }

    public function voteAction(): Response|ViewModel
    {
        $pollId = (int) $this->params()->fromRoute('id');

        if (!$pollId || !$this->pollsTable->findById($pollId)) {
            return $this->notFoundAction();
        }

        $info = $this->pollsTable->findById((int)$pollId);

      
        if ($info->getTimeout() < date('Y-m-d H:i:s')) {
            $this->pollsTable->closePoll((int) $info->getPollId());

            return $this->redirect()->toRoute('poll', ['action' => 'view', 'id' => $info->getPollId(), 'slug' => $info->getSlug()]);
        }

        $voteForm = new VoteForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $voteForm->setInputFilter($this->votesTable->getVoteFormSanitizer());
            $voteForm->setData($request->getPost()->toArray());

            if ($voteForm->isValid()) {
                $data = $voteForm->getData();

                try {

                    $this->optionsTable->updateVoteTally((int) $data['optionId'], (int) $info->getPollId());
                    $this->votesTable->insertVote($data, (int) $info->getPollId());
                    $this->pollsTable->updateTotalVotes((int) $info->getPollId());

                    $this->flashMessenger()->addSuccessMessage('Your vote has been saved.');
                    return $this->redirect()->toRoute('poll', [
                        'action' => 'view',
                        'id' => $info->getPollId(),
                        'slug' => $info->getSlug()
                    ]);

                } catch (RuntimeException $e) {
                    $this->flashMessenger()->addErrorMessage($e->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel([
            'form' => $voteForm,
            'poll' => $info,
            'voteTbl' => $this->votesTable,
            'optionsTbl' => $this->optionsTable,
            'currentPage' => $this->getRequest()->getRequestUri()
        ]);
    }

    public function viewAction(): ViewModel
    {
        $pollId = (int) $this->params()->fromRoute('id');
        if (!$pollId || !$this->pollsTable->findById($pollId)) {
            return $this->notFoundAction();
        }

        $info = $this->pollsTable->findById($pollId);

        return new ViewModel([
            'poll' => $info,
            'optionsTbl' => $this->optionsTable,
        ]);
    }
}
