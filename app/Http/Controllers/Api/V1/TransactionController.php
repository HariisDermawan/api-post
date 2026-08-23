<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\TransactionResource;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class TransactionController extends Controller
{
    /**
     * PPN 11%.
     */
    private const TAX_RATE = '0.11';

    /**
     * Display a listing of the resource.
     */
    public function index(TransactionRequest $request): JsonResponse
    {
        $transactions = Transaction::query()
            ->with(['items.product', 'customer'])
            ->search($request->search)
            ->latest()
            ->paginate($request->limit ?? 10);

        return ApiResponse::success(
            new PaginatedResource($transactions, TransactionResource::class),
            'Transactions list'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $transaction = DB::transaction(function () use ($data) {
                $quantities = collect($data['items'])
                    ->groupBy('product_id')
                    ->map(fn ($rows): int => $rows->sum('quantity'));

                $products = Product::query()
                    ->whereKey($quantities->keys())
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $subtotal = '0';
                $itemData = [];

                foreach ($quantities as $productId => $quantity) {
                    $product = $products->get($productId);

                    if ($product->stock < $quantity) {
                        throw ValidationException::withMessages([
                            "items.{$productId}.quantity" => "Insufficient stock for {$product->name}. Available: {$product->stock}, requested: {$quantity}.",
                        ]);
                    }

                    $itemSubtotal = bcmul((string) $product->price, (string) $quantity, 2);
                    $subtotal = bcadd($subtotal, $itemSubtotal, 2);

                    $itemData[] = [
                        'product_id' => $product->id,
                        'price' => $product->price,
                        'quantity' => $quantity,
                        'subtotal' => $itemSubtotal,
                    ];
                }

                $tax = bcmul($subtotal, self::TAX_RATE, 2);
                $total = bcadd($subtotal, $tax, 2);

                $transaction = Transaction::create([
                    'code' => 'TRX-'.now()->format('YmdHis').'-'.strtoupper(bin2hex(random_bytes(3))),
                    'customer_id' => $data['customer_id'],
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $total,
                ]);

                $transaction->items()->createMany($itemData);

                foreach ($quantities as $productId => $quantity) {
                    $products[$productId]->decrement('stock', $quantity);
                }

                return $transaction;
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            report($e);

            return ApiResponse::error(
                'Failed to create transaction',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return ApiResponse::success(
            new TransactionResource($transaction->load(['items.product', 'customer'])),
            'Transaction created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $transaction = Transaction::query()
            ->with(['items.product', 'customer'])
            ->findOrFail($id);

        return ApiResponse::success(
            new TransactionResource($transaction),
            'Transaction detail'
        );
    }
}
