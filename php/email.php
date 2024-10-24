<?php
    include('connection.php');
    session_start();

    #Instalação Composer + PHPMailer
    #https://getcomposer.org/doc/00-intro.md#installation-windows SE NECESSÁRIO
    #https://git-scm.com/downloads/win SE NECESSÁRIO
    #cd C:\xampp\htdocs\software-quality-checklist-main
    #composer require phpmailer/phpmailer
    require '../vendor/autoload.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;      

    function formatarTemplate($itemchecklist, $checklist, $conn) {
        $responsavel_id = $itemchecklist['Responsavel'];
    
        $sql_responsavel = "SELECT Nome FROM usuario WHERE Cod_Usuario = '$responsavel_id'";
        $resultado_responsavel = mysqli_query($conn, $sql_responsavel);
    
        if ($resultado_responsavel && mysqli_num_rows($resultado_responsavel) > 0) {
            $responsavel = mysqli_fetch_assoc($resultado_responsavel)['Nome'];
        } else {
            $responsavel = "Responsável não encontrado";
        }
    
        if ($itemchecklist['Complexidade'] == 'Baixa') {
            $prazo_resolucao = '1 dia';
        } elseif ($itemchecklist['Complexidade'] == 'Média') {
            $prazo_resolucao = '3 dias';
        } elseif ($itemchecklist['Complexidade'] == 'Alta') {
            $prazo_resolucao = '5 dias';
        } else {
            $prazo_resolucao = 'Prazo indefinido';
        }
    
        $template = "
        <html>
        <head>
            <meta charset='UTF-8'>
        </head>
        <body>
            <h2>Solicitação de Resolução de Não Conformidade</h2>
            <table border='1' style='border-collapse: collapse; width: 100%;'>
                <tr>
                    <td><b>Código de Controle:</b></td>
                    <td>NCF-" . $itemchecklist['ID'] . "</td>
                </tr>
                <tr>
                    <td><b>Projeto:</b></td>
                    <td>" . $checklist['Titulo'] . "</td>
                </tr>
                <tr>
                    <td><b>Responsável:</b></td>
                    <td>" . $responsavel . "</td>
                </tr>
                <tr>
                    <td><b>Data da Solicitação:</b></td>
                    <td>" . date('d/m/Y') . "</td>
                </tr>
                <tr>
                    <td><b>Prazo de Resolução:</b></td>
                    <td>" . $prazo_resolucao . "</td>
                </tr>
                <tr>
                    <td><b>Descrição:</b></td>
                    <td>" . $itemchecklist['Nome'] . "</td>
                </tr>
                <tr>
                    <td><b>Classificação:</b></td>
                    <td>" . $itemchecklist['Complexidade'] . "</td>
                </tr>
            </table>
            <p><b>Observações:</b> Entrar em contato com o RQA responsável para mais informações.</p>
        </body>
        </html>
        ";
        
        return $template;
    }

    function enviarEmail($remetente, $destinatario, $assunto, $corpo_email) {
        $mail = new PHPMailer(true);
    
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'yonuttxd@gmail.com';
            $mail->Password = 'tjen zpmz jwlp bphw';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            $mail->setFrom($remetente, 'Sistema de Notificação');
            $mail->addAddress($destinatario);
    
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $assunto;
            $mail->Body    = $corpo_email;
    
            $mail->send();
            echo "E-mail enviado para $destinatario <br>";
        } catch (Exception $e) {
            echo "Falha ao enviar e-mail: {$mail->ErrorInfo} <br>";
        }
    }

    function main($conn) {
        $ID = $_POST['codChecklist'];
    
        $sqlC = "SELECT Titulo FROM checklist WHERE Cod_Checklist = '$ID'";
        $resultC = mysqli_query($conn, $sqlC);
        $rowC = mysqli_fetch_assoc($resultC);
    
        $sql = "SELECT ID, Nome, Complexidade, Responsavel, Escalonamento 
                FROM itemchecklist 
                WHERE fk_Cod_Checklist = '$ID' AND Conforme = 'NC'";
        $result = mysqli_query($conn, $sql);
    
        $remetente = 'daniel.mussee@pucpr.edu.br';
    
        while ($row = mysqli_fetch_assoc($result)) {
            $responsavel_id = $row['Responsavel'];
            $sql_responsavel = "SELECT Email FROM usuario WHERE Cod_Usuario = '$responsavel_id'";
            $destinatario_result = mysqli_query($conn, $sql_responsavel);
            
            if ($destinatario = mysqli_fetch_assoc($destinatario_result)) {
                $email = $destinatario['Email']; 
        
                $assunto = "Notificação de Não Conformidade Seção " . $row['ID'];
                $corpo_email = formatarTemplate($row, $rowC, $conn);
                enviarEmail($remetente, $email, $assunto, $corpo_email); 
            } else {
                echo "Email do responsável não encontrado.";
            }
        }
    }
    
    main($conn);
?>