<?php

namespace App\Lib\Api;

use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\AbstractPaginator;
use App\Model\Invoice;

class InvoiceApi extends BaseApi
{
    protected $route = 'api_v1_invoice_find';

    protected $include_variables = [
        'invoice_type', 'creator', 'course', 'histories',
    ];

    protected function transform(Invoice $model)
    {
        return [
            'id' => $model->id,
            'type' => $model->type,
            'code' => $model->code,
            'invoice_type_id' => $model->invoice_type_id,
            'course_id' => $model->course_id,
            'amount' => $model->amount,
            'creator_id' => $model->creator_id,
            'creator_id' => $model->creator_id,
            'name' => $model->name,
            'phone' => $model->phone,
            'address' => $model->address,
            'slug' => $model->slug,
            'unit' => $model->unit,
            'content' => $model->content,
            'checkout_method' => $model->checkout_method,
            'note' => $model->note,
            'confirm' => $model->confirm,
            'status' => $model->status,
            'status_text' => $model->getStatusText(),
            'created_at' => $this->dateFormat($model->created_at, 'Y-m-d H:i:s'),
            'updated_at' => $this->dateFormat($model->updated_at, 'Y-m-d H:i:s'),
        ];
    }

    protected function includeCreator(Invoice $invoice)
    {
        return $this->get(UserApi::class)->render($invoice->creator);
    }

    protected function includeCourse(Invoice $invoice)
    {
        return $this->get(CourseApi::class)->render($invoice->course);
    }

    protected function includeInvoiceType(Invoice $invoice)
    {
        return $this->get(InvoiceTypeApi::class)->render($invoice->invoice_type);
    }

    protected function includeHistories(Invoice $invoice)
    {
        return $this->get(InvoiceUpdateHistoryApi::class)->render($invoice->update_histories);
    }

    protected function renderCollection(Collection $collection, array $relations)
    {
        $result = parent::renderCollection($collection, $relations);

        $thu = 0;
        $chi = 0;
        $tong = 0;

        foreach ($collection as $invoice) {
            if ($invoice->type == Invoice::TYPE_THU) {
                $thu += $invoice->amount;
                $tong += $invoice->amount;
            } elseif ($invoice->type == Invoice::TYPE_CHI) {
                $chi += $invoice->amount;
                $tong -= $invoice->amount;
            }
        }

        $result['meta']['invoice'] = [
            'chi' => $chi,
            'thu' => $thu,
            'tong' => $tong,
        ];

        return $result;
    }

    protected function renderCollectionWithPagination(AbstractPaginator $data, array $relations)
    {
        $result = parent::renderCollectionWithPagination($data, $relations);

        $thu = 0;
        $chi = 0;
        $tong = 0;

        foreach ($data as $invoice) {
            if ($invoice->type == Invoice::TYPE_THU) {
                $thu += $invoice->amount;
                $tong += $invoice->amount;
            } elseif ($invoice->type == Invoice::TYPE_CHI) {
                $chi += $invoice->amount;
                $tong -= $invoice->amount;
            }
        }

        $result['meta']['invoice'] = [
            'chi' => $chi,
            'thu' => $thu,
            'tong' => $tong,
        ];

        return $result;
    }

    protected function getHeaderExport(): array
    {
        return [
            'LOOPINDEX' => 'STT',
            'code' => 'Mã phiếu',
            'checkout_method' => 'TM/CK',
            'type' => 'Loại',
            'content' => 'Nội dung',
            'creator' => 'Người thu/chi',
            'name' => 'Người thanh toán',
            'confirm' => 'Xác nhận',
            'unit' => 'Đơn vị',
            'amount' => 'Số tiền',
        ];
    }

    protected function getMapExport(): array
    {
        return [
            'checkout_method' => function(Invoice $model) {
                return $model->checkout_method == Invoice::METHOD_TIEN_MAT ? 'Tiền mặt' : $model->checkout_method == Invoice::METHOD_CHUYEN_KHOAN_CA_NHAN ? 'CK cá nhân' : $model->checkout_method == Invoice::METHOD_CHUYEN_KHOAN_CTY ? 'CK công ty' : 'Quẹt thẻ';
            },
            'creator' => function(Invoice $model) {
                return $model->creator_id ? $model->creator->name : '';
            },
            'type' => function(Invoice $model) {
                return $model->type == Invoice::TYPE_THU ? 'Phiếu thu' : 'Phiếu chi';
            },
            'confirm' => function(Invoice $model) {
                return $model->confirm == Invoice::CONFIRM ? 'Đã XN' : 'Chưa XN';
            },
        ];
    }

    protected function getCenterColumnsExport(): array
    {
        return [
            'A', 'E', 'H',
        ];
    }

    protected function getTitleExport(Request $request): string
    {
        $from_date = $request->query->get('from_date', null);
        $to_date = $request->query->get('to_date', null);
        if ($from_date && $to_date) {
            return 'THỐNG KÊ THU CHI TỪ ' . date_format(date_create($from_date), 'd/m/Y') . ' ĐẾN ' . date_format(date_create($to_date), 'd/m/Y');
        }

        return 'THỐNG KÊ THU CHI';
    }
}
