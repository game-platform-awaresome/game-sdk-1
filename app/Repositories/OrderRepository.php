<?php
namespace App\Repositories;

use App\Constants\OrderStatus;
use App\Exceptions\Code;
use App\Exceptions\RenderException;
use App\Models\Order;
use App\Traits\OrderRepositoryTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class OrderRepository
{
    use OrderRepositoryTrait;

    /**
     * @var string
     */
    protected $tablePrefix = 'orders_';

    /**
     * @var int
     */
    protected $appId;

    /**
     * @var Order
     */
    protected $model;

    /**
     * OrderRepository constructor.
     * @param int $appId
     * @throws RenderException
     */
    public function __construct(int $appId)
    {
        $this->model = new Order();
        $this->appId = $appId;
        $this->checkTableExist($this->tablePrefix . $this->appId);
    }

    /**
     * @param $tableName
     * @throws RenderException
     */
    protected function checkTableExist($tableName)
    {
        if (!Schema::hasTable($tableName)) {
            throw new RenderException(Code::ORDER_DATABASE_NOT_FOUND, 'Order database not found, please contact developers');
        }
        $this->model->setTable($tableName);
    }

    /**
     * 获取订单全部信息
     *
     * @param string $orderId
     * @param array $columns
     * @return Order|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder
     * @throws RenderException
     */
    public function getOrderByOrderId(string $orderId, array $columns = ['*'])
    {
        try {
            return $this->model->where('order_id', $orderId)->firstOrFail($columns);
        } catch (ModelNotFoundException $e) {
            throw new RenderException(Code::INVALID_ORDER_ID, 'Invalid ORDER ID');
        }
    }

    /**
     * 获取订单状态
     *
     * @param string $orderId
     * @return int
     * @throws RenderException
     */
    public function getStatusByOrderId(string $orderId)
    {
        try {
            return $this->model->where('order_id', $orderId)->firstOrFail('status')->status;
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_ORDER_ID, 'Invalid ORDER ID');
        }
    }

    /**
     * 获取订单价格
     *
     * @param string $orderId
     * @return mixed
     * @throws RenderException
     */
    public function getAmountByOrderId(string $orderId)
    {
        try {
            return $this->model->where('order_id', $orderId)->firstOrFail('amount')->amount;
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_ORDER_ID, 'Invalid ORDER ID');
        }
    }

    /**
     * @param string $orderId
     * @param mixed $callbackAmount
     * @throws RenderException
     */
    public function updateCallbackAmountByOrderId(string $orderId, $callbackAmount): void
    {
        try {
            $this->model->where('order_id', $orderId)->update([
                'callback_amount' => $callbackAmount
            ]);
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_ORDER_ID, 'Invalid ORDER ID');
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::UPDATE_ORDER_FAIL, 'Order Update Fail');
        }
    }

    /**
     * 修改订单状态
     *
     * @param string $orderId
     * @param int $status
     * @throws RenderException
     */
    public function updateStatusByOrderId(string $orderId, int $status): void
    {
        try {
            $this->model->where('order_id', $orderId)->update([
                'status' => $status
            ]);
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_ORDER_ID, 'Invalid ORDER ID');
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::UPDATE_ORDER_FAIL, 'Order Update Fail');
        }
    }

    /**
     * 修改订单渠道
     *
     * @param string $orderId
     * @param int $payChannel
     * @throws RenderException
     */
    public function updatePayChannelByOrderId(string $orderId, int $payChannel): void
    {
        try {
            $this->model->where('order_id', $orderId)->update([
                'pay_channel' => $payChannel
            ]);
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_ORDER_ID, 'Invalid ORDER ID');
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::UPDATE_ORDER_FAIL, 'Order Update Fail');
        }
    }

    /**
     * @param string $orderId
     * @param string $outOrderNo
     * @throws RenderException
     */
    public function updateOutOrderNoByOrderId(string $orderId, string $outOrderNo): void
    {
        try {
            $this->model->where('order_id', $orderId)->update([
                'out_order_no' => $outOrderNo
            ]);
        } catch (ModelNotFoundException $exception) {
            throw new RenderException(Code::INVALID_ORDER_ID, 'Invalid ORDER ID');
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::UPDATE_ORDER_FAIL, 'Order Update Fail');
        }
    }

    /**
     * @param string $orderId
     * @param array $data
     * @return Order|\Illuminate\Database\Eloquent\Model
     * @throws RenderException
     */
    public function createOrder(string $orderId, array $data)
    {
        try {
            return $this->model->create([
                'order_id' => $orderId,
                "open_id" => $data['open_id'],
                "amount" => $data['amount'],
                "device" => $data['device'],
                "item_id" => $data['item_id'],
                "item_name" => $data['item_name'],
                "order_type" => $data['order_type'],
                "role_id" => $data['role_id'],
                "role_name" => $data['role_name'],
                "server_id" => $data['server_id'],
                "os" => $data['os'],
                "ip" => $data['ip'],
                "cp_order_no" => $data['cp_order_no'],
                "sdk_version" => '2.0',
                "extra_data" => $data['extra_data'] ?? null,
                "channel_id" => $data['channel_id'] ?? null,
                "sub_chan_merchant" => $data['sub_chan_merchant'] ?? null,
                "status" => OrderStatus::INIT
            ]);
        } catch (Exception $exception) {
            Log::channel('sdk')->error($exception->getMessage());
            Log::error($exception->getMessage());
            throw new RenderException(Code::CREATE_ORDER_FAIL, 'Create Order Fail');
        }
    }
}
