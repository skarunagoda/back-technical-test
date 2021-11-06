<?php

namespace App\Handler;

use App\Entity\Order;
use Doctrine\Common\Collections\Collection;

class OrderIssueHandler
{
  public function handle(Order $order): array
  {
    return $order->getIssues()->toArray();
  }

}
