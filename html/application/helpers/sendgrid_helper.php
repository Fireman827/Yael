<?php
function MailSend($tos,$subject,$body,$attachment=array())
{
  require 'vendor/autoload.php';
  $cfg = TraerUnDato('configuraciones', "parametroConfiguracion = 'SENDGRID_API_KEY' AND estadoConfiguracion = 'Activo'");
  $key = $cfg ? $cfg->valorConfiguracion : '';
  $email = new \SendGrid\Mail\Mail();
  $email->setFrom('reportes@digitalsmindssystems.com', "DTE - DMS");
  $email->setSubject($subject);
  foreach ($tos as $to) {
      $email->addTo($to["email"], $to["name"]);
  }
  $email->addContent(
    "text/html", $body
  );
  if(count($attachment) > 0){
    foreach ($attachment as $file_attach) {
      $file_encoded = base64_encode(file_get_contents($file_attach["url"]));
      $email->addAttachment(
         $file_encoded,
         "application/".$file_attach["extension"],
         $file_attach["name"],
         "attachment"
      );
    }
  }
  $sendgrid = new \SendGrid($key);
  try {
    $response = $sendgrid->send($email);
    // echo $response->statusCode();
    if($response->statusCode() == 200 || $response->statusCode()==202){
      $exit = 1;
    }else {
      $exit = 0;
    }
  } catch (Exception $e){
    $exit = 0;
  }
  return $exit;
}
