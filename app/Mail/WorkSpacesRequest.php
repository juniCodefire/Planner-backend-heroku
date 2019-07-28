<?php

 namespace App\Mail;


 use Illuminate\Bus\Queueable;
 use Illuminate\Mail\Mailable;
 use Illuminate\Queue\SerializesModels;
 use Illuminate\Contracts\Queue\ShouldQueue;


 class WorkSpacesRequest extends Mailable
 {
    use Queueable,
        SerializesModels;

    public $requester;
    public $requestee;
    public $workspace;

    public function __construct($requester, $requestee, $workspace) {

      $this->requester  = $requester;
      $this->requestee  = $requestee;
      $this->workspace = $workspace;

    }

    public function build() {
      $requester = $this->requester;
      $requestee = $this->requestee;
      $workspace = $this->workspace;

      return $this->view('workspace-request', compact($requester, $requestee, $workspace));
    }

 }
