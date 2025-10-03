<?php

namespace App\Services;

use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventoryService
{
    private int $scale = 3;
    /**
     * @param 'import'|'export'|'adjust' $type
     * @param array $opts = [
     *   productID(int), warehouseID(int), unitID(?int),
     *   quantity(float > 0), reference(?Model), code(?string), note(?string)
     * ]
     */
    public function record(string $type, array $opts): InventoryTransaction
    {
        // 1) Kiểm tra tham số
        if (!in_array($type, ['import', 'export', 'adjust'], true)) {
            throw new InvalidArgumentException('Không hợp lệ.');
        }
        if (!isset($opts['productID'], $opts['warehouseID'], $opts['quantity'])) {
            throw new InvalidArgumentException('Gửi yêu cầu thất bại.');
        }
        $productID   = (int) $opts['productID'];
        $warehouseID = (int) $opts['warehouseID'];
        $unitID      = $opts['unitID'] ?? null;
        $qtyRaw      = (float) $opts['quantity'];
        $reference   = $opts['reference'] ?? null;
        $date   = $opts['date'] ?? null;
        if ($qtyRaw <= 0) {
            throw new InvalidArgumentException('Số lượng phải > 0.');
        }
        $quantityChanged =
            $type === 'export' ? -abs($qtyRaw) : ($type === 'import' ? abs($qtyRaw) : (float)$qtyRaw);
        $quantityChanged = round($quantityChanged, $this->scale);
        return DB::transaction(function () use ($productID, $warehouseID, $unitID, $type, $quantityChanged, $opts, $reference) {
            // 2) Khóa dòng tồn tổng
            $stock = InventoryStock::where('productID', $productID)
                ->where('warehouseID', $warehouseID)
                ->lockForUpdate()
                ->first();
            if (!$stock) {
                // tạo mới nếu chưa có
                $stock = InventoryStock::create([
                    'productID'   => $productID,
                    'warehouseID' => $warehouseID,
                    'unitID'      => $unitID,
                    'quantity'    => 0,
                    'status'      => 'active',
                ]);
            }
            // 3) Tính số dư sau giao dịch
            $current = round((float)$stock->quantity, $this->scale);
            $newBalance = round($current + $quantityChanged, $this->scale);
            // clamp sai số cực nhỏ về 0 (ví dụ -0.0000001)
            if ($newBalance < 0 && abs($newBalance) < pow(10, -$this->scale)) {
                $newBalance = 0.0;
            }
            // 4) Chặn âm tồn (mọi type)
            if ($newBalance < 0) {
                $need = $type === 'export' ? abs($quantityChanged) : 0;
                throw new InvalidArgumentException(
                    "Không đủ tồn kho. Hiện tại: {$current}, Thay đổi: {$quantityChanged}, Cần: {$need}"
                );
            }
            // 5) Cập nhật tồn tổng
            $stock->update(['quantity' => $newBalance]);
            // 6) Ghi sổ cái
            $tx = new InventoryTransaction([
                'productID'        => $productID,
                'warehouseID'      => $warehouseID,
                'unitID'           => $unitID,
                'type'             => $type,
                'quantity_changed' => $quantityChanged,
                'balance_after'    => $newBalance,
                'code'             => $opts['code'] ?? null,
                'note'             => $opts['note'] ?? null,
                'date'             => $opts['date'] ?? null,
                'created_by'       => Auth::id(),
            ]);
            if ($reference) {
                $tx->reference()->associate($reference);
            }
            $tx->save();
            return $tx;
        });
    }
}
