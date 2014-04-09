<?php
namespace Jamm\GoogleCloudPrint;

use Jamm\HTTP\Factory;
use Jamm\HTTP\SerializerJSON;

class API
{
  use Factory;

  private $url = "https://www.google.com/cloudprint/interface/";
  private $throw_exceptions = false;

  private $auth_token;

  public function __construct($auth_token, $throw_exceptions = false)
  {
    $this->throw_exceptions = $throw_exceptions;
    if (empty($auth_token)) {
      return $this->raiseError("Authentication token required", 400);
    }
    $this->auth_token = $auth_token;
    return true;
  }

  public function getPrinters()
  {
    $response = $this->sendApiRequest('search', 'GET');
    if (!$response) {
      return false;
    }
    return $response['printers'];
  }

  public function sendFileToPrinter($printer_id, $job_title, $file_path, $content_type = 'application/pdf')
  {
    if (empty($printer_id)) {
      return $this->raiseError("Printer ID required", 400);
    }
    $contents = file_get_contents($file_path);
    if (!$contents) {
      return $this->raiseError("Can't read file contents", 500);
    }

    $data     = array(
      'printerid'               => $printer_id,
      'title'                   => $job_title,
      'contentTransferEncoding' => 'base64',
      'content'                 => base64_encode($contents),
      'contentType'             => $content_type
    );
    $response = $this->sendApiRequest('submit', 'POST', $data);
    if (!$response) {
      return false;
    }
    return true;
  }

  public function sendURLToPrinter($printerid, $printjobtitle, $url)
  {
    if (empty($printer_id)) {
      return $this->raiseError("Printer ID required", 400);
    }

    $data     = array(
      'printerid'   => $printerid,
      'title'       => $printjobtitle,
      'content'     => $url,
      'contentType' => 'url'
    );
    $response = $this->sendApiRequest('submit', 'POST', $data);
    if (!$response) {
      return false;
    }
    return true;
  }

  public function sendContentToPrinter($printer_id, $print_job_title, $content, $content_type = 'application/pdf')
  {
    if (empty($printer_id)) {
      return $this->raiseError("Printer ID required", 400);
    }

    if (empty($content)) {
      return $this->raiseError("Empty content", 400);
    }

    $data     = array(
      'printerid'               => $printer_id,
      'title'                   => $print_job_title,
      'contentTransferEncoding' => 'base64',
      'content'                 => base64_encode($content),
      'contentType'             => $content_type
    );
    $response = $this->sendApiRequest('submit', 'POST', $data);
    if (!$response) {
      return false;
    }
    return true;
  }

  public function sendApiRequest($resource, $method = 'POST', $data = null)
  {
    $Request    = $this->getNewRequest();
    $Response   = $this->getNewResponse();
    $Serializer = new SerializerJSON();
    $Response->setSerializer($Serializer);

    $Request->setHeader('Authorization', 'GoogleLogin auth='.$this->auth_token);
    $Request->setHeader('GData-Version', '3.0');
    $Request->setMethod($method);
    if (!empty($data)) {
      $Request->setData($data);
    }
    $Request->send($this->url.$resource, $Response);
    if ($Response->isStatusError()) {
      return $this->raiseError($Response->getBody(), $Response->getStatusCode());
    }
    $body = $Response->getBody();
    if (!isset($body['success']) || empty($body['success'])) {
      return $this->raiseError($body['message'], $body['errorCode']);
    }
    return $body;
  }

  /**
   * @param boolean $throw_exceptions
   */
  public function setThrowExceptions($throw_exceptions = true)
  {
    $this->throw_exceptions = $throw_exceptions;
  }

  protected function raiseError($error, $code = 0)
  {
    if ($this->throw_exceptions) {
      throw new \Exception($error, $code);
    }
    if (!empty($code)) {
      $error = $code.': '.$error;
    }
    trigger_error($error, E_USER_WARNING);
    return false;
  }
}