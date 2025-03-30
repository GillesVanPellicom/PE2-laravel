<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class testMail extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'test:mail';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';

  /**
   * Execute the console command.
   */
  public function handle(): void {
    try {

      // DO NOT FORGET TO CHANGE THE EMAIL ADDRESS
      $addr = "gilles.van.pellicom1@gmail.com";

      Mail::send('emails.test', [], function ($message) use ($addr) {
        $message->to($addr)
          ->subject('Test Email');
      });
      $this->info('Test email sent successfully to '.$addr);
    } catch (\Exception $e) {
      $this->error('Failed to send email: '.$e->getMessage());
    }
  }
}
