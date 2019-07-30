<?php

 namespace App\Mail;


 use Illuminate\Bus\Queueable;
 use Illuminate\Mail\Mailable;
 use Illuminate\Queue\SerializesModels;
 use Illuminate\Contracts\Queue\ShouldQueue;


 class CompanyRequest extends Mailable
 {
    use Queueable,
        SerializesModels;

    public $requester;
    public $requestee;
    public $company;

    public function __construct($requester, $requestee, $company) {

      $this->requester  = $requester;
      $this->requestee  = $requestee;
      $this->company = $company;

    }

    public function build() {
      $requester = $this->requester;
      $requestee = $this->requestee;
      $company = $this->company;

      return $this->view('company-request', compact($requester, $requestee, $company));
    }

 }
