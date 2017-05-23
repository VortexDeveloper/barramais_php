<?php
require_once("PHPMailer-master/PHPMailerAutoload.php");

class Contact {
  private $mailer;
  private $error_info;
  private $format_table_message = false;
  private $mailbox_email = 'noreply@barramais.com.br';
  private $mailbox_name = "Não Responder";
  private $sender_email;
  private $sender_name;
  private $subject;
  private $message;

  function __construct($sender_email, $sender_name) {
    $this->sender_email = $sender_email;
    $this->sender_name = $sender_name;

    $this->mailer = new PHPMailer;
    $this->mailer->AddReplyTo($this->sender_email, $this->sender_name);
    $this->mailer->setFrom($this->mailbox_email, $this->mailbox_name);
    $this->config_mailer();
  }

  public function send_to($addresse_email, $addresse_name) {
    $this->mailer->AddAddress($addresse_email, $addresse_name);
  }

  public function send($subject, $message){
    $this->subject = $subject;
    $this->message = $message;
    $this->mailer->Subject = $this->subject;

    //REFATORAR
    if($this->format_table_message)
      $this->mailer->msgHTML($this->_format_table_message());
    else
      $this->mailer->msgHTML($this->message);

    return $this->_send();
  }

  public function get_error_info() {
    return $this->error_info;
  }

  public function format_table_message($format) {
    $this->format_table_message = $format;
  }

  private function config_mailer() {
    $this->mailer->IsSMTP(); // Define que a mensagem será SMTP
		$this->mailer->Host = "smtp.barramais.com.br"; // Endereço do servidor SMTP
		$this->mailer->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
		$this->mailer->Username = $this->mailbox_email; // Usuário do servidor SMTP
		$this->mailer->Password = 'barramais2016'; // Senha do servidor SMTP
		$this->mailer->Wordwrap = 50;
    $this->mailer->IsHTML(true); // Define que o e-mail será enviado como HTML
		$this->mailer->CharSet = 'UTF-8'; // Charset da mensagem (opcional)
    // $this->mailer->SMTPDebug = 2;
    $this->mailer->Port = 587;
  }

  private function _send() {
		$sent = $this->mailer->Send();
		$this->mailer->ClearAllRecipients();
		$this->mailer->ClearAttachments();
    $this->error_info = $this->mailer->ErrorInfo;

    return $sent;
  }

  //REFATORAR PARA RECEBER OS CAMPOS POR PARÂMETRO
  private function _format_table_message() {
    $body = "<style>
         *{margin:0; padding: 0;}
         td{border: 2px solid #ccc !important; border-collapse:collapse; padding: 5px;}
         </style>
         <table>
           <tr>
             Contato Feito Pelo Site
           </tr>
           <tr>
             <td>Nome:</td>
             <td>$this->sender_name</td>
           </tr>
           <tr>
             <td>Email:</td>
             <td>$this->sender_email</td>
           </tr>
           <tr>
             <td>Mensagem:</td>
             <td>$this->message</td>
           </tr>
         </table>";

    $date = date('d/m/Y H:i');
 		$body .="<br><p>Enviado em: ".$date."";
    utf8_decode($body);

    return $body;
  }
}
?>
