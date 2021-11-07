<?php

namespace App\Controller;

use App\Entity\Order;
use App\Handler\OrderIssueHandler;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ListOrderIssuesController extends AbstractController
{
    protected $orderIssueHandler;

    public function __construct(OrderIssueHandler $orderIssueHandler)
    {
        $this->orderIssueHandler = $orderIssueHandler;
    }

    public function __invoke(Order $data): array
    {
        $issues = $this->orderIssueHandler->handle($data, true);

        foreach ($issues as $key => $issue) {
            $issuesArray[] = [
            'id' => $issue->getId(),
            'issue' => $issue->getIssue(),
          ];
        }

        return $issuesArray;
    }
}
