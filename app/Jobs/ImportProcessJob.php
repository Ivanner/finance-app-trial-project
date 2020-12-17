<?php

namespace App\Jobs;

use App\Events\ImportProcessComplete;
use App\Models\ImportProcess;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\Reader\Csv;

class ImportProcessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var ImportProcess $fileImport */
    public $fileImport;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fileImport)
    {
        $this->fileImport = $fileImport;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->fileImport->imported) {
            $file = $this->fileImport->fileName;
            $reader = new Csv();
            $spreadsheet = $reader->load(public_path().'/'.$file);
            $worksheet = $spreadsheet->getActiveSheet();
            $data = $worksheet->toArray();
            $isHeader = true; // first row is header
            $transactions=[];
            foreach ($data as $row) {
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                } else {
                    array_push($transactions, [
                        'label' => $row[0],
                        'amount' => floatval($row[1]),
                        'date' => $row[2],
                        'import_process_id' => $this->fileImport->id
                    ]);
                }
            }
            $user = User::find($this->fileImport->user_id);
            $user->transactions()->createMany($transactions);
            $this->fileImport->imported = true;
            $this->fileImport->save();
            // make them visible to the user
            Transaction::where('import_process_id', $this->fileImport->id)->update(['user_id' =>  $this->fileImport->user_id]);
            ImportProcessComplete::dispatch($this->fileImport); // dispatch event
        }
    }
}
