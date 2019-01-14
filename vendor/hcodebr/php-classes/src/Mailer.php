<?php 
	namespace Hcode;
	use Rain\Tpl;
	class Mailer {
	    const USERNAME = "visaodigitalgroup@gmail.com";
	    const PASSWORD = "lassana94";
	    const NAME_FROM = "PHP Ecommerce";
	    
	    private $mail;
	    public function __construct($toAddress, $toName, $subject, $tplName, $data = array())
	    {
	        $config = array(
	            "tpl_dir"       => $_SERVER['DOCUMENT_ROOT']."/views/email/",
	            "cache_dir"     => $_SERVER['DOCUMENT_ROOT']."/views-cache/",
	            "debug"         => false
	        );
	        Tpl::configure( $config );
	        $tpl = new Tpl;
	        foreach ($data as $key => $value) { 
	            $tpl->assign($key, $value);
	        }
	        $html = $tpl->draw($tplName, true);
	        //instanciar e configurar
	        $this->mail = new \PHPMailer;
	        $this->mail->CharSet = 'UTF-8';
	        $this->mail->isSMTP();
	        $this->mail->SMTPOptions = array(
	            'ssl' => array(
	                'verify_peer' => false,
	                'verify_peer_name' => false,
	                'allow_self_signed' => true
	            )
	        );
	        //configuração
	        $this->mail->SMTPDebug = 0;
	        $this->mail->Debugoutput = 'html';
	        $this->mail->Host = 'smtp.gmail.com';
	        $this->mail->Port = 587;
	        $this->mail->SMTPSecure = 'tls';
	        $this->mail->SMTPAuth = true;
	        $this->mail->Username = Mailer::USERNAME;
	        $this->mail->Password = Mailer::PASSWORD;
	        $this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);
	        //Dados de email
	        $this->mail->addAddress($toAddress, $toName);
	        $this->mail->Subject = $subject;
	        $this->mail->msgHTML($html);
	        $this->mail->AltBody = 'This is a plain-text message body';
	    }
	    // função de envio
	    public function send()
	    {
	        return $this->mail->send();
	    }
	}
?>