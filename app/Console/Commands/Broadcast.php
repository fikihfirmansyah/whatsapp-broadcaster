<?php

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;

class Broadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Respectively send an exclusive broadcast to everyone daily via whatsapp.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $messages = Message::where('status', "pending")->take(5)->get();

        foreach ($messages as $message) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://agathabot002.herokuapp.com/send-message',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('number' => $message->target . '@c.us', 'message' => $message->message),
            ));
            curl_exec($curl);
            curl_close($curl);

            $message->status = "sent";
            $message->save();
        }

        return Command::SUCCESS;
    }
}
