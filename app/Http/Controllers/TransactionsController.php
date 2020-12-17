<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Jobs\ImportProcessJob;
use App\Models\ImportProcess;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class TransactionsController extends Controller
{
    /**
     * Gets a list of transactions.
     * Ideally this would be limited to the user and with a date range
     * @param  Request  $request
     * @return array
     */
    public function list(Request $request): array
    {
        /** @var User $user */
        $user = $request->user();
        $totalBalance = $user->transactions()->sum('amount');
        $data = $user->transactions()->orderBy('date', 'desc')->paginate(50);
        return ['totalBalance' => $totalBalance, 'transactions' => $data];
    }

    /**
     * Creates a new transaction
     * @param  Request  $request
     * @return int|array
     */
    public function create(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        //validation for inputs
        $validator = Validator::make(
            $request->all(),
            [
                'label' => 'required',
                'amount' => 'required|numeric',
                'date' => 'required|date'
            ]
        );
        if ($validator->fails()) {
            // display errors on each form field
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                array_push($errors, $message[0]);
            }
            return ['message' => implode('. ', $errors)];
        }
        /** @var Transaction $transaction */
        $transaction = $user->transactions()->save(new Transaction(
                                        $request->all()
                                    ));
        return $transaction->id;
    }

    /**
     * Updates an existing transaction
     * @param  Transaction $transaction
     * @param  TransactionRequest $request
     * @return bool|string
     */
    public function update(Transaction $transaction, TransactionRequest $request)
    {
        return $transaction->save($request->validated());
    }

    /**
     * Deletes a transaction
     * Ideally this would be limited to the user's transactions
     * @param  Transaction  $transaction
     * @return bool
     */
    public function delete(Transaction $transaction): bool
    {
        try {
            return $transaction->delete();
        } catch (Exception $ex) {
            return false;
        }
    }

    public function import(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'file' => 'required|mimes:csv,txt'
            ]
        );
        if ($validator->fails()) {
            // display errors on each form field
            $errors = [];
            foreach ($validator->errors()->getMessages() as $message) {
                array_push($errors, $message[0]);
            }
            return ['message' => implode('. ', $errors)];
        }

        $file = $request->file('file');
        $file->move(storage_path(), $file->getClientOriginalName());
        $reader = new Csv();
        $spreadsheet = $reader->load(storage_path().'/'.$file->getClientOriginalName());
        $worksheet = $spreadsheet->getActiveSheet();
        // Get the highest row and column numbers referenced in the worksheet
        $highestRow = $worksheet->getHighestRow() - 1; // assuming first row is headers
        /** @var ImportProcess $importProcess */
        $importProcess = ImportProcess::firstOrNew(['fileName' => $file->getClientOriginalName()]);
        $importProcess->user_id = $request->user()->id;
        $importProcess->imported = 0; //file was not imported
        $importProcess->rowsImported = 0; //file was not imported
        $importProcess->totalRows = $highestRow;
        $importProcess->save();
        ImportProcessJob::dispatch($importProcess);
        return $importProcess;
    }
}
