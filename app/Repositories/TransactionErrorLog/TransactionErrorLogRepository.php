<?php

namespace App\Repositories\TransactionErrorLog;

use Exception;
use App\Repositories\Base\BaseRepository;
use App\Repositories\TransactionErrorLog\Contracts\TransactionErrorLogRepositoryInterface;
use App\Models\TransactionErrorLog;

class TransactionErrorLogRepository extends BaseRepository implements TransactionErrorLogRepositoryInterface
{
    /**
     * @param TransactionErrorLog $transactionErrorLog
     *
     * @return void
     */
    public function __construct(TransactionErrorLog $transactionErrorLog)
    {
        $this->model = $transactionErrorLog;
    }

    /**
     * @param Exception $e
     * @param array $information
     *
     * @return void
     */
    public function saveLog(Exception $e, array $information = []): void
    {
        $content = '';

        foreach ($information as $key => $info) {
            $content .= '[' . $key . ': ' . $info . '] ';
        }

        $this->model->create([
            'information'       => $content,
            'exception_message' => $e->getMessage(),
            'exception_trace'   => $e->getTraceAsString(),
        ]);
    }
}
