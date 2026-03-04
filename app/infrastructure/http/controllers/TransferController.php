<?php

namespace app\infrastructure\http\controllers;

use app\application\account\transfer\TransferMoneyHandler;
use app\application\controllers\JsonResponse;
use app\application\controllers\Request;
use app\application\controllers\TransferMoneyCommand;
use yii\web\Controller;

class TransferController extends Controller
{
    public function __construct(
        private TransferMoneyHandler $handler
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $command = new TransferMoneyCommand(
            $data['from'],
            $data['to'],
            (int) $data['amount'],
            $data['currency'],
            $data['reference']
        );

        $this->handler->handle($command);

        return new JsonResponse(['status' => 'ok']);
    }

}
