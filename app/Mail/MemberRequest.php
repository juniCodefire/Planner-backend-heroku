<?php

 namespace App\Mail;


 use Illuminate\Bus\Queueable;
 use Illuminate\Mail\Mailable;
 use Illuminate\Queue\SerializesModels;
 use Illuminate\Contracts\Queue\ShouldQueue;


 class MemberRequest extends Mailable
 {
    use Queueable,
        SerializesModels;

    public $requester;
    public $requestee;
    public $workspace;
    public $company;

    public function __construct($requester, $requestee, $workspace, $company) {

      $this->requester  = $requester;
      $this->requestee  = $requestee;
      $this->workspace = $workspace;
      $this->company = $company;

    }

    public function build() {
      $requester = $this->requester;
      $requestee = $this->requestee;
      $workspace = $this->workspace;
      $company   = $this->company;

      return $this->view('member-request', compact($requester, $requestee, $workspace, $company));
    }

 }
