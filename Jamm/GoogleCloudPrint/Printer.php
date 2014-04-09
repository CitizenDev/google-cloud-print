<?php
namespace Jamm\GoogleCloudPrint;

class Printer
{
  /** @var API */
  private $API;
  private $id;
  private $ticket;

  public function __construct(API $API, $id)
  {
    $this->API = $API;
    $this->id  = $id;
  }

  public function printFile($file, $content_type = 'application/pdf', $job_title = '')
  {
    if (empty($job_title)) {
      $job_title = basename($file).' '.date('Y-m-d H:i:s');
    }
    return $this->API->sendFileToPrinter($this->id, $job_title, $file, $content_type, $this->ticket);
  }

  public function printURL($url, $job_title = '')
  {
    if (empty($job_title)) {
      $job_title = basename($url).' '.date('Y-m-d H:i:s');
    }
    return $this->API->sendURLToPrinter($this->id, $job_title, $url, $this->ticket);
  }

  public function printContent($content, $content_type = 'application/pdf', $job_title = '')
  {
    if (empty($job_title)) {
      $job_title = $content_type.' '.date('Y-m-d H:i:s');
    }
    return $this->API->sendContentToPrinter($this->id, $job_title, $content, $content_type, $this->ticket);
  }

  /**
   * @return mixed
   */
  public function getTicket()
  {
    return $this->ticket;
  }

  /**
   * @param mixed $settings
   */
  public function setTicket($settings)
  {
    $this->ticket = $settings;
  }
} 