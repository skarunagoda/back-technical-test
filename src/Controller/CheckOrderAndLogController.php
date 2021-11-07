<?php

namespace App\Controller;

use App\Entity\Order;
use App\Handler\CheckOrderHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class CheckOrderAndLogController extends AbstractController
{
    protected $checkOrderHandler;

    public function __construct(CheckOrderHandler $checkOrderHandler)
    {
        $this->checkOrderHandler = $checkOrderHandler;
    }

    public function __invoke(Order $data): Order
    {
        $this->checkOrderHandler->handle($data, true);

        return $data;
    }
}
