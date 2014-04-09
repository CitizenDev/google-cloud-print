<?php
namespace Jamm\GoogleCloudPrint;

use Jamm\HTTP\Factory;

class Authenticator
{
  use Factory;

  private $url = "https://www.google.com/accounts/ClientLogin";
  private $throw_exceptions = false;

  public function getAuthToken($email, $password)
  {
    if (empty($email)) {
      return $this->raiseError("Email required", 400);
    }
    if (empty($password)) {
      return $this->raiseError("Password required", 400);
    }
    $Request = $this->getNewRequest();
    $Request->setMethod('POST');
    $Request->setData(['accountType' => 'HOSTED_OR_GOOGLE',
                       'Email'       => $email,
                       'Passwd'      => $password,
                       'service'     => 'cloudprint',
                       'source'      => 'GCP']);
    $Response = $this->getNewResponse();
    $Request->Send($this->url, $Response);
    if ($Response->isStatusError()) {
      return $this->raiseError($Response->getBody(), $Response->getStatusCode());
    }
    preg_match("/Auth=([a-z0-9_-]+)/i", $Response->getBody(), $matches);
    if (!empty($matches[1])) {
      return $matches[1];
    }
    return false;
  }

  /**
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
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
