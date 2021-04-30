<?php

namespace App\Command;

use App\Repository\LabelRepository;
use App\Repository\UserRepository;
use App\Service\Mail\LabelRequestEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LabelRequestCommand extends Command
{
    protected static $defaultName = 'app:store-label-request';
    private UserRepository $userRepository;
    private LabelRepository $labelRepository;
    private LabelRequestEmail $labelRequestEmail;


    public function __construct(UserRepository $userRepository, LabelRepository $labelRepository, LabelRequestEmail $labelRequestEmail)
    {
        parent::__construct(null);
        $this->userRepository = $userRepository;
        $this->labelRepository = $labelRepository;
        $this->labelRequestEmail = $labelRequestEmail;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $adminsStore = $this->userRepository->findBy(['isValid'=>true, 'type' => 1]);

        foreach ($adminsStore as $admin){
            $store = $admin->getStore();

            $labels = $this->labelRepository->labelsRequest($store->getId());

            if ($labels && count($labels) > 0){
                $this->labelRequestEmail->send($admin, $labels);
            }

        }
        return 1;
    }
}
